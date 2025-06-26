<?php
function getNearestDateTime(string $dayName, string $time): string {
    $dayName = ucfirst(strtolower(trim($dayName)));
    $validDays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    if (!in_array($dayName, $validDays)) throw new Exception("Invalid day: $dayName");

    $today = new DateTime();
    $todayDay = (int) $today->format('w');
    $targetDay = array_search($dayName, $validDays);

    $daysAhead = ($targetDay - $todayDay + 7) % 7;
    if ($daysAhead === 0 && $today->format('H:i') >= $time) $daysAhead = 7;

    $target = (new DateTime())->modify("+$daysAhead days");
    return $target->format("Y-m-d") . " $time:00";
}

$pdo = new PDO('mysql:host=localhost;dbname=teras', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["image"])) {
    $tmp = $_FILES["image"]["tmp_name"];
    $file_data = base64_encode(file_get_contents($tmp));

    $apiKey = "AIzaSyBOHZnk8G4GQky7QixQ-R0sO_GfFzuaGHo";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

    $prompt = "Dari gambar jadwal berikut, buatkan daftar aktivitas dalam format JSON array. \
Setiap elemen harus memiliki 'hari', 'mulai', 'selesai', dan 'nama'. \
Format waktu 24 jam (contoh: 08:00) dan tuliskan hari dalam bahasa inggris, nama aktivitas adalah nama kegiatan seperti mata kuliah dan aktivitas.";

    $data = [
        "contents" => [[
            "parts" => [
                ["text" => $prompt],
                ["inline_data" => ["mime_type" => "image/jpeg", "data" => $file_data]]
            ]
        ]]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    $text = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (preg_match('/\[(.*?)\]/s', $text, $match)) {
        $json = json_decode($match[0], true);
        foreach ($json as $item) {
            $day = $item['hari'] ?? '';
            $start = $item['mulai'] ?? '';
            $end = $item['selesai'] ?? '';
            $nama = $item['nama'] ?? '';
            if ($day && $start && $end && $nama) {
                $startDate = getNearestDateTime($day, $start);
                $endDate = getNearestDateTime($day, $end);
                $stmt = $pdo->prepare("INSERT INTO jadwal (userId, namaAktivitas, hariAgenda, tanggalMulaiAgenda, tanggalSelesaiAgenda) VALUES (1, ?, ?, ?, ?)");
                $stmt->execute([$nama, $day, $startDate, $endDate]);
            }
        }
        echo "<p>Agenda berhasil dimasukkan ke database.</p>";
    } else {
        echo "<p>Gagal memproses hasil dari AI.</p>";
    }
}

date_default_timezone_set("Asia/Jakarta");
$now = new DateTime();
$stmt = $pdo->query("SELECT * FROM jadwal ORDER BY tanggalMulaiAgenda ASC");
$jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

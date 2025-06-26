<?php  
include '../koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

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

$json = json_decode($_POST['formData'] ?? '', true);
        foreach ($json as $item) {
            $day = $item['hari'] ?? '';
            $start = $item['mulai'] ?? '';
            $end = $item['selesai'] ?? '';
            $nama = $item['nama'] ?? '';
            if ($day && $start && $end && $nama) {
                $startDate = getNearestDateTime($day, $start);
                $endDate = getNearestDateTime($day, $end);
                $sql = "INSERT INTO jadwal (userId, namaAktivitas, tanggalMulaiAgenda, tanggalSelesaiAgenda, mengulang) VALUES ('$user_id', '$nama','$startDate','$endDate','1')";
                if (mysqli_query($conn, $sql)){
                    echo "successfull";
                } else {
                    echo "Error:".mysqli_error($conn);
                }         
                        }
                    }

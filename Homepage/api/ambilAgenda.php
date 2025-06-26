<?php
require '../koneksi.php';
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once 'weather-lib.php';

$id_user = $_SESSION['user_id'];
$weatherdata = json_decode(getRealtimeData($id_user), true);
$forecastdata = json_decode(getForecastData($id_user), true)['timelines']['hourly']; 

$weatherByHour = [];
$weatherTimezone = new DateTimeZone('Asia/Jakarta');

foreach ($forecastdata as $hourData) {
    $dt = new DateTime($hourData['time']);
    $dt->setTimezone($weatherTimezone);
    $key = $dt->format('Y-m-d H:i');
    $weatherByHour[$key] = $hourData['values'];
}

$timezone = isset($_GET['timezone']) ? $_GET['timezone'] : 'UTC';
if (!in_array($timezone, timezone_identifiers_list())) {
    $timezone = 'UTC';
}

$now = new DateTime();
$futureLimit = (clone $now)->modify('+7 days');

$query = "SELECT * FROM jadwal WHERE userId = $id_user";
$result = mysqli_query($conn, $query);

$agendaByDate = [];

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['agendaId'];
    $title = $row['namaAktivitas'];
    $repeat = (int)$row['mengulang'];
    $start = new DateTime($row['tanggalMulaiAgenda']);
    $end = new DateTime($row['tanggalSelesaiAgenda']);

    if ($end < $now) {
        continue;
    }

    $intervalSpec = match ($repeat) {
        2 => 'P1D',
        3 => 'P1W',
        4 => 'P2W',
        5 => 'P1M',
        default => null,
    };

    if (!$intervalSpec) {
        if ($end >= $now && $start <= $futureLimit) {
            $agendaByDate[$start->format('Y-m-d')][] = [
                'id' => $id,
                'title' => $title,
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
                'isRunning' => ($now >= $start && $now <= $end),
            ];
        }
    } else {
        $next = clone $start;
        $endRepeat = clone $futureLimit;
        $interval = new DateInterval($intervalSpec);
        $durationInSeconds = $end->getTimestamp() - $start->getTimestamp();

        while ($next <= $endRepeat) {
            $nextEnd = (clone $next)->modify("+$durationInSeconds seconds");

            if ($nextEnd < $now) {
                $next->add($interval);
                continue;
            }

            if ($next > $futureLimit) {
                break;
            }

            $agendaByDate[$next->format('Y-m-d')][] = [
                'id' => $id,
                'title' => $title,
                'start' => $next->format('H:i'),
                'end' => $nextEnd->format('H:i'),
                'isRunning' => ($now >= $next && $now <= $nextEnd),
            ];

            $next->add($interval);
        }
    }
}

ksort($agendaByDate);

// Output
if (empty($agendaByDate)) {
    echo '<div class="no-agenda">You haven\'t set any future agenda.</div>';
} else {
    foreach ($agendaByDate as $date => $items) {
        $isToday = ($date === $now->format('Y-m-d'));
        $displayDate = $isToday ? 'today' : date('l, d F Y', strtotime($date));
        $dayname = substr(date('l', strtotime($date)), 0, 3);
        $daydate = date('j', strtotime($date));
        usort($items, fn($a, $b) => strcmp($a['start'], $b['start']));
?>
        <div class="agenda-date">
            <div class="wrapper-day">
                <?php if ($isToday): ?>
                    <p class="today">Today</p>
                <?php else: ?>
                    <div class="day"><?= $dayname . ',' ?></div>
                    <div class="tanggal"><?= $daydate ?></div>
                <?php endif; ?>
            </div>
            <div class="rectangle-container">
<?php
        foreach ($items as $item) {
            $agendaDate = $date;
            $startHour = DateTime::createFromFormat('Y-m-d H:i', "$agendaDate {$item['start']}");
            $endHour = DateTime::createFromFormat('Y-m-d H:i', "$agendaDate {$item['end']}");
            $weatherStatus = 'cerah';

            if ($item['isRunning']) {
                $rainIntensity = $weatherdata['data']['values']['rainIntensity'] ?? 0;
                $weatherStatus = ($rainIntensity > 0) ? 'hujan' : 'cerah';
            } else {
                $foundRain = false;
                $rainAtStart = false;

                $checkHour = clone $startHour;
                while ($checkHour <= $endHour) {
                    $lookupKey = $checkHour->format('Y-m-d H:00');
                    if (isset($weatherByHour[$lookupKey])) {
                        $rainIntensity = $weatherByHour[$lookupKey]['rainIntensity'] ?? 0;
                        if ($rainIntensity > 0) {
                            $foundRain = true;
                            if ($checkHour == $startHour) {
                                $rainAtStart = true;
                                break;
                            }
                        }
                    }
                    $checkHour->modify('+1 hour');
                }

                if ($rainAtStart) {
                    $weatherStatus = 'hujan';
                } elseif ($foundRain) {
                    $weatherStatus = 'mendung';
                }
            }

            $status = $item['isRunning'] ? " <strong>(Sedang berlangsung)</strong>" : "";
            $iconPath = match ($weatherStatus) {
                'hujan' => './assets/carbon_umbrella.png',
                'mendung' => './assets/material-symbols-light_umbrella-rounded.png',
                default => './assets/Sun.png'
            };
?>
            <div class="rec-items">
                <div class="delete-button">
                    <button onclick="Delete(<?= $item['id'] ?>)">
                        <img src="./assets/material-symbols_delete-rounded.png" alt="delete">
                    </button>
                </div>
                <div class="<?= $weatherStatus ?>" onclick="toggleSlide(this)" data-agenda-id="<?= $item['id'] ?>">
                    <div>
                        <p><?= $item['title'] . $status ?></p>
                        <p class="jam-cerah"><?= $item['start'] . ' - ' . $item['end'] ?></p>
                    </div>
                    <img class="right-side-icon" src="<?= $iconPath ?>" alt="<?= $weatherStatus ?>">
                </div>
            </div>
<?php   } ?>
            </div> 
        </div> 
<?php
    }
}
?>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require 'weather-lib.php';

$timezone = isset($_GET['timezone']) ? $_GET['timezone'] : 'UTC';
if (!in_array($timezone, timezone_identifiers_list())) {
    $timezone = 'UTC';
}


$json_data = json_decode(getForecastData($_SESSION['user_id']), true); 
$data = $json_data['timelines']['hourly'];

for ($i = 1; $i < min(13, count($data)); $i++) {
    $dt = new DateTime($data[$i]['time'], new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone($timezone));
    $localTime = $dt->format('g A'); 
?>
<div class="hourly"> <!-- ini kotak infonya -->
    <div class="timezone hour">
        <?= $localTime ?>
    </div>

    <div class="hourly-temp">
        <?= round($data[$i]['values']['temperature'],0).'°' ?>
    </div>
</div>
<?php
}


?>
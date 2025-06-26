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


$json_data = json_decode(getForecastDaily($_SESSION['user_id']), true); 
$data = $json_data['timelines']['daily'];

foreach ($data as $item) {
    $dt = new DateTime($item['time'], new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone($timezone));
    $localTime = $dt->format('D, d'); 
    $src = "";
    $alt = "";
    if ($item["values"]["humidityAvg"]>=90){
        $src = "./assets/wi-rain.png"; 
        $alt ="hujan deras";
    } if ($item["values"]["humidityAvg"]<90&&$item["values"]["humidityAvg"]>=70){
        $src = "./assets/wi-showers.png";
        $alt="hujan petir";
    } if ($item["values"]["humidityAvg"]<70&&$item["values"]["humidityAvg"]>=50){
        $src = "./assets/wi-cloudy-windy.png"; 
        $alt ="awan gelap";
    } if ($item["values"]["humidityAvg"]<50&&$item["values"]["humidityAvg"]>=30){
        $src="./assets/wi-cloudy.png";
        $alt="berawan";
    } if ($item["values"]["humidityAvg"]<30){
        $src="./assets/wi-day-sunny.png";
        $alt="cerah";
    }
?>
<div class="info-container"> 
            <div class="date">
                <?= $localTime ?>
            </div>

            <div class="humidity">
                <?= $item["values"]["humidityAvg"]."%" ?>
            </div>

            <div class="icon">
                <img src="<?= $src ?>" alt="<? $alt?>">
            </div>
        </div>
<?php
}


?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
include "weather-lib.php";
$userId = $_SESSION['user_id']; // Unique per user


$data = json_decode(getRealtimeData($userId), true);
$forecast = json_decode(getForecastDaily($userId), true)["timelines"]["daily"][0]['values'];
function degreeToDirection($degree) {
  $directions = array('North', 'North East', 'East', 'South East', 'South', 'South West', 'West', 'North West');
  $index = round($degree / 45) % 8;
  return $directions[$index];
}

$simplified = [
  'temperature' => $data['data']['values']['temperature'],
  'temperatureApparent' => $data['data']['values']['temperatureApparent'],
  'humidity' => $data['data']['values']['humidity'],
  'location' => $data['location']['name'],
  'rainIntensity' => $data['data']['values']['rainIntensity'],
  'windSpeed' => $data['data']['values']['windSpeed'],
  'windDirection' => degreeToDirection($data['data']['values']['windDirection']),
  'sunriseTime'=> $forecast['sunriseTime'],
  'sunsetTime' => $forecast['sunsetTime'],
  'visibility' => $data['data']['values']['visibility']
];

if ($data['data']['values']['rainIntensity']== 0){
  $simplified += ['rainStatus' => 'Not Raining'];
} elseif (0<$data['data']['values']['rainIntensity'] && $data['data']['values']['rainIntensity']<=2.5){
  $simplified += ['rainStatus' => 'Light Rain'];
} elseif (2.5<$data['data']['values']['rainIntensity'] && $data['data']['values']['rainIntensity']<=10){
  $simplified += ['rainStatus' => 'Moderate Rain'];
} elseif (11<$data['data']['values']['rainIntensity'] && $data['data']['values']['rainIntensity']<=30){
  $simplified += ['rainStatus' => 'Heavy Rain'];
} else{
  $simplified += ['rainStatus' => 'Extreme Rain'];
}

if(array_key_exists('uvIndex',$data['data']['values'])){
  $simplified += ['uvIndex' => $data['data']['values']['uvIndex']];
if (0<=$data['data']['values']['uvIndex']&&$data['data']['values']['uvIndex']<=2){
  $simplified += ['uvStatus' => 'Low'];
} elseif (3<=$data['data']['values']['uvIndex'] && $data['data']['values']['uvIndex']<=5){
  $simplified += ['uvStatus' => 'Moderate'];
} elseif (6<=$data['data']['values']['uvIndex'] && $data['data']['values']['uvIndex']<=7){
  $simplified += ['uvStatus' => 'High'];
} elseif (8<=$data['data']['values']['uvIndex'] && $data['data']['values']['uvIndex']<=10){
  $simplified += ['uvStatus' => 'Very High'];
} else{
  $simplified += ['uvStatus' => 'Extreme'];
}}


header('Content-Type: application/json');
echo json_encode($simplified, JSON_PRETTY_PRINT);
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
require 'weather-lib.php';

$data = getForecastData($_SESSION['user_id']);
header('Content-Type: application/json');
echo $data;
?>
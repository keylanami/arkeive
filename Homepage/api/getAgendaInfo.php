<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';
$id_user = $_SESSION['user_id'];
$id_agenda = intval($_GET['id']);
$sql = "SELECT * FROM jadwal WHERE agendaId = $id_agenda AND userId = $id_user";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
header('Content-Type: application/json');
echo json_encode($row);
?>
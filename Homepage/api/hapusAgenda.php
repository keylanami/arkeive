<?php  
include '../koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$id_user = $_SESSION['user_id'];
$id_agenda = intval($_GET['id']);

$sql = "SELECT userId FROM jadwal WHERE agendaId = $id_agenda";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row && $row['userId'] == $id_user) {
    $sql = "DELETE FROM jadwal WHERE agendaId = $id_agenda";
    mysqli_query($conn, $sql);
} else {
    exit();
}
?>

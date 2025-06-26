<?php  
include '../koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
if ($_POST['repeat']<0&&$_POST['repeat']>5){
    exit();
}

$id_user = $_SESSION['user_id'];
$title = trim(mysqli_real_escape_string($conn, $_POST['agendaTitle']));
$tanggalMulai = $_POST['startingDate'].' '.$_POST['startingHour'];
$tanggalSelesai = $_POST['endingDate'].' '.$_POST['endingHour'];

$id_user = $_SESSION['user_id'];
$id_agenda = intval($_POST['id']);

$sql = "SELECT userId FROM jadwal WHERE agendaId = $id_agenda";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row && $row['userId'] == $id_user) {
    $sql = "UPDATE jadwal SET namaAktivitas = '$title', tanggalMulaiAgenda = '$tanggalMulai', tanggalSelesaiAgenda = '$tanggalSelesai', mengulang = '$_POST[repeat]' WHERE agendaId = '$id_agenda'";
    mysqli_query($conn, $sql);
} else {
    exit();
}
?>

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

if (!empty($title)) {
    $sql = "INSERT INTO jadwal (userId, namaAktivitas, tanggalMulaiAgenda, tanggalSelesaiAgenda, mengulang) VALUES ('$id_user', '$title','
    $tanggalMulai','$tanggalSelesai','$_POST[repeat]')";
    if (mysqli_query($conn, $sql)){
        exit();
    } else {
        echo "Error:".mysqli_error($conn);
    }
}
?>

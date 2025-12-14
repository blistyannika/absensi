<?php
session_start();
include 'koneksi.php';

if (!isset($_POST['login_id']) || !isset($_POST['password'])) {
  header("Location: login.php");
  exit;
}

$login_id = mysqli_real_escape_string($conn, $_POST['login_id']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

/* ================= LOGIN GURU ================= */
$q_guru = mysqli_query($conn,"
  SELECT * FROM guru 
  WHERE username='$login_id' 
  AND password='$password'
");

if (mysqli_num_rows($q_guru) === 1) {
  $guru = mysqli_fetch_assoc($q_guru);

  $_SESSION['login'] = true;
  $_SESSION['id_guru'] = $guru['id_guru'];
  $_SESSION['nama_guru'] = $guru['nama_guru'];
  $_SESSION['role'] = 'guru';

  header("Location: guru.php");
  exit;
}

/* ================= LOGIN SISWA ================= */
$q_siswa = mysqli_query($conn,"
  SELECT * FROM siswa 
  WHERE nis='$login_id' 
  AND password='$password'
");

if (mysqli_num_rows($q_siswa) === 1) {
  $siswa = mysqli_fetch_assoc($q_siswa);

  $_SESSION['login_siswa'] = true;
  $_SESSION['id_siswa'] = $siswa['id_siswa'];
  $_SESSION['nama_siswa'] = $siswa['nama_siswa'];
  $_SESSION['role'] = 'siswa';

  header("Location: siswa.php");
  exit;
}

/* ================= GAGAL ================= */
echo "<script>
  alert('Username / NIS atau Password salah!');
  window.location='login.php';
</script>";

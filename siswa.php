<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login_siswa']) || $_SESSION['role'] != 'siswa') {
  header("Location: login.php");
  exit;
}

$id_siswa = $_SESSION['id_siswa'];
$page = $_GET['page'] ?? 'dashboard';

/* ================= DATA SISWA ================= */
$siswa = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT siswa.*, kelas.nama_kelas
  FROM siswa
  JOIN kelas ON siswa.kelas = kelas.id_kelas
  WHERE siswa.id_siswa = '$id_siswa'
"));

if (!$siswa) {
  echo "Data siswa tidak ditemukan";
  exit;
}

/* ================= DATA MATA PELAJARAN ================= */
$mapel = mysqli_query($conn,"
  SELECT mata_pelajaran.nama_mapel, guru.nama_guru
  FROM mengajar
  JOIN mata_pelajaran ON mengajar.id_mapel = mata_pelajaran.id_mapel
  JOIN guru ON mengajar.id_guru = guru.id_guru
  WHERE mengajar.id_kelas = '$siswa[kelas]'
");

/* ================= DATA ABSENSI ================= */
$absensi = mysqli_query($conn,"
  SELECT tanggal, status
  FROM absensi
  WHERE id_siswa = '$id_siswa'
  ORDER BY tanggal DESC
");

/* ================= HITUNG PERSENTASE ================= */
$q_total = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) AS total FROM absensi WHERE id_siswa='$id_siswa'
"));

$q_hadir = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) AS hadir FROM absensi
  WHERE id_siswa='$id_siswa' AND status='Hadir'
"));

$total_absen = $q_total['total'];
$total_hadir = $q_hadir['hadir'];
$persentase = ($total_absen > 0) ? round(($total_hadir / $total_absen) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Siswa Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;font-family:'Inter',sans-serif}
body{margin:0;background:#f4f6fb}
.sidebar{width:240px;height:100vh;background:#27ae60;position:fixed;color:#fff;padding:24px}
.sidebar h2{margin:0 0 30px}
.sidebar a{display:block;color:#fff;text-decoration:none;padding:12px 14px;border-radius:10px;margin-bottom:8px;font-size:14px}
.sidebar a.active,.sidebar a:hover{background:rgba(255,255,255,.2)}
.main{margin-left:240px;padding:24px}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px}
.card{background:#fff;padding:22px;border-radius:16px;box-shadow:0 10px 20px rgba(0,0,0,.05)}
table{width:100%;border-collapse:collapse}
th,td{padding:12px}
th{background:#f1f3f9}
</style>
</head>

<body>

<div class="sidebar">
  <h2>Siswa</h2>
  <a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>">Dashboard</a>
  <a href="?page=mapel" class="<?= $page=='mapel'?'active':'' ?>">Mata Pelajaran</a>
  <a href="?page=absensi" class="<?= $page=='absensi'?'active':'' ?>">Absensi</a>
  <a href="logout.php">Logout</a>
</div>

<div class="main">

<!-- ================= DASHBOARD ================= -->
<?php if ($page=='dashboard'): ?>
<div class="cards">
  <div class="card">
    <h3>Profil Singkat</h3>
    <p><b><?= $siswa['nama_siswa'] ?></b></p>
    <p>NIS: <?= $siswa['nis'] ?></p>
    <p>Kelas: <?= $siswa['nama_kelas'] ?></p>
  </div>

  <div class="card">
    <h3>Persentase Kehadiran</h3>
    <p style="font-size:32px;font-weight:600;color:#27ae60">
      <?= $persentase ?>%
    </p>
    <small>Hadir <?= $total_hadir ?> dari <?= $total_absen ?> pertemuan</small>
  </div>
</div>
<?php endif; ?>

<!-- ================= MAPEL ================= -->
<?php if ($page=='mapel'): ?>
<div class="card">
  <h3>Mata Pelajaran</h3>
  <table>
    <tr><th>No</th><th>Mata Pelajaran</th><th>Guru</th></tr>
    <?php $no=1; while($m=mysqli_fetch_assoc($mapel)): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $m['nama_mapel'] ?></td>
      <td><?= $m['nama_guru'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php endif; ?>

<!-- ================= ABSENSI ================= -->
<?php if ($page=='absensi'): ?>
<div class="card">
  <h3>Riwayat Absensi</h3>
  <table>
    <tr><th>Tanggal</th><th>Status</th></tr>
    <?php while($a=mysqli_fetch_assoc($absensi)): ?>
    <tr>
      <td><?= date('d-m-Y', strtotime($a['tanggal'])) ?></td>
      <td><?= $a['status'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php endif; ?>

</div>
</body>
</html>

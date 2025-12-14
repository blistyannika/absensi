<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'guru') {
  header("Location: login.php");
  exit;
}

$page = $_GET['page'] ?? 'dashboard';
$id_guru = $_SESSION['id_guru'];

// inisialisasi variabel
$showRekap = false;
$hadir = 0;
$tidak_hadir = 0;
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Guru Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;font-family:'Inter',sans-serif}
body{margin:0;background:#f4f6fb}
.sidebar{width:240px;height:100vh;background:#2f80ed;position:fixed;color:#fff;padding:24px}
.sidebar h2{margin:0 0 30px}
.sidebar a{display:block;color:#fff;text-decoration:none;padding:12px 14px;border-radius:10px;margin-bottom:8px;font-size:14px}
.sidebar a.active,.sidebar a:hover{background:rgba(255,255,255,.2)}
.main{margin-left:240px;padding:24px}
.topbar{background:#fff;padding:18px 22px;border-radius:14px;display:flex;justify-content:space-between;margin-bottom:24px}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;margin-bottom:24px}
.card{background:#fff;padding:22px;border-radius:16px;box-shadow:0 10px 20px rgba(0,0,0,.05)}
.card h3{margin:0;font-size:14px;color:#555}
.card p{margin-top:10px;font-size:28px;font-weight:600;color:#2f80ed}
.table-card{background:#fff;border-radius:16px;box-shadow:0 10px 20px rgba(0,0,0,.05);overflow:hidden}
table{width:100%;border-collapse:collapse}
th,td{padding:14px;font-size:14px}
th{background:#f1f3f9}
tr:not(:last-child) td{border-bottom:1px solid #eee}
.btn{padding:6px 14px;border:none;border-radius:8px;background:#2f80ed;color:#fff;cursor:pointer}
select{padding:6px}
</style>
</head>

<body>

<div class="sidebar">
  <h2>Guru</h2>
  <a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>">Dashboard</a>
  <a href="?page=absensi" class="<?= $page=='absensi'?'active':'' ?>">Absensi</a>
  <a href="logout.php">Logout</a>
</div>

<div class="main">

<div class="topbar">
  <strong>Selamat Datang, <?= $_SESSION['nama_guru'] ?></strong>
  <span><?= date('l, d F Y') ?></span>
</div>

<?php
/* ================= DASHBOARD ================= */
if ($page=='dashboard') {

$q1 = mysqli_query($conn,"SELECT COUNT(*) t FROM mata_pelajaran");
$q2 = mysqli_query($conn,"SELECT COUNT(*) t FROM kelas");
$q3 = mysqli_query($conn,"SELECT COUNT(*) t FROM siswa");

$mapel = mysqli_fetch_assoc($q1);
$kelas = mysqli_fetch_assoc($q2);
$siswa = mysqli_fetch_assoc($q3);
?>
<div class="cards">
  <div class="card"><h3>Total Mata Pelajaran</h3><p><?= $mapel['t'] ?></p></div>
  <div class="card"><h3>Total Kelas</h3><p><?= $kelas['t'] ?></p></div>
  <div class="card"><h3>Total Siswa</h3><p><?= $siswa['t'] ?></p></div>
</div>
<?php } ?>


<?php
/* ================= MENU ABSENSI ================= */
if ($page=='absensi') {

$q = mysqli_query($conn,"
  SELECT mengajar.id_mengajar, mata_pelajaran.nama_mapel, kelas.nama_kelas
  FROM mengajar
  JOIN mata_pelajaran ON mengajar.id_mapel=mata_pelajaran.id_mapel
  JOIN kelas ON mengajar.id_kelas=kelas.id_kelas
  WHERE mengajar.id_guru='$id_guru'
");
?>
<div class="table-card">
<table>
<tr>
  <th>Mata Pelajaran</th>
  <th>Kelas</th>
  <th>Aksi</th>
</tr>
<?php while($r=mysqli_fetch_assoc($q)){ ?>
<tr>
  <td><?= $r['nama_mapel'] ?></td>
  <td><?= $r['nama_kelas'] ?></td>
  <td>
    <a href="?page=isi_absen&id=<?= $r['id_mengajar'] ?>">
      <button class="btn">Absen</button>
    </a>
  </td>
</tr>
<?php } ?>
</table>
</div>
<?php } ?>


<?php
/* ================= ISI ABSENSI ================= */
if ($page == 'isi_absen') {

$id_mengajar = $_GET['id'];

// ambil info kelas & mapel
$info = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT 
    mengajar.id_kelas,
    kelas.nama_kelas,
    mata_pelajaran.nama_mapel
  FROM mengajar
  JOIN kelas ON mengajar.id_kelas = kelas.id_kelas
  JOIN mata_pelajaran ON mengajar.id_mapel = mata_pelajaran.id_mapel
  WHERE mengajar.id_mengajar = '$id_mengajar'
"));

// ambil siswa SESUAI kolom `kelas` (INT)
$siswa = mysqli_query($conn,"
  SELECT id_siswa, nama_siswa
  FROM siswa
  WHERE kelas = '{$info['id_kelas']}'
  ORDER BY nama_siswa ASC
");

// simpan absensi
if (isset($_POST['simpan']) && isset($_POST['status'])) {

  $hadir = 0;
  $tidak_hadir = 0;

  foreach ($_POST['status'] as $id_siswa => $status) {

    if ($status === 'Hadir') {
      $hadir++;
    } else {
      $tidak_hadir++;
    }

    mysqli_query($conn,"
      INSERT INTO absensi (id_siswa, tanggal, status)
      VALUES ('$id_siswa', CURDATE(), '$status')
      ON DUPLICATE KEY UPDATE status='$status'
    ");
  }

  $showRekap = true;
}
?>
<div class="card">
  <h3>Absensi Siswa</h3>
  <p><b><?= $info['nama_mapel'] ?></b> | <?= $info['nama_kelas'] ?></p>
</div>

<?php if ($showRekap): ?>
<div class="cards">
  <div class="card"><h3>Hadir</h3><p><?= $hadir ?></p></div>
  <div class="card"><h3>Tidak Hadir</h3><p><?= $tidak_hadir ?></p></div>
</div>
<?php endif; ?>

<div class="table-card">
<form method="POST">
<table>
<tr>
  <th>No</th>
  <th>Nama Siswa</th>
  <th>Status</th>
</tr>
<?php $no=1; while($s=mysqli_fetch_assoc($siswa)){ ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= $s['nama_siswa'] ?></td>
  <td>
    <select name="status[<?= $s['id_siswa'] ?>]">
      <option value="Hadir">Hadir</option>
      <option value="Tidak Hadir">Tidak Hadir</option>
    </select>
  </td>
</tr>
<?php } ?>
</table>
<br>
<button type="submit" name="simpan" class="btn">💾 Simpan Absensi</button>
</form>
</div>

<?php } ?>

</div>
</body>
</html>

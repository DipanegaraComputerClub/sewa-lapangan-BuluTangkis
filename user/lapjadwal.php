<?php
session_start();
error_reporting();
require "../functions.php";
require "../session.php";

if ($role !== 'User') {
  header("location:../login.php");
};

$id_user = $_SESSION["id_user"];

$sewa = query("SELECT sewa.*, lapangan.nm, user.nama_lengkap
FROM sewa
JOIN lapangan ON sewa.idlap = lapangan.idlap
LEFT JOIN user ON sewa.iduser = user.id_user
WHERE user.id_user = '$id_user'
");
$profil = query("SELECT * FROM user WHERE id_user = '$id_user'")[0];

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Jadwal Lapangan</title>
  <link rel="stylesheet" href="../style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>
<div class="container ">
    <nav class="navbar fixed-top bg-body-secondary navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand" href="#">
          <h1>Aptisi</h1>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
            <li class="nav-item ">
              <a class="nav-link active" aria-current="page" href="../index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="lapangan.php">Lapangan</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="bayar.php">Pembayaran</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="lapjadwal.php">Jadwal</a>
            </li>
          </ul>
          <?php
          if (isset($_SESSION['id_user'])) {
            // jika user telah login, tampilkan tombol profil dan sembunyikan tombol login
            echo '<a href="" data-bs-toggle="modal" data-bs-target="#profilModal" class="btn btn-inti"><i data-feather="user"></i></a>';
          } else {
            // jika user belum login, tampilkan tombol login dan sembunyikan tombol profil
            echo '<a href="login.php" class="btn btn-inti" type="submit">Login</a>';
          }
          ?>
        </div>
      </div>
    </nav>
  </div>  

  <div class="modal fade" id="profilModal" tabindex="-1" aria-labelledby="profilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="profilModalLabel">Profil Pengguna</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="" method="post">
          <div class="modal-body">
            <div class="row">
              <div class="col-4 my-5">
                <img src="../img/<?= $profil["foto"]; ?>" alt="Foto Profil" class="img-fluid ">
              </div>
              <div class="col-8">
                <h5 class="mb-3"><?= $profil["nama_lengkap"]; ?></h5>
                <p><?= $profil["jenis_kelamin"]; ?></p>
                <p><?= $profil["email"]; ?></p>
                <p><?= $profil["hp"]; ?></p>
                <p><?= $profil["alamat"]; ?></p>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
                <a href="" data-bs-toggle="modal" data-bs-target="#editProfilModal" class="btn btn-inti">Edit Profil</a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <section class="lapangan mb-5" id="lapangan">
    <div class="container-fluid">
      <h2 class="text-head"><span>Jadwal</span> Lapangan </h2>
      <form action="" method="post" class="px-4">
                    <table class="table table-hover mt-3">
                    <thead class="table-inti">
                        <tr>
                        <th scope="col">No</th>
                        <th scope="col">Tanggal Pesan</th>
                        <th scope="col">Nama Pemesan</th>
                        <th scope="col">Nama Lapangan</th>
                        <th scope="col">Jam Main</th>
                        <th scope="col">Lama Sewa</th>
                        <th scope="col">jam Habis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($sewa as $row) : ?>
                        <tr>
                            <th scope="row"><?= $i++; ?></th>
                            <td><?= $row["tgl_pesan"] ?></td>
                            <td><?= $row["nama_lengkap"] ?></td>
                            <td><?= $row["nm"] ?></td>
                            <td><?= $row["jmulai"] ?></td>
                            <td><?= $row["lama"] ?> Jam</td>
                            <td><?= $row["jhabis"] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </form>
    </div>
  </section>
       <!-- footer -->
  <footer class="py-3">
    <div class="social">
      <a href="#"><i data-feather="instagram"></i></a>
      <a href="#"><i data-feather="facebook"></i></a>
      <a href="#"><i data-feather="twitter"></i></a>
    </div>

    <div class="links">
      <a href="#home">Home</a>
      <a href="#about">Lapangan</a>
      <a href="#menu">Pembayaran</a>
      <a href="#contact">Kontak</a>
    </div>

    <div class="credit">
      <p>Created by <a href="#">Sela & Riska</a> &copy; 2023</p>
    </div>
  </footer>
  <!-- End Footer -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  <script>
    feather.replace();
  </script>
</body>

</html>




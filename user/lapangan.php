<?php
session_start();
require "../functions.php";
require "../session.php";
if ($role !== 'User') {
  header("location:../login.php");
}

$id = $_SESSION["id_user"];

$lapangan = query("SELECT * FROM lapangan");
$profil = query("SELECT * FROM user WHERE id_user = '$id'")[0];

if (isset($_POST["simpan"])) {
  if (edit($_POST) > 0) {
    echo "<script>
          alert('Berhasil Diubah');
          </script>";
  } else {
    echo "<script>
          alert('Gagal Diubah');
          </script>";
  }
}


// Ambil data dari formulir
if (isset($_POST["pesan"])) {
  $userid = $_SESSION["id_user"];
  $idlpg = $_POST["id_lpg"];
  $lama =  $_POST["jam_mulai"];
  $tanggal = $_POST["tgl_main"];
  $waktu = $_POST["waktu_main"];
  $mulai = $tanggal."T".$waktu; // mengubah ke format datetime-local 

  $mulai_waktu = strtotime($mulai); // mengubah format datetime-local menjadi format UNIX timestamp
  $habis_waktu = $mulai_waktu + (intval($lama) * 3600); // menambahkan waktu dalam menit ke waktu awal
  $habis = date('Y-m-d\TH:i', $habis_waktu); // mengubah format waktu kembali ke datetime-local
  $habis_datetime_local = date('Y-m-d\TH:i:s', strtotime($habis)); // mengubah format waktu dari Y-m-d\TH:i ke format datetime-local
  $habis = $habis_datetime_local; // menyimpan hasil ke dalam variabel $habis_datetime_local
  $harga = $_POST["harga"];
  // $total = date("H", strtotime($lama)) * $harga;
  $total = date('Y-m-d\TH:i', $lama * $harga);
  
  // Periksa ketersediaan lapangan
  $query = "SELECT COUNT(*) AS jumlah FROM sewa
            WHERE idlap = $idlpg
            AND ((jmulai <= '$mulai' AND jhabis >= '$habis')
            OR (jmulai <= '$mulai' AND jhabis >= '$habis'))";
  
  $result = $conn->query($query);
  $row = $result->fetch_assoc();
  $jumlah_pesan = $row['jumlah'];
  
    if ($jumlah_pesan > 0) {
        echo "<script>
        alert('Maaf, lapangan tidak tersedia pada waktu yang diminta!.');
        document.location.href = 'lapangan.php';
        </script>";
    } else {
        // Simpan pesanan ke database
        $query = "INSERT INTO sewa (iduser, idlap,lama,jmulai,jhabis,harga,tot) VALUES ('$userid','$idlpg','$lama','$mulai','$habis','$harga','$total')";
  
        if ($conn->query($query) === TRUE) {
          echo "<script>
          alert('Pesanan Berhasil!');
          document.location.href = 'lapangan.php';
          </script>";
        } else {
          echo "<script>
          alert('Pesanan Gagal!.');
          </script>";
        }
    }
  }



?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Lapangan</title>
  <link rel="stylesheet" href="../style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <script src="https://unpkg.com/feather-icons"></script>
  <script lang='javascript' defer>
    dateElem = document.getElementsByName('tgl_main')

    const dateChange = (e) => {
      time = e.value.split('T')[1]
      time = time.split(':')
      time[1] = '00'

      datetime = e.value.split('T')
      datetime[1] = time.join(':')
      e.value = datetime.join('T')
    }
  </script>
</head>

<body>
  <!-- Navbar -->
  <div class="container ">
    <nav class="navbar fixed-top bg-body-secondary navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand" href="#">
          <h1 class="d-inline-block align-text-top" style="font-size: 70;">Aptisi</h1>
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
            echo '<a href="user/profil.php" data-bs-toggle="modal" data-bs-target="#profilModal" class="btn btn-inti"><i data-feather="user"></i></a>';
          } else {
            // jika user belum login, tampilkan tombol login dan sembunyikan tombol profil
            echo '<a href="login.php" class="btn btn-inti" type="submit">Login</a>';
          }
          ?>
        </div>
      </div>
    </nav>
  </div>
  <!-- End Navbar -->

  <!-- Modal Profil -->
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
  <!-- Modal Profil -->

  <!-- Edit profil -->
  <div class="modal fade" id="editProfilModal" tabindex="-1" aria-labelledby="editProfilModalLabel" aria-hidden="true">
    <div class="modal-dialog edit modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfilModalLabel">Edit Profil</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="fotoLama" class="form-control" id="exampleInputPassword1" value="<?= $profil["foto"]; ?>">
          <div class="modal-body">
            <div class="row justify-content-center align-items-center">
              <div class="mb-3">
                <img src="../img/<?= $profil["foto"]; ?>" alt="Foto Profil" class="img-fluid ">
              </div>
              <div class="col">
                <div class="mb-3">
                  <label for="exampleInputPassword1" class="form-label">Nama Lengkap</label>
                  <input type="text" name="nama_lengkap" class="form-control" id="exampleInputPassword1" value="<?= $profil["nama_lengkap"]; ?>">
                </div>
                <div class="mb-3">
                  <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                  <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="Laki-laki" <?php if ($profil['jenis_kelamin'] == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
                    <option value="Perempuan" <?php if ($profil['jenis_kelamin'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                  </select>
                </div>
              </div>
              <div class="col">
                <div class="mb-3">
                  <label for="exampleInputPassword1" class="form-label">No Telp</label>
                  <input type="number" name="hp" class="form-control" id="exampleInputPassword1" value="<?= $profil["hp"]; ?>">
                </div>
                <div class="mb-3">
                  <label for="exampleInputPassword1" class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" id="exampleInputPassword1" value="<?= $profil["email"]; ?>">
                </div>
              </div>
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">alamat</label>
                <input type="text" name="alamat" class="form-control" id="exampleInputPassword1" value="<?= $profil["alamat"]; ?>">
              </div>
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Foto : </label>
                <input type="file" name="foto" class="form-control" id="exampleInputPassword1">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-inti" name="simpan" id="simpan">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- End Edit Modal -->

  <section class="lapangan" id="lapangan">
    <div class="container">
      <main class="contain" data-aos="fade-right" data-aos-duration="1000">
        <h2 class="text-head">Lapangan di <span>Aptisi</span></h2>
        <div class="row row-cols-1 row-cols-md-4">
          <?php foreach ($lapangan as $row) : ?>
            <div class="col">
              <div class="card">
                <img src="../img/<?= $row["foto"]; ?>" alt="gambar lapangan" class="card-img-top">
                <div class="card-body text-center">
                  <h5 class="card-title"><?= $row["nm"]; ?></h5>
                  <p class="card-text"><?= $row["ket"]; ?></p>
                  <p class="card-price"><?= $row["harga"]; ?>/jam</p>
                  <!-- <a href="jadwal.php?id=<?= $row["idlap"]; ?>" type="button" class="btn btn-secondary">Jadwal</a> -->
                  <button type="button" class="btn btn-inti" data-bs-toggle="modal" data-bs-target="#pesanModal<?= $row["idlap"]; ?>">Pesan</button>
                </div>
              </div>
            </div>

            <!-- Modal Pesan -->
            <div class="modal fade" id="pesanModal<?= $row["idlap"]; ?>" tabindex="-1" aria-labelledby="pesanModalLabel<?= $row["idlap"]; ?>" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="pesanModalLabel<?= $row["idlap"]; ?>">Pesan Lapangan <?= $row["nm"]; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="" method="post">
                    <div class="modal-body">
                      <!-- konten form modal -->
                      <div class="row justify-content-center align-items-center">
                        <div class="mb-3">
                          <img src="../img/<?= $row["foto"]; ?>" alt="gambar lapangan" class="img-fluid">
                        </div>
                        <div class="text-center">
                          <h6 name="harga">Harga : <?= $row["harga"]; ?></h6>
                        </div>
                        <div class="col">
                          <input type="hidden" name="id_lpg" class="form-control" id="exampleInputPassword1" value="<?= $row["idlap"]; ?>">
                          <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Tanggal Awal</label>
                            <input type="date" name="tgl_main" min="<?= date('Y-m-d'); ?>" class="form-control" id="exampleInputPassword1">
                          </div>
                        </div>
                        <div class="col">
                          <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Waktu Main</label>
                            <input type="time" name="waktu_main" class="form-control" id="exampleInputPassword1">
                          </div>
                        </div>
                        <div class="col">
                          <input type="hidden" name="harga" class="form-control" id="exampleInputPassword1" value="<?= $row["harga"]; ?>">
                          <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Tanggal Akhir</label>
                            <input type="date" name="jam_mulai" class="form-control" id="exampleInputPassword1">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-inti" name="pesan" id="pesan">Pesan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!-- End Modal Pesan -->
          <?php endforeach; ?>
        </div>
      </main>
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
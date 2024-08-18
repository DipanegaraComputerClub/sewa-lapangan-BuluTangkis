<?php

$conn = mysqli_connect("localhost", "root", "", "db_aptisi");

function query($query)
{
  global $conn;
  $result = mysqli_query($conn, $query);
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}

function hapusMember($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM user WHERE id_user = $id");

  return mysqli_affected_rows($conn);
}

function hapusLpg($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM lapangan WHERE idlap = $id");

  return mysqli_affected_rows($conn);
}

function hapusAdmin($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM admin WHERE id_user = $id");

  return mysqli_affected_rows($conn);
}

function hapusPesan($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM sewa WHERE idsewa = $id");

  return mysqli_affected_rows($conn);
}

function daftar($data)
{
  global $conn;

  $username = strtolower(stripslashes($data["email"]));
  $password = $data["password"];
  $nama = $data["nama"];
  $hp = $data["hp"];
  $alamat = $data["alamat"];
  $gender = $data["gender"];
  //Upload Gambar
  $upload = upload();
  if (!$upload) {
    return false;
  }

  $result = mysqli_query($conn, "SELECT email FROM user WHERE email = '$username'");

  if (mysqli_fetch_assoc($result)) {
    echo "<script>
            alert('Username sudah terdaftar!');
        </script>";
    return false;
  }
  mysqli_query($conn, "INSERT INTO user (email,password,hp,jenis_kelamin,nama_lengkap,alamat,foto) VALUES ('$username','$password','$hp','$gender','$nama','$alamat','$upload')");
  return mysqli_affected_rows($conn);
}

function edit($data)
{
  global $conn;

  $userid = $_SESSION["id_user"];
  $username = strtolower(stripslashes($data["email"]));
  $nama = $data["nama_lengkap"];
  $hp = $data["hp"];
  $gender = $data["jenis_kelamin"];
  //Upload Gambar
  $upload = upload();
  if (!$upload) {
    return false;
  }

  $query = "UPDATE user SET email = '$username', 
  nama_lengkap = '$nama',
  hp = '$hp',
  jenis_kelamin = '$gender',
  foto = '$upload'
  WHERE id_user = '$userid'
  ";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}

function pesan($data)
{
  global $conn;

  $userid = $_SESSION["id_user"];
  $idlpg = $data["id_lpg"];
  $lama =  $data["jam_mulai"];
  $mulai = $data["tgl_main"];
  $mulai_waktu = strtotime($mulai); // mengubah format datetime-local menjadi format UNIX timestamp
  $habis_waktu = $mulai_waktu + (intval($lama) * 3600); // menambahkan waktu dalam menit ke waktu awal
  $habis = date('Y-m-d\TH:i', $habis_waktu); // mengubah format waktu kembali ke datetime-local
  $habis_datetime_local = date('Y-m-d\TH:i:s', strtotime($habis)); // mengubah format waktu dari Y-m-d\TH:i ke format datetime-local
  $habis = $habis_datetime_local; // menyimpan hasil ke dalam variabel $habis_datetime_local
  $harga = $data["harga"];
  // $total = date("H", strtotime($lama)) * $harga;
  $total = $lama * $harga;

  mysqli_query($conn, "INSERT INTO sewa (iduser, idlap,lama,jmulai,jhabis,harga,tot) VALUES ('$userid','$idlpg','$lama','$mulai','$habis','$harga','$total') ");

  return mysqli_affected_rows($conn);
}

function tambahBayar($data)
{
  global $conn;

  $idsewa = $data["idsewa"];
  $order_id = $data["order_id"];

  mysqli_query($conn, "INSERT INTO bayar (idsewa, order_id, payment_type, konfirmasi) VALUES ('$idsewa', $order_id, '', 'Pending')");

  return mysqli_affected_rows($conn);
}

function konfirmasiBayar($data)
{
  global $conn;

  $order_id = $data["order_id"];
  $payment = $data["payment_type"];

  mysqli_query($conn, "UPDATE bayar SET konfirmasi = 'Terkonfirmasi', payment_type = '$payment' WHERE order_id = '$order_id'");

  $rows = mysqli_affected_rows($conn);

  $bayar_sql = mysqli_query($conn, "SELECT * FROM bayar WHERE order_id = '$order_id'");
  $bayar_data = mysqli_fetch_assoc($bayar_sql);

  $result = array(
    'rows' => $rows,
    'idsewa' => $bayar_data['idsewa'],
  );

  return $result;
}

function toLunas($idsewa)
{
  global $conn;
  
  mysqli_query($conn, "UPDATE sewa SET `status` = 'Lunas' WHERE idsewa = '$idsewa'");
  
  return mysqli_affected_rows($conn);
}

function konfirmasiBatalBayar($data)
{
  global $conn;
  
  $order_id = $data['order_id'];

  $bayar_sql = mysqli_query($conn, "SELECT * FROM bayar WHERE order_id = '$order_id'");
  $bayar_data = mysqli_fetch_assoc($bayar_sql);

  $result = array(
    'idsewa' => $bayar_data['idsewa'],
  );

  mysqli_query($conn, "DELETE FROM bayar WHERE order_id = '$order_id'");

  return $result;
}

function tambahLpg($data)
{
  global $conn;

  $lapangan = $data["lapangan"];
  $harga = $data["harga"];

  //Upload Gambar
  $upload = upload();
  if (!$upload) {
    return false;
  }


  $query = "INSERT INTO lapangan (nm,harga,foto) VALUES ('$lapangan','$harga','$upload')";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}

function upload()
{
  $namaFile = $_FILES['foto']['name'];
  $ukuranFile = $_FILES['foto']['size'];
  $error = $_FILES['foto']['error'];
  $tmpName = $_FILES['foto']['tmp_name'];

  // Cek apakah tidak ada gambar yang di upload
  if ($error === 4) {
    echo "<script>
    alert('Pilih gambar terlebih dahulu');
    </script>";
    return false;
  }

  // Cek apakah gambar
  $extensiValid = ['jpg', 'png', 'jpeg'];
  $extensiGambar = explode('.', $namaFile);
  $extensiGambar = strtolower(end($extensiGambar));

  if (!in_array($extensiGambar, $extensiValid)) {
    echo "<script>
    alert('Yang anda upload bukan gambar!');
    </script>";
    return false;
  }

  if ($ukuranFile > 1000000) {
    echo "<script>
    alert('Ukuran Gambar Terlalu Besar!');
    </script>";
    return false;
  }

  $namaFileBaru = uniqid();
  $namaFileBaru .= '.';
  $namaFileBaru .= $extensiGambar;
  // Move File
  move_uploaded_file($tmpName, '../img/' . $namaFileBaru);
  return $namaFileBaru;
}

function editLpg($data)
{
  global $conn;

  $id = $data["idlap"];
  $lapangan = $data["lapangan"];
  $ket = $data["ket"];
  $harga = $data["harga"];
  $gambarLama =  $data["fotoLama"];

  // Cek apakah User pilih gambar baru
  if ($_FILES["foto"]["error"] === 4) {
    $gambar = $gambarLama;
  } else {
    $gambar = upload();
  }


  $query = "UPDATE lapangan SET 
  nm = '$lapangan',
  ket = '$ket',
  harga = '$harga',
  foto = '$gambar' WHERE idlap = '$id'
  ";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}


function tambahAdmin($data)
{
  global $conn;

  $username = $data["username"];
  $password = $data["password"];
  $nama = $data["nama"];
  $hp = $data["hp"];
  $email = $data["email"];

  $query = "INSERT INTO admin (username,password,nama,phone,email) VALUES ('$username','$password','$nama','$hp','$email')";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}

function editAdmin($data)
{
  global $conn;

  $id = $data["id"];
  $username = $data["username"];
  $password = $data["password"];
  $nama = $data["nama"];
  $hp = $data["hp"];
  $email = $data["email"];

  $query = "UPDATE admin SET 
  username = '$username',
  password = '$password',
  nama = '$nama',
  phone = '$hp',
  email  = '$email' WHERE id_user = '$id'
  
  ";

  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}

function konfirmasi($idsewa)
{
  global $conn;

  $id = $idsewa;

  mysqli_query($conn, "UPDATE bayar set konfirmasi = ('Terkonfirmasi') WHERE idsewa = '$id'");
  return mysqli_affected_rows($conn);
}

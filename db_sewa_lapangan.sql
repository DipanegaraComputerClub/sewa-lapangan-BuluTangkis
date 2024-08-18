-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2023 at 12:10 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sewa_lapangan`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_user` int(3) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_user`, `username`, `password`, `nama`, `phone`, `email`) VALUES
(1, 'admin@admin', 'admin', 'Admin Rojak', '08902932', 'admin@admin');

-- --------------------------------------------------------

--
-- Table structure for table `bayar`
--

CREATE TABLE `bayar` (
  `idbayar` int(11) NOT NULL,
  `idsewa` int(11) NOT NULL,
  `order_id` text NOT NULL,
  `payment_type` text NOT NULL,
  `tgl_upload` date NOT NULL DEFAULT current_timestamp(),
  `konfirmasi` varchar(50) NOT NULL DEFAULT 'Belum'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bayar`
--

INSERT INTO `bayar` (`idbayar`, `idsewa`, `order_id`, `payment_type`, `tgl_upload`, `konfirmasi`) VALUES
(56, 125, '1012434948', 'qris', '2023-08-05', 'Terkonfirmasi'),
(57, 127, '1578576502', 'qris', '2023-08-05', 'Terkonfirmasi'),
(58, 128, '839734192', 'qris', '2023-08-05', 'Terkonfirmasi'),
(59, 129, '1327779588', 'qris', '2023-08-05', 'Terkonfirmasi');

-- --------------------------------------------------------

--
-- Table structure for table `lapangan`
--

CREATE TABLE `lapangan` (
  `idlap` int(11) NOT NULL,
  `nm` varchar(35) NOT NULL,
  `ket` text NOT NULL,
  `harga` int(11) NOT NULL,
  `foto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lapangan`
--

INSERT INTO `lapangan` (`idlap`, `nm`, `ket`, `harga`, `foto`) VALUES
(23, 'Dewa', 'ini lapangan Dewa', 30000, 'footbal.jpg'),
(24, 'Emas', 'Ini Lapangan Emas', 10000, 'badmintoon.jpg'),
(25, 'Silver', 'Ini Lapangan Silver', 50000, 'basket.jpg'),
(26, 'Golf', 'Ini Lapangan Golf4', 40000, 'futsal.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `sewa`
--

CREATE TABLE `sewa` (
  `idsewa` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `idlap` int(11) NOT NULL,
  `tgl_pesan` date NOT NULL DEFAULT current_timestamp(),
  `lama` int(11) NOT NULL,
  `jmulai` datetime NOT NULL,
  `jhabis` datetime NOT NULL,
  `harga` int(11) NOT NULL,
  `tot` int(11) NOT NULL,
  `status` enum('Pending','Lunas','Dibatalkan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sewa`
--

INSERT INTO `sewa` (`idsewa`, `iduser`, `idlap`, `tgl_pesan`, `lama`, `jmulai`, `jhabis`, `harga`, `tot`, `status`) VALUES
(125, 98, 24, '2023-08-02', 4, '2023-08-10 14:15:00', '2023-08-10 18:15:00', 10000, 40000, 'Lunas'),
(127, 98, 25, '2023-08-05', 2, '2023-08-06 10:45:00', '2023-08-06 12:45:00', 50000, 100000, 'Lunas'),
(128, 98, 26, '2023-08-05', 4, '2023-08-06 11:00:00', '2023-08-06 15:00:00', 40000, 160000, 'Lunas'),
(129, 98, 23, '2023-08-05', 3, '2023-08-06 17:00:00', '2023-08-06 20:00:00', 30000, 90000, 'Lunas');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `hp` varchar(20) NOT NULL,
  `jenis_kelamin` varchar(10) NOT NULL,
  `nama_lengkap` varchar(60) NOT NULL,
  `alamat` text NOT NULL,
  `foto` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `email`, `password`, `hp`, `jenis_kelamin`, `nama_lengkap`, `alamat`, `foto`) VALUES
(98, 'risky12@gmail.com', '123', '08972423', 'Laki-laki', 'Rizky', 'Bekasi', '645229918b946.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `bayar`
--
ALTER TABLE `bayar`
  ADD PRIMARY KEY (`idbayar`);

--
-- Indexes for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`idlap`);

--
-- Indexes for table `sewa`
--
ALTER TABLE `sewa`
  ADD PRIMARY KEY (`idsewa`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_user` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bayar`
--
ALTER TABLE `bayar`
  MODIFY `idbayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `idlap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `sewa`
--
ALTER TABLE `sewa`
  MODIFY `idsewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;
COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

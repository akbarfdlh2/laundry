<?php 
	// Menghubungkan dengan koneksi
	include '../koneksi.php';

	// Menangkap data yang dikirim dari form
	$pelanggan = $_POST['pelanggan'];
	$berat = $_POST['berat'];
	$tgl_selesai = $_POST['tgl_selesai'];

	$tgl_hari_ini = date('Y-m-d');
	$status = 0;

	// Mengambil data harga per kilo dari database
	$h = mysqli_query($koneksi,"SELECT harga_per_kilo FROM harga");
	$harga_per_kilo = mysqli_fetch_assoc($h);

	// Menghitung harga laundry, harga perkilo x berat cucian
	$harga = $berat * $harga_per_kilo['harga_per_kilo'];

	// Input data ke tabel transaksi menggunakan prepared statement
	$stmt = $koneksi->prepare("INSERT INTO transaksi (transaksi_tgl, transaksi_pelanggan, transaksi_harga, transaksi_berat, transaksi_tgl_selesai, transaksi_status) VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->bind_param('ssddsi', $tgl_hari_ini, $pelanggan, $harga, $berat, $tgl_selesai, $status);
	$stmt->execute();

	// Menyimpan id dari data yang di simpan pada query insert data sebelumnya
	$id_terakhir = $stmt->insert_id;

	// Menangkap data form input array (jenis pakaian dan jumlah pakaian)
	$jenis_pakaian = $_POST['jenis_pakaian'];
	$jumlah_pakaian = $_POST['jumlah_pakaian'];

	// Input data cucian berdasarkan id transaksi (invoice) ke tabel pakaian menggunakan prepared statement
	$stmt_pakaian = $koneksi->prepare("INSERT INTO pakaian (pakaian_transaksi, pakaian_jenis, pakaian_jumlah) VALUES (?, ?, ?)");
	for($x = 0; $x < count($jenis_pakaian); $x++){
		if($jenis_pakaian[$x] != ""){
			$stmt_pakaian->bind_param('isi', $id_terakhir, $jenis_pakaian[$x], $jumlah_pakaian[$x]);
			$stmt_pakaian->execute();
		}
	}

	// Redirect ke halaman transaksi.php
	header("Location: transaksi.php");
?>

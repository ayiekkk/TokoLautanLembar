<?php
// 1. Hubungkan ke database (pastikan nama DB sudah benar, misal: lautanlembar_db atau tokolautan_db)
$koneksi = new mysqli("localhost", "root", "", "tokolautan_db"); 

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// 2. Query untuk menghitung jumlah total kategori
$query_kategori = $koneksi->query("SELECT COUNT(*) AS total_kategori FROM kategori");
$data_kategori = $query_kategori->fetch_assoc();
$total_kategori = $data_kategori['total_kategori'];

// (Opsional) Jika kamu juga ingin menghitung total buku nanti:
$query_buku = $koneksi->query("SELECT COUNT(*) AS total_buku FROM buku");
$data_buku = $query_buku->fetch_assoc();
$total_buku = $data_buku['total_buku'];
?>

<h1>Dashboard</h1>
<div class="info-boxes">
    <div class="box">
        <div class="box-icon">
            <i class="fa-solid fa-book"></i>
        </div>
        <div class="box-content">
            <div class="sub-title">
                <h5>Total Buku</h5>
            </div>
            <div class="total">
                <h2><?= $total_buku ?></h2>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-icon">
            <i class="fa-solid fa-bookmark"></i>
        </div>
        <div class="box-content">
            <div class="sub-title">
                <h5>Total Kategori</h5>
            </div>
            <div class="total">
                <h2><?= $total_kategori ?></h2>
            </div>
        </div>
    </div>
</div>
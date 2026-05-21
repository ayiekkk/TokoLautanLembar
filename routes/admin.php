<?php

$page = $_GET['page'] ?? 'dashboard';

switch ($page) {

    case 'dashboard':

        include __DIR__ . '/../pages/admin/dashboard/dashboard.php';

        break;

    case 'tambah-buku':

        include __DIR__ . '/../pages/admin/buku/form_buku.php';

        break;

    case 'tambah-kategori':

        include __DIR__ . '/../pages/admin/kategori/form_kategori.php';

        break;

    default:

        echo "<h1>404 - Halaman Tidak Ditemukan</h1>";

        break;
}
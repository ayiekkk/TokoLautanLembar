<?php

$page = isset($_GET['page']) ? $_GET['page'] : "dashboard";

if ($page == "dashboard") {
    include __DIR__ . "/../pages/admin/dashboard/dashboard.php";
} elseif ($page == "tambah-buku") {
    include __DIR__ . "/../pages/admin/buku/form_buku.php";
} elseif ($page == "tambah-kategori") {
    include __DIR__ . "/../pages/admin/kategori/form_kategori.php";
}
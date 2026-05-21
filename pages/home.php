<?php
// 1. Hubungkan ke Database (Sesuaikan path jika folder user Anda berbeda)
include_once $_SERVER['DOCUMENT_ROOT'] . "/LautanLembar-v2/config/database.php";

$db = new Database();
$conn = $db->getConnection();

// 2. Query Gabungkan tabel buku dengan tabel kategori agar nama kategori bisa muncul
$query = "SELECT buku.*, kategori.nama_kategori 
          FROM buku 
          LEFT JOIN kategori ON buku.id_kategori = kategori.id 
          ORDER BY buku.id DESC";

$daftar_buku = $conn->query($query);
?>

<?php include "components/hero.php" ?>

<div class="category-container">
    <button class="category-btn active">Semua</button>
    <button class="category-btn">Fiksi</button>
    <button class="category-btn">Sains</button>
    <button class="category-btn">Sejarah</button>
    <button class="category-btn">Biografi</button>
</div>

<section class="popular-collection">
    <div class="collection-header">
        <div class="header-text">
            <h2>Koleksi Terpopuler</h2>
            <p>Buku-buku yang sedang hangat dibicarakan oleh para penikmat literasi.</p>
        </div>
    </div>
    <div class="book-grid">
        <?php if ($daftar_buku && $daftar_buku->num_rows > 0): ?>
            <?php while ($buku = $daftar_buku->fetch_assoc()): ?>
                <div class="book-card">
                    <div class="book-cover">
                        <?php if (!empty($buku['sampul']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/LautanLembar-v2/pages/admin/buku/uploads/" . $buku['sampul'])): ?>
                            <img src="/LautanLembar-v2/pages/admin/buku/uploads/<?= $buku['sampul'] ?>" alt="<?= htmlspecialchars($buku['nama_buku']) ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=500&auto=format&fit=crop" alt="Default Cover">
                        <?php endif; ?>
                        <span class="badge"><?= htmlspecialchars($buku['nama_kategori'] ?? 'UMUM') ?></span>
                    </div>
                    <div class="book-info">
                        <h3><?= htmlspecialchars($buku['nama_buku']) ?></h3>
                        <p class="author"><?= htmlspecialchars($buku['penulis']) ?></p>
                        <p class="price">Rp <?= number_format($buku['harga'], 0, ',', '.') ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; color: #94a3b8; padding: 20px;">
                <p>Belum ada data buku.</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="load-more-container">
        <button class="load-more-btn">Lihat Selengkapnya</button>
    </div>
</section>
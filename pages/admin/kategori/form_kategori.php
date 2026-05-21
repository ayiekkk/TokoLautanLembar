<?php

$koneksi = new mysqli("localhost", "root", "", "tokolautan_db"); // Sesuaikan nama DB jika berbeda

$notifikasi = "";

// Inisialisasi variabel untuk form edit
$is_edit = false;
$id_edit = "";
$nama_kategori_val = "";
$deskripsi_kategori_val = "";

/* AMBIL DATA UNTUK DIEDIT (PROSES READ SINGLE DATA) */
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $result_edit = $koneksi->query("SELECT * FROM kategori WHERE id = '$id_edit'");
    
    if ($result_edit->num_rows > 0) {
        $data_edit = $result_edit->fetch_assoc();
        $nama_kategori_val = $data_edit['nama_kategori'];
        $deskripsi_kategori_val = $data_edit['deskripsi_kategori'];
        $is_edit = true; // Tandai bahwa form sedang dalam mode edit
    }
}

/* PROSES SIMPAN DATA (TAMBAH & EDIT) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaKategori = trim($_POST['nama_kategori']);
    $deskripsiKategori = trim($_POST['deskripsi_kategori']);

    if (empty($namaKategori) || empty($deskripsiKategori)) {
        $notifikasi = "
            <div class='error' style='color: red; padding: 10px; background: #fee; margin-bottom: 10px;'>
                Semua field wajib diisi!
            </div>
        ";
    } else {
        if (isset($_POST['aksi_simpan']) && $_POST['aksi_simpan'] === 'update') {
            /* 1. Jika mode UPDATE/EDIT */
            $id_update = $_POST['id_kategori'];
            $stmt = $koneksi->prepare("UPDATE kategori SET nama_kategori = ?, deskripsi_kategori = ? WHERE id = ?");
            $stmt->bind_param("ssi", $namaKategori, $deskripsiKategori, $id_update);

            if ($stmt->execute()) {
                echo "<script>
                    alert('Data kategori berhasil diperbarui!');
                    window.location.href = '?page=tambah-kategori';
                </script>";
                exit;
            } else {
                $notifikasi = "<div class='error'>Gagal memperbarui data!</div>";
            }
        } else {
            /* 2. Jika mode INSERT/TAMBAH BARU */
            $stmt = $koneksi->prepare("INSERT INTO kategori (nama_kategori, deskripsi_kategori) VALUES (?, ?)");
            $stmt->bind_param("ss", $namaKategori, $deskripsiKategori);

            if ($stmt->execute()) {
                $notifikasi = "
                    <div class='success' style='color: green; padding: 10px; background: #efe; margin-bottom: 10px;'>
                        Data kategori berhasil ditambahkan!
                    </div>
                ";
            } else {
                $notifikasi = "<div class='error'>Gagal menambahkan data!</div>";
            }
        }
    }
}

/* PROSES HAPUS DATA */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM kategori WHERE id = '$id'");
    echo "<script>
        alert('Data kategori dengan ID " . $id . " berhasil dihapus!');
        window.location.href = '?page=tambah-kategori';
    </script>";
    exit;
}

/* Ambil semua data untuk ditampilkan di tabel */
$data_kategori = $koneksi->query("SELECT * FROM kategori ORDER BY id DESC");
?>

<main>
    <h1><?= $is_edit ? "Edit Kategori" : "Tambah Kategori" ?></h1>
    
    <?= $notifikasi ?>

    <div class="form">
        <form method="POST" action="?page=tambah-kategori">
            
            <?php if ($is_edit): ?>
                <input type="hidden" name="id_kategori" value="<?= $id_edit ?>">
                <input type="hidden" name="aksi_simpan" value="update">
            <?php else: ?>
                <input type="hidden" name="aksi_simpan" value="tambah">
            <?php endif; ?>

            <div class="form-group">
                <label>Nama Kategori</label>
                <input
                    type="text"
                    name="nama_kategori"
                    placeholder="Contoh : Fiksi"
                    value="<?= htmlspecialchars($nama_kategori_val) ?>" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <input
                    type="text"
                    name="deskripsi_kategori"
                    placeholder="Masukkan deskripsi"
                    value="<?= htmlspecialchars($deskripsi_kategori_val) ?>" required>
            </div>
            
            <button type="submit" name="submit">
                <?= $is_edit ? "Simpan Perubahan" : "Tambah" ?>
            </button>
            
            <?php if ($is_edit): ?>
                <a href="?page=tambah-kategori" style="display: inline-block; margin-left: 10px; padding: 8px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 3px;">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container" style="margin-top: 30px;">
        <h2>Data Kategori</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php if ($data_kategori->num_rows > 0): ?>
                    <?php while ($row = $data_kategori->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi_kategori']) ?></td>
                            <td>
                                <a href="?page=tambah-kategori&edit=<?= $row['id'] ?>" class="btn-edit" style="padding: 5px 10px; background: #ffa500; color: white; text-decoration: none; border-radius: 3px; font-size: 14px; margin-right: 5px;">Edit</a>
                                <button onclick="konfirmasiHapus('<?= $row['id'] ?>')" style="padding: 5px 10px; background: #ff0000; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 14px;">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 10px;">
                            Belum ada data kategori.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    function konfirmasiHapus(id) {
        if (confirm('Yakin ingin menghapus data dengan ID ' + id + '?')) {
            window.location = '?page=tambah-kategori&hapus=' + id;
        }
    }
</script>
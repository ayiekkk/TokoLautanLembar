<?php
// 1. Hubungkan ke Database via Router Path Aman
include_once $_SERVER['DOCUMENT_ROOT'] . "/LautanLembar-v2/config/database.php";

$db = new Database();
$conn = $db->getConnection();

$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Tempat menampung data yang akan diedit (Default: kosong untuk tambah data)
$edit_data = null;

// 2. PROSES LOGIKA BACKEND (MURNI HTTP POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- PROSES ACTION: TRIGGER EDIT (MENAIKKAN DATA KE FORM) ---
    if (isset($_POST['action']) && $_POST['action'] === 'trigger_edit') {
        $edit_id = intval($_POST['id']);
        $stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $edit_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        // Jangan di-redirect, biarkan datanya langsung naik ke form di bawah!
    }

    // --- PROSES ACTION: SIMPAN / UPDATE ---
    if (isset($_POST['action']) && $_POST['action'] === 'save') {
        $id           = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nama_buku    = $_POST['nama_buku'];
        $nama_penulis = $_POST['penulis'];
        $harga        = intval($_POST['harga']);
        $stok         = intval($_POST['stok']);
        $id_kategori  = intval($_POST['id_kategori']);
        $filename     = null;

        if ($id > 0) {
            $stmt = $conn->prepare("SELECT sampul FROM buku WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $filename = $res['sampul'] ?? null;
            $stmt->close();
        }

        if (isset($_FILES['sampul']) && $_FILES['sampul']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['sampul']['name'], PATHINFO_EXTENSION);
            $new_filename = time() . '_' . uniqid() . '.' . $ext;

            if (move_uploaded_file($_FILES['sampul']['tmp_name'], $upload_dir . $new_filename)) {
                if ($id > 0 && $filename && file_exists($upload_dir . $filename)) {
                    unlink($upload_dir . $filename);
                }
                $filename = $new_filename;
            }
        }

        if ($id === 0) {
            $stmt = $conn->prepare("INSERT INTO buku (nama_buku, penulis, harga, stok, sampul, id_kategori) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiisi", $nama_buku, $nama_penulis, $harga, $stok, $filename, $id_kategori);
        } else {
            $stmt = $conn->prepare("UPDATE buku SET nama_buku = ?, penulis = ?, harga = ?, stok = ?, sampul = ?, id_kategori = ? WHERE id = ?");
            $stmt->bind_param("ssiisii", $nama_buku, $nama_penulis, $harga, $stok, $filename, $id_kategori, $id);
        }

        $stmt->execute();
        $stmt->close();

        // Segarkan halaman agar form kembali bersih
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    // --- PROSES ACTION: HAPUS ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = intval($_POST['id']);

        $stmt = $conn->prepare("SELECT sampul FROM buku WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['sampul'] && file_exists($upload_dir . $res['sampul'])) {
            unlink($upload_dir . $res['sampul']);
        }
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM buku WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute(); // Mengeksekusi hapus ke DB
        $stmt->close();

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Ambil data untuk tabel penampil bawah
$daftar_buku = $conn->query("SELECT * FROM buku ORDER BY id DESC");
$daftar_kategori = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>

<main>
    <h1 id="form-title"><?= $edit_data ? 'Ubah Data Buku' : 'Tambah Data Buku' ?></h1>

    <div class="form-container">
        <form id="book-form" method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $edit_data ? $edit_data['id'] : '0' ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Buku</label>
                    <input type="text" name="nama_buku" value="<?= $edit_data ? htmlspecialchars($edit_data['nama_buku']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Penulis</label>
                    <input type="text" name="penulis" value="<?= $edit_data ? htmlspecialchars($edit_data['penulis']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" value="<?= $edit_data ? $edit_data['harga'] : '' ?>" min="0" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" value="<?= $edit_data ? $edit_data['stok'] : '' ?>" min="0" required>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Kategori Buku</label>
                    <select name="id_kategori" required style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                        <option value="">-- Pilih Kategori --</option>
                        <?php if ($daftar_kategori && $daftar_kategori->num_rows > 0): ?>
                            <?php while ($kat = $daftar_kategori->fetch_assoc()): ?>
                                <option value="<?= $kat['id'] ?>" <?= ($edit_data && $edit_data['id_kategori'] == $kat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="upload-group">
                <label>Sampul Buku (Cover)</label>
                <div id="drop-zone">
                    <svg class="upload-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="drop-text">Seret sampul ke sini, atau <span>pilih berkas</span></p>
                </div>
                <input type="file" name="sampul" id="file-input" accept="image/*" hidden>
                <div id="file-preview">
                    <?php if ($edit_data && $edit_data['sampul']): ?>
                        <div class="file-item" style="margin-top: 10px; padding: 10px; background: #e2e8f0; border-radius: 6px;"><span>Sampul saat ini: <strong><?= $edit_data['sampul'] ?></strong></span></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="submit-btn"><?= $edit_data ? 'Perbarui Data Buku' : 'Simpan Data Buku' ?></button>
            
            <?php if ($edit_data): ?>
                <a href="" class="cancel-btn" style="display: block; text-align: center; text-decoration: none; line-height: 2.5; background: #ef4444; color: white; border-radius: 4px; margin-top: 8px;">Batal Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <h2>Daftar Buku Tersimpan</h2>
    <br>
    <table>
        <thead>
            <tr>
                <th>Sampul</th>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($daftar_buku && $daftar_buku->num_rows > 0): ?>
                <?php while ($row = $daftar_buku->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if ($row['sampul']): ?>
                                <img src="uploads/<?= $row['sampul'] ?>" class="thumb" width="50">
                            <?php else: ?>
                                <div class="thumb" style="display:flex;align-items:center;justify-content:center;font-size:10px;color:#94a3b8">No Cover</div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['nama_buku']) ?></td>
                        <td><?= htmlspecialchars($row['nama_penulis'] ?? '') ?></td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td><?= $row['stok'] ?></td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="action" value="trigger_edit">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn-edit" style="font-size: 13px; padding: 6px 10px; background: #eab308; color: white; border: none; border-radius: 4px; cursor: pointer;">Ubah</button>
                                </form>

                                <form method="POST" action="" onsubmit="return confirm('Yakin hapus buku ini?')" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn-delete" style="font-size: 13px; padding: 6px 10px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#94a3b8;">Belum ada data buku.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
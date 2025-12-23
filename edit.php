<?php
include 'koneksi.php';

/* ===============================
   FUNGSI CLEAN INPUT (WAJIB ADA)
   =============================== */
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$page_title = "Edit Barang";

// AMBIL ID DARI URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// AMBIL DATA BARANG
$query  = "SELECT * FROM barang WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    $_SESSION['pesan'] = "Barang tidak ditemukan!";
    $_SESSION['tipe']  = "error";
    header("Location: index.php?page=data_barang");
    exit();
}

// PROSES UPDATE DATA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $kode_barang = clean_input($_POST['kode_barang']);
    $nama_barang = clean_input($_POST['nama_barang']);
    $kategori    = clean_input($_POST['kategori']);
    $stok        = (int) $_POST['stok'];
    $harga       = (int) $_POST['harga'];
    $deskripsi   = clean_input($_POST['deskripsi']);
    $status      = clean_input($_POST['status']);

    // --- LOGIKA UPLOAD FOTO BARANG ---
$query_foto = "";

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $nama_file = $_FILES['foto']['name'];
    $tmp_file  = $_FILES['foto']['tmp_name'];
    $ekstensi  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
    $valid_ext = ['jpg', 'jpeg', 'png'];

    if (in_array($ekstensi, $valid_ext)) {

        // Hapus foto lama
        if (!empty($barang['foto']) && file_exists("uploads/" . $barang['foto'])) {
            unlink("uploads/" . $barang['foto']);
        }

        // Upload foto baru
        $nama_foto_baru = time() . "_" . $nama_file;
        move_uploaded_file($tmp_file, "uploads/" . $nama_foto_baru);

        // Simpan ke query
        $query_foto = ", foto = '$nama_foto_baru'";
    }
}

    // Cek kode barang unik (kecuali data ini)
    $cek = mysqli_query(
        $koneksi,
        "SELECT id FROM barang WHERE kode_barang='$kode_barang' AND id != $id"
    );

    if (mysqli_num_rows($cek) > 0) {

        $_SESSION['pesan'] = "Kode barang sudah digunakan!";
        $_SESSION['tipe']  = "error";

    } else {

        $update = "UPDATE barang SET
    kode_barang = '$kode_barang',
    nama_barang = '$nama_barang',
    kategori    = '$kategori',
    stok        = '$stok',
    harga       = '$harga',
    deskripsi   = '$deskripsi',
    status      = '$status'
    $query_foto,
    updated_at  = NOW()
WHERE id = $id";


        if (mysqli_query($koneksi, $update)) {
            $_SESSION['pesan'] = "Barang berhasil diperbarui!";
            $_SESSION['tipe']  = "success";
            header("Location: index.php?page=data_barang");
            exit();
        } else {
            $_SESSION['pesan'] = "Gagal memperbarui barang: " . mysqli_error($koneksi);
            $_SESSION['tipe']  = "error";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="content-wrapper">
    <?php include 'includes/menu.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h2>Edit Barang</h2>
        </div>

        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Data Barang</h3>
                    <a href="index.php?page=data_barang" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" class="form-vertical">
                        <div class="form-row">
    <div class="form-group" style="width:100%;">
        <label><i class="fas fa-camera"></i> Foto Barang</label>

        <div style="display:flex; gap:20px; align-items:center;">
            <div>
                <?php if (!empty($barang['foto']) && file_exists("uploads/" . $barang['foto'])): ?>
                    <img src="uploads/<?= $barang['foto']; ?>"
                         style="width:90px; height:90px; object-fit:cover; border-radius:8px; border:1px solid #ddd;">
                <?php else: ?>
                    <div style="width:90px; height:90px; background:#eee;
                                display:flex; align-items:center; justify-content:center;
                                border-radius:8px;">
                        No Image
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <input type="file" name="foto" accept=".jpg,.jpeg,.png">
                <small style="display:block; color:#666;">
                    Biarkan kosong jika tidak ingin mengganti foto.
                </small>
            </div>
        </div>
    </div>
</div>


                        <div class="form-row">
                            <div class="form-group">
                                <label>Kode Barang *</label>
                                <input type="text" name="kode_barang"
                                    value="<?= htmlspecialchars($barang['kode_barang']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Nama Barang *</label>
                                <input type="text" name="nama_barang"
                                    value="<?= htmlspecialchars($barang['nama_barang']); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Kategori *</label>
                                <select name="kategori" required>
                                    <?php
                                    $kategori = ['Elektronik','Pakaian','Makanan','Minuman','Alat Tulis','Olahraga','Lainnya'];
                                    foreach ($kategori as $k) {
                                        $selected = ($barang['kategori'] == $k) ? 'selected' : '';
                                        echo "<option value='$k' $selected>$k</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Stok *</label>
                                <input type="number" name="stok" min="0"
                                    value="<?= $barang['stok']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Harga *</label>
                                <input type="number" name="harga" min="0"
                                    value="<?= $barang['harga']; ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="aktif" <?= $barang['status']=='aktif'?'selected':''; ?>>Aktif</option>
                                    <option value="nonaktif" <?= $barang['status']=='nonaktif'?'selected':''; ?>>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" rows="4"><?= htmlspecialchars($barang['deskripsi']); ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
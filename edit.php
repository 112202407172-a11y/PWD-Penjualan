<?php
include 'koneksi.php';

/* ===============================
   FUNGSI CLEAN INPUT (WAJIB ADA)
   =============================== */
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$page_title = "Edit Barang";

/* ===============================
   AMBIL ID DARI URL
   =============================== */
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* ===============================
   AMBIL DATA BARANG
   =============================== */
$query  = "SELECT * FROM barang WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    $_SESSION['pesan'] = "Barang tidak ditemukan!";
    $_SESSION['tipe']  = "error";
    header("Location: index.php?page=data_barang");
    exit();
}

/* ===============================
   PROSES UPDATE DATA
   =============================== */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $kode_barang = clean_input($_POST['kode_barang']);
    $nama_barang = clean_input($_POST['nama_barang']);
    $kategori    = clean_input($_POST['kategori']);
    $stok        = (int) $_POST['stok'];
    $harga       = (int) $_POST['harga'];
    $deskripsi   = clean_input($_POST['deskripsi']);
    $status      = clean_input($_POST['status']);

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
            status      = '$status',
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
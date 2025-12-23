<?php
include 'koneksi.php';

$page_title = "Tambah Barang";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $kode_barang = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $stok        = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $harga       = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    /* =============================
       LOGIKA UPLOAD FOTO BARANG
       ============================= */
    $nama_foto_baru = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nama_file_asli = $_FILES['foto']['name'];
        $tmp_file       = $_FILES['foto']['tmp_name'];

        $ekstensi = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];

        if (in_array($ekstensi, $ekstensi_valid)) {
            $nama_foto_baru = time() . "_" . $nama_file_asli;
            move_uploaded_file($tmp_file, "uploads/" . $nama_foto_baru);
        } else {
            $_SESSION['pesan'] = "Format foto harus JPG atau PNG!";
            $_SESSION['tipe']  = "error";
        }
    }

    /* =============================
       AUTO GENERATE KODE BARANG
       ============================= */
    if (empty($kode_barang)) {
        $prefix = "BRG";
        $query  = "SELECT MAX(SUBSTRING(kode_barang, 4)) AS max_code FROM barang";
        $result = mysqli_query($koneksi, $query);
        $row    = mysqli_fetch_assoc($result);
        $next   = ($row['max_code'] ?? 0) + 1;
        $kode_barang = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /* =============================
       CEK KODE BARANG
       ============================= */
    $cek = mysqli_query($koneksi, "SELECT kode_barang FROM barang WHERE kode_barang='$kode_barang'");
    if (mysqli_num_rows($cek) > 0) {

        $_SESSION['pesan'] = "Kode barang sudah digunakan!";
        $_SESSION['tipe']  = "error";

    } else {

        /* =============================
           INSERT DATA BARANG + FOTO
           ============================= */
        $query = "INSERT INTO barang 
        (kode_barang, nama_barang, kategori, stok, harga, deskripsi, status, foto)
        VALUES
        ('$kode_barang', '$nama_barang', '$kategori', '$stok', '$harga', '$deskripsi', 'aktif', '$nama_foto_baru')";

        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Barang berhasil ditambahkan!";
            $_SESSION['tipe']  = "success";
            header("Location: index.php?page=data_barang");
            exit();
        } else {
            $_SESSION['pesan'] = "Gagal menambahkan barang: " . mysqli_error($koneksi);
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
            <h2>Tambah Barang Baru</h2>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="index.php?page=data_barang">Data Barang</a>
                <i class="fas fa-chevron-right"></i>
                <span>Tambah Barang</span>
            </div>
        </div>

        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h3>Form Tambah Barang</h3>
                    <a href="index.php?page=data_barang" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" class="form-vertical" enctype="multipart/form-data">

                        <!-- FOTO BARANG -->
                        <div class="form-row">
                            <div class="form-group" style="width:100%;">
                                <label for="foto">
                                    <i class="fas fa-camera"></i> Foto Barang
                                </label>
                                <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png">
                                <small style="color:#666;">
                                    Format JPG / PNG (Opsional)
                                </small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="kode_barang">
                                    <i class="fas fa-barcode"></i> Kode Barang
                                </label>
                                <input type="text" id="kode_barang" name="kode_barang"
                                       placeholder="Kosongkan untuk auto-generate (BRGxxx)">
                            </div>

                            <div class="form-group">
                                <label for="nama_barang">
                                    <i class="fas fa-box"></i> Nama Barang *
                                </label>
                                <input type="text" id="nama_barang" name="nama_barang" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="kategori">
                                    <i class="fas fa-tags"></i> Kategori *
                                </label>
                                <select id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Elektronik">Elektronik</option>
                                    <option value="Pakaian">Pakaian</option>
                                    <option value="Makanan">Makanan</option>
                                    <option value="Minuman">Minuman</option>
                                    <option value="Alat Tulis">Alat Tulis</option>
                                    <option value="Olahraga">Olahraga</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="stok">
                                    <i class="fas fa-cubes"></i> Stok *
                                </label>
                                <input type="number" id="stok" name="stok" min="0" required>
                            </div>

                            <div class="form-group">
                                <label for="harga">
                                    <i class="fas fa-money-bill-wave"></i> Harga (Rp) *
                                </label>
                                <input type="number" id="harga" name="harga" min="0" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">
                                <i class="fas fa-align-left"></i> Deskripsi
                            </label>
                            <textarea id="deskripsi" name="deskripsi" rows="4"></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Barang
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>

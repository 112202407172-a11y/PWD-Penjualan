<?php
include 'koneksi.php';

$page_title = "Tambah Pegawai";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_pegawai    = mysqli_real_escape_string($koneksi, $_POST['id_pegawai']);
    $nama          = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email         = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_telepon    = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $jabatan       = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $alamat        = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $gaji          = mysqli_real_escape_string($koneksi, $_POST['gaji']);
    $tanggal_masuk = mysqli_real_escape_string($koneksi, $_POST['tanggal_masuk']);
    $status        = mysqli_real_escape_string($koneksi, $_POST['status']);

    // --- [BARU] LOGIKA UPLOAD FOTO ---
    $nama_foto_baru = null; // Default kosong (NULL) jika tidak ada foto

    // Cek apakah ada file foto yang dikirim dan tidak error
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nama_file_asli = $_FILES['foto']['name'];
        $tmp_file       = $_FILES['foto']['tmp_name'];
        
        // Ambil ekstensi file (contoh: jpg, png)
        $ekstensi = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
        $ekstensi_valid = ['jpg', 'jpeg', 'png', 'gif'];

        // Cek validasi ekstensi
        if (in_array($ekstensi, $ekstensi_valid)) {
            // Buat nama file unik: waktu_namasli.jpg (biar gak bentrok)
            $nama_foto_baru = time() . "_" . $nama_file_asli;
            
            // Pindahkan file ke folder uploads
            // Pastikan folder 'uploads' sudah dibuat!
            move_uploaded_file($tmp_file, "uploads/" . $nama_foto_baru);
        } else {
            // Jika format salah, kembalikan error (opsional)
            $_SESSION['pesan'] = "Format foto harus JPG atau PNG!";
            $_SESSION['tipe'] = "error";
            header("Location: index.php?page=data_pegawai");
            exit();
        }
    }
    // --- [AKHIR BARU] ---

    // Auto Generate ID Pegawai
    if (empty($id_pegawai)) {
        $prefix = "PEG";
        $query = "SELECT MAX(SUBSTRING(id_pegawai, 4)) AS max_code FROM pegawai";
        $result = mysqli_query($koneksi, $query);
        $row = mysqli_fetch_assoc($result);
        $next_num = ($row['max_code'] ?? 0) + 1;
        $id_pegawai = $prefix . str_pad($next_num, 3, '0', STR_PAD_LEFT);
    }

    // Cek ID Pegawai
    $check = mysqli_query($koneksi, "SELECT id_pegawai FROM pegawai WHERE id_pegawai='$id_pegawai'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['pesan'] = "ID Pegawai sudah digunakan!";
        $_SESSION['tipe'] = "error";
    } else {

        // --- [UPDATE] Query Insert ditambah kolom 'foto' ---
        $query = "INSERT INTO pegawai 
        (id_pegawai, nama, jabatan, no_telepon, email, alamat, status, gaji, tanggal_masuk, foto)
        VALUES
        ('$id_pegawai', '$nama', '$jabatan', '$no_telepon', '$email', '$alamat', '$status', '$gaji', '$tanggal_masuk', '$nama_foto_baru')";

        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Pegawai berhasil ditambahkan!";
            $_SESSION['tipe'] = "success";
            header("Location: index.php?page=data_pegawai");
            exit();
        } else {
            $_SESSION['pesan'] = "Gagal menambahkan pegawai: " . mysqli_error($koneksi);
            $_SESSION['tipe'] = "error";
        }
    }
}
?>


<?php include 'includes/header.php'; ?>

<div class="content-wrapper">
    <?php include 'includes/menu.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h2>Tambah Pegawai Baru</h2>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="index.php?page=data_pegawai">Data Pegawai</a>
                <i class="fas fa-chevron-right"></i>
                <span>Tambah Pegawai</span>
            </div>
        </div>

        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h3>Form Tambah Pegawai</h3>
                    <a href="index.php?page=data_pegawai" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" class="form-vertical" enctype="multipart/form-data">

                        <div class="form-row">
                            <div class="form-group" style="width: 100%;">
                                <label for="foto">
                                    <i class="fas fa-camera"></i> Foto Profil
                                </label>
                                <input type="file" id="foto" name="foto" accept=".jpg, .jpeg, .png">
                                <small style="display:block; margin-top:5px; color:#666;">
                                    Format: JPG, JPEG, PNG. (Boleh dikosongkan)
                                </small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="id_pegawai">
                                    <i class="fas fa-id-card"></i> ID Pegawai
                                </label>
                                <input type="text" id="id_pegawai" name="id_pegawai"
                                    placeholder="Kosongkan untuk auto-generate (PEGxxx)">
                            </div>

                            <div class="form-group">
                                <label for="nama">
                                    <i class="fas fa-user"></i> Nama Lengkap *
                                </label>
                                <input type="text" id="nama" name="nama" required placeholder="Nama lengkap pegawai">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i> Email *
                                </label>
                                <input type="email" id="email" name="email" required placeholder="email@contoh.com">
                            </div>

                            <div class="form-group">
                                <label for="no_telepon">
                                    <i class="fas fa-phone"></i> No. Telepon *
                                </label>
                                <input type="text" id="no_telepon" name="no_telepon" required
                                    placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="jabatan">
                                    <i class="fas fa-briefcase"></i> Jabatan *
                                </label>
                                <select id="jabatan" name="jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Supervisor">Supervisor</option>
                                    <option value="Staff Admin">Staff Admin</option>
                                    <option value="Staff Gudang">Staff Gudang</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="IT Support">IT Support</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="gaji">
                                    <i class="fas fa-money-bill-wave"></i> Gaji (Rp) *
                                </label>
                                <input type="number" id="gaji" name="gaji" min="0" required
                                    placeholder="Contoh: 5000000">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group" style="width: 100%;">
                                <label for="alamat">
                                    <i class="fas fa-map-marker-alt"></i> Alamat
                                </label>
                                <textarea id="alamat" name="alamat" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tanggal_masuk">
                                    <i class="fas fa-calendar-alt"></i> Tanggal Masuk *
                                </label>
                                <input type="date" id="tanggal_masuk" name="tanggal_masuk" required>
                            </div>

                            <div class="form-group">
                                <label for="status_aktif">
                                    <i class="fas fa-toggle-on"></i> Status Aktif *
                                </label>
                                <select id="status_aktif" name="status" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Non-Aktif">Non-Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pegawai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
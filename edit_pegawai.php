<?php
// Pastikan session dimulai di awal sebelum output apapun
if (!isset($_SESSION)) {
    session_start();
}

include 'koneksi.php';

// --- DEFINISI FUNGSI CLEAN_INPUT ---
function clean_input($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if (isset($koneksi)) {
        $data = mysqli_real_escape_string($koneksi, $data);
    }
    return $data;
}
// -------------------------------------

$page_title = "Edit Pegawai";

// Ambil ID lama dari URL
if (isset($_GET['id'])) {
    $id_lama = mysqli_real_escape_string($koneksi, $_GET['id']);
} else {
    header("Location: index.php?page=data_pegawai");
    exit();
}

// Ambil data pegawai berdasarkan ID lama
$query = "SELECT * FROM pegawai WHERE id_pegawai = '$id_lama'";
$result = mysqli_query($koneksi, $query);
$pegawai = mysqli_fetch_assoc($result);

if (!$pegawai) {
    $_SESSION['pesan'] = "Data pegawai tidak ditemukan!";
    $_SESSION['tipe'] = "error";
    header("Location: index.php?page=data_pegawai");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil input form dan bersihkan
    $id_pegawai_baru = clean_input($_POST['id_pegawai']);
    $nama           = clean_input($_POST['nama']);
    $email          = clean_input($_POST['email']);
    $no_telepon     = clean_input($_POST['no_telepon']);
    $alamat         = clean_input($_POST['alamat']);
    $jabatan        = clean_input($_POST['jabatan']);
    $gaji           = clean_input($_POST['gaji']);
    $tanggal_masuk  = clean_input($_POST['tanggal_masuk']);
    $status_aktif   = clean_input($_POST['status_aktif']);

    // --- [BARU] LOGIKA UPLOAD FOTO ---
    $query_foto = ""; // Variabel tambahan untuk query SQL
    
    // Cek apakah user mengupload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nama_file = $_FILES['foto']['name'];
        $tmp_file  = $_FILES['foto']['tmp_name'];
        $ekstensi  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $valid_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ekstensi, $valid_ext)) {
            // 1. Hapus foto lama jika ada
            if (!empty($pegawai['foto']) && file_exists("uploads/" . $pegawai['foto'])) {
                unlink("uploads/" . $pegawai['foto']);
            }

            // 2. Upload foto baru
            $nama_foto_baru = time() . "_" . $nama_file;
            move_uploaded_file($tmp_file, "uploads/" . $nama_foto_baru);

            // 3. Siapkan potongan query update
            $query_foto = ", foto = '$nama_foto_baru'";
        }
    }
    // --- [AKHIR LOGIKA FOTO] ---

    // Cek duplikasi ID baru jika ID diubah
    $check_query = "SELECT id_pegawai FROM pegawai WHERE id_pegawai = '$id_pegawai_baru' AND id_pegawai != '$id_lama'";
    $check_result = mysqli_query($koneksi, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['pesan'] = "ID Pegawai sudah digunakan oleh pegawai lain!";
        $_SESSION['tipe'] = "error";
    } else {
        // Query UPDATE (Ditambahkan $query_foto di tengahnya)
        $query = "UPDATE pegawai SET 
                  id_pegawai = '$id_pegawai_baru',
                  nama = '$nama',
                  email = '$email',
                  no_telepon = '$no_telepon',
                  alamat = '$alamat', 
                  jabatan = '$jabatan',
                  gaji = '$gaji',
                  tanggal_masuk = '$tanggal_masuk',
                  status = '$status_aktif'
                  $query_foto 
                  WHERE id_pegawai = '$id_lama'";

        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Data pegawai berhasil diperbarui!";
            $_SESSION['tipe'] = "success";
            header("Location: index.php?page=data_pegawai");
            exit();
        } else {
            $_SESSION['pesan'] = "Gagal memperbarui pegawai: " . mysqli_error($koneksi);
            $_SESSION['tipe'] = "error";
        }
    }
    
    // Refresh data jika gagal redirect
    $query_reloaded = "SELECT * FROM pegawai WHERE id_pegawai = '$id_pegawai_baru'";
    $result_reloaded = mysqli_query($koneksi, $query_reloaded);
    $pegawai = mysqli_fetch_assoc($result_reloaded);
    $id_lama = $id_pegawai_baru;
}
?>

<?php include 'includes/header.php'; ?>

<div class="content-wrapper">
    <?php include 'includes/menu.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h2>Edit Pegawai</h2>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="index.php?page=data_pegawai">Data Pegawai</a>
                <i class="fas fa-chevron-right"></i>
                <span>Edit Pegawai</span>
            </div>
        </div>

        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Data Pegawai (ID: <?php echo htmlspecialchars($pegawai['id_pegawai']); ?>)</h3>
                    <a href="index.php?page=data_pegawai" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" class="form-vertical" enctype="multipart/form-data"> 

                        <div class="form-row">
                            <div class="form-group" style="width: 100%;">
                                <label><i class="fas fa-camera"></i> Foto Profil</label>
                                <div style="display: flex; gap: 20px; align-items: center;">
                                    <div class="img-preview">
                                        <?php if (!empty($pegawai['foto']) && file_exists("uploads/" . $pegawai['foto'])): ?>
                                            <img src="uploads/<?php echo $pegawai['foto']; ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid #ddd;">
                                        <?php else: ?>
                                            <div style="width: 80px; height: 80px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center;">No Img</div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <input type="file" name="foto" accept=".jpg, .jpeg, .png">
                                        <small style="display: block; color: #666; margin-top: 5px;">Biarkan kosong jika tidak ingin mengganti foto.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="id_pegawai">
                                    <i class="fas fa-id-card"></i> ID Pegawai *
                                </label>
                                <input type="text" id="id_pegawai" name="id_pegawai"
                                    value="<?php echo htmlspecialchars($pegawai['id_pegawai']); ?>" required>
                                <small class="form-hint">Hati-hati mengubah ID Pegawai.</small>
                            </div>

                            <div class="form-group">
                                <label for="nama">
                                    <i class="fas fa-user"></i> Nama Lengkap *
                                </label>
                                <input type="text" id="nama" name="nama"
                                    value="<?php echo htmlspecialchars($pegawai['nama']); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i> Email *
                                </label>
                                <input type="email" id="email" name="email"
                                    value="<?php echo htmlspecialchars($pegawai['email']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="no_telepon">
                                    <i class="fas fa-phone"></i> No. Telepon *
                                </label>
                                <input type="text" id="no_telepon" name="no_telepon"
                                    value="<?php echo htmlspecialchars($pegawai['no_telepon']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat">
                                <i class="fas fa-map-marker-alt"></i> Alamat *
                            </label>
                            <textarea id="alamat" name="alamat" required class="form-control" rows="3"><?php echo htmlspecialchars($pegawai['alamat'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="jabatan">
                                    <i class="fas fa-briefcase"></i> Jabatan *
                                </label>
                                <select id="jabatan" name="jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                    <?php
                                    $jabatan_list = ["Manager", "Supervisor", "Staff Admin", "Staff Gudang", "Marketing", "IT Support"];
                                    foreach ($jabatan_list as $jab) {
                                        $selected = (isset($pegawai['jabatan']) && $pegawai['jabatan'] == $jab) ? 'selected' : '';
                                        echo "<option value='$jab' $selected>$jab</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="gaji">
                                    <i class="fas fa-money-bill-wave"></i> Gaji (Rp) *
                                </label>
                                <input type="number" id="gaji" name="gaji" value="<?php echo htmlspecialchars($pegawai['gaji']); ?>"
                                    min="0" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tanggal_masuk">
                                    <i class="fas fa-calendar-alt"></i> Tanggal Masuk *
                                </label>
                                <input type="date" id="tanggal_masuk" name="tanggal_masuk"
                                    value="<?php echo htmlspecialchars($pegawai['tanggal_masuk']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="status_aktif">
                                    <i class="fas fa-toggle-on"></i> Status Aktif *
                                </label>
                                <select id="status_aktif" name="status_aktif" required>
                                    <option value="Aktif" <?php echo ($pegawai['status'] == "Aktif") ? 'selected' : ''; ?>>
                                        Aktif</option>
                                    <option value="Non-Aktif" <?php echo ($pegawai['status'] == "Non-Aktif") ? 'selected' : ''; ?>>
                                        Non-Aktif</option>
                                </select>
                            </div>
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
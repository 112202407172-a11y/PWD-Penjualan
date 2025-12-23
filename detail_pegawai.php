<?php
include 'koneksi.php';

$id_pegawai = $_GET['id_pegawai'] ?? '';

if ($id_pegawai === '') {
    http_response_code(400);
    echo '<div class="alert alert-danger">ID Pegawai tidak ditemukan.</div>';
    exit;
}

$id_pegawai = mysqli_real_escape_string($koneksi, $id_pegawai);

$query = "SELECT * FROM pegawai WHERE id_pegawai = '$id_pegawai'";
$result = mysqli_query($koneksi, $query);
$pegawai = mysqli_fetch_assoc($result);

if (!$pegawai) {
    http_response_code(404);
    echo '<div class="alert alert-danger">Data pegawai tidak ditemukan.</div>';
    exit;
}
?>

<div style="text-align: center; margin-bottom: 20px;">
    <?php 
    // Cek apakah ada foto dan filenya ada di folder
    if (!empty($pegawai['foto']) && file_exists("uploads/" . $pegawai['foto'])) {
        // Tampilkan Foto (Ukuran 180px biar besar dan jelas)
        echo '<img src="uploads/' . htmlspecialchars($pegawai['foto']) . '" 
              style="width: 180px; height: 180px; object-fit: cover; border-radius: 50%; border: 5px solid #ddd; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">';
    } else {
        // Tampilkan Kotak Kosong jika tidak ada foto
        echo '<div style="width: 180px; height: 180px; background: #f0f0f0; border-radius: 50%; border: 5px solid #ddd; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: #888;">
                <div style="text-align:center;">
                    <i class="fas fa-user fa-4x"></i><br>
                    <span style="font-size:12px;">No Photo</span>
                </div>
              </div>';
    }
    ?>
</div>
<form class="form-vertical">

    <div class="form-group">
        <label for="id_pegawai">ID Pegawai</label>
        <input type="text" class="form-control" disabled value="<?php echo htmlspecialchars($pegawai['id_pegawai']); ?>">
    </div>

    <div class="form-group">
        <label><i class="fas fa-user"></i> Nama Lengkap</label>
        <input type="text" disabled value="<?php echo htmlspecialchars($pegawai['nama']); ?>">
    </div>

    <div class="form-row detail-row">
        <div class="form-group detail-col">
            <label><i class="fas fa-envelope"></i> Email</label>
            <input type="email" disabled value="<?php echo htmlspecialchars($pegawai['email']); ?>">
        </div>

        <div class="form-group detail-col">
            <label><i class="fas fa-phone"></i> No Telepon</label>
            <input type="text" disabled value="<?php echo htmlspecialchars($pegawai['no_telepon']); ?>">
        </div>
    </div>

    <div class="form-group">
        <label><i class="fas fa-map-marker-alt"></i> Alamat</label>
        <textarea disabled style="width:100%; min-height:70px;"><?php echo htmlspecialchars($pegawai['alamat']); ?></textarea>
    </div>


    <div class="form-row detail-row">
        <div class="form-group detail-col">
            <label><i class="fas fa-briefcase"></i> Jabatan</label>
            <input type="text" disabled value="<?php echo htmlspecialchars($pegawai['jabatan']); ?>">
        </div>

        <div class="form-group detail-col">
            <label><i class="fas fa-money-bill-wave"></i> Gaji (Rp)</label>
            <input type="text" disabled value="Rp <?php echo number_format($pegawai['gaji'], 0, ',', '.'); ?>">
        </div>
    </div>

    <div class="form-row detail-row">
        <div class="form-group detail-col">
            <label><i class="fas fa-calendar-alt"></i> Tanggal Masuk</label>
            <input type="text" disabled value="<?php echo date('d F Y', strtotime($pegawai['tanggal_masuk'])); ?>">
        </div>

        <div class="form-group detail-col">
            <label><i class="fas fa-info-circle"></i> Status</label>
            <?php
                $is_active = $pegawai['status'] == 'Aktif';
                $statusLabel = $is_active ? 'Aktif' : 'Non-Aktif';
                $statusColor = $is_active ? 'green' : 'red';
            ?>
            <input type="text" disabled
                value="<?= htmlspecialchars($statusLabel); ?>"
                style="font-weight: bold; color: <?= $statusColor; ?>;">
        </div>
    </div>
</form>
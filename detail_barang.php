<?php
include 'koneksi.php';

$id = $_GET['id'] ?? '';

if ($id === '') {
    http_response_code(400);
    echo '<div class="alert alert-danger">ID Barang tidak ditemukan.</div>';
    exit;
}

$id = mysqli_real_escape_string($koneksi, $id);

$query = "SELECT * FROM barang WHERE id = '$id'";
$result = mysqli_query($koneksi, $query);
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    http_response_code(404);
    echo '<div class="alert alert-danger">Data barang tidak ditemukan.</div>';
    exit;
}
?>

<!-- FOTO BARANG -->
<div style="text-align:center; margin-bottom:20px;">
<?php if (!empty($barang['foto']) && file_exists("uploads/".$barang['foto'])): ?>
    <img src="uploads/<?= htmlspecialchars($barang['foto']); ?>"
         style="width:180px;height:180px;object-fit:cover;border-radius:12px;
                border:5px solid #ddd;box-shadow:0 4px 8px rgba(0,0,0,.1);">
<?php else: ?>
    <div style="width:180px;height:180px;background:#f0f0f0;border-radius:12px;
                display:flex;align-items:center;justify-content:center;margin:auto;color:#888;">
        <i class="fas fa-box fa-4x"></i>
    </div>
<?php endif; ?>
</div>

<form class="form-vertical">

    <div class="form-group">
        <label>Kode Barang</label>
        <input type="text" disabled value="<?= htmlspecialchars($barang['kode_barang']); ?>">
    </div>

    <div class="form-group">
        <label>Nama Barang</label>
        <input type="text" disabled value="<?= htmlspecialchars($barang['nama_barang']); ?>">
    </div>

    <div class="form-row detail-row">
        <div class="form-group detail-col">
            <label>Kategori</label>
            <input type="text" disabled value="<?= htmlspecialchars($barang['kategori']); ?>">
        </div>

        <div class="form-group detail-col">
            <label>Stok</label>
            <input type="text" disabled value="<?= $barang['stok']; ?> unit">
        </div>
    </div>

    <div class="form-row detail-row">
        <div class="form-group detail-col">
            <label>Harga</label>
            <input type="text" disabled value="Rp <?= number_format($barang['harga'],0,',','.'); ?>">
        </div>

        <div class="form-group detail-col">
            <label>Status</label>
            <?php
                $aktif = $barang['status'] === 'aktif';
            ?>
            <input type="text" disabled
                value="<?= ucfirst($barang['status']); ?>"
                style="font-weight:bold;color:<?= $aktif?'green':'red'; ?>">
        </div>
    </div>

    <div class="form-group">
        <label>Deskripsi</label>
        <textarea disabled style="width:100%;min-height:70px;">
<?= htmlspecialchars($barang['deskripsi']); ?>
        </textarea>
    </div>

</form>
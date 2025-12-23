<?php
include 'koneksi.php';

// Query data barang
$query = "SELECT * FROM barang ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<style>
    /* Thumbnail foto barang */
    .img-thumbnail {
        width: 65px;
        height: 65px;
        object-fit: cover;
        border-radius: 10px; /* Barang kotak, tidak bulat */
        border: 2px solid #ddd;
    }

    /* Tengah vertikal */
    .data-table tbody td {
        vertical-align: middle !important;
    }

    .action-buttons {
        text-align: center;
        vertical-align: middle !important;
    }
    
</style>

<div class="card">
    <div class="card-header">
        <h3>DATA BARANG</h3>
        <div class="card-actions">
            <a href="tambah.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Barang
            </a>
            <button class="btn btn-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++; ?></td>

                            <!-- FOTO BARANG -->
                            <td>
                                <?php
                                if (!empty($row['foto']) && file_exists("uploads/" . $row['foto'])) {
                                    echo '<img src="uploads/'.$row['foto'].'" class="img-thumbnail">';
                                } else {
                                    echo '<img src="https://via.placeholder.com/65?text=Barang" class="img-thumbnail">';
                                }
                                ?>
                            </td>

                            <td><strong><?= htmlspecialchars($row['kode_barang']); ?></strong></td>

                            <td><?= htmlspecialchars($row['nama_barang']); ?></td>

                            <td>
                                <span class="badge 
                                    <?= $row['stok'] > 10 ? 'badge-success' : ($row['stok'] > 0 ? 'badge-warning' : 'badge-danger'); ?>">
                                    <?= $row['stok']; ?> unit
                                </span>
                            </td>

                            <td>
                                <span class="text-primary">
                                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                </span>
                            </td>

                            <td>
                                <span class="status <?= $row['status'] == 'aktif' ? 'status-active' : 'status-inactive'; ?>">
                                    <i class="fas fa-circle"></i>
                                    <?= ucfirst($row['status']); ?>
                                </span>
                            </td>

                            <td class="action-buttons">
                                <div style="display:flex; justify-content:center; align-items:center; gap:4px; min-height:65px;">
                                    <a href="edit.php?id=<?= $row['id']; ?>" class="btn-action btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus.php?id=<?= $row['id']; ?>"
                                       class="btn-action btn-delete"
                                       onclick="return confirm('Yakin hapus barang ini?')"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                  <a href="#" class="btn-action btn-view view-barang-btn"
   data-id="<?= $row['id']; ?>" title="Detail">
   <i class="fas fa-eye"></i>
</a>

                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-box-open fa-3x"></i>
                                    <h4>Belum ada data barang</h4>
                                    <p>Mulai dengan menambahkan barang baru</p>
                                    <a href="tambah.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Barang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL DETAIL BARANG -->
<div class="modal" id="barangDetailModal">
    <div class="modal-content">

        <div class="modal-header">
            <h5>Detail Barang</h5>
            <button type="button" class="modal-close" data-target="barangDetailModal">&times;</button>
        </div>

        <div class="modal-body" id="barangDetailContent">
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p>Memuat data...</p>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary modal-close-btn" data-target="barangDetailModal">
                Tutup
            </button>
            <a id="editBarangBtn" href="#" class="btn btn-primary" style="display:none;">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>

    </div>
</div>

<script>
$(document).ready(function () {

    function showBarangModal(id) {
        $('#barangDetailModal').css('display', 'block');
        $('#barangDetailContent').html(
            '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Memuat data...</p></div>'
        );
        $('#editBarangBtn').hide();

        $.ajax({
            url: 'detail_barang.php',
            type: 'GET',
            data: { id: id },
            success: function (data) {
                $('#barangDetailContent').html(data);
                $('#editBarangBtn').attr('href', 'edit.php?id=' + id).show();
            },
            error: function () {
                $('#barangDetailContent').html(
                    '<div class="alert alert-danger">Gagal memuat detail barang.</div>'
                );
            }
        });
    }

    function closeBarangModal() {
        $('#barangDetailModal').hide();
        $('#barangDetailContent').html('');
        $('#editBarangBtn').hide();
    }

    $('.view-barang-btn').on('click', function (e) {
        e.preventDefault();
        showBarangModal($(this).data('id'));
    });

    $('.modal-close, .modal-close-btn').on('click', function () {
        closeBarangModal();
    });

    $(window).on('click', function (e) {
        if ($(e.target).is('#barangDetailModal')) {
            closeBarangModal();
        }
    });

});
</script>
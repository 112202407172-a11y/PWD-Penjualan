<?php
// Pastikan file koneksi.php sudah di-include
include 'koneksi.php';

// Query data pegawai
$query = "SELECT * FROM pegawai ORDER BY id_pegawai DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* --- CSS untuk Foto Kecil (Thumbnail) --- */
        .img-thumbnail {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 50%; /* Membuat foto jadi bulat */
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
</head>
<body>

            <div class="card">
                <div class="card-header">
                    <h3>DATA PEGAWAI</h3>
                    <div class="card-actions">
                        <a href="tambah_pegawai.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Pegawai
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
                                    <th>ID Pegawai</th>
                                    <th>Nama Pegawai</th>
                                    <th>Jabatan</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th width="15%" style="text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>

                                    <td>
                                        <?php 
                                            // Cek foto
                                            if (!empty($row['foto']) && file_exists("uploads/" . $row['foto'])) {
                                                echo '<img src="uploads/'.$row['foto'].'" class="img-thumbnail">';
                                            } else {
                                                echo '<img src="https://via.placeholder.com/50?text=User" class="img-thumbnail">';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['id_pegawai']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['nama']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($row['jabatan']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </td>
                                    <td>
                                        <span class="status <?= $row['status'] === 'Aktif' ? 'status-active' : 'status-inactive'; ?>">
                                            <i class="fas fa-circle"></i>
                                            <?php echo $row['status'] === 'Aktif' ? 'Aktif' : 'Non-Aktif'; ?>
                                        </span>
                                    </td>
                                    
                                   <td class="action-buttons">
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; min-height: 65px;">
                                            
                                            <a href="edit_pegawai.php?id=<?php echo $row['id_pegawai']; ?>" 
                                               class="btn-action btn-edit" title="Edit" style="margin: 0 2px;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="hapus_pegawai.php?id_pegawai=<?php echo $row['id_pegawai']; ?>"
                                               class="btn-action btn-delete"
                                               onclick="return confirm('Yakin hapus pegawai ini?')" 
                                               title="Hapus" style="margin: 0 2px;">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            
                                            <a href="#" class="btn-action btn-view view-detail-btn"
                                                data-id="<?php echo $row['id_pegawai']; ?>" 
                                                title="Detail Lengkap" style="margin: 0 2px;">
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
                                            <i class="fas fa-users fa-3x"></i>
                                            <h4>Tidak ada data pegawai</h4>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>  
            </div>
                                    

<div class="modal" id="employeeDetailModal">
    <div class="modal-content"> 
        <div class="modal-header">
            <h5 class="modal-title" id="employeeDetailModalLabel">Detail Pegawai</h5>
            <button type="button" class="modal-close" data-target-modal="employeeDetailModal">&times;</button>
        </div>
        
        <div class="modal-body" id="detailContent">
            <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat data...</p></div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close-btn" data-target-modal="employeeDetailModal">Tutup</button>
            <a id="editButton" href="#" class="btn btn-primary" style="display:none;">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
</div>

<script src="path/ke/jquery.min.js"></script> 

<script>
$(document).ready(function() {
    
    // Fungsi untuk menampilkan Modal
    function showModal(id) {
        var modal = $('#employeeDetailModal');
        var modalBody = $('#detailContent');
        var editButton = $('#editButton');

        modal.css('display', 'block');
        
        // Tampilkan loading sebelum AJAX
        modalBody.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat data...</p></div>');
        editButton.hide(); 

        // Panggil data detail menggunakan AJAX
        $.ajax({
            url: 'detail_pegawai.php', 
            type: 'GET',
            data: { id_pegawai: id },
            success: function(data) {
                modalBody.html(data);
                editButton.attr('href', 'edit_pegawai.php?id=' + id).show();
            },
            error: function(xhr, status, error) {
                modalBody.html('<div class="alert alert-danger" role="alert">Gagal memuat data detail pegawai.</div>');
            }
        });
    }

    // Fungsi untuk menyembunyikan Modal
    function hideModal() {
        var modal = $('#employeeDetailModal');
        modal.css('display', 'none');
        
        $('#detailContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat data...</p></div>');
        $('#editButton').hide();
    }

    $('.view-detail-btn').on('click', function(e) {
        e.preventDefault(); 
        var pegawaiId = $(this).data('id');
        showModal(pegawaiId);
    });

    $('.modal-close, .modal-close-btn').on('click', function() {
        hideModal();
    });

    $(window).on('click', function(event) {
        if ($(event.target).is('#employeeDetailModal')) {
            hideModal();
        }
    });

});
</script>

</body>
</html>
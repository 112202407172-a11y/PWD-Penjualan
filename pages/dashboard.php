<?php 
include 'koneksi.php';
?>

<div class="content-wrapper">
    
    <main class="main-content">       
        <div class="content">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="form-vertical">
                        
                        <!-- tampilkan data........... -->
                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <!-- Charts Stock .............................................................. -->
                                <div class="stat-card">
                                    <div class="stat-icon" style="background-color: #FF9800;">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h3>Barang dengan stok dibawah 10 </h3>
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM barang WHERE stok < 5";
                                        $result = $koneksi->query($sql);  
                                        $row = $result->fetch_assoc();
                                        ?>
                                        <p class="stat-number"><?php echo $row['total'] ?? 0; ?></p>
                                    </div>
                                </div>

                                <div class="stat-card">
                                    <div class="stat-icon" style="background-color: #2196F3;">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h3>Jumlah macam barang</h3>
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM barang";
                                        $result = $koneksi->query($sql);  // <-- PERBAIKAN
                                        $row = $result->fetch_assoc();
                                        ?>
                                        <p class="stat-number"><?php echo $row['total']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                
                </div>
                <div class="card-header">
                    <a href="index.php?page=data_barang" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>    
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>

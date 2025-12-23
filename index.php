<?php
// Set judul halaman
$page_title = "Dashboard";

// Ambil parameter page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<?php include 'includes/header.php'; ?>

<div class="content-wrapper">
    <?php include 'includes/menu.php'; ?>
    
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <?php
            $page_titles = array(
                'dashboard' => 'Dashboard',
                'data_barang' => 'Data Barang',
                'pegawai' => 'Pegawai',
                'laporan' => 'Laporan',
                'pengaturan' => 'Pengaturan Sistem'
            );
            ?>
            <h2><?php echo $page_titles[$page] ?? 'Dashboard'; ?></h2>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span><?php echo $page_titles[$page] ?? 'Dashboard'; ?></span>
            </div>
        </div>
        
        <!-- Konten Utama -->
        <div class="content">
            <?php
            // Load konten berdasarkan halaman
            switch($page) {
                case 'dashboard':
                    include 'pages/dashboard.php';
                    break;
                case 'data_barang':
                    include 'pages/data_barang.php';
                    break;
                case 'pegawai':
                    include 'pages/pegawai.php';
                    break;
                default:
                    include 'pages/dashboard.php';
            }
            ?>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>

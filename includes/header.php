<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Sistem Pendataan Barang</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="logo">
                    <i class="fas fa-boxes"></i>
                    <h1>Sistem Pendataan Barang</h1>
                </div>
            </div>
            <div class="header-right">
                <div class="date-time">
                    <i class="fas fa-clock"></i>
                    <span id="current-date"><?php echo date('d/m/Y'); ?></span>
                </div>
            </div>
        </header>

        <!-- Notifikasi -->
        <?php if(isset($_SESSION['pesan'])): ?>
        <div class="notification <?php echo $_SESSION['tipe']; ?>">
            <div class="notification-content">
                <i class="fas <?php echo $_SESSION['tipe'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <span><?php echo $_SESSION['pesan']; ?></span>
            </div>
            <button class="notification-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php 
        unset($_SESSION['pesan']);
        unset($_SESSION['tipe']);
        endif; 
        ?>

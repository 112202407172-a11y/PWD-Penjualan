<?php
// Ambil parameter page dengan aman
$page = $_GET['page'] ?? '';
$current_file = basename($_SERVER['PHP_SELF']);

// Data menu sidebar
$menu_items = [
    'dashboard' => [
        'icon'  => 'fas fa-home',
        'title' => 'Dashboard',
        'link'  => 'index.php',
        'active'=> ($page === '')
    ],
    'data_barang' => [
        'icon'  => 'fas fa-box',
        'title' => 'Data Barang',
        'link'  => 'index.php?page=data_barang',
        'active'=> (
            $page === 'data_barang' ||
            $current_file === 'tambah.php' ||
            $current_file === 'edit.php'
        )
    ],
    'pegawai' => [
        'icon'  => 'fas fa-tags',
        'title' => 'Pegawai',
        'link'  => 'index.php?page=pegawai',
        'active'=> ($page === 'pegawai')
    ],
];
?>

<!-- SIDEBAR -->
<aside class="sidebar">
   

    <nav class="main-menu">
        <ul>
            <?php foreach ($menu_items as $item): ?>
                <li>
                    <a href="<?= $item['link']; ?>" class="<?= $item['active'] ? 'active' : ''; ?>">
                        <i class="<?= $item['icon']; ?>"></i>
                        <span><?= $item['title']; ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="login.php" class="login-btn">
            <i class="fas fa-sign-in-alt"></i>
            <span>Masuk</span>
        </a>
    </div>
       <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>

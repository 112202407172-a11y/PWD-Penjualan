<?php
include 'koneksi.php';

// Ambil ID dari URL (Pastikan aman dari karakter aneh)
$id = isset($_GET['id_pegawai']) ? mysqli_real_escape_string($koneksi, $_GET['id_pegawai']) : "";

if (!empty($id)) {
    
    // --- [BARU] LOGIKA HAPUS FOTO DARI FOLDER ---
    // 1. Ambil nama foto dari database sebelum data dihapus
    $query_cek = "SELECT foto FROM pegawai WHERE id_pegawai = '$id'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if ($result_cek && mysqli_num_rows($result_cek) > 0) {
        $data = mysqli_fetch_assoc($result_cek);
        $foto_lama = $data['foto'];

        // 2. Hapus file fisik jika ada di folder uploads
        if (!empty($foto_lama) && file_exists("uploads/" . $foto_lama)) {
            unlink("uploads/" . $foto_lama);
        }
    }
    // --- [AKHIR LOGIKA HAPUS FOTO] ---


    // 3. Hapus data dari database
    $query = "DELETE FROM pegawai WHERE id_pegawai = '$id'";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['pesan'] = "Pegawai berhasil dihapus!"; // Saya ganti 'Barang' jadi 'Pegawai'
        $_SESSION['tipe'] = "success";
    } else {
        $_SESSION['pesan'] = "Gagal menghapus pegawai: " . mysqli_error($koneksi);
        $_SESSION['tipe'] = "error";
    }
}

header("Location: index.php?page=data_pegawai");
exit();
?>
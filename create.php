<?php
session_start();
require_once 'config/database.php';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');
    $kuota = isset($_POST['kuota']) ? (int)$_POST['kuota'] : 0;
    $status = trim($_POST['status'] ?? 'BUKA');

    if (empty($judul) || empty($deskripsi) || empty($lokasi) || empty($tanggal)) {
        $error = 'Semua field wajib diisi.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO activities (judul, deskripsi, lokasi, kuota, status, tanggal) VALUES (:judul, :deskripsi, :lokasi, :kuota, :status, :tanggal)");
            $stmt->execute([
                ':judul' => $judul,
                ':deskripsi' => $deskripsi,
                ':lokasi' => $lokasi,
                ':kuota' => $kuota,
                ':status' => $status,
                ':tanggal' => $tanggal

            ]);
            $_SESSION['message'] = 'Aktivitas berhasil ditambahkan.';
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan saat menyimpan data.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Aktivitas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Tambah Aktivitas Relawan</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Judul Aktivitas:</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>" required>

        <label>Deskripsi:</label>
        <textarea name="deskripsi" rows="5" required><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>

        <label>Lokasi:</label>
        <input type="text" name="lokasi" value="<?= htmlspecialchars($_POST['lokasi'] ?? '') ?>" required>

        <label>Tanggal:</label>
        <input type="date" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>" required>

        <label>Kuota (jumlah relawan maksimal):</label>
        <input type="number" name="kuota" value="<?= htmlspecialchars($_POST['kuota'] ?? '0') ?>" min="0" required>

        <label>Status:</label>
        <select name="status" required>
            <option value="BUKA" <?= (($_POST['status'] ?? 'BUKA') == 'BUKA') ? 'selected' : '' ?>>BUKA</option>
            <option value="TUTUP" <?= (($_POST['status'] ?? 'TUTUP') == 'TUTUP') ? 'selected' : '' ?>>TUTUP</option>
        </select>
        <button type="submit" class="btn">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script src="js/script.js"></script>
</body>
</html>
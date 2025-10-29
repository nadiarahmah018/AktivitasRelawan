<?php
session_start();
require_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM activities WHERE id = :id");
$stmt->execute([':id' => $id]);
$activity = $stmt->fetch();

if (!$activity) {
    die("Aktivitas tidak ditemukan.");
}

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
            $stmt = $pdo->prepare("UPDATE activities SET judul = :judul, deskripsi = :deskripsi, lokasi = :lokasi, kuota = :kuota, status = :status, tanggal = :tanggal WHERE id = :id");
            $stmt->execute([
                ':judul' => $judul,
                ':deskripsi' => $deskripsi,
                ':lokasi' => $lokasi,
                ':tanggal' => $tanggal,
                ':kuota' => $kuota,
                ':status' => $status,
                ':id' => $id
            ]);
            $_SESSION['message'] = 'Aktivitas berhasil diperbarui.';
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan saat memperbarui data.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Aktivitas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Edit Aktivitas Relawan</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Judul Aktivitas:</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($activity['judul']) ?>" required>

        <label>Deskripsi:</label>
        <textarea name="deskripsi" rows="5" required><?= htmlspecialchars($activity['deskripsi']) ?></textarea>

        <label>Lokasi:</label>
        <input type="text" name="lokasi" value="<?= htmlspecialchars($activity['lokasi']) ?>" required>

        <label>Kuota (jumlah relawan maksimal):</label>
        <input type="number" name="kuota" value="<?= htmlspecialchars($activity['kuota']) ?>" min="0" required>

        <label>Status:</label>
        <select name="status" required>
            <option value="BUKA" <?= ($activity['status'] == 'BUKA') ? 'selected' : '' ?>>BUKA</option>
            <option value="TUTUP" <?= ($activity['status'] == 'TUTUP') ? 'selected' : '' ?>>TUTUP</option>
        </select>

        <label>Tanggal:</label>
        <input type="date" name="tanggal" value="<?= htmlspecialchars($activity['tanggal']) ?>" required>

        <button type="submit" class="btn">Perbarui</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script src="js/script.js"></script>
</body>
</html>
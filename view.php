<?php
require_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM activities WHERE id = :id");
$stmt->execute([':id' => $id]);
$activity = $stmt->fetch();

if (!$activity) {
    die("Aktivitas tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Aktivitas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Detail Aktivitas</h1>

    <p><strong>ID:</strong> <?= htmlspecialchars($activity['id']) ?></p>
    <p><strong>Judul:</strong> <?= htmlspecialchars($activity['judul']) ?></p>
    <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($activity['deskripsi'])) ?></p>
    <p><strong>Lokasi:</strong> <?= htmlspecialchars($activity['lokasi']) ?></p>
    <p><strong>Kuota:</strong> <?= htmlspecialchars($activity['kuota']) ?> relawan</p>
    <p><strong>Status:</strong> 
        <span style="color: <?= $activity['status'] === 'BUKA' ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($activity['status']) ?>
        </span>
    </p>
    <p><strong>Tanggal:</strong> <?= htmlspecialchars($activity['tanggal']) ?></p>
    <p><strong>Dibuat pada:</strong> <?= htmlspecialchars($activity['created_at']) ?></p>

    <a href="index.php" class="btn btn-secondary">Kembali ke Daftar</a>
</div>
<script src="js/script.js"></script>
</body>
</html>
<?php
session_start();
$message = $_SESSION['message'] ?? '';
if ($message) {
    echo '<div class="alert alert-success auto-hide">' . htmlspecialchars($message) . '</div>';
    unset($_SESSION['message']);
}
require_once 'config/database.php';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchClause = '';
$params = [];

if ($search !== '') {
    $searchClause = "WHERE judul LIKE :search OR lokasi LIKE :search OR deskripsi LIKE :search";
    $params[':search'] = "%$search%";
}

$countSql = "SELECT COUNT(*) FROM activities $searchClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

$sql = "SELECT * FROM activities $searchClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
if ($search !== '') {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->execute();
$activities = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Aktivitas Relawan</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Daftar Aktivitas Relawan</h1>

    <form method="GET" style="margin-bottom:20px;">
        <input type="text" name="search" placeholder="Cari judul, lokasi, atau deskripsi..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Cari</button>
        <a href="index.php" class="btn btn-secondary">Reset</a>
    </form>

    <?php if (empty($activities)): ?>
        <p>Tidak ada data aktivitas.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Lokasi</th>
                    <th>Kuota</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $act): ?>
                    <tr>
                        <td><?= htmlspecialchars($act['id']) ?></td>
                        <td><?= htmlspecialchars($act['judul']) ?></td>
                        <td><?= htmlspecialchars($act['lokasi']) ?></td>
                        <td><?= htmlspecialchars($act['kuota']) ?></td>
                        <td>
                            <span style="color: <?= $act['status'] === 'BUKA' ? 'green' : 'red' ?>;">
                                <?= htmlspecialchars($act['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($act['tanggal']) ?></td>
                        <td><?= htmlspecialchars($act['created_at']) ?></td>
                        <td>
                            <a href="view.php?id=<?= $act['id'] ?>">Lihat</a> |
                            <a href="edit.php?id=<?= $act['id'] ?>">Edit</a> |
                            <a href="delete.php?id=<?= $act['id'] ?>" onclick="return confirm('Yakin hapus aktivitas ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">&laquo; Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Berikutnya &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div style="margin-top: 20px;">
    <a href="create.php" class="btn">+ Tambah Aktivitas</a>
    </div>
</div>
<script src="js/script.js"></script>
</body>
</html>
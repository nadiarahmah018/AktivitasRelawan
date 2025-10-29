<?php
require_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM activities WHERE id = :id");
        $stmt->execute([':id' => $id]);
    } catch (Exception $e) {
    }
}

header("Location: index.php");
exit;
<?php
session_start();
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

$id     = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id) {
    echo json_encode(['success'=>false,'message'=>'ID tidak valid']);
    exit();
}

if ($action === 'approve') {
    $stmt = $conn->prepare("UPDATE gabung SET status='approved' WHERE id=?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success'=>true,'message'=>'Berhasil di-approve']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Data tidak ditemukan atau sudah approved']);
    }
} else {
    echo json_encode(['success'=>false,'message'=>'Aksi tidak dikenal']);
}

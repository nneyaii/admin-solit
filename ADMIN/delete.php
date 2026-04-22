<?php
session_start();
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['success'=>false,'message'=>'ID tidak valid']);
    exit();
}

$stmt = $conn->prepare("DELETE FROM gabung WHERE id=?");
$stmt->bind_param('i', $id);
if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success'=>true,'message'=>'Data berhasil dihapus']);
} else {
    echo json_encode(['success'=>false,'message'=>'Data tidak ditemukan']);
}

<?php
header('Content-Type: application/json');
require 'dbconnect.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$conn->query("DELETE FROM items WHERE id=$id");
echo json_encode(['success' => true]);

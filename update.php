<?php

header('Content-Type: application/json');
require 'dbconnect.php';

$id = (int)($_POST['id'] ?? 0);
$name = $conn->real_escape_string($_POST['name'] ?? '');
$quantity = (int)($_POST['quantity'] ?? 0);

if ($id <= 0 || $name === '') {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$conn->query("UPDATE items SET name='$name', quantity=$quantity WHERE id=$id");
echo json_encode(['success' => true]);

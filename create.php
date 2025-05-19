<?php
header('Content-Type: application/json');
require 'dbconnect.php';

$name = $conn->real_escape_string($_POST['name'] ?? '');
$quantity = (int)($_POST['quantity'] ?? 0);

if ($name === '') {
    echo json_encode(['error' => 'Name required']);
    exit;
}

$conn->query("INSERT INTO items (name, quantity) VALUES ('$name', $quantity)");
echo json_encode(['success' => true]);

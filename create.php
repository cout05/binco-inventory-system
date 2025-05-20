<?php
header('Content-Type: application/json');
require 'dbconnect.php';

// Sanitize inputs
$name     = $conn->real_escape_string(trim($_POST['name'] ?? ''));
$size     = $conn->real_escape_string(trim($_POST['size'] ?? ''));
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

// Validate required fields
if ($name === '') {
    echo json_encode(['error' => 'Name is required']);
    exit;
}
if ($size === '') {
    echo json_encode(['error' => 'Size is required']);
    exit;
}

// Build and execute insert
$sql = "INSERT INTO items (name, size, quantity) VALUES ('{$name}', '{$size}', {$quantity})";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'id' => $conn->insert_id]);
} else {
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
}

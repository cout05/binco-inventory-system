<?php

header('Content-Type: application/json');
require 'dbconnect.php';

// Retrieve and sanitize inputs
$id       = (int)($_POST['id'] ?? 0);
$name     = $conn->real_escape_string($_POST['name'] ?? '');
$size     = $conn->real_escape_string($_POST['size'] ?? '');
$quantity = (int)($_POST['quantity'] ?? 0);

// Basic validation
if ($id <= 0 || $name === '' || $size === '') {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Prepare and execute update
$sql = "UPDATE items
        SET name = '$name',
            size ='$size',
            quantity = '$quantity'
        WHERE id = '$id' ";

if ($conn->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => $conn->error]);
}

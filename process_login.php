<?php
require 'dbconnect.php';
session_start();

// JSON Format
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
$pass  = trim($_POST['pass']  ?? '');

if (!$email || !$pass) {
    echo json_encode(['success' => false, 'message' => 'Please fill in both fields.']);
    exit;
}

$query  = "SELECT id, password FROM user_account WHERE email = '$email'";
$result = $conn->query($query);

if ($result->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'No account found with that email.']);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($pass, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit;
}

$_SESSION['user_id']  = $user['id'];
$_SESSION['is_login'] = true;
echo json_encode(['success' => true]);
exit;

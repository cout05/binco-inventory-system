<?php
require "dbconnect.php";


// JSON Format
header('Content-Type: application/json');

$email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
$pass  = trim($_POST['pass']  ?? '');

if (!$email || !$pass) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$res = $conn->query("SELECT id FROM user_account WHERE email = '$email' LIMIT 1");
if ($res && $res->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'That email is already registered']);
    exit;
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
if ($conn->query("INSERT INTO user_account (email,password) VALUES ('$email','" . $conn->real_escape_string($hash) . "')")) {
    echo json_encode(['success' => true, 'message' => 'Signup successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not create account']);
}
exit;

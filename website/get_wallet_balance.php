<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'کاربر لاگین نیست']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$db = new mysqli("localhost", "root", "", "daspokht");
if ($db->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'خطا در اتصال به دیتابیس']);
    exit;
}

$stmt = $db->prepare("SELECT wallet_Balance FROM users WHERE user_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($walletBalance);
$stmt->fetch();
$stmt->close();
$db->close();

echo json_encode([
    'status' => 'ok',
    'wallet' => floatval($walletBalance)
]);

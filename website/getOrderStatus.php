<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "Admin/db.php";
require_once "SessionCheck.php";

$userId = $_SESSION['user_id'];

header("Content-Type: application/json");

$query = "
    SELECT order_ID, status
    FROM orders
    WHERE user_ID = $userId
";

$result = mysqli_query($conn, $query);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[$row['order_ID']] = $row['status'];
}

echo json_encode($orders);

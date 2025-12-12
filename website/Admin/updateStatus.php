<?php
require_once "db.php";

$orderId = intval($_POST['orderId']);
$status  = $_POST['status'];

// 1. ابتدا اطلاعات سفارش فعلی را بگیر
$orderSql = "SELECT user_ID, total_Price, status FROM orders WHERE order_ID = $orderId";
$orderResult = mysqli_query($conn, $orderSql);
$order = mysqli_fetch_assoc($orderResult);

if (!$order) {
    echo "error";
    exit();
}

$userId = $order['user_ID'];
$totalPrice = $order['total_Price'];
$oldStatus = $order['status'];

// 2. اگر سفارش قبلاً لغو شده باشد، اجازه تغییر نده
if ($oldStatus == 'canceled') {
    echo "canceled_no_change";
    exit();
}

// 3. به‌روزرسانی وضعیت سفارش
$sql = "UPDATE orders 
        SET status = '$status' 
        WHERE order_ID = $orderId";

$success = mysqli_query($conn, $sql);

// 4. اگر وضعیت جدید "canceled" باشد
if ($success && $status == 'canceled') {
    // اضافه کردن مبلغ به کیف پول کاربر
    $walletSql = "UPDATE users 
                  SET wallet_Balance = wallet_Balance + $totalPrice 
                  WHERE user_ID = $userId";
    
    mysqli_query($conn, $walletSql);
}

if ($success) {
    echo "success";
} else {
    echo "error";
}

mysqli_close($conn);
?>
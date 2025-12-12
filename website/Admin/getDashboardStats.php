<?php
// getDashboardStats.php - نسخه ساده‌تر
require_once "db.php";

header('Content-Type: application/json');

$stats = [
    'new_orders' => 0,
    'total_sales' => 0,
    'total_users' => 0
];

try {
    // ۱. سفارشات امروز
    $sql1 = "SELECT COUNT(*) as count FROM orders WHERE DATE(created_At) = CURDATE()";
    $result1 = mysqli_query($conn, $sql1);
    if ($result1) {
        $row1 = mysqli_fetch_assoc($result1);
        $stats['new_orders'] = (int)$row1['count'];
    }
    
    // ۲. مجموع فروش
    $sql2 = "SELECT SUM(total_Price) as total FROM orders";
    $result2 = mysqli_query($conn, $sql2);
    if ($result2) {
        $row2 = mysqli_fetch_assoc($result2);
        $total = $row2['total'] ? $row2['total'] : 0;
        $stats['total_sales'] = number_format($total);
    }
    
    // ۳. تعداد کاربران
    $sql3 = "SELECT COUNT(*) as count FROM users";
    $result3 = mysqli_query($conn, $sql3);
    if ($result3) {
        $row3 = mysqli_fetch_assoc($result3);
        $stats['total_users'] = (int)$row3['count'];
    }
    
} catch (Exception $e) {
    // در صورت خطا، داده‌های پیش‌فرض
    $stats = [
        'new_orders' => 0,
        'total_sales' => '0',
        'total_users' => 0
    ];
}

mysqli_close($conn);
echo json_encode($stats);
?>
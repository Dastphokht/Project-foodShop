<?php
// getChartData.php - شمارش سفارشات واقعی
require_once "db.php";

header('Content-Type: application/json');

// تست اتصال
if (!$conn) {
    echo json_encode(['error' => 'خطا در اتصال به دیتابیس']);
    exit;
}

// کوئری اصلاح شده: تعداد سفارشات هر دسته غذا
// این کوئری تعداد آیتم‌های سفارش داده شده از هر نوع غذا را می‌شمارد
$sql = "SELECT 
            f.Type as food_type,
            SUM(oi.quantity) as total_ordered
        FROM order_items oi
        JOIN foods f ON oi.food_ID = f.food_ID
        JOIN orders o ON oi.order_ID = o.order_ID
        WHERE o.status != 'canceled'  -- سفارش‌های لغو شده حساب نشوند
        GROUP BY f.Type";

$result = mysqli_query($conn, $sql);

if (!$result) {
    // نمایش خطا برای دیباگ
    $error = mysqli_error($conn);
    echo json_encode([
        'error' => 'خطا در اجرای کوئری',
        'sql_error' => $error
    ]);
    exit;
}

// مقدار اولیه
$stats = [
    'irani' => 0,
    'fastfood' => 0,
    'kebab' => 0,
    'sokhari' => 0,
    'drink' => 0
];

// پر کردن آمار
while ($row = mysqli_fetch_assoc($result)) {
    $type = strtolower($row['food_type']); // مطمئن شو حروف کوچک هستند
    if (isset($stats[$type])) {
        $stats[$type] = (int)$row['total_ordered'];
    }
}

mysqli_close($conn);

// خروجی JSON
echo json_encode($stats);
?>
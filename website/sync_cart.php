<?php
// sync_cart.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// ۱. چک لاگین
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'کاربر لاگین نیست']);
    exit;
}

$userId = (int)$_SESSION['user_id'];

// ۲. خواندن JSON ارسال‌شده از جاوااسکریپت
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data || !isset($data['cart']) || !is_array($data['cart'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'داده‌ی سبد خرید نامعتبر است']);
    exit;
}

$cart = $data['cart'];

$connect = mysqli_connect('localhost', 'root', '', 'daspokht');
if (!$connect) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'خطا در اتصال به دیتابیس']);
    exit;
}

// ۳. ساده‌ترین استراتژی: پاک کردن سبد قبلی و درج سبد جدید
mysqli_begin_transaction($connect);

try {
    // حذف تمام ردیف‌های سبد این کاربر
    $stmt = mysqli_prepare($connect, "DELETE FROM carts WHERE user_ID = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // آماده‌سازی دستور insert
    $stmt = mysqli_prepare($connect, "INSERT INTO carts (user_ID, food_ID, Quantity, added_at) VALUES (?, ?, ?, NOW())");

    foreach ($cart as $item) {
        // انتظار داریم item ساختارش مثل cartItem در js باشه:
        // { id: food_ID, name: ..., price: ..., qty: ... }
        if (!isset($item['id']) || !isset($item['qty'])) {
            continue;
        }

        $foodId = (int)$item['id'];
        $qty    = (int)$item['qty'];

        if ($qty <= 0) {
            continue;
        }

        mysqli_stmt_bind_param($stmt, "iii", $userId, $foodId, $qty);
        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
    mysqli_commit($connect);

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    mysqli_rollback($connect);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'خطا در ذخیره سبد خرید']);
}

mysqli_close($connect);

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// ⚠️ ۱. بررسی لاگین کاربر
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html?redirect=dargah.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// ⚠️ ۲. خواندن متغیرهای POST ارسالی از dargah.js
$payType = $_POST['pay_type'] ?? 'unknown';
$amount = $_POST['final_amount'] ?? 0;

// بررسی صحت مبلغ
if (!is_numeric($amount) || $amount <= 0) {
    die("خطا: مبلغ پرداخت معتبر نیست.");
}

// ⚠️ ۳. اتصال به دیتابیس
$db = new mysqli("localhost", "root", "", "daspokht"); 
if ($db->connect_error) {
    die("خطا در اتصال به دیتابیس: " . $db->connect_error);
}

// ------------------------------------------------
// 🔍 نقطه اصلی: تشخیص نوع پرداخت و اجرای عملیات مربوطه
// ------------------------------------------------

if ($payType === 'wallet') {
    // ------------------------------------------------
    // حالت ۱: شارژ کیف پول (Wallet Charge)
    // ------------------------------------------------
    
    // به‌روزرسانی (UPDATE) فیلد 'wallet' در جدول 'users'
    // مقدار جدید = موجودی قبلی + مبلغ جدید
    $stmt = $db->prepare("UPDATE users SET wallet_Balance = wallet_Balance + ? WHERE user_ID = ?");
    
    // 'd' برای float/double (مبلغ) و 'i' برای integer (شناسه کاربر)
    $stmt->bind_param("di", $amount, $user_id);
    
    if ($stmt->execute()) {
        // ⬅️ انتقال به صفحه موفقیت شارژ (باید چنین صفحه‌ای وجود داشته باشد)
        header("Location: wallet_success.php?amount=" . $amount); 
        exit();
    } else {
        die("خطا در به‌روزرسانی کیف پول: " . $stmt->error);
    }
    
} elseif ($payType === 'order') {
    // ------------------------------------------------
    // حالت ۲: ثبت سفارش (Order Registration) - منطق قبلی
    // ------------------------------------------------
    
    // ⚠️ بررسی وجود اطلاعات سفارش (فقط برای حالت سفارش این چک لازم است)
    if (!isset($_SESSION["cart"]) || !isset($_SESSION["order_info"])) {
        header("Location: shoppingCart.php");
        exit();
    }
    
    // ۱. محاسبه مجموع قیمت سفارش (مبلغی که باید پرداخت می‌شد)
    $total_price_from_session = 0;
    foreach ($_SESSION["cart"] as $item) {
        $total_price_from_session += $item["price"] * $item["qty"]; 
    }
    $expected_total = $total_price_from_session + $_SESSION["order_info"]["shipping_cost"];

    // 🛡️ مرحله امنیتی: چک کردن تطابق مبلغ پرداخت شده با مبلغ مورد انتظار
    // از آنجایی که مبلغ قابل پرداخت از سمت کاربر آمده است، باید با مقدار محاسبه شده از سشن چک شود.
    if (abs($amount - $expected_total) > 0.01) { 
        die("خطا: مبلغ پرداخت شده با مبلغ سفارش مطابقت ندارد. عملیات لغو شد.");
    }
    
    // ۲. ثبت سفارش در جدول orders
    $stmt = $db->prepare("INSERT INTO orders (user_ID, total_Price, status, created_At) VALUES (?, ?, 'confirmed', NOW())");
    $stmt->bind_param("id", $user_id, $expected_total); 
    $stmt->execute();
    $order_id = $stmt->insert_id; 

    // ۳. ثبت آیتم‌های سفارش در جدول order_items
    foreach ($_SESSION["cart"] as $item) {
        $stmt = $db->prepare("INSERT INTO order_items (order_ID, food_ID, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", 
            $order_id, 
            $item["food_id"], 
            $item["qty"],     
            $item["price"]
        );
        $stmt->execute();
    }

        // ۴. اتمام موفقیت‌آمیز سفارش
        unset($_SESSION["cart"]);
        unset($_SESSION["order_info"]);
    
        // پاک کردن سبد خرید دیتابیس بعد از پرداخت موفق
        $deleteCart = $db->prepare("DELETE FROM carts WHERE user_ID = ?");
        $deleteCart->bind_param("i", $user_id);
        $deleteCart->execute();
        $deleteCart->close();
    
        // ⬅️ انتقال به صفحه موفقیت سفارش
        header("Location: order_success.php?order_id=" . $order_id);
        exit();
    
} else {
    // ------------------------------------------------
    // حالت ۳: نوع پرداخت نامشخص
    // ------------------------------------------------
    die("خطای سیستم: نوع پرداخت ارسالی نامشخص است.");
}
?>
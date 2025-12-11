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
    
} 
 elseif ($payType === 'order') {

    // ------------------------------------------------
    // حالت ۲: ثبت سفارش (Order Registration)
    // ------------------------------------------------

    // ۱) چک اینکه اطلاعات سفارش در سشن هست
    if (!isset($_SESSION["cart"]) || !isset($_SESSION["order_info"])) {
        header("Location: shoppingCart.php");
        exit();
    }

    // ۲) محاسبه مجموع قیمت غذاها (بدون تخفیف)
    $total_price_from_session = 0;
    foreach ($_SESSION["cart"] as $item) {
        $total_price_from_session += $item["price"] * $item["qty"];
    }

    // هزینه ارسال از سشن
    $shipping_cost = isset($_SESSION["order_info"]["shipping_cost"])
        ? floatval($_SESSION["order_info"]["shipping_cost"])
        : 0;

    // مبلغ کامل فاکتور (غذا بدون تخفیف + ارسال)
    $full_total = $total_price_from_session + $shipping_cost;

    // مبلغی که از درگاه اومده (غذا بعد تخفیف + ارسال)
    $amount = floatval($amount);

    // 🛡️ چک امنیتی:
    // اگر مبلغ <=0 یا بیشتر از مبلغ کامل فاکتور بود → خطا
    if ($amount <= 0 || $amount > $full_total) {
        die("خطا: مبلغ پرداخت شده با مبلغ سفارش مطابقت ندارد. عملیات لغو شد.");
    }

    // ✅ مبلغ نهایی سفارش (همون مبلغ پرداخت‌شده تخفیف‌خورده + ارسال)
    $final_total = $amount;

    // (اختیاری) اگر خواستی تخفیف رو هم داشته باشی:
    // $discount_amount = $full_total - $final_total;

    // ۳) ثبت سفارش در جدول orders با مبلغ نهایی
    $stmt = $db->prepare("
        INSERT INTO orders (user_ID, total_Price, status, created_At)
        VALUES (?, ?, 'confirmed', NOW())
    ");
    $stmt->bind_param("id", $user_id, $final_total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // ۴) ثبت آیتم‌های سفارش در جدول order_items
    foreach ($_SESSION["cart"] as $item) {
        $stmt = $db->prepare("
            INSERT INTO order_items (order_ID, food_ID, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iiid",
            $order_id,
            $item["food_id"],
            $item["qty"],
            $item["price"]
        );
        $stmt->execute();
    }

    // ۵) خالی کردن سشن و سبد دیتابیس
    unset($_SESSION["cart"]);
    unset($_SESSION["order_info"]);

    $deleteCart = $db->prepare("DELETE FROM carts WHERE user_ID = ?");
    $deleteCart->bind_param("i", $user_id);
    $deleteCart->execute();
    $deleteCart->close();

    header("Location: order_success.php?order_id=" . $order_id);
    exit();
}

 else {
    // ------------------------------------------------
    // حالت ۳: نوع پرداخت نامشخص
    // ------------------------------------------------
    die("خطای سیستم: نوع پرداخت ارسالی نامشخص است.");
}
?>
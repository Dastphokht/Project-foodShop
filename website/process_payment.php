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

// ⚠️ ۲. خواندن متغیرهای POST ارسالی از dargah / information
$payType = $_POST['pay_type'] ?? 'unknown';
$amount  = $_POST['final_amount'] ?? 0;

// بررسی صحت مبلغ
if (!is_numeric($amount) || $amount <= 0) {
    die("خطا: مبلغ پرداخت معتبر نیست.");
}

// ⚠️ ۳. اتصال به دیتابیس
$db = new mysqli("localhost", "root", "", "daspokht");
if ($db->connect_error) {
    die("خطا در اتصال به دیتابیس: " . $db->connect_error);
}

// تابع کمکی: گرفتن هزینه ارسال از دیتابیس در صورت نیاز
function getShippingCostFromDB($db) {
    $default = 25000;
    $res = $db->query("SELECT cost FROM shipping_settings WHERE shop_ID = 1 LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $cost = (int)$row['cost'];
        $res->free();
        return $cost;
    }
    if ($res) $res->free();
    return $default;
}

// ------------------------------------------------
// 🔍 تشخیص نوع پرداخت
// ------------------------------------------------

/**
 * حالت ۱: شارژ کیف پول (wallet.php → dargah.php → process_payment)
 */
if ($payType === 'wallet') {

    $stmt = $db->prepare("UPDATE users SET wallet_Balance = wallet_Balance + ? WHERE user_ID = ?");
    $stmt->bind_param("di", $amount, $user_id);

    if ($stmt->execute()) {
        header("Location: wallet_success.php?amount=" . $amount);
        exit();
    } else {
        die("خطا در به‌روزرسانی کیف پول: " . $stmt->error);
    }

}
/**
 * حالت ۲: پرداخت سفارش از طریق کیف پول (information.php → process_payment)
 */
elseif ($payType === 'wallet_order') {

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
  // هزینه ارسال از سشن (در صورت نبود → از دیتابیس)
if (isset($_SESSION["order_info"]["shipping_cost"])) {
    $shipping_cost = floatval($_SESSION["order_info"]["shipping_cost"]);
} else {
    $shipping_cost = (float) getShippingCostFromDB($db);
}



    // مبلغ کامل فاکتور (غذا بدون تخفیف + ارسال)
    $full_total = $total_price_from_session + $shipping_cost;

    // مبلغی که از JS برای پرداخت با کیف پول فرستاده شده (غذا بعد تخفیف + ارسال)
    $amount = floatval($amount);

    // چک اولیه: مبلغ باید مثبت و حداکثر برابر مبلغ کامل فاکتور باشد
    if ($amount <= 0 || $amount > $full_total) {
        die("خطا: مبلغ پرداخت شده با مبلغ سفارش مطابقت ندارد. عملیات لغو شد.");
    }

    // ۳) گرفتن موجودی کیف پول از دیتابیس
    $stmt = $db->prepare("SELECT wallet_Balance FROM users WHERE user_ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($walletBalance);
    $stmt->fetch();
    $stmt->close();

    // اگر موجودی کافی نباشد → برای اطمینان سروری هم خطا بده
    if ($walletBalance < $amount) {
        die("خطا: موجودی کیف پول کافی نیست.");
    }

    // ۴) کم کردن مبلغ از کیف پول
    $stmt = $db->prepare("UPDATE users SET wallet_Balance = wallet_Balance - ? WHERE user_ID = ?");
    $stmt->bind_param("di", $amount, $user_id);
    $stmt->execute();
    $stmt->close();

    // ۵) ثبت سفارش با مبلغ نهایی پرداخت‌شده
    $final_total = $amount;

    $stmt = $db->prepare("
        INSERT INTO orders (user_ID, total_Price, status, created_At)
        VALUES (?, ?, 'confirmed', NOW())
    ");
    $stmt->bind_param("id", $user_id, $final_total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // آیتم‌های سفارش
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

    // 🔻 کاهش موجودی کالاها
    foreach ($_SESSION["cart"] as $item) {
        $stmt = $db->prepare("UPDATE foods SET Quantity = Quantity - ? WHERE food_ID = ?");
        $stmt->bind_param("ii", $item["qty"], $item["food_id"]);
        $stmt->execute();
    }

    // پاک‌کردن سشن و سبد دیتابیس
    unset($_SESSION["cart"]);
    unset($_SESSION["order_info"]);

    $deleteCart = $db->prepare("DELETE FROM carts WHERE user_ID = ?");
    $deleteCart->bind_param("i", $user_id);
    $deleteCart->execute();
    $deleteCart->close();

    // صفحه موفقیت
    header("Location: order_success.php?order_id=" . $order_id);
    exit();
}
/**
 * حالت ۳: پرداخت سفارش از طریق درگاه بانکی (دکمه «پرداخت آنلاین»)
 */
elseif ($payType === 'order') {

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
// هزینه ارسال از سشن (در صورت نبود → از دیتابیس)
if (isset($_SESSION["order_info"]["shipping_cost"])) {
    $shipping_cost = floatval($_SESSION["order_info"]["shipping_cost"]);
} else {
    $shipping_cost = (float) getShippingCostFromDB($db);
}


    // مبلغ کامل فاکتور (غذا بدون تخفیف + ارسال)
    $full_total = $total_price_from_session + $shipping_cost;

    // مبلغی که از درگاه اومده (غذا بعد تخفیف + ارسال)
    $amount = floatval($amount);

    // اگر مبلغ <=0 یا بیشتر از مبلغ کامل فاکتور بود → خطا
    if ($amount <= 0 || $amount > $full_total) {
        die("خطا: مبلغ پرداخت شده با مبلغ سفارش مطابقت ندارد. عملیات لغو شد.");
    }

    // مبلغ نهایی سفارش = همون مبلغ پرداخت‌شده
    $final_total = $amount;

    // ۳) ثبت سفارش
    $stmt = $db->prepare("
        INSERT INTO orders (user_ID, total_Price, status, created_At)
        VALUES (?, ?, 'confirmed', NOW())
    ");
    $stmt->bind_param("id", $user_id, $final_total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // ۴) آیتم‌ها
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

    // 🔻 کاهش موجودی کالاها
    foreach ($_SESSION["cart"] as $item) {
        $stmt = $db->prepare("UPDATE foods SET Quantity = Quantity - ? WHERE food_ID = ?");
        $stmt->bind_param("ii", $item["qty"], $item["food_id"]);
        $stmt->execute();
    }


    // ۵) خالی کردن سبد
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
    // حالت نامشخص
    die("خطای سیستم: نوع پرداخت ارسالی نامشخص است.");
}
?>

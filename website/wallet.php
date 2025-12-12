<?php
// 1. بلوک PHP در ابتدای فایل (قبل از هر تگ HTML)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// بررسی لاگین کاربر
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?redirect=wallet.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// اتصال به دیتابیس
$db = new mysqli("localhost", "root", "", "daspokht"); 
if ($db->connect_error) {
    die("خطا در اتصال به دیتابیس: " . $db->connect_error);
}

// خواندن موجودی کیف پول کاربر (با نام فیلد صحیح wallet_Balance)
$stmt = $db->prepare("SELECT wallet_Balance FROM users WHERE user_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$current_balance = $user['wallet_Balance'] ?? 0; 

// (اختیاری) دیباگ موقت برای اطمینان
// echo "<div>[DEBUG] موجودی خوانده شده از دیتابیس: " . $current_balance . "</div>";

$stmt->close();
$db->close();

function formatFarsi($num) {
    if (!isset($num) || $num === 0) return '۰';
    
    // ۱. اضافه کردن کامای هزارگان
    $english = number_format($num); 
    
    // ۲. تعریف نقشه‌برداری اعداد فارسی
    $farsi_numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $english_numbers = range(0, 9);
    
    // ۳. جایگزینی اعداد انگلیسی با فارسی
    $output = str_replace($english_numbers, $farsi_numbers, $english);
    return $output;
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>کیف پول من</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="css/wallet.css">
</head>

<body>

<?php include('Menu.php') ?>

<div class="container">

    <header class="header">
        <h2>کیف پول من</h2>
    </header>

    <div class="balance-card">
        <div class="balance-left">
            <span class="label">موجودی فعلی: </span>
            <span class="balance" id="balance">
                <?= formatFarsi($current_balance) ?> تومان
            </span>
        </div>
        <div class="balance-icon">
            <img src="asset/icon/wallet.png">
        </div>
    </div>

    <div class="add-section">
        <span class="error-msg" id="errorMsg" style="display: none;"></span> 
     
        <input type="text" id="amountInput" placeholder="مبلغ را وارد کنید">

        <div class="quick-amounts">
            <button type="button" data-amount="100000">۱۰۰,۰۰۰</button>
            <button type="button" data-amount="200000">۲۰۰,۰۰۰</button>
            <button type="button" data-amount="500000">۵۰۰,۰۰۰</button>
            <button type="button" data-amount="1000000">۱۰۰۰,۰۰۰</button>
        </div>

        <button id="confirmBtn">افزایش موجودی و پرداخت</button>
    </div>

</div>


<script src="js/wallet.js"></script>

</body>
</html>
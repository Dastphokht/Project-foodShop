<?php
// چک لاگین بودن کاربر
require_once "SessionCheck.php";
require_once "db.php";

$userId = $_SESSION['user_ID'];

// گرفتن لیست سفارش‌ها
$orderQuery = "
    SELECT * FROM orders 
    WHERE user_ID = $userId 
    ORDER BY order_ID DESC
";

$orderResult = mysqli_query($conn, $orderQuery);
?>



<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سفارش‌های من</title>

    <link rel="stylesheet" href="css/orderStatus.css">
</head>

<body>

<h2>سفارش‌های من</h2>

<div class="order-card">
    <div class="order-header">
        <div>سفارش #25148</div>
        <div class="status preparing">در حال آماده‌سازی</div>
    </div>

    <div>تاریخ: 1402/11/12</div>
    <div>مبلغ کل: 640,000 تومان</div>

    <div class="items">

        <div class="item">
            <img src="asset/img/kabab2.jpg" alt="غذا">
            <div class="item-info">
                <div class="item-title">کباب کوبیده مخصوص</div>
                <div class="item-details">تعداد: 1 | قیمت: 450,000 تومان</div>
            </div>
        </div>

        <div class="item">
            <img src="asset/img/noshidani1.jpg" alt="غذا">
            <div class="item-info">
                <div class="item-title">دوغ</div>
                <div class="item-details">تعداد: 2 | قیمت: 35,000 تومان</div>
            </div>
        </div>

    </div>
</div>

<div class="order-card">
    <div class="order-header">
        <div>سفارش #25149</div>
        <div class="status sending">در حال ارسال</div>
    </div>

    <div>تاریخ: 1402/10/30</div>
    <div>مبلغ کل: 280,000 تومان</div>

    <div class="items">
        <div class="item">
            <img src="asset/img/fastfood1.jpg" alt="غذا">
            <div class="item-info">
                <div class="item-title">پیتزا مخصوص</div>
                <div class="item-details">تعداد: 1 | قیمت: 780,000 تومان</div>
            </div>
        </div>
    </div>
</div>

<div class="order-card">
    <div class="order-header">
        <div>سفارش #25150</div>
        <div class="status registered">ثبت سفارش</div>
    </div>

    <div>تاریخ: 1402/09/18</div>
    <div>مبلغ کل: 1,200,000 تومان</div>

    <div class="items">
        <div class="item">
            <img src="asset/img/kabab1.jpg" alt="غذا">
            <div class="item-info">
                <div class="item-title">جوجه کباب</div>
                <div class="item-details">تعداد: 1 | قیمت: 1,200,000 تومان</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

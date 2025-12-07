<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// چک لاگین بودن کاربر
require_once "SessionCheck.php";
require_once "Admin/db.php";

$userId = $_SESSION['user_id'];

// گرفتن سفارش‌های کاربر
$orderQuery = "
    SELECT orders.*, users.user_Name 
    FROM orders
    JOIN users ON users.user_ID = orders.user_ID
    WHERE orders.user_ID = $userId
    ORDER BY orders.order_ID DESC
";
$orderResult = mysqli_query($conn, $orderQuery);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سفارش‌های من</title>
    <link rel="stylesheet" href="css/orderStatus.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php require "Menu.php"; ?>

<h2>سفارش‌های من</h2>

<?php while ($order = mysqli_fetch_assoc($orderResult)): ?>

    <div class="order-card" id="order-<?= $order['order_ID'] ?>">

        <!-- هدر سفارش -->
        <div class="order-header">
            <div>سفارش #<?= $order['order_ID'] ?></div>

            <?php
                $statusClass = "registered";
                $statusTitle = "درحال ثبت سفارش";

                if ($order['status'] === "preparing") {
                    $statusClass = "registered";
                    $statusTitle = "در حال آماده‌سازی";
                }
                if ($order['status'] === "delivering") {
                    $statusClass = "preparing";
                    $statusTitle = " در حال ارسال";
                }
                if ($order['status'] === "canceled") {
                    $statusClass = "canceled";
                    $statusTitle = "لغو شده";
                }
            ?>

            <div class="status <?= $statusClass ?>"><?= $statusTitle ?></div>
        </div>

        <div>تاریخ: <?= $order['created_At'] ?></div>
        <div>مبلغ کل: <?= number_format($order['total_Price']) ?> تومان</div>

        <!-- آیتم های سفارش -->
        <div class="items">
            <?php
                $orderId = $order['order_ID'];
                $itemQuery = "
                    SELECT order_items.*, foods.food_Name, foods.img_url
                    FROM order_items
                    JOIN foods ON foods.food_ID = order_items.food_ID
                    WHERE order_items.order_ID = $orderId
                ";
                $itemResult = mysqli_query($conn, $itemQuery);

                while ($item = mysqli_fetch_assoc($itemResult)):
            ?>

                <div class="item">
                    <img src="asset/img/FoodsImage/<?= $item['img_url'] ?>" alt="غذا">

                    <div class="item-info">
                        <div class="item-title"><?= $item['food_Name'] ?></div>
                        <div class="item-details">
                            تعداد: <?= $item['quantity'] ?> |
                            قیمت: <?= number_format($item['price']) ?> تومان
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>

    </div>

<?php endwhile; ?>

<script>
function checkStatusUpdates() {

    fetch("getOrderStatus.php")
        .then(res => res.json())
        .then(data => {

            Object.keys(data).forEach(orderId => {

                let newStatus = data[orderId];
                let statusElement = document.querySelector(`#order-${orderId} .status`);

                if (!statusElement) return;

                // گرفتن وضعیت فعلی
                let currentText = statusElement.textContent.trim();

                // مقایسه وضعیت‌های جدید با متن قبلی
                let newText = "";

                switch (newStatus) {
                    case "registered": newText = "در انتظار ثبت سفارش"; break;
                    case "preparing": newText = "در حال آماده‌سازی"; break;
                    case "delivering": newText = "در حال ارسال"; break;
                    case "canceled": newText = "لغو شده"; break;
                }

                // اگر تغییر کرده؟
                if (currentText !== newText) {

                    // آپدیت متن
                    statusElement.textContent = newText;

                    // تغییر کلاس‌های رنگی
                    statusElement.classList.remove("registered", "preparing","delivering", "canceled");

                    if (newStatus === "registered") statusElement.classList.add("registered");
                    if (newStatus === "preparing") statusElement.classList.add("preparing");
                    if (newStatus === "delivering") statusElement.classList.add("delivering");
                    if (newStatus === "canceled") statusElement.classList.add("canceled");

                    // انیمیشن برای توجه بیشتر
                    statusElement.style.transition = "0.3s";
                    statusElement.style.transform = "scale(1.1)";
                    setTimeout(() => {
                        statusElement.style.transform = "scale(1)";
                    }, 300);

                }

            });

        });

}

// هر ۵ ثانیه وضعیت را چک کن
setInterval(checkStatusUpdates, 2000);
</script>


</body>
</html>

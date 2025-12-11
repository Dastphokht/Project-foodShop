<?php
session_start();

// بررسی لاگین کاربر
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?redirect=information.php");
    exit();
}

$isWalletPayment = isset($_GET["wallet"]) && $_GET["wallet"] == "1";

// 📦 خواندن هزینه ارسال از دیتابیس
$shippingCost = 25000; // مقدار پیش‌فرض اگر دیتابیس مشکلی داشت

$mysqli = new mysqli("localhost", "root", "", "daspokht");
if (!$mysqli->connect_error) {
    $res = $mysqli->query("SELECT cost FROM shipping_settings WHERE shop_ID = 1 LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $shippingCost = (int)$row['cost'];
    }
    $res->free();
    $mysqli->close();
}

// اگر کاربر لاگین کرده بود، ادامه کد اجرا می‌شود
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فرم مشتری</title>
    <link rel="stylesheet" href="css/information.css">
</head>
<body>
    <div class="header">
        <h1>اطلاعات مشتری و جزئیات سفارش</h1>
    </div>
    <div class="payment-container">
        <div class="payment-left">
            <form id="customer-form" method="POST" action="save_info.php">
                <div class="form-group">
                    <label for="first-name">نام</label>
                    <input type="text" id="first-name" name="first_name" placeholder="مثال: علی">
                    <span class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label for="last-name">نام خانوادگی</label>
                    <input type="text" id="last-name" name="last_name" placeholder="مثال: رضایی">
                    <span class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label for="city">شهر</label>
                    <input type="text" id="city" name="city" placeholder="مثال: تهران">
                    <span class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label for="address">آدرس</label>
                    <input type="text" id="address" name="address" placeholder="مثال: تهران، پلاک ۱۲">
                    <span class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label>هزینه سفارش:</label>
                    <div id="order-total-text">0 تومان</div>
                </div>
                <div class="form-group">
                    <label>هزینه ارسال:</label>
                <div
                    id="shipping-cost-text"
                    data-shipping="<?php echo $shippingCost; ?>"
                >
        <?php echo number_format($shippingCost) . " تومان"; ?>
    </div>
</div>

                <div class="form-group">
                    <label>قابل پرداخت:</label>
                    <div id="payable-amount-text">0 تومان</div>
                </div>
                <div class="buttons">
                    <button type="button" id="cancel">انصراف</button>
                    <!-- دکمه پرداخت آنلاین -->
                    <button type="button" id="pay-online">پرداخت آنلاین</button>

                    <!-- دکمه پرداخت با کیف پول -->
                    <button type="button" id="pay-wallet">پرداخت از کیف پول</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/information.js"></script>
</body>
</html>

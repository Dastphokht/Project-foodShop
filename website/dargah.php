<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// بررسی لاگین کاربر
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html?redirect=dargah.php");
    exit();
}
// اگر از صفحه شارژ کیف پول آمدی:
if (isset($_GET['mode']) && $_GET['mode'] === 'wallet') {
    $_SESSION['pay_type'] = 'wallet';
} else {
    // پیش‌فرض: پرداخت سفارش
    $_SESSION['pay_type'] = 'order';
}

?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>درگاه پرداخت</title>
    <link rel="shortcut icon" href="asset/icon/logo1.png" type="image/x-icon">
    <link rel="stylesheet" href="css/dargah.css">
</head>
<body>
    <div class="payment-container">
        <div class="payment-right">
            <div class="form-logo">
                <img src="asset/icon/logo1.png" alt="لوگو درگاه">
            </div>
            <h2>اطلاعات پذیرنده</h2>
            <p><strong>نام پذیرنده:</strong> دستپخت</p>
            <p><strong>شماره تماس:</strong> 021-12345678</p>
            <p><strong>شماره ترمینال:</strong> 12345678</p>
            <p><strong>وب‌سایت:</strong> www.Dastpokht.com</p>
            <div class="amount-box">
                <span>مبلغ قابل پرداخت:</span>
                <strong id="payment-amount">---</strong>
            </div>
            <div class="timer-box">
                <span>زمان باقی مانده برای پرداخت:</span>
                <strong id="timer">10:00</strong>
            </div>
        </div>
        <div class="payment-left">
            <form id="payForm" action="process_payment.php" method="POST">
                <!-- نوع پرداخت: سفارش غذا -->
                <input type="hidden" name="pay_type" value="order">

                


                <div class="form-group">
                    <label>شماره کارت</label>
                    <input type="text" id="card" name="card_number" maxlength="16" placeholder="6037 99xx xxxx xxxx">
                    <span class="error-msg" id="card-error"></span>
                </div>
                <div class="form-group">
                    <label>CVV2</label>
                    <input type="text" id="cvv" name="cvv2" maxlength="4" placeholder="123">
                    <span class="error-msg" id="cvv-error"></span>
                </div>
                <div class="two-input">
                    <div>
                        <label>ماه</label>
                        <input type="text" id="mm" name="expiry_month" maxlength="2" placeholder="02">
                        <span class="error-msg" id="mm-error"></span>
                    </div>
                    <div>
                        <label>سال</label>
                        <input type="text" id="yy" name="expiry_year" maxlength="2" placeholder="25">
                        <span class="error-msg" id="yy-error"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>رمز اینترنتی کارت</label>
                    <input type="password" id="pin" name="pin" maxlength="6" placeholder="رمز اینترنتی">
                    <span class="error-msg" id="pin-error"></span>
                </div>
                <div class="form-group">
                    <label>ایمیل (اختیاری)</label>
                    <input type="email" id="email" name="email" placeholder="example@gmail.com">
                    <span class="error-msg" id="email-error"></span>
                </div>
                <!-- کپچا عددی -->
                <div class="form-group captcha-group">
                    <label>کد امنیتی</label>
                    <div class="captcha-box">
                        <span id="captcha-text"></span>
                        <button type="button" id="refresh-captcha">&#x21bb;</button>
                    </div>
                    <input type="text" id="captcha-input" name="captcha" placeholder="کد بالا را وارد کنید">
                    <span class="error-msg" id="captcha-error"></span>
                </div>
                <div class="buttons">
                    <button type="button" id="cancel">انصراف</button>
                    <button type="submit" id="pay">پرداخت</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/dargah.js"></script>
</body>
</html>

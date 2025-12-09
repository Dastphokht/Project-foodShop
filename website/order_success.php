<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سفارش موفق</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="success-container" style="text-align: center; margin-top: 50px;">
        <h1>🎉 !سفارش شما با موفقیت ثبت شد</h1>
        <?php if (isset($_GET['order_id'])): ?>
            <p style="font-size: 1.2em; color: #4CAF50;">:شماره پیگیری سفارش شما <strong><?php echo htmlspecialchars($_GET['order_id']); ?></strong></p>
        <?php endif; ?>
        <pوضعیت سفارش خود را میتوانید از صفحه پیگیری سفارش، مشاهده کنید</p>
        <a href="food.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">بازگشت به منوی اصلی</a>
    </div>
    
    <script>
        // ⬅️ پاک کردن نهایی سبد خرید و اطلاعات قیمت از مرورگر
        localStorage.removeItem('cartItems');
        localStorage.removeItem('finalAmount');
        localStorage.removeItem('payableAmount');
        localStorage.removeItem("payType");
        localStorage.clear();

    </script>
</body>
</html>
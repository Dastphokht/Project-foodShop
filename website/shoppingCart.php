<?php
session_start();
$userLoggedIn = isset($_SESSION["user_id"]);
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-logged-in" content="<?php echo $userLoggedIn ? 'true' : 'false'; ?>">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="css/shoppingCart.css">
    <title>سبد خرید</title>
</head>
<body>
<?php require ('Menu.php'); ?>
<h2 class="title_shopCard">سبد خرید شما</h2>
    <div class="cart-page">
     
        <div class="cart-items">
            <!-- محصولات از JS اضافه میشن -->
        </div>
        <div class="cart-summary">
            <h3 class="shop-box-text">خلاصه سفارش</h3>
            <div class="jamkol_txt">
                <p>جمع کل: <strong>0 تومان</strong></p>
            </div>
            <div class="discount-box">
                <input type="text" placeholder="کد تخفیف" class="discount-input" />
                <button class="apply-discount">اعمال تخفیف</button>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
    <!-- کاربر لاگین است: مستقیم برود به مرحله اطلاعات/پرداخت -->
    <a href="information.php">
        <button class="pay-btn">پرداخت</button>
    </a>
    <?php else: ?>
        <!-- کاربر لاگین نیست: اول وارد شود، بعد برگردد به همین سبد خرید -->
        <a href="login.html?redirect=shoppingCart.php">
            <button class="pay-btn">برای پرداخت وارد شوید</button>
        </a>
    <?php endif; ?>
        </div>
    </div>
   
    <script src="js/shoppingCart.js"></script>
</body>
</html>

<?php
// بررسی می‌کنیم که آیا session_start قبلاً فراخوانی شده یا نه
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// اگر کاربر از طریق URL پارامتر user رو ارسال کرده، در سشن ذخیره می‌کنیم
if (isset($_GET['user'])) {
    $_SESSION['username'] = $_GET['user'];
}
?>

<div class="menu">
    <div class="menuRight">
        <ul>
            <li class="logo"><a href="index.html"><img src="asset/icon/logo.png" alt="logo"></a></li>
            <li class="hover"><a href="index.html">خانه</a></li>
            <li class="hover"><a href="food.php">منو</a></li>
            <li class="hover"><a href="about.html">درباره ما</a></li>
            <li class="hover"><a href="contact.html">تماس با ما</a></li>
        </ul>
        <i class="fa fa-bars" id="bars"></i>
    </div>

    <div class="menuLeft">
        <ul>
            <li>
                <a href="shoppingCart.php">
                    <img src="asset/icon/card 1.png" alt="shopping-card">
                </a>
            </li>
            <li class="user-menu-wrapper">
    
    <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
        
        <?php require_once 'SessionCheck.php'; ?>

        <a href="#" class="buttonLogin btn_menu_a user-toggle"><?php echo htmlspecialchars($_SESSION['name']); ?></a>
        <a href="#" class="logoLogin btn_menu_a user-toggle">
            <img src="asset/icon/user.png" alt="user">
        </a>

        <!-- منوی بازشونده فقط وقتی لاگین است -->
        <div class="user-dropdown">
            <a href="orderStatus.php">سفارش‌های من</a>
            <a href="wallet.php">کیف پول من</a>
            <a href="logOut.php">خروج</a>
        </div>

    <?php else: ?>

        <a href="login.html" class="buttonLogin btn_menu_a">ورود/عضویت</a>
        <a href="login.html" class="logoLogin btn_menu_a">
            <img src="asset/icon/user.png" alt="user">
        </a>

    <?php endif; ?>

</li>


        </ul>
    </div>
</div>
<script>
// یک پرچم برای ردگیری وضعیت کلیک
let isToggling = false;

// تابع هندلر اصلی برای باز و بسته کردن منو
function handleUserToggle(event) {
    event.preventDefault();

    const userWrapper = document.querySelector(".user-menu-wrapper");
    const userDropdown = userWrapper ? userWrapper.querySelector(".user-dropdown") : null;
    
    if (userDropdown) {
        userDropdown.classList.toggle("show");
        
        // تنظیم پرچم برای جلوگیری از بسته شدن فوری
        isToggling = true;
        setTimeout(() => {
            isToggling = false;
        }, 10); // 10 میلی‌ثانیه برای دور زدن چرخه رویداد مرورگر کافی است
        
        console.log("Toggle executed. Class is now:", userDropdown.className);
    }
}

// استفاده از Event Capturing روی سند برای اطمینان از اولویت بالا
document.addEventListener("click", function(event) {
    // بررسی می‌کنیم که آیا عنصری که کلیک شده یا یکی از والدهای آن، کلاس user-toggle را دارد.
    const toggleElement = event.target.closest(".user-toggle");
    
    if (toggleElement) {
        handleUserToggle(event);
        // دیگر نیازی به event.stopPropagation() نیست، چون منطق بستن را تغییر داده‌ایم
        return; 
    }

    // منطق بستن منو (فقط در صورت باز بودن و کلیک بیرون از منو)
    const userWrapper = document.querySelector(".user-menu-wrapper");
    const userDropdown = document.querySelector(".user-dropdown");
    
    // نکته کلیدی: چک کردن پرچم isToggling
    if (userDropdown && userWrapper && userDropdown.classList.contains("show") && !userWrapper.contains(event.target) && !isToggling) {
        userDropdown.classList.remove("show");
        console.log("Closed by clicking outside.");
    }

}, true); // همچنان در حالت Capturing می‌مانیم
</script>
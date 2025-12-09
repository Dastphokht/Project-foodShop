<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>خروج...</title>
</head>
<body>
<script>
    // پاک کردن کامل cart
    localStorage.removeItem("cartItems");
    localStorage.removeItem("payType");
    localStorage.removeItem("payableAmount");
    localStorage.removeItem("finalAmount");

    // انتقال به صفحه لاگین
    window.location.href = "index.html";
</script>
</body>
</html>


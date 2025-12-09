<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$username = "";
$password = "";
$connect = mysqli_connect('localhost', 'root', '', 'daspokht');
if (!$connect) {
    die("خطا در اتصال به دیتابیس: " . mysqli_connect_error());
}

if (isset($_POST['input_user']) && !empty($_POST['input_user']) &&
    isset($_POST['input_pass']) && !empty($_POST['input_pass'])) {
    $username = $_POST['input_user'];
    $password = $_POST['input_pass'];
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'food.php?index=1';
    $sql = 'SELECT * FROM users WHERE user_Name="' . $username . '" AND user_Password="' . $password . '"';
    $query = mysqli_query($connect, $sql);
    $row = mysqli_fetch_array($query);
    if ($row) {
        $_SESSION['login'] = true;
        $_SESSION['name'] = $row['full_Name'];
        $_SESSION['username'] = $row['user_Name'];
        $_SESSION['user_id'] = $row['user_ID'];
        $_SESSION['last_activity'] = time();
       // header("Location: " . $redirect . "&msg=" . urlencode('خوش آمدید') . "&user=" . urlencode($row['full_Name']));
       
        // مطمئن می‌شیم redirect همیشه یه آدرس معتبره
        $redirect = isset($_POST['redirect']) && !empty($_POST['redirect'])
        ? $_POST['redirect']
        : 'food.php?index=1';

        // اگر داخل redirect قبلاً ? نبود، یعنی هنوز پارامتر نداره → ? بذار
        // اگر ? داشت، یعنی خودش پارامتر داره → & بذار
        $separator = (strpos($redirect, '?') === false) ? '?' : '&';

        header(
        "Location: " .
        $redirect .
        $separator .
        "msg=" . urlencode('خوش آمدید') .
        "&user=" . urlencode($row['full_Name'])
        );
        exit();
    } else {
        header("Location: login.html?msg=" . urlencode('نام کاربری یا کلمه عبور اشتباه است'));
        exit();
    }
} else {
    header("Location: login.html?msg=" . urlencode('نام کاربری یا رمز عبور وارد نشده است'));
    exit();
}
mysqli_close($connect);
?>

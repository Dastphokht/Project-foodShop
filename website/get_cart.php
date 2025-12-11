<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// اگر لاگین نیست → سبد خالی
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'guest', 'cart' => []]);
    exit;
}

$userId = (int)$_SESSION['user_id'];

$connect = mysqli_connect("localhost", "root", "", "daspokht");
if (!$connect) {
    echo json_encode(['status' => 'error', 'message' => 'DB Error']);
    exit;
}

/*
   carts:
   cart_ID | user_ID | food_ID | Quantity | added_at
   foods:
   food_ID | food_Name | price | Quantity | ...
*/

$sql = "
    SELECT 
        c.food_ID,
        c.Quantity      AS cart_qty,
        f.food_Name     AS name,
        f.price         AS price,
        f.Quantity      AS stock
    FROM carts c
    INNER JOIN foods f ON f.food_ID = c.food_ID
    WHERE c.user_ID = ?
";

$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($result)) {

    $stock   = (int)$row['stock'];      // موجودی واقعی الان
    $cartQty = (int)$row['cart_qty'];   // تعداد ذخیره شده در carts

    // اگر موجودی تموم شده → اصلاً این آیتم رو به سبد برنگردون
    if ($stock <= 0) {
        continue;
    }

    // اگر تعداد سبد بیشتر از موجودی فعلی است → محدودش کن به موجودی
    if ($cartQty > $stock) {
        $cartQty = $stock;
    }

    // اگر بعد از اصلاح، تعداد ۰ شد → چیزی برنگردون
    if ($cartQty <= 0) {
        continue;
    }

    $items[] = [
        "id"    => (int)$row['food_ID'],
        "name"  => $row['name'],
        "price" => (int)$row['price'],
        "qty"   => $cartQty,
        "stock" => $stock      // اختیاری، اگر JS خواست استفاده کند
    ];
}

echo json_encode(['status' => 'ok', 'cart' => $items]);
exit;

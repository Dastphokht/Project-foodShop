<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

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
   جدول carts فرضاً:
   cart_ID | user_ID | food_ID | Quantity | added_at
   جدول foods فرضاً:
   food_ID | foodname | price | ...
*/

$sql = "
    SELECT 
        c.food_ID,
        c.Quantity,
        f.food_Name AS name,
        f.price    AS price
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
    $items[] = [
        "id"    => (int)$row['food_ID'],
        "name"  => $row['name'],
        "price" => (int)$row['price'],
        "qty"   => (int)$row['Quantity']
    ];
}

echo json_encode(['status' => 'ok', 'cart' => $items]);
exit;

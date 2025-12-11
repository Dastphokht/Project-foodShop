<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

// سبد خرید باید از POST بیاید
if (!isset($_POST['cart'])) {
    echo json_encode(["status" => "error", "message" => "No cart data"]);
    exit;
}

$cart = json_decode($_POST['cart'], true);

$connect = new mysqli("localhost", "root", "", "daspokht");
if ($connect->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Error"]);
    exit;
}

$insufficient = [];

foreach ($cart as $item) {
    $foodId = intval($item['id']);
    $needed = intval($item['qty']);

    $stmt = $connect->prepare("SELECT food_Name, Quantity FROM foods WHERE food_ID = ?");
    $stmt->bind_param("i", $foodId);
    $stmt->execute();
    $stmt->bind_result($name, $stock);
    $stmt->fetch();
    $stmt->close();

    if ($stock < $needed) {
        $insufficient[] = [
            "name" => $name,
            "needed" => $needed,
            "available" => $stock
        ];
    }
}

if (!empty($insufficient)) {
    echo json_encode(["status" => "fail", "items" => $insufficient]);
    exit;
}

echo json_encode(["status" => "ok"]);
exit;
?>

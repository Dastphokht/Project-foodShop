<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 📦 خواندن هزینه ارسال از دیتابیس به‌صورت داینامیک
    $shippingCost = 25000; // مقدار پیش‌فرض

    $db = new mysqli("localhost", "root", "", "daspokht");
    if (!$db->connect_error) {
        $res = $db->query("SELECT cost FROM shipping_settings WHERE shop_ID = 1 LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            $shippingCost = (int)$row['cost'];
        }
        if ($res) $res->free();
        $db->close();
    }

    // ذخیره اطلاعات سفارش در سشن
    $_SESSION["order_info"] = [
        "first_name" => $_POST["first_name"] ?? null,
        "last_name"  => $_POST["last_name"] ?? null,
        "city"       => $_POST["city"] ?? null,
        "address"    => $_POST["address"] ?? null,
        "shipping_cost" => $shippingCost,  // ✅ داینامیک
    ];
    
    $cart_data_json = $_POST['cart_data'] ?? null;
    $cart_items = json_decode($cart_data_json, true);
    
    $_SESSION['cart'] = [];
    if (!empty($cart_items) && is_array($cart_items)) {
        foreach ($cart_items as $item) {
            $food_id  = $item['id']   ?? null; 
            $price    = $item['price'] ?? 0;
            $quantity = $item['qty']   ?? 0;

            if ($food_id) {
                $_SESSION['cart'][] = [
                    "food_id" => $food_id,
                    "qty"     => $quantity,
                    "price"   => $price,
                ];
            }
        }
    }

    http_response_code(200); 
    exit(); 
}

http_response_code(405); 
?>

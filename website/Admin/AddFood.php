<?php

function db_connect() {
    $conn = mysqli_connect('localhost','root','', 'daspokht');
    if (!$conn) {
        die("DB connect error: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn, "utf8");
    return $conn;
}



function Uploader() {
    if (!isset($_FILES['food_image']) || empty($_FILES['food_image']['name'])) {
        return null;
    }
    $pic_name = $_FILES['food_image']['name'];
    $array = explode(".", $pic_name);
    $ext = end($array);
    $new_name = rand() . "." . $ext;
    $from = $_FILES['food_image']['tmp_name'];
    $to = "../asset/img/FoodsImage/" . $new_name;
    if (move_uploaded_file($from, $to)) {
        return $new_name;
    }
    return null;
}


function AddFood($food_info, $image_name) {
    $conn = db_connect();

    $name = mysqli_real_escape_string($conn, $food_info['name']);
    $desc = mysqli_real_escape_string($conn, $food_info['description']);
    $price = mysqli_real_escape_string($conn, $food_info['price']);
    $type = mysqli_real_escape_string($conn, $food_info['type']);
    $qty = mysqli_real_escape_string($conn, $food_info['qty']);
    $img = $image_name ? mysqli_real_escape_string($conn, $image_name) : '';

    $sql = "INSERT INTO foods (food_Name, description, price, img_url, type,Quantity)
            VALUES ('$name', '$desc', '$price', '$img', '$type','$qty')";

    $res = mysqli_query($conn, $sql);
    mysqli_close($conn);
    return (bool)$res;
}


function UpdateFood($id, $food_info, $image_name) {
    $conn = db_connect();

    $id = mysqli_real_escape_string($conn, $id);
    $name = mysqli_real_escape_string($conn, $food_info['name']);
    $desc = mysqli_real_escape_string($conn, $food_info['description']);
    $price = mysqli_real_escape_string($conn, $food_info['price']);
    $type = mysqli_real_escape_string($conn, $food_info['type']);
    $qty = mysqli_real_escape_string($conn, $food_info['qty']);

    $set_img = "";
    if ($image_name) {
        $img = mysqli_real_escape_string($conn, $image_name);
        $set_img = ", img_url='$img'";
    }

    $sql = "UPDATE foods SET 
                food_Name='$name',
                description='$desc',
                price='$price',
                type='$type',
                Quantity=$qty
            $set_img
            WHERE food_ID='$id'";

    $res = mysqli_query($conn, $sql);
    mysqli_close($conn);
    return (bool)$res;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $food_info = $_POST['food_info'];


    if (!empty($_POST['food_id'])) {

        $id = $_POST['food_id'];


        $image_name = !empty($_FILES['food_image']['name']) ? Uploader() : null;

        $ok = UpdateFood($id, $food_info, $image_name);

        header("Location: panel.php?tab=product&status=" . ($ok ? "success" : "error"));
        exit;
    }

    $image_name = !empty($_FILES['food_image']['name']) ? Uploader() : null;

    $ok = AddFood($food_info, $image_name);

    header("Location: panel.php?tab=addProduct&status=" . ($ok ? "success" : "error"));
    exit;
}

?>

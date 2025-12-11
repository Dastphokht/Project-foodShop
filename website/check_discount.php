<?php
header("Content-Type: application/json");

$code = isset($_POST["code"]) ? trim($_POST["code"]) : "";

if ($code === "") {
    echo json_encode(["status" => "error", "message" => "empty"]);
    exit;
}

$connect = new mysqli("localhost", "root", "", "daspokht");
if ($connect->connect_error) {
    echo json_encode(["status" => "error", "message" => "db"]);
    exit;
}

$stmt = $connect->prepare("SELECT percent_off FROM discount_codes WHERE code = ? AND is_active = 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(["status" => "not_found"]);
    exit;
}

$stmt->bind_result($percent_off);
$stmt->fetch();

echo json_encode([
    "status" => "ok",
    "percent" => $percent_off
]);
exit;
?>

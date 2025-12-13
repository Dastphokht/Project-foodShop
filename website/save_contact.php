<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$full_name = trim($_POST["name"] ?? "");
$email     = trim($_POST["email"] ?? "");
$phone     = trim($_POST["phone"] ?? "");
$subject   = trim($_POST["subject"] ?? "");
$message   = trim($_POST["message"] ?? "");

// اعتبارسنجی ساده
if ($full_name === "" || $email === "" || $phone === "" || $subject === "" || $message === "") {
    echo json_encode(["status" => "error", "message" => "همه فیلدها الزامی است"]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "ایمیل معتبر نیست"]);
    exit;
}

// تبدیل value های select به متن فارسی که تو ENUM ذخیره میشه
$map = [
    "order"      => "سفارش",
    "complaint"  => "شکایت",
    "suggestion" => "پیشنهاد",
    "other"      => "سایر",
];

if (!isset($map[$subject])) {
    echo json_encode(["status" => "error", "message" => "موضوع نامعتبر است"]);
    exit;
}

$subject_fa = $map[$subject];

// اتصال DB
$db = new mysqli("localhost", "root", "", "daspokht");
$db->set_charset("utf8mb4");

if ($db->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Error"]);
    exit;
}

$stmt = $db->prepare("
    INSERT INTO contact_messages (full_name, email, phone, subject, message)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("sssss", $full_name, $email, $phone, $subject_fa, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error", "message" => "خطا در ذخیره پیام"]);
}

$stmt->close();
$db->close();

<?php
require_once "db.php";
header('Content-Type: application/json; charset=utf-8');
require_once "SessionCheck.php";

$result = mysqli_query($conn, "
    SELECT 
        msg_ID AS id,
        full_name,
        email,
        phone,
        subject,
        message,
        created_at
    FROM contact_messages
    ORDER BY created_at DESC
");

$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

echo json_encode(["status" => "ok", "messages" => $items]);
exit;

<?php
require_once "db.php";
header('Content-Type: application/json; charset=utf-8');
require_once "SessionCheck.php";

$id = intval($_POST["id"] ?? 0);
if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid ID"]);
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM contact_messages WHERE msg_ID = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
exit;

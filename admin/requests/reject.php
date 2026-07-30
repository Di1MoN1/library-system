<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/db.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$request_id = (int)$_GET["id"];

// Меняем статус заявки
$stmt = $conn->prepare("
UPDATE requests
SET status='Отклонена'
WHERE id=?
");

$stmt->bind_param("i", $request_id);
$stmt->execute();

header("Location: index.php");
exit;
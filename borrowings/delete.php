<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET["id"];

$stmt = $conn->prepare("
DELETE FROM borrowings
WHERE id=?
");

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
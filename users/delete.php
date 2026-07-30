<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["role"] != "superadmin") {
    header("Location: ../admin/index.php");
    exit;
}

require_once "../config/db.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET["id"];

// Нельзя удалить самого себя
if ($id == $_SESSION["user_id"]) {
    die("Нельзя удалить самого себя.");
}

// Проверяем пользователя
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$user = $result->fetch_assoc();

// Если удаляем superadmin — проверяем, что он не последний
if ($user["role"] == "superadmin") {

    $count = $conn->query("
        SELECT COUNT(*) AS total
        FROM users
        WHERE role='superadmin'
    ")->fetch_assoc()["total"];

    if ($count <= 1) {
        die("Нельзя удалить последнего главного администратора.");
    }
}

// Удаляем
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
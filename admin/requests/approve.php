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

// Получаем заявку
$stmt = $conn->prepare("
SELECT *
FROM requests
WHERE id=?
");

$stmt->bind_param("i", $request_id);
$stmt->execute();

$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    die("Заявка не найдена.");
}

// Проверяем, есть ли книга в наличии
$stmt = $conn->prepare("
SELECT copies
FROM books
WHERE id=?
");

$stmt->bind_param("i", $request["book_id"]);
$stmt->execute();

$book = $stmt->get_result()->fetch_assoc();

if ($book["copies"] <= 0) {

    echo "<script>
    alert('Книга закончилась.');
    location='index.php';
    </script>";

    exit;
}

// Только одобряем заявку
$stmt = $conn->prepare("
UPDATE requests
SET status='Одобрена'
WHERE id=?
");

$stmt->bind_param("i", $request_id);
$stmt->execute();

echo "<script>
alert('Заявка одобрена.');
location='index.php';
</script>";

exit;
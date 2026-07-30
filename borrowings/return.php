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

// Получаем информацию о выдаче
$stmt = $conn->prepare("
SELECT *
FROM borrowings
WHERE id=?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$borrowing = $result->fetch_assoc();

// Если книга уже возвращена
if ($borrowing["status"] == "Возвращена") {

    header("Location: index.php");
    exit;

}

// Обновляем запись
$return_date = date("Y-m-d");

$stmt = $conn->prepare("
UPDATE borrowings
SET
    return_date=?,
    status='Возвращена'
WHERE id=?
");

$stmt->bind_param(
    "si",
    $return_date,
    $id
);

$stmt->execute();

// Возвращаем экземпляр книги
$stmt = $conn->prepare("
UPDATE books
SET copies = copies + 1
WHERE id=?
");

$stmt->bind_param(
    "i",
    $borrowing["book_id"]
);

$stmt->execute();

header("Location: index.php");
exit;
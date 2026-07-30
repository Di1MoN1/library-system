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

$request_id = (int)$_GET["id"];

// Получаем информацию о заявке
$stmt = $conn->prepare("
SELECT
    return_requests.borrowing_id,
    borrowings.book_id
FROM return_requests

INNER JOIN borrowings
ON return_requests.borrowing_id = borrowings.id

WHERE return_requests.id=?
");

$stmt->bind_param("i", $request_id);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

if (!$data) {

    header("Location: index.php");
    exit;

}

$borrowing_id = $data["borrowing_id"];
$book_id      = $data["book_id"];

$return_date = date("Y-m-d");

// =======================================
// 1. Закрываем выдачу
// =======================================

$stmt = $conn->prepare("
UPDATE borrowings

SET

status='Возвращена',
return_date=?

WHERE id=?
");

$stmt->bind_param(
    "si",
    $return_date,
    $borrowing_id
);

$stmt->execute();

// =======================================
// 2. Возвращаем экземпляр книги
// =======================================

$stmt = $conn->prepare("
UPDATE books
SET copies = copies + 1
WHERE id=?
");

$stmt->bind_param("i", $book_id);
$stmt->execute();

// =======================================
// 3. Удаляем обработанную заявку
// =======================================

$stmt = $conn->prepare("
UPDATE return_requests
SET status='Принята'
WHERE id=?
");

$stmt->bind_param("i", $request_id);
$stmt->execute();

echo "<script>

alert('Книга успешно принята.');

location='index.php';

</script>";

exit;
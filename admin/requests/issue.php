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

// Проверяем, что заявка одобрена
if ($request["status"] != "Одобрена") {

    echo "<script>
    alert('Сначала необходимо одобрить заявку.');
    location='index.php';
    </script>";

    exit;
}

// Проверяем наличие экземпляров книги
$stmt = $conn->prepare("
SELECT copies
FROM books
WHERE id=?
");

$stmt->bind_param("i", $request["book_id"]);
$stmt->execute();

$book = $stmt->get_result()->fetch_assoc();

if (!$book || $book["copies"] <= 0) {

    echo "<script>
    alert('Свободных экземпляров книги нет.');
    location='index.php';
    </script>";

    exit;
}

// Сегодня
$issue_date = date("Y-m-d");

// Получаем настройки библиотеки
$settings = $conn->query("
SELECT borrow_days
FROM settings
LIMIT 1
")->fetch_assoc();

$borrow_days = (int)$settings["borrow_days"];

// Считаем дату возврата
$due_date = date(
    "Y-m-d",
    strtotime("+".$borrow_days." days")
);

// Создаем выдачу
$stmt = $conn->prepare("
INSERT INTO borrowings
(
    reader_id,
    book_id,
    issue_date,
    due_date,
    status
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    'Выдана'
)
");

$stmt->bind_param(
    "iiss",
    $request["reader_id"],
    $request["book_id"],
    $issue_date,
    $due_date
);

$stmt->execute();

// Уменьшаем количество экземпляров
$stmt = $conn->prepare("
UPDATE books
SET copies = copies - 1
WHERE id=?
");

$stmt->bind_param("i", $request["book_id"]);
$stmt->execute();

$stmt = $conn->prepare("
UPDATE books
SET copies = copies - 1
WHERE id=?
");

$stmt->bind_param("i", $request["book_id"]);
$stmt->execute();

// Удаляем обработанную заявку
$stmt = $conn->prepare("
DELETE FROM requests
WHERE id=?
");

$stmt->bind_param("i", $request_id);
$stmt->execute();

echo "<script>
alert('Книга успешно выдана.');
location='index.php';
</script>";
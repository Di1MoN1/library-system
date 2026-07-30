<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "reader") {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

// Получаем id и ФИО читателя
$stmt = $conn->prepare("
SELECT id, full_name
FROM readers
WHERE user_id=?
");

$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$reader = $stmt->get_result()->fetch_assoc();

$reader_id = $reader["id"];

// Имя для приветствия: ФИО, если заполнено, иначе логин
$displayName = !empty(trim($reader["full_name"]))
    ? $reader["full_name"]
    : $_SESSION["username"];

// Все книги
$totalBooks = $conn->query("
SELECT COUNT(*) total
FROM borrowings
WHERE reader_id=$reader_id
AND status='Выдана'
")->fetch_assoc()["total"];

// Скоро вернуть
$soon = $conn->query("
SELECT
books.title,
borrowings.due_date

FROM borrowings

LEFT JOIN books
ON borrowings.book_id=books.id

WHERE borrowings.reader_id=$reader_id
AND borrowings.status='Выдана'

ORDER BY borrowings.due_date
");

// Просроченные
$overdue = $conn->query("
SELECT
books.title,
borrowings.due_date

FROM borrowings

LEFT JOIN books
ON borrowings.book_id=books.id

WHERE borrowings.reader_id=$reader_id
AND borrowings.status='Выдана'
AND borrowings.due_date<CURDATE()
");

include "../includes/header.php";
include "navbar.php";
?>

<div class="container mt-5">

<div class="card shadow-lg mb-4">

<div class="card-body">

<h2>

👋 Добро пожаловать,

<?= htmlspecialchars($displayName) ?>

</h2>

<hr>

<h5>

📚 Сейчас у вас книг на руках:

<span class="badge bg-primary">

<?= $totalBooks ?>

</span>

</h5>

</div>

</div>

<div class="row">

<div class="col-md-6">

<div class="card shadow mb-4">

<div class="card-header bg-warning">

⏳ Скоро вернуть

</div>

<div class="card-body">

<?php

$have=false;

while($book=$soon->fetch_assoc()){

$days=floor(
(strtotime($book["due_date"])-time())/86400
);

if($days<=7 && $days>=0){

$have=true;

?>

<p>

📖

<b>

<?= htmlspecialchars($book["title"]) ?>

</b>

<br>

Осталось

<?= $days ?>

дн.

</p>

<hr>

<?php

}

}

if(!$have){

echo "Нет книг, срок возврата которых скоро истекает.";

}

?>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card shadow mb-4">

<div class="card-header bg-danger text-white">

❗ Просроченные книги

</div>

<div class="card-body">

<?php

if($overdue->num_rows>0){

while($book=$overdue->fetch_assoc()){

$days=abs(
floor(
(time()-strtotime($book["due_date"]))/86400
)
);

?>

<p>

📕 <b>

<?= htmlspecialchars($book["title"]) ?>

</b>

<br>

Просрочена на

<?= $days ?>

дн.

</p>

<hr>

<?php

}

}else{

echo "Просроченных книг нет.";

}

?>

</div>

</div>

</div>

</div>

<div class="row">

<div class="col-md-3">

<a href="catalog.php" class="btn btn-primary w-100">

📚 Каталог

</a>

</div>

<div class="col-md-3">

<a href="requests.php" class="btn btn-warning w-100">

📩 Мои заявки

</a>

</div>

<div class="col-md-3">

<a href="my_books.php" class="btn btn-success w-100">

📖 Мои книги

</a>

</div>

<div class="col-md-3">

<a href="profile.php" class="btn btn-secondary w-100">

👤 Профиль

</a>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>

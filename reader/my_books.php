<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "reader") {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

// Получаем id читателя
$stmt = $conn->prepare("
SELECT id
FROM readers
WHERE user_id=?
");

$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$reader = $stmt->get_result()->fetch_assoc();

$reader_id = $reader["id"];

// ===========================
// Выданные книги
// ===========================

$activeBooks = $conn->query("
SELECT

borrowings.id AS borrowing_id,

books.title,
books.image,

authors.full_name,

borrowings.issue_date,
borrowings.due_date,
borrowings.status

FROM borrowings

LEFT JOIN books
ON borrowings.book_id = books.id

LEFT JOIN authors
ON books.author_id = authors.id

WHERE borrowings.reader_id = $reader_id
AND borrowings.status='Выдана'

ORDER BY borrowings.issue_date DESC
");

// ===========================
// История
// ===========================

$historyBooks = $conn->query("
SELECT

borrowings.id AS borrowing_id,

books.title,
books.image,

authors.full_name,

borrowings.issue_date,
borrowings.due_date,
borrowings.return_date

FROM borrowings

LEFT JOIN books
ON borrowings.book_id = books.id

LEFT JOIN authors
ON books.author_id = authors.id

WHERE borrowings.reader_id = $reader_id
AND borrowings.status='Возвращена'

ORDER BY borrowings.return_date DESC
");

include "../includes/header.php";
include "navbar.php";
?>

<div class="container mt-5">

<h2 class="mb-4">

📖 Мои книги

</h2>

<ul class="nav nav-tabs mb-4">

<li class="nav-item">

<button
class="nav-link active"
data-bs-toggle="tab"
data-bs-target="#active">

🟢 Выданы

</button>

</li>

<li class="nav-item">

<button
class="nav-link"
data-bs-toggle="tab"
data-bs-target="#history">

📜 История

</button>

</li>

</ul>

<div class="tab-content">

<!-- ======================================= -->
<!-- АКТИВНЫЕ КНИГИ -->
<!-- ======================================= -->

<div class="tab-pane fade show active" id="active">

<div class="row">

<?php if($activeBooks->num_rows>0): ?>

<?php while($row=$activeBooks->fetch_assoc()): ?>

<div class="col-lg-4 col-md-6 mb-4">

<div class="card book-card h-100">

<div class="book-cover">

<?php if(!empty($row["image"])): ?>

<img src="../assets/images/<?= htmlspecialchars($row["image"]) ?>" class="book-image" alt="<?= htmlspecialchars($row["title"]) ?>">

<?php else: ?>

<img src="../assets/images/no-cover.png" class="book-image" alt="Нет обложки">

<?php endif; ?>

</div>

<div class="card-body">

<h5 class="book-title">

<?= htmlspecialchars($row["title"]) ?>

</h5>

<p>

<b>Автор:</b><br>

<?= htmlspecialchars($row["full_name"]) ?>

</p>

<hr>

<p>

📅 Получена

<br>

<?= date("d.m.Y",strtotime($row["issue_date"])) ?>

</p>

<p>

📅 Вернуть до

<br>

<?= date("d.m.Y",strtotime($row["due_date"])) ?>

</p>

<?php

$days = floor(
(strtotime($row["due_date"]) - time()) / 86400
);

if($days < 0){

echo "<div class='alert alert-danger text-center'>
❌ Просрочена на ".abs($days)." дн.
</div>";

}elseif($days <= 3){

echo "<div class='alert alert-warning text-center'>
⚠ Осталось ".$days." дн.
</div>";

}else{

echo "<div class='alert alert-success text-center'>
✅ До возврата ".$days." дн.
</div>";

}

?>

<a
href="request_return.php?id=<?= $row["borrowing_id"] ?>"
class="btn btn-danger w-100 mt-3">

↩ Запросить возврат

</a>

</div>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="col-12">

<div class="alert alert-info text-center">

У вас сейчас нет книг.

</div>

</div>

<?php endif; ?>

</div>

</div>
<!-- ======================================= -->
<!-- ИСТОРИЯ -->
<!-- ======================================= -->

<div class="tab-pane fade" id="history">

<div class="row">

<?php if($historyBooks->num_rows>0): ?>

<?php while($row=$historyBooks->fetch_assoc()): ?>

<div class="col-lg-4 col-md-6 mb-4">

<div class="card book-card h-100 border-secondary">

<div class="book-cover">

<?php if(!empty($row["image"])): ?>

<img src="../assets/images/<?= htmlspecialchars($row["image"]) ?>" class="book-image" alt="<?= htmlspecialchars($row["title"]) ?>">

<?php else: ?>

<img src="../assets/images/no-cover.png" class="book-image" alt="Нет обложки">

<?php endif; ?>

</div>

<div class="card-body">

<h5 class="book-title">

<?= htmlspecialchars($row["title"]) ?>

</h5>

<p>

<b>Автор:</b><br>

<?= htmlspecialchars($row["full_name"]) ?>

</p>

<hr>

<p>

📅 Получена

<br>

<?= date("d.m.Y", strtotime($row["issue_date"])) ?>

</p>

<p>

📅 Вернуть нужно было

<br>

<?= date("d.m.Y", strtotime($row["due_date"])) ?>

</p>

<p>

✅ Возвращена

<br>

<?= date("d.m.Y", strtotime($row["return_date"])) ?>

</p>

<div class="alert alert-secondary text-center">

Книга успешно возвращена

</div>

</div>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="col-12">

<div class="alert alert-info text-center">

История пока пуста.

</div>

</div>

<?php endif; ?>

</div>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>

<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "reader") {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

if (!isset($_GET["id"])) {
    die("Книга не найдена.");
}

$id = (int)$_GET["id"];

$sql = "
SELECT
    books.*,
    authors.full_name,
    genres.name AS genre
FROM books
LEFT JOIN authors
ON books.author_id = authors.id

LEFT JOIN genres
ON books.genre_id = genres.id

WHERE books.id = $id
";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Книга не найдена.");
}

$book = $result->fetch_assoc();

include "../includes/header.php";
include "navbar.php";
?>

<div class="container mt-5">

<div class="row">

<div class="col-lg-4">

<?php if(!empty($book["image"])): ?>

<img
src="../assets/images/<?= htmlspecialchars($book["image"]) ?>"
class="img-fluid rounded shadow">

<?php else: ?>

<img
src="../assets/images/no-cover.png"
class="img-fluid rounded shadow">

<?php endif; ?>

</div>

<div class="col-lg-8">

<h2 class="mb-3">

<?= htmlspecialchars($book["title"]) ?>

</h2>

<table class="table">

<tr>
<th width="180">Автор</th>
<td><?= htmlspecialchars($book["full_name"]) ?></td>
</tr>

<tr>
<th>Жанр</th>
<td><?= htmlspecialchars($book["genre"]) ?></td>
</tr>

<tr>
<th>Год</th>
<td><?= $book["year_published"] ?></td>
</tr>

<tr>
<th>Экземпляров</th>

<td>

<?php if($book["copies"]>0): ?>

<span class="badge bg-success">

<?= $book["copies"] ?>

</span>

<?php else: ?>

<span class="badge bg-danger">

Нет в наличии

</span>

<?php endif; ?>

</td>

</tr>

</table>

<h5 class="mt-4">

Описание

</h5>

<div class="border rounded p-3 bg-light mb-4">

<?= nl2br(htmlspecialchars($book["description"])) ?>

</div>

<?php if($book["copies"]>0): ?>

<a
href="request.php?book_id=<?= $book["id"] ?>"
class="btn btn-success btn-lg">

📩 Оформить заявку

</a>

<?php else: ?>

<button
class="btn btn-secondary btn-lg"
disabled>

Книга отсутствует

</button>

<?php endif; ?>

<a
href="catalog.php"
class="btn btn-outline-secondary btn-lg ms-2">

← Назад

</a>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

$result = $conn->query("
SELECT
    genres.*,
    COUNT(books.id) AS total_books
FROM genres
LEFT JOIN books
ON genres.id = books.genre_id
GROUP BY genres.id
ORDER BY genres.name
");

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>🎭 Жанры</h2>

<a href="add.php" class="btn btn-success">

➕ Добавить жанр

</a>

</div>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Название жанра</th>

<th>Книг</th>

<th width="180">Действия</th>

</tr>

</thead>

<tbody>

<?php while($genre = $result->fetch_assoc()): ?>

<tr>

<td><?= $genre["id"] ?></td>

<td><?= htmlspecialchars($genre["name"]) ?></td>

<td><?= $genre["total_books"] ?></td>

<td>

<a
href="edit.php?id=<?= $genre["id"] ?>"
class="btn btn-warning btn-sm">

✏️

</a>

<a
href="delete.php?id=<?= $genre["id"] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Удалить жанр?')">

🗑️

</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php include "../includes/footer.php"; ?>
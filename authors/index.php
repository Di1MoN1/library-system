<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

$result = $conn->query("
SELECT
    authors.*,
    COUNT(books.id) AS total_books
FROM authors
LEFT JOIN books
ON authors.id = books.author_id
GROUP BY authors.id
ORDER BY authors.full_name
");

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>👨 Авторы</h2>

<a href="add.php" class="btn btn-success">

➕ Добавить автора

</a>

</div>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>ФИО автора</th>

<th>Книг</th>

<th width="180">Действия</th>

</tr>

</thead>

<tbody>

<?php while($author = $result->fetch_assoc()): ?>

<tr>

<td><?= $author["id"] ?></td>

<td><?= htmlspecialchars($author["full_name"]) ?></td>

<td><?= $author["total_books"] ?></td>

<td>

<a
href="edit.php?id=<?= $author["id"] ?>"
class="btn btn-warning btn-sm">

✏️

</a>

<a
href="delete.php?id=<?= $author["id"] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Удалить автора?')">

🗑️

</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php include "../includes/footer.php"; ?>
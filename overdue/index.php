<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

include "../includes/header.php";
include "../includes/navbar.php";

$sql = "
SELECT

borrowings.id,

books.title,

readers.full_name,

borrowings.issue_date,

borrowings.due_date,

DATEDIFF(CURDATE(), borrowings.due_date) AS overdue_days

FROM borrowings

LEFT JOIN books
ON borrowings.book_id = books.id

LEFT JOIN readers
ON borrowings.reader_id = readers.id

WHERE borrowings.status='Выдана'
AND borrowings.due_date < CURDATE()

ORDER BY borrowings.due_date ASC
";

$result = $conn->query($sql);
?>

<div class="container mt-5">

<h2 class="mb-4">

⚠ Просроченные книги

</h2>

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Книга</th>

<th>Читатель</th>

<th>Дата выдачи</th>

<th>Вернуть до</th>

<th>Просрочка</th>

</tr>

</thead>

<tbody>

<?php if($result->num_rows>0): ?>

<?php while($row=$result->fetch_assoc()): ?>

<tr>

<td><?= $row["id"] ?></td>

<td><?= htmlspecialchars($row["title"]) ?></td>

<td><?= htmlspecialchars($row["full_name"]) ?></td>

<td><?= date("d.m.Y", strtotime($row["issue_date"])) ?></td>

<td><?= date("d.m.Y", strtotime($row["due_date"])) ?></td>

<td>

<span class="badge bg-danger">

<?= $row["overdue_days"] ?> дн.

</span>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="6" class="text-center">

🎉 Просроченных книг нет.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

<?php include "../includes/footer.php"; ?>
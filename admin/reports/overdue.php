<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/db.php";

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

$totalFound = $result->num_rows;

include "../../includes/header.php";
include "../../includes/navbar.php";
?>

<div class="container mt-5 mb-5">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2 class="d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill text-danger"></i>
        Просроченные книги
        <?php if ($totalFound > 0): ?>
            <span class="badge bg-danger"><?= $totalFound ?></span>
        <?php endif; ?>
    </h2>

    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
        Назад
    </a>

</div>

<div class="card border-0 shadow">

<div class="card-body p-0">

<?php if ($totalFound === 0): ?>

    <div class="text-center text-muted py-5">
        <i class="bi bi-check2-circle text-success fs-1 d-block mb-2"></i>
        🎉 Просроченных книг нет.
    </div>

<?php else: ?>

    <table class="table table-hover align-middle mb-0">

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

    <?php while ($row = $result->fetch_assoc()): ?>

    <tr class="table-danger">

        <td><?= $row["id"] ?></td>

        <td class="fw-bold"><?= htmlspecialchars($row["title"]) ?></td>

        <td><?= htmlspecialchars($row["full_name"]) ?></td>

        <td><?= date("d.m.Y", strtotime($row["issue_date"])) ?></td>

        <td><?= date("d.m.Y", strtotime($row["due_date"])) ?></td>

        <td>

            <span class="badge bg-danger fs-6">
                <i class="bi bi-clock-history"></i>
                <?= $row["overdue_days"] ?> дн.
            </span>

        </td>

    </tr>

    <?php endwhile; ?>

    </tbody>

    </table>

<?php endif; ?>

</div>

</div>

</div>

<?php include "../../includes/footer.php"; ?>

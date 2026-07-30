<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/db.php";

$sql = "
SELECT

readers.full_name,

COUNT(borrowings.id) AS total

FROM borrowings

LEFT JOIN readers
ON borrowings.reader_id = readers.id

GROUP BY readers.id

ORDER BY total DESC

LIMIT 10
";

$result = $conn->query($sql);

$rows = [];
$maxTotal = 1;

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $maxTotal = max($maxTotal, (int) $row["total"]);
}

include "../../includes/header.php";
include "../../includes/navbar.php";
?>

<div class="container mt-5 mb-5">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2 class="d-flex align-items-center gap-2">
        <i class="bi bi-people-fill text-success"></i>
        Самые активные читатели
    </h2>

    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
        Назад
    </a>

</div>

<div class="card border-0 shadow">

<div class="card-body">

<?php if (empty($rows)): ?>

    <div class="text-center text-muted py-5">
        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
        Пока нет данных о выдачах.
    </div>

<?php else: ?>

    <table class="table table-hover align-middle mb-0">

    <thead class="table-dark">

    <tr>

        <th width="70">№</th>

        <th>Читатель</th>

        <th width="280">Книг взято</th>

    </tr>

    </thead>

    <tbody>

    <?php $i = 1; foreach ($rows as $row): ?>

    <tr>

        <td class="text-center fs-5">

            <?php if ($i === 1): ?>
                🥇
            <?php elseif ($i === 2): ?>
                🥈
            <?php elseif ($i === 3): ?>
                🥉
            <?php else: ?>
                <span class="text-muted"><?= $i ?></span>
            <?php endif; ?>

        </td>

        <td class="fw-bold"><?= htmlspecialchars($row["full_name"]) ?></td>

        <td>

            <div class="d-flex align-items-center gap-2">

                <div class="progress flex-grow-1" style="height:10px;">
                    <div
                        class="progress-bar bg-success"
                        style="width: <?= round(($row["total"] / $maxTotal) * 100) ?>%;">
                    </div>
                </div>

                <span class="badge bg-success"><?= $row["total"] ?></span>

            </div>

        </td>

    </tr>

    <?php $i++; endforeach; ?>

    </tbody>

    </table>

<?php endif; ?>

</div>

</div>

</div>

<?php include "../../includes/footer.php"; ?>

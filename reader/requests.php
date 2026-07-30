<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "reader") {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

$stmt = $conn->prepare("
SELECT id
FROM readers
WHERE user_id=?
");

$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$reader = $stmt->get_result()->fetch_assoc();

$reader_id = $reader["id"];

$result = $conn->query("

SELECT

requests.*,

books.title,
books.image

FROM requests

LEFT JOIN books
ON requests.book_id=books.id

WHERE reader_id=$reader_id

ORDER BY requests.id DESC

");

$totalFound = $result->num_rows;

include "../includes/header.php";
include "navbar.php";
?>

<div class="container mt-5 mb-5">

    <h2 class="mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-envelope-paper-fill text-primary"></i>
        Мои заявки
    </h2>

    <div class="card border-0 shadow">

        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-dark">

                    <tr>

                        <th style="width:70px;">Обложка</th>

                        <th>Книга</th>

                        <th>Дата заявки</th>

                        <th>Статус</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if ($totalFound === 0): ?>

                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                У вас пока нет заявок.
                                <br>
                                <a href="catalog.php" class="btn btn-primary btn-sm mt-3">
                                    <i class="bi bi-book-half"></i>
                                    Перейти в каталог
                                </a>
                            </td>
                        </tr>

                    <?php endif; ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td>

                            <?php if (!empty($row["image"])): ?>

                                <img
                                    src="../assets/images/<?= htmlspecialchars($row["image"]) ?>"
                                    class="rounded"
                                    style="width:48px;height:64px;object-fit:cover;">

                            <?php else: ?>

                                <img
                                    src="../assets/images/no-cover.png"
                                    class="rounded"
                                    style="width:48px;height:64px;object-fit:cover;">

                            <?php endif; ?>

                        </td>

                        <td class="fw-bold">

                            <?= htmlspecialchars($row["title"]) ?>

                        </td>

                        <td>

                            <?= date("d.m.Y", strtotime($row["request_date"])) ?>

                        </td>

                        <td>

                            <?php

                            if ($row["status"] == "Ожидает") {

                                echo "<span class='badge bg-warning text-dark'>
                                <i class='bi bi-hourglass-split'></i> Ожидает
                                </span>";

                            } elseif ($row["status"] == "Одобрена") {

                                echo "<span class='badge bg-success'>
                                <i class='bi bi-check-circle'></i> Одобрена
                                </span>";

                            } else {

                                echo "<span class='badge bg-danger'>
                                <i class='bi bi-x-circle'></i> Отклонена
                                </span>";

                            }

                            ?>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>
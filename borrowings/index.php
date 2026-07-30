<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

/*
|--------------------------------------------------------------------------
| Поиск и фильтр по статусу
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");
$statusFilter = $_GET["status"] ?? "";

$sql = "
SELECT
    borrowings.*,
    books.title,
    readers.full_name
FROM borrowings
LEFT JOIN books ON borrowings.book_id = books.id
LEFT JOIN readers ON borrowings.reader_id = readers.id
WHERE 1=1
";

$params = [];
$types = "";

if ($search !== "") {

    $sql .= " AND (books.title LIKE ? OR readers.full_name LIKE ?) ";

    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";

}

if ($statusFilter === "Выдана") {

    $sql .= " AND borrowings.status = 'Выдана' AND borrowings.due_date >= CURDATE() ";

} elseif ($statusFilter === "Просрочена") {

    $sql .= " AND borrowings.status = 'Выдана' AND borrowings.due_date < CURDATE() ";

} elseif ($statusFilter === "Возвращена") {

    $sql .= " AND borrowings.status = 'Возвращена' ";

}

$sql .= " ORDER BY borrowings.id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

$totalFound = $result->num_rows;

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="bi bi-journal-check"></i>
            Выдача книг
        </h2>

        <a href="add.php" class="btn btn-success">

            <i class="bi bi-plus-circle"></i>

            Выдать книгу

        </a>

    </div>

    <!-- ================= ПОИСК И ФИЛЬТР ================= -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        <i class="bi bi-search"></i>
                        Поиск
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Книга или читатель..."
                        value="<?= htmlspecialchars($search) ?>">

                </div>

                <div class="col-md-4">

                    <label class="form-label fw-bold">Статус</label>

                    <select name="status" class="form-select">

                        <option value="">Все статусы</option>

                        <option value="Выдана" <?= $statusFilter === "Выдана" ? "selected" : "" ?>>
                            Выдана (в срок)
                        </option>

                        <option value="Просрочена" <?= $statusFilter === "Просрочена" ? "selected" : "" ?>>
                            Просрочена
                        </option>

                        <option value="Возвращена" <?= $statusFilter === "Возвращена" ? "selected" : "" ?>>
                            Возвращена
                        </option>

                    </select>

                </div>

                <div class="col-md-2 d-grid">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                        Найти
                    </button>

                </div>

            </form>

            <?php if ($search !== "" || $statusFilter !== ""): ?>

                <div class="mt-3 d-flex align-items-center gap-3 flex-wrap">

                    <span class="text-muted">
                        Найдено: <strong><?= $totalFound ?></strong>
                    </span>

                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                        Сбросить
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">

            <tr>

                <th>ID</th>

                <th>Книга</th>

                <th>Читатель</th>

                <th>Дата выдачи</th>

                <th>Вернуть до</th>

                <th>Дата возврата</th>

                <th>Статус</th>

                <th width="170">Действия</th>

            </tr>

        </thead>

        <tbody>

<?php if ($totalFound === 0): ?>

    <tr>
        <td colspan="8" class="text-center text-muted py-4">
            Ничего не найдено по заданным критериям.
        </td>
    </tr>

<?php endif; ?>

<?php while($row = $result->fetch_assoc()): ?>

<?php

$isOverdue = (
    $row["status"] == "Выдана"
    &&
    strtotime($row["due_date"]) < time()
);

?>

<tr class="<?= $isOverdue ? 'table-danger' : '' ?>">

<td><?= $row["id"] ?></td>

<td><?= htmlspecialchars($row["title"]) ?></td>

<td><?= htmlspecialchars($row["full_name"]) ?></td>

<td><?= $row["issue_date"] ?></td>

<td><?= $row["due_date"] ?></td>

<td>

<?= $row["return_date"] ? $row["return_date"] : "-" ?>

</td>

<td>

<?php

if ($row["status"] == "Выдана") {

    $days = floor(
        (time() - strtotime($row["due_date"])) / 86400
    );

    if ($days > 0) {

        echo "<span class='badge bg-danger'>
        Просрочена
        </span><br>";

        echo "<small class='text-danger'>
        {$days} дн.
        </small>";

    } else {

        echo "<span class='badge bg-success'>
        Выдана
        </span>";

    }

} else {

    echo "<span class='badge bg-secondary'>
    Возвращена
    </span>";

}

?>

</td>

<td>

<?php if($row["status"]=="Выдана"): ?>

<a
href="return.php?id=<?= $row["id"] ?>"
class="btn btn-primary btn-sm">

<i class="bi bi-arrow-return-left"></i>

</a>

<?php endif; ?>

<a
href="delete.php?id=<?= $row["id"] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Удалить запись?')">

<i class="bi bi-trash"></i>

</a>

</td>

</tr>

<?php endwhile; ?>

        </tbody>

    </table>

</div>

<?php include "../includes/footer.php"; ?>

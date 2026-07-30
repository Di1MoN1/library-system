<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| Поиск и фильтр по статусу
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");
$statusFilter = $_GET["status"] ?? "";

$sql = "
SELECT

requests.id,
requests.reader_id,
requests.book_id,
requests.request_date,
requests.visit_date,
requests.visit_time,
requests.comment,
requests.status,

books.title,

readers.full_name

FROM requests

LEFT JOIN books
ON requests.book_id = books.id

LEFT JOIN readers
ON requests.reader_id = readers.id

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

if (in_array($statusFilter, ["Ожидает", "Одобрена", "Отклонена"])) {

    $sql .= " AND requests.status = ? ";
    $params[] = $statusFilter;
    $types .= "s";

}

$sql .= " ORDER BY requests.id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

$totalFound = $result->num_rows;

include "../../includes/header.php";
include "../../includes/navbar.php";
?>

<div class="container mt-5">

<h2 class="mb-4">

📩 Заявки читателей

</h2>

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
                    placeholder="Читатель или книга..."
                    value="<?= htmlspecialchars($search) ?>">

            </div>

            <div class="col-md-4">

                <label class="form-label fw-bold">Статус</label>

                <select name="status" class="form-select">

                    <option value="">Все статусы</option>

                    <option value="Ожидает" <?= $statusFilter === "Ожидает" ? "selected" : "" ?>>
                        🟡 Ожидает
                    </option>

                    <option value="Одобрена" <?= $statusFilter === "Одобрена" ? "selected" : "" ?>>
                        🟢 Одобрена
                    </option>

                    <option value="Отклонена" <?= $statusFilter === "Отклонена" ? "selected" : "" ?>>
                        🔴 Отклонена
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

<div class="card shadow">

<div class="card-body">

<table class="table table-hover table-bordered align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Читатель</th>

<th>Книга</th>

<th>Дата заявки</th>

<th>Дата прихода</th>

<th>Время</th>

<th>Комментарий</th>

<th>Статус</th>

<th width="220">

Действия

</th>

</tr>

</thead>

<tbody>

<?php if ($totalFound === 0): ?>

    <tr>
        <td colspan="9" class="text-center text-muted py-4">
            Ничего не найдено по заданным критериям.
        </td>
    </tr>

<?php endif; ?>

<?php while($row=$result->fetch_assoc()): ?>

<tr>

<td>

<?= $row["id"] ?>

</td>

<td>

<?= htmlspecialchars($row["full_name"]) ?>

</td>

<td>

<?= htmlspecialchars($row["title"]) ?>

</td>

<td>

<?= date("d.m.Y", strtotime($row["request_date"])) ?>

</td>

<td>

<?php

if(!empty($row["visit_date"])){

    $today = date("Y-m-d");
    $tomorrow = date("Y-m-d", strtotime("+1 day"));

    if($row["visit_date"] == $today){

        echo "<span class='badge bg-success'>
        🟢 Сегодня
        </span><br>";

    }

    elseif($row["visit_date"] == $tomorrow){

        echo "<span class='badge bg-warning text-dark'>
        🟡 Завтра
        </span><br>";

    }

    elseif($row["visit_date"] < $today){

        echo "<span class='badge bg-danger'>
        🔴 Не пришёл
        </span><br>";

    }

    else{

        echo "<span class='badge bg-primary'>
        🔵 Запланировано
        </span><br>";

    }

    echo date("d.m.Y", strtotime($row["visit_date"]));

}else{

    echo "<span class='text-muted'>—</span>";

}

?>

</td>

<td>

<?= !empty($row["visit_time"])
? substr($row["visit_time"],0,5)
: "<span class='text-muted'>—</span>" ?>

</td>

<td style="min-width:220px;">

<?= !empty($row["comment"])
? nl2br(htmlspecialchars($row["comment"]))
: "<span class='text-muted'>Без комментария</span>" ?>

</td>

<td>

<?php

if($row["status"]=="Ожидает"){

echo "<span class='badge bg-warning text-dark'>Ожидает</span>";

}elseif($row["status"]=="Одобрена"){

echo "<span class='badge bg-success'>Одобрена</span>";

}else{

echo "<span class='badge bg-danger'>Отклонена</span>";

}

?>

</td>

<td>

<?php if($row["status"]=="Ожидает"): ?>

<a
href="approve.php?id=<?= $row["id"] ?>"
class="btn btn-success btn-sm mb-1">

<i class="bi bi-check-circle"></i>

Одобрить

</a>

<a
href="reject.php?id=<?= $row["id"] ?>"
class="btn btn-danger btn-sm">

<i class="bi bi-x-circle"></i>

Отклонить

</a>

<?php elseif($row["status"]=="Одобрена"): ?>

<a
href="issue.php?id=<?= $row["id"] ?>"
class="btn btn-primary btn-sm">

<i class="bi bi-journal-check"></i>

📚 Выдать книгу

</a>

<?php else: ?>

—

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</div>

<?php include "../../includes/footer.php"; ?>

<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

$result = $conn->query("
SELECT

return_requests.id,
books.title,
readers.full_name,

return_requests.request_date,
return_requests.visit_date,
return_requests.visit_time,
return_requests.comment,
return_requests.status

FROM return_requests

LEFT JOIN borrowings
ON return_requests.borrowing_id = borrowings.id

LEFT JOIN books
ON borrowings.book_id = books.id

LEFT JOIN readers
ON return_requests.reader_id = readers.id

WHERE return_requests.status='Ожидает'

ORDER BY return_requests.visit_date,
return_requests.visit_time
");

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

<h2>

↩ Заявки на возврат

</h2>

<table class="table table-bordered table-hover mt-4">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Книга</th>

<th>Читатель</th>

<th>Дата заявки</th>

<th>Дата прихода</th>

<th>Время</th>

<th>Комментарий</th>

<th>Статус</th>

<th>Действие</th>

</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td><?= $row["id"] ?></td>

<td><?= htmlspecialchars($row["title"]) ?></td>

<td><?= htmlspecialchars($row["full_name"]) ?></td>

<td><?= date("d.m.Y", strtotime($row["request_date"])) ?></td>

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
        🔴 Просрочена
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
: "—" ?>

</td>

<td>

<?= !empty($row["comment"])
? nl2br(htmlspecialchars($row["comment"]))
: "<span class='text-muted'>Без комментария</span>" ?>

</td>

<td>

<span class="badge bg-warning">

<?= $row["status"] ?>

</span>

</td>

<td>

<a
href="approve.php?id=<?= $row["id"] ?>"
class="btn btn-success btn-sm">

📚 Принять книгу

</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php include "../includes/footer.php"; ?>
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

// Книги, которые есть в наличии
$books = $conn->query("
SELECT *
FROM books
WHERE copies > 0
ORDER BY title
");

// Читатели
$readers = $conn->query("
SELECT *
FROM readers
ORDER BY full_name
");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $book_id = (int)$_POST["book_id"];
    $reader_id = (int)$_POST["reader_id"];

    $issue_date = $_POST["issue_date"];
    $due_date = $_POST["due_date"];

    // Проверяем остаток книги
    $book = $conn->query("
        SELECT copies
        FROM books
        WHERE id=$book_id
    ")->fetch_assoc();

    if ($book["copies"] <= 0) {

        $error = "Данной книги больше нет в наличии.";

    } else {

        // Выдаем книгу
        $stmt = $conn->prepare("
        INSERT INTO borrowings
        (book_id,reader_id,issue_date,due_date,status)
        VALUES (?,?,?,?,?)
        ");

        $status = "Выдана";

        $stmt->bind_param(
            "iisss",
            $book_id,
            $reader_id,
            $issue_date,
            $due_date,
            $status
        );

        if ($stmt->execute()) {

            // уменьшаем количество экземпляров
            $conn->query("
            UPDATE books
            SET copies = copies - 1
            WHERE id=$book_id
            ");

            header("Location: index.php");
            exit;

        } else {

            $error = "Ошибка выдачи.";

        }

    }

}

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5" style="max-width:700px;">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>

<i class="bi bi-journal-check"></i>

Выдать книгу

</h3>

</div>

<div class="card-body">

<?php if($error!=""): ?>

<div class="alert alert-danger">

<?= $error ?>

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Книга

</label>

<select
name="book_id"
class="form-select"
required>

<option value="">Выберите книгу</option>

<?php while($book=$books->fetch_assoc()): ?>

<option value="<?= $book["id"] ?>">

<?= htmlspecialchars($book["title"]) ?>

(Осталось: <?= $book["copies"] ?>)

</option>

<?php endwhile; ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Читатель

</label>

<select
name="reader_id"
class="form-select"
required>

<option value="">Выберите читателя</option>

<?php while($reader=$readers->fetch_assoc()): ?>

<option value="<?= $reader["id"] ?>">

<?= htmlspecialchars($reader["full_name"]) ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Дата выдачи

</label>

<input
type="date"
id="issue_date"
name="issue_date"
class="form-control"
value="<?= date('Y-m-d') ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Вернуть до

</label>

<input
type="date"
id="due_date"
name="due_date"
class="form-control"
value="<?= date('Y-m-d', strtotime('+14 days')) ?>"
required>

</div>

<button class="btn btn-success">

<i class="bi bi-check-circle"></i>

Выдать книгу

</button>

<a href="index.php" class="btn btn-secondary">

Отмена

</a>

</form>

</div>

</div>

</div>

<script>

document.getElementById("issue_date").addEventListener("change", function(){

    let date = new Date(this.value);

    date.setDate(date.getDate() + 14);

    let year = date.getFullYear();
    let month = String(date.getMonth()+1).padStart(2,'0');
    let day = String(date.getDate()).padStart(2,'0');

    document.getElementById("due_date").value =
        year + "-" + month + "-" + day;

});

</script>

<?php include "../includes/footer.php"; ?>
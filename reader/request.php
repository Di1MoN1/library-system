<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "reader") {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

if (!isset($_GET["book_id"])) {
    header("Location: catalog.php");
    exit;
}

$book_id = (int)$_GET["book_id"];

// Получаем читателя
$stmt = $conn->prepare("
SELECT id
FROM readers
WHERE user_id=?
");

$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$reader = $stmt->get_result()->fetch_assoc();

if (!$reader) {
    die("Читатель не найден.");
}

$reader_id = $reader["id"];

// Если форма отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $visit_date = $_POST["visit_date"];
    $visit_time = $_POST["visit_time"];
    $comment = trim($_POST["comment"]);

    // Проверяем существующую заявку
    $stmt = $conn->prepare("
    SELECT id
    FROM requests
    WHERE reader_id=?
    AND book_id=?
    AND status='Ожидает'
    ");

    $stmt->bind_param("ii", $reader_id, $book_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {

        echo "<script>
        alert('Вы уже отправили заявку на эту книгу.');
        location='catalog.php';
        </script>";

        exit;
    }

    // Добавляем заявку
    $stmt = $conn->prepare("
    INSERT INTO requests
    (
        reader_id,
        book_id,
        request_date,
        visit_date,
        visit_time,
        comment,
        status
    )
    VALUES
    (
        ?,
        ?,
        CURDATE(),
        ?,
        ?,
        ?,
        'Ожидает'
    )
    ");

    $stmt->bind_param(
        "iisss",
        $reader_id,
        $book_id,
        $visit_date,
        $visit_time,
        $comment
    );

    $stmt->execute();

    echo "<script>
    alert('Заявка успешно отправлена.');
    location='requests.php';
    </script>";

    exit;
}

include "../includes/header.php";
include "navbar.php";
?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

📚 Запись на получение книги

</h4>

</div>

<div class="card-body">

<form method="post">

<div class="mb-3">

<label class="form-label">

Дата посещения

</label>

<input
type="date"
name="visit_date"
class="form-control"
required
min="<?= date('Y-m-d') ?>">

</div>

<div class="mb-3">

<label class="form-label">

Время посещения

</label>

<input
type="time"
name="visit_time"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Комментарий (необязательно)

</label>

<textarea
name="comment"
class="form-control"
rows="3"
placeholder="Например: приду после работы"></textarea>

</div>

<button
class="btn btn-success w-100">

📚 Отправить заявку

</button>

</form>

</div>

</div>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
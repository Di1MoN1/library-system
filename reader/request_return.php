<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "reader") {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

if (!isset($_GET["id"])) {
    header("Location: my_books.php");
    exit;
}

$borrowing_id = (int)$_GET["id"];

// Получаем reader_id
$stmt = $conn->prepare("
SELECT id
FROM readers
WHERE user_id=?
");

$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$reader = $stmt->get_result()->fetch_assoc();

$reader_id = $reader["id"];

// Если отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $visit_date = $_POST["visit_date"];
    $visit_time = $_POST["visit_time"];
    $comment = trim($_POST["comment"]);

    // Проверяем, нет ли уже заявки
    $stmt = $conn->prepare("
    SELECT id
    FROM return_requests
    WHERE borrowing_id=?
    AND status='Ожидает'
    ");

    $stmt->bind_param("i", $borrowing_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows == 0) {

        $today = date("Y-m-d");

        $stmt = $conn->prepare("
        INSERT INTO return_requests
        (
            borrowing_id,
            reader_id,
            request_date,
            visit_date,
            visit_time,
            comment
        )
        VALUES
        (
            ?,?,?,?,?,?
        )
        ");

        $stmt->bind_param(
            "iissss",
            $borrowing_id,
            $reader_id,
            $today,
            $visit_date,
            $visit_time,
            $comment
        );

        $stmt->execute();
    }

    echo "<script>
    alert('Заявка на возврат отправлена.');
    location='my_books.php';
    </script>";

    exit;
}

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-dark text-white">

<h3>

↩ Заявка на возврат книги

</h3>

</div>

<div class="card-body">

<form method="post">

<div class="mb-3">

<label class="form-label">

Дата прихода

</label>

<input
type="date"
name="visit_date"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Время прихода

</label>

<input
type="time"
name="visit_time"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Комментарий

</label>

<textarea
name="comment"
class="form-control"
rows="4"
placeholder="Например: приду после работы"></textarea>

</div>

<button
class="btn btn-success">

📨 Отправить заявку

</button>

<a
href="my_books.php"
class="btn btn-secondary">

Отмена

</a>

</form>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
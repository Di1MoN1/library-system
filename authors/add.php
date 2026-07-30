<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"]);

    if ($full_name != "") {

        $stmt = $conn->prepare("INSERT INTO authors(full_name) VALUES(?)");
        $stmt->bind_param("s", $full_name);

        if ($stmt->execute()) {

            header("Location: index.php");
            exit;

        } else {

            $error = "Ошибка при добавлении автора.";

        }

    } else {

        $error = "Введите имя автора.";

    }

}

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>➕ Добавить автора</h3>

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

ФИО автора

</label>

<input
type="text"
name="full_name"
class="form-control"
required>

</div>

<button class="btn btn-success">

Сохранить

</button>

<a href="index.php" class="btn btn-secondary">

Назад

</a>

</form>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
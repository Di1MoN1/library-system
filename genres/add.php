<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);

    if ($name != "") {

        $stmt = $conn->prepare("INSERT INTO genres(name) VALUES(?)");
        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {

            header("Location: index.php");
            exit;

        } else {

            $error = "Ошибка при добавлении жанра.";

        }

    } else {

        $error = "Введите название жанра.";

    }

}

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>➕ Добавить жанр</h3>

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

Название жанра

</label>

<input
type="text"
name="name"
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
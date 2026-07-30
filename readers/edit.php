<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET["id"];

$stmt = $conn->prepare("SELECT * FROM readers WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$reader = $result->fetch_assoc();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);

    $errors = [];

    if ($full_name === "" || mb_strlen($full_name) < 2) {
        $errors[] = "Укажите ФИО читателя (не менее 2 символов).";
    }

    if ($phone !== "" && !preg_match('/^[0-9+\-\s()]{5,20}$/', $phone)) {
        $errors[] = "Телефон может содержать только цифры, пробелы, +, - и скобки.";
    }

    if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный формат email.";
    }

    if (empty($errors)) {

    $stmt = $conn->prepare("
        UPDATE readers
        SET
            full_name=?,
            phone=?,
            email=?,
            address=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssssi",
        $full_name,
        $phone,
        $email,
        $address,
        $id
    );

    if ($stmt->execute()) {

        header("Location: index.php");
        exit;

    } else {

        $error = "Ошибка сохранения.";

    }

    } else {

        $error = implode("<br>", $errors);

        $reader["full_name"] = $full_name;
        $reader["phone"] = $phone;
        $reader["email"] = $email;
        $reader["address"] = $address;

    }

}

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5" style="max-width:700px;">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>

<i class="bi bi-pencil-square"></i>

Редактировать читателя

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

ФИО

</label>

<input
type="text"
name="full_name"
class="form-control"
required
value="<?= htmlspecialchars($reader["full_name"]) ?>">

</div>

<div class="mb-3">

<label class="form-label">

Телефон

</label>

<input
type="text"
name="phone"
class="form-control"
pattern="[0-9+\-\s()]{5,20}"
title="Только цифры, пробелы, +, - и скобки"
value="<?= htmlspecialchars($reader["phone"]) ?>">

</div>

<div class="mb-3">

<label class="form-label">

Email

</label>

<input
type="email"
name="email"
class="form-control"
value="<?= htmlspecialchars($reader["email"]) ?>">

</div>

<div class="mb-3">

<label class="form-label">

Адрес

</label>

<textarea
name="address"
class="form-control"
rows="3"><?= htmlspecialchars($reader["address"]) ?></textarea>

</div>

<button class="btn btn-warning">

<i class="bi bi-check-circle"></i>

Сохранить

</button>

<a href="index.php" class="btn btn-secondary">

Отмена

</a>

</form>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
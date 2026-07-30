<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "reader") {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

$user_id = $_SESSION["user_id"];

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $old = $_POST["old_password"];
    $new = $_POST["new_password"];
    $confirm = $_POST["confirm_password"];

    // Получаем пароль пользователя
    $stmt = $conn->prepare("
    SELECT password
    FROM users
    WHERE id=?
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    // Проверяем старый пароль
    if (
        !password_verify($old, $user["password"])
        && $old !== $user["password"]
    ) {

        $error = "Старый пароль неверный.";

    } elseif ($new != $confirm) {

        $error = "Новые пароли не совпадают.";

    } elseif (strlen($new) < 6) {

        $error = "Пароль должен содержать минимум 6 символов.";

    } else {

        $hash = password_hash($new, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
        UPDATE users
        SET password=?
        WHERE id=?
        ");

        $stmt->bind_param(
            "si",
            $hash,
            $user_id
        );

        $stmt->execute();

        $success = "Пароль успешно изменён.";

    }

}

include "../includes/header.php";
include "navbar.php";
?>

<div class="container mt-5" style="max-width:600px;">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>

🔒 Смена пароля

</h3>

</div>

<div class="card-body">

<?php if($error!=""): ?>

<div class="alert alert-danger">

<?= $error ?>

</div>

<?php endif; ?>

<?php if($success!=""): ?>

<div class="alert alert-success">

<?= $success ?>

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label>

Старый пароль

</label>

<input
type="password"
name="old_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Новый пароль

</label>

<input
type="password"
name="new_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Повторите новый пароль

</label>

<input
type="password"
name="confirm_password"
class="form-control"
required>

</div>

<button class="btn btn-success">

💾 Изменить пароль

</button>

<a
href="profile.php"
class="btn btn-secondary">

Назад

</a>

</form>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
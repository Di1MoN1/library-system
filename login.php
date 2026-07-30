<?php
require_once "config/db.php";

session_start();

// Если пользователь уже вошел
if (isset($_SESSION["user_id"])) {

    if ($_SESSION["role"] == "reader") {
        header("Location: reader/index.php");
    } else {
        header("Location: admin/index.php");
    }

    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("
        SELECT *
        FROM users
        WHERE username=?
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Проверяем пароль
        if (
            password_verify($password, $user["password"])
            || $password === $user["password"]
        ) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            // Перенаправление по роли
            if ($user["role"] == "reader") {

                header("Location: reader/index.php");

            } else {

                header("Location: admin/index.php");

            }

            exit;

        } else {

            $error = "Неверный пароль.";

        }

    } else {

        $error = "Пользователь не найден.";

    }

}

include "includes/header.php";
?>

<div class="container mt-5" style="max-width:500px;">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>

<i class="bi bi-box-arrow-in-right"></i>

Вход в систему

</h3>

</div>

<div class="card-body">

<?php if($error): ?>

<div class="alert alert-danger">

<?= $error ?>

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Логин

</label>

<input
type="text"
name="username"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Пароль

</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<button
type="submit"
class="btn btn-primary w-100">

<i class="bi bi-box-arrow-in-right me-2"></i>

Войти

</button>

<a
href="reader/register.php"
class="btn btn-success w-100 mt-2">

<i class="bi bi-person-plus-fill me-2"></i>

Регистрация читателя

</a>

<a
href="index.php"
class="btn btn-secondary w-100 mt-2">

<i class="bi bi-arrow-left me-2"></i>

На главную

</a>

</form>

</div>

</div>

</div>

<?php include "includes/footer.php"; ?>
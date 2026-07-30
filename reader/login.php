<?php
session_start();

require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("
        SELECT *
        FROM users
        WHERE username = ?
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {

        if ($user["role"] != "reader") {

            $error = "Это не учетная запись читателя.";

        } else {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            header("Location: index.php");
            exit;

        }

    } else {

        $error = "Неверный логин или пароль.";

    }

}

include "../includes/header.php";
?>

<div class="container mt-5" style="max-width:500px;">

    <div class="card shadow">

        <div class="card-header bg-success text-white">

            <h3>Вход читателя</h3>

        </div>

        <div class="card-body">

            <?php if(isset($_GET["registered"])): ?>

                <div class="alert alert-success">

                    Регистрация прошла успешно! Теперь войдите в систему.

                </div>

            <?php endif; ?>

            <?php if($error!=""): ?>

                <div class="alert alert-danger">

                    <?= $error ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label>Логин</label>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        required>

                </div>

                <div class="mb-3">

                    <label>Пароль</label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required>

                </div>

                <button class="btn btn-success w-100">

                    Войти

                </button>

            </form>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>
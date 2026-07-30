<?php
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password != $confirm) {
        $error = "Пароли не совпадают.";
    }

    if ($error == "" && ($fullname === "" || mb_strlen($fullname) < 2)) {
        $error = "Укажите ФИО (не менее 2 символов).";
    }

    if ($error == "" && !preg_match('/^[0-9+\-\s()]{5,20}$/', $phone)) {
        $error = "Телефон может содержать только цифры, пробелы, +, - и скобки.";
    }

    if ($error == "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Некорректный формат email.";
    }

    if ($error == "" && !preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        $error = "Логин должен содержать от 3 до 30 символов: латинские буквы, цифры, знак подчёркивания.";
    }

    if ($error == "" && strlen($password) < 6) {
        $error = "Пароль должен содержать не менее 6 символов.";
    }

    if ($error == "") {

        $check = $conn->prepare("
            SELECT id
            FROM users
            WHERE username = ?
        ");

        $check->bind_param("s", $username);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {

            $error = "Такой логин уже существует.";

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $role = "reader";

            $stmt = $conn->prepare("
                INSERT INTO users(username,password,role)
                VALUES(?,?,?)
            ");

            $stmt->bind_param(
                "sss",
                $username,
                $hash,
                $role
            );

            if ($stmt->execute()) {

                $user_id = $conn->insert_id;

                $stmt2 = $conn->prepare("
                    INSERT INTO readers(user_id,full_name,phone,email)
                    VALUES(?,?,?,?)
                ");

                $stmt2->bind_param(
                    "isss",
                    $user_id,
                    $fullname,
                    $phone,
                    $email
                );

                $stmt2->execute();

                // Переход на страницу входа
                header("Location: login.php?registered=1");
                exit;

            } else {

                $error = "Ошибка регистрации.";

            }

        }

    }

}

include "../includes/header.php";
?>

<div class="container mt-5" style="max-width:700px;">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h3>Регистрация читателя</h3>

        </div>

        <div class="card-body">

            <?php if ($error != ""): ?>

                <div class="alert alert-danger">

                    <?= $error ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label>ФИО</label>

                    <input type="text" name="fullname" class="form-control"
                        value="<?= htmlspecialchars($_POST["fullname"] ?? "") ?>" required>

                </div>

                <div class="mb-3">

                    <label>Телефон</label>

                    <input type="text" name="phone" class="form-control"
                        pattern="[0-9+\-\s()]{5,20}" title="Только цифры, пробелы, +, - и скобки"
                        value="<?= htmlspecialchars($_POST["phone"] ?? "") ?>" required>

                </div>

                <div class="mb-3">

                    <label>Email</label>

                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($_POST["email"] ?? "") ?>" required>

                </div>

                <div class="mb-3">

                    <label>Логин</label>

                    <input type="text" name="username" class="form-control"
                        pattern="[A-Za-z0-9_]{3,30}" title="3-30 символов: латинские буквы, цифры, подчёркивание"
                        value="<?= htmlspecialchars($_POST["username"] ?? "") ?>" required>

                </div>

                <div class="mb-3">

                    <label>Пароль</label>

                    <input type="password" name="password" class="form-control" minlength="6" required>

                </div>

                <div class="mb-3">

                    <label>Повторите пароль</label>

                    <input type="password" name="confirm" class="form-control" minlength="6" required>

                </div>

                <button class="btn btn-success w-100">

                    Зарегистрироваться

                </button>

                <a href="index.php" class="btn btn-outline-secondary">
                    ← Назад
                </a>

            </form>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>
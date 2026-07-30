<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["role"] != "superadmin") {
    header("Location: ../admin/index.php");
    exit;
}

require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    // Проверяем логин
    $stmt = $conn->prepare("
        SELECT id
        FROM users
        WHERE username=?
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {

        $error = "Такой логин уже существует.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO users(username,password,role)
            VALUES(?,?,?)
        ");

        $stmt->bind_param(
            "sss",
            $username,
            $password,
            $role
        );

        if ($stmt->execute()) {

            header("Location: index.php");
            exit;

        } else {

            $error = "Ошибка добавления.";

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
                <i class="bi bi-person-plus-fill"></i>
                Добавить пользователя
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

                <div class="mb-3">

                    <label class="form-label">
                        Роль
                    </label>

                    <select
                        name="role"
                        class="form-select">

                        <option value="admin">
                            Обычный администратор
                        </option>

                        <option value="superadmin">
                            Главный администратор
                        </option>

                        <option value="reader">
                            Читатель
                        </option>

                    </select>

                </div>

                <button class="btn btn-success">
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
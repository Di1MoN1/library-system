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

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$user = $result->fetch_assoc();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    if (!empty($_POST["password"])) {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    } else {
        $password = $user["password"];
    }
    $role = $_POST["role"];

    // Проверяем, не занят ли логин другим пользователем
    $check = $conn->prepare("
        SELECT id
        FROM users
        WHERE username=? AND id<>?
    ");

    $check->bind_param("si", $username, $id);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        $error = "Такой логин уже существует.";

    } else {

        $stmt = $conn->prepare("
            UPDATE users
            SET
                username=?,
                password=?,
                role=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "sssi",
            $username,
            $password,
            $role,
            $id
        );

        if ($stmt->execute()) {

            // Если назначили роль "reader"
            if ($role == "reader") {

                $checkReader = $conn->prepare("
            SELECT id
            FROM readers
            WHERE user_id = ?
        ");

                $checkReader->bind_param("i", $id);
                $checkReader->execute();

                if ($checkReader->get_result()->num_rows == 0) {

                    $insertReader = $conn->prepare("
                INSERT INTO readers
                (
                    user_id,
                    full_name,
                    phone,
                    email
                )
                VALUES
                (
                    ?,
                    ?,
                    '',
                    ''
                )
            ");

                    // В качестве ФИО используем логин пользователя
                    $insertReader->bind_param(
                        "is",
                        $id,
                        $username
                    );

                    $insertReader->execute();

                }

            }

            header("Location: index.php");
            exit;

        } else {

            $error = "Ошибка сохранения.";

        }

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
                Редактирование пользователя
            </h3>

        </div>

        <div class="card-body">

            <?php if ($error != ""): ?>

                <div class="alert alert-danger">

                    <?= $error ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">

                        Логин

                    </label>

                    <input type="text" name="username" class="form-control" required
                        value="<?= htmlspecialchars($user["username"]) ?>">

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Пароль

                    </label>

                    <input type="password" name="password" class="form-control"
                        placeholder="Оставьте пустым, чтобы не менять пароль">

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Роль

                    </label>

                    <select name="role" class="form-select">

                        <option value="reader" <?= $user["role"] == "reader" ? "selected" : "" ?>>

                            Читатель

                        </option>

                        <option value="admin" <?= $user["role"] == "admin" ? "selected" : "" ?>>

                            Администратор

                        </option>

                        <option value="superadmin" <?= $user["role"] == "superadmin" ? "selected" : "" ?>>

                            Главный администратор

                        </option>

                    </select>

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
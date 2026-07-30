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

$result = $conn->query("
SELECT *
FROM users
ORDER BY id DESC
");

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="bi bi-people-fill"></i>
            Администраторы
        </h2>

        <a href="add.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i>
            Добавить администратора
        </a>

    </div>

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">

            <tr>

                <th>ID</th>
                <th>Логин</th>
                <th>Роль</th>
                <th width="170">Действия</th>

            </tr>

        </thead>

        <tbody>

        <?php while($user = $result->fetch_assoc()): ?>

        <tr>

            <td><?= $user["id"] ?></td>

            <td><?= htmlspecialchars($user["username"]) ?></td>

            <td><?= htmlspecialchars($user["role"]) ?></td>

            <td>

                <a
                    href="edit.php?id=<?= $user["id"] ?>"
                    class="btn btn-warning btn-sm">

                    ✏️

                </a>

                <a
                    href="delete.php?id=<?= $user["id"] ?>"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Удалить администратора?')">

                    🗑️

                </a>

            </td>

        </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>

<?php include "../includes/footer.php"; ?>
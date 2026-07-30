<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

/*
|--------------------------------------------------------------------------
| Поиск
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");

$sql = "SELECT * FROM readers WHERE 1=1";

$params = [];
$types = "";

if ($search !== "") {

    $sql .= " AND (full_name LIKE ? OR phone LIKE ? OR email LIKE ?) ";

    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";

}

$sql .= " ORDER BY full_name";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

$totalFound = $result->num_rows;

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="bi bi-people-fill"></i>
            Читатели
        </h2>

        <a href="add.php" class="btn btn-success">

            <i class="bi bi-person-plus-fill"></i>

            Добавить читателя

        </a>

    </div>

    <!-- ================= ПОИСК ================= -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-10">

                    <label class="form-label fw-bold">
                        <i class="bi bi-search"></i>
                        Поиск
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="ФИО, телефон или email..."
                        value="<?= htmlspecialchars($search) ?>">

                </div>

                <div class="col-md-2 d-grid">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                        Найти
                    </button>

                </div>

            </form>

            <?php if ($search !== ""): ?>

                <div class="mt-3 d-flex align-items-center gap-3 flex-wrap">

                    <span class="text-muted">
                        Найдено: <strong><?= $totalFound ?></strong>
                    </span>

                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                        Сбросить
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">

            <tr>

                <th>ID</th>

                <th>ФИО</th>

                <th>Телефон</th>

                <th>Email</th>

                <th>Дата регистрации</th>

                <th width="170">Действия</th>

            </tr>

        </thead>

        <tbody>

<?php if ($totalFound === 0): ?>

    <tr>
        <td colspan="6" class="text-center text-muted py-4">
            Ничего не найдено.
        </td>
    </tr>

<?php endif; ?>

<?php while($reader = $result->fetch_assoc()): ?>

<tr>

<td><?= $reader["id"] ?></td>

<td><?= htmlspecialchars($reader["full_name"]) ?></td>

<td><?= htmlspecialchars($reader["phone"]) ?></td>

<td><?= htmlspecialchars($reader["email"]) ?></td>

<td><?= $reader["registered_at"] ?></td>

<td>

<a
href="edit.php?id=<?= $reader["id"] ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

</a>

<a
href="delete.php?id=<?= $reader["id"] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Удалить читателя?')">

<i class="bi bi-trash"></i>

</a>

</td>

</tr>

<?php endwhile; ?>

        </tbody>

    </table>

</div>

<?php include "../includes/footer.php"; ?>

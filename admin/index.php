<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

/*
|--------------------------------------------------------------------------
| СТАТИСТИКА
|--------------------------------------------------------------------------
*/

$books = $conn->query("SELECT COUNT(*) total FROM books")->fetch_assoc()["total"];

$authors = $conn->query("SELECT COUNT(*) total FROM authors")->fetch_assoc()["total"];

$genres = $conn->query("SELECT COUNT(*) total FROM genres")->fetch_assoc()["total"];

$users = $conn->query("SELECT COUNT(*) total FROM users")->fetch_assoc()["total"];

$readers = $conn->query("SELECT COUNT(*) total FROM readers")->fetch_assoc()["total"];

$borrowed = $conn->query("
SELECT COUNT(*) total
FROM borrowings
WHERE status='Выдана'
")->fetch_assoc()["total"];

$returned = $conn->query("
SELECT COUNT(*) total
FROM borrowings
WHERE status='Возвращена'
")->fetch_assoc()["total"];

$overdue = $conn->query("
SELECT COUNT(*) total
FROM borrowings
WHERE status='Выдана'
AND due_date<CURDATE()
")->fetch_assoc()["total"];

/*
|--------------------------------------------------------------------------
| ПОСЛЕДНИЕ КНИГИ
|--------------------------------------------------------------------------
*/

$lastBooks = $conn->query("
SELECT
books.title,
authors.full_name

FROM books

LEFT JOIN authors
ON books.author_id=authors.id

ORDER BY books.id DESC

LIMIT 5
");

/*
|--------------------------------------------------------------------------
| ПОПУЛЯРНЫЕ КНИГИ
|--------------------------------------------------------------------------
*/

$popularBooks = $conn->query("
SELECT
books.title,
COUNT(borrowings.id) total

FROM borrowings

LEFT JOIN books
ON borrowings.book_id=books.id

GROUP BY books.id

ORDER BY total DESC

LIMIT 5
");

/*
|--------------------------------------------------------------------------
| ПОСЛЕДНИЕ ВЫДАЧИ
|--------------------------------------------------------------------------
*/

$lastBorrowings = $conn->query("
SELECT
books.title,
readers.full_name,
borrowings.issue_date,
borrowings.status

FROM borrowings

LEFT JOIN books
ON borrowings.book_id=books.id

LEFT JOIN readers
ON borrowings.reader_id=readers.id

ORDER BY borrowings.id DESC

LIMIT 5
");

include "../includes/header.php";
include "../includes/navbar.php";

/*
|--------------------------------------------------------------------------
| ТОП-5 самых активных читателей
|--------------------------------------------------------------------------
*/

$topReaders = $conn->query("
SELECT

readers.full_name,
COUNT(borrowings.id) AS total

FROM borrowings

LEFT JOIN readers
ON borrowings.reader_id = readers.id

GROUP BY readers.id

ORDER BY total DESC

LIMIT 5
");

// =======================================
// Кто сегодня придёт за книгой
// =======================================

$today = date("Y-m-d");

$todayVisitors = $conn->query("
SELECT

requests.visit_time,
books.title,
readers.full_name

FROM requests

LEFT JOIN books
ON requests.book_id = books.id

LEFT JOIN readers
ON requests.reader_id = readers.id

WHERE requests.status='Одобрена'
AND requests.visit_date='$today'

ORDER BY requests.visit_time ASC
");

/*
|--------------------------------------------------------------------------
| ДАННЫЕ ДЛЯ ГРАФИКА
|--------------------------------------------------------------------------
*/

$chartResult = $conn->query("
SELECT

DATE_FORMAT(issue_date,'%Y-%m') AS month,

COUNT(*) AS total

FROM borrowings

GROUP BY DATE_FORMAT(issue_date,'%Y-%m')

ORDER BY month ASC
");

$months = [];
$totals = [];

while ($row = $chartResult->fetch_assoc()) {

    $months[] = date("m.Y", strtotime($row["month"] . "-01"));
    $totals[] = (int) $row["total"];

}
?>


<div class="container mt-5 mb-5">

    <h2 class="mb-4">

        👋 Добро пожаловать,
        <strong><?= htmlspecialchars($_SESSION["username"]) ?></strong>

    </h2>

    <!-- ================= СТАТИСТИКА: КАТАЛОГ ================= -->

    <div class="row g-4">

        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card gradient-blue">
                <div class="card-body text-center">
                    <i class="bi bi-book-half fs-1 mb-3"></i>
                    <div class="dashboard-number"><?= $books ?></div>
                    <div class="dashboard-title">Книг</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card gradient-green">
                <div class="card-body text-center">
                    <i class="bi bi-person-lines-fill fs-1 mb-3"></i>
                    <div class="dashboard-number"><?= $authors ?></div>
                    <div class="dashboard-title">Авторов</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card gradient-orange">
                <div class="card-body text-center">
                    <i class="bi bi-tags-fill fs-1 mb-3"></i>
                    <div class="dashboard-number"><?= $genres ?></div>
                    <div class="dashboard-title">Жанров</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card gradient-red">
                <div class="card-body text-center">
                    <i class="bi bi-shield-lock-fill fs-1 mb-3"></i>
                    <div class="dashboard-number"><?= $users ?></div>
                    <div class="dashboard-title">Администраторов</div>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= СТАТИСТИКА: ОПЕРАЦИИ ================= -->

    <div class="row g-4 mt-1">

        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card gradient-blue">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill fs-1 mb-3"></i>
                    <div class="dashboard-number"><?= $readers ?></div>
                    <div class="dashboard-title">Читателей</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card gradient-green">
                <div class="card-body text-center">
                    <i class="bi bi-journal-check fs-1 mb-3"></i>
                    <div class="dashboard-number"><?= $borrowed ?></div>
                    <div class="dashboard-title">Выдано</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card gradient-orange">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-return-left fs-1 mb-3"></i>
                    <div class="dashboard-number"><?= $returned ?></div>
                    <div class="dashboard-title">Возвращено</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">

            <a href="../overdue/index.php" class="text-decoration-none">

                <div class="card dashboard-card gradient-red">

                    <div class="card-body text-center">

                        <i class="bi bi-exclamation-triangle-fill fs-1 mb-3"></i>

                        <div class="dashboard-number"><?= $overdue ?></div>

                        <div class="dashboard-title">Просрочено</div>

                    </div>

                </div>

            </a>

        </div>

    </div>

    <!-- ================= БЫСТРЫЕ ДЕЙСТВИЯ ================= -->

    <div class="row justify-content-center mt-4">

        <div class="col-lg-8">

            <div class="card shadow h-100">

                <div class="card-header bg-dark text-white">

                    <i class="bi bi-lightning-charge-fill"></i>
                    Быстрые действия

                </div>

                <div class="card-body">

                    <div class="row row-cols-1 row-cols-md-2 g-3">

                        <div class="col">
                            <a href="../books/add.php" class="btn btn-primary w-100">
                                <i class="bi bi-book-half me-2"></i>
                                Добавить книгу
                            </a>
                        </div>

                        <div class="col">
                            <a href="../authors/add.php" class="btn btn-success w-100">
                                <i class="bi bi-person-plus-fill me-2"></i>
                                Добавить автора
                            </a>
                        </div>

                        <div class="col">
                            <a href="../genres/add.php" class="btn btn-warning w-100">
                                <i class="bi bi-tags-fill me-2"></i>
                                Добавить жанр
                            </a>
                        </div>

                        <div class="col">
                            <a href="../readers/add.php" class="btn btn-info text-white w-100">
                                <i class="bi bi-person-vcard-fill me-2"></i>
                                Добавить читателя
                            </a>
                        </div>

                        <div class="col">
                            <a href="../borrowings/add.php" class="btn btn-secondary w-100">
                                <i class="bi bi-journal-check me-2"></i>
                                Выдать книгу
                            </a>
                        </div>

                        <?php if ($_SESSION["role"] == "superadmin"): ?>

                        <div class="col">
                            <a href="../users/index.php" class="btn btn-danger w-100">
                                <i class="bi bi-people-fill me-2"></i>
                                Управление администраторами
                            </a>
                        </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- ================= ПОПУЛЯРНЫЕ + ПОСЛЕДНИЕ ВЫДАЧИ ================= -->

    <div class="row mt-4">

        <div class="col-lg-6 mb-4">

            <div class="card shadow h-100">

                <div class="card-header bg-primary text-white">

                    🔥 Самые популярные книги

                </div>

                <ul class="list-group list-group-flush">

                    <?php while ($book = $popularBooks->fetch_assoc()): ?>

                    <li class="list-group-item d-flex justify-content-between">

                        <span><?= htmlspecialchars($book["title"]) ?></span>

                        <span class="badge bg-primary rounded-pill">

                            <?= $book["total"] ?>

                        </span>

                    </li>

                    <?php endwhile; ?>

                </ul>

            </div>

        </div>

        <div class="col-lg-6 mb-4">

            <div class="card shadow h-100">

                <div class="card-header bg-warning">

                    📝 Последние выдачи

                </div>

                <ul class="list-group list-group-flush">

                    <?php while ($row = $lastBorrowings->fetch_assoc()): ?>

                    <li class="list-group-item">

                        <strong><?= htmlspecialchars($row["title"]) ?></strong>

                        <br>

                        <small><?= htmlspecialchars($row["full_name"]) ?></small>

                        <span class="float-end badge bg-secondary">

                            <?= $row["issue_date"] ?>

                        </span>

                    </li>

                    <?php endwhile; ?>

                </ul>

            </div>

        </div>

    </div>

    <!-- ================= АКТИВНЫЕ ЧИТАТЕЛИ + ВИЗИТЫ СЕГОДНЯ ================= -->

    <div class="row">

        <div class="col-lg-6 mb-4">

            <div class="card shadow h-100">

                <div class="card-header bg-success text-white">

                    👤 Самые активные читатели

                </div>

                <div class="card-body">

                    <table class="table table-striped mb-0">

                        <thead>

                            <tr>

                                <th>Читатель</th>

                                <th class="text-end">Книг</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php while($reader = $topReaders->fetch_assoc()): ?>

                            <tr>

                                <td><?= htmlspecialchars($reader["full_name"]) ?></td>

                                <td class="text-end">

                                    <span class="badge bg-success">

                                        <?= $reader["total"] ?>

                                    </span>

                                </td>

                            </tr>

                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-lg-6 mb-4">

            <div class="card shadow h-100">

                <div class="card-header bg-info text-white">

                    📅 Сегодня ожидаются посетители

                </div>

                <div class="card-body">

                    <?php if($todayVisitors->num_rows > 0): ?>

                        <ul class="list-group list-group-flush">

                            <?php while($visitor = $todayVisitors->fetch_assoc()): ?>

                            <li class="list-group-item">

                                <strong>

                                    <?= htmlspecialchars($visitor["full_name"]) ?>

                                </strong>

                                <br>

                                <small>

                                    📚 <?= htmlspecialchars($visitor["title"]) ?>

                                </small>

                                <span class="float-end badge bg-primary">

                                    <?= substr($visitor["visit_time"],0,5) ?>

                                </span>

                            </li>

                            <?php endwhile; ?>

                        </ul>

                    <?php else: ?>

                        <div class="alert alert-success text-center mb-0">

                            Сегодня посетителей нет 🎉

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

    <!-- ================= ГРАФИК ================= -->

    <div class="row mt-4 justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow">

                <div class="card-header bg-info text-white">

                    📊 Выдача книг по месяцам

                </div>

                <div class="card-body">

                    <canvas id="borrowChart"></canvas>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>

<script>
window.addEventListener("load", function () {

    const canvas = document.getElementById("borrowChart");

    if (!canvas) return;

    if (typeof Chart === "undefined") {
        console.error("Chart.js не загружен");
        return;
    }

    new Chart(canvas, {
        type: "bar",

        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: "Количество выдач",
                data: <?= json_encode($totals) ?>,
                borderWidth: 1
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: "top",
                    align: "center"
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }

    });

});
</script>

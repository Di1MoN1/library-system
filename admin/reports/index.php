<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}

include "../../includes/header.php";
include "../../includes/navbar.php";
?>

<div class="container mt-5 mb-5">

    <h2 class="mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-bar-chart-fill text-primary"></i>
        Отчёты библиотеки
    </h2>

    <div class="row g-4">

        <div class="col-md-6 col-lg-3">

            <a href="popular_books.php" class="text-decoration-none">

                <div class="card dashboard-card gradient-blue h-100">

                    <div class="card-body text-center p-4">

                        <i class="bi bi-book-half fs-1 mb-3"></i>

                        <h5 class="fw-bold mb-2">Популярные книги</h5>

                        <p class="mb-0 opacity-75 small">

                            Какие книги выдавались чаще всего

                        </p>

                    </div>

                </div>

            </a>

        </div>

        <div class="col-md-6 col-lg-3">

            <a href="active_readers.php" class="text-decoration-none">

                <div class="card dashboard-card gradient-green h-100">

                    <div class="card-body text-center p-4">

                        <i class="bi bi-people-fill fs-1 mb-3"></i>

                        <h5 class="fw-bold mb-2">Активные читатели</h5>

                        <p class="mb-0 opacity-75 small">

                            Кто чаще всего берёт книги

                        </p>

                    </div>

                </div>

            </a>

        </div>

        <div class="col-md-6 col-lg-3">

            <a href="borrow_stats.php" class="text-decoration-none">

                <div class="card dashboard-card gradient-orange h-100">

                    <div class="card-body text-center p-4">

                        <i class="bi bi-graph-up fs-1 mb-3"></i>

                        <h5 class="fw-bold mb-2">Статистика выдач</h5>

                        <p class="mb-0 opacity-75 small">

                            Количество выдач по месяцам

                        </p>

                    </div>

                </div>

            </a>

        </div>

        <div class="col-md-6 col-lg-3">

            <a href="overdue.php" class="text-decoration-none">

                <div class="card dashboard-card gradient-red h-100">

                    <div class="card-body text-center p-4">

                        <i class="bi bi-exclamation-triangle-fill fs-1 mb-3"></i>

                        <h5 class="fw-bold mb-2">Просроченные книги</h5>

                        <p class="mb-0 opacity-75 small">

                            Все книги с просрочкой возврата

                        </p>

                    </div>

                </div>

            </a>

        </div>

    </div>

</div>

<?php include "../../includes/footer.php"; ?>

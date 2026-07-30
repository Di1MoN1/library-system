<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| УВЕДОМЛЕНИЯ (только для авторизованных админов)
|--------------------------------------------------------------------------
*/

$notifPendingRequests = [];
$notifOverdue = [];
$notifPendingReturns = [];
$notifTotal = 0;

if (isset($_SESSION["user_id"]) && isset($conn)) {

    $r = $conn->query("
        SELECT requests.id, requests.request_date, books.title, readers.full_name
        FROM requests
        LEFT JOIN books ON requests.book_id = books.id
        LEFT JOIN readers ON requests.reader_id = readers.id
        WHERE requests.status='Ожидает'
        ORDER BY requests.id DESC
        LIMIT 5
    ");
    if ($r) { while ($row = $r->fetch_assoc()) { $notifPendingRequests[] = $row; } }

    $r = $conn->query("
        SELECT borrowings.id, borrowings.due_date, books.title, readers.full_name
        FROM borrowings
        LEFT JOIN books ON borrowings.book_id = books.id
        LEFT JOIN readers ON borrowings.reader_id = readers.id
        WHERE borrowings.status='Выдана' AND borrowings.due_date < CURDATE()
        ORDER BY borrowings.due_date ASC
        LIMIT 5
    ");
    if ($r) { while ($row = $r->fetch_assoc()) { $notifOverdue[] = $row; } }

    $r = $conn->query("
        SELECT return_requests.id, return_requests.visit_date, books.title, readers.full_name
        FROM return_requests
        LEFT JOIN borrowings ON return_requests.borrowing_id = borrowings.id
        LEFT JOIN books ON borrowings.book_id = books.id
        LEFT JOIN readers ON return_requests.reader_id = readers.id
        WHERE return_requests.status='Ожидает'
        ORDER BY return_requests.id DESC
        LIMIT 5
    ");
    if ($r) { while ($row = $r->fetch_assoc()) { $notifPendingReturns[] = $row; } }

    // Реальные totals (не ограниченные LIMIT 5) — для цифры на бейдже
    $notifTotal =
        (int) ($conn->query("SELECT COUNT(*) c FROM requests WHERE status='Ожидает'")->fetch_assoc()["c"])
        + (int) ($conn->query("SELECT COUNT(*) c FROM borrowings WHERE status='Выдана' AND due_date < CURDATE()")->fetch_assoc()["c"])
        + (int) ($conn->query("SELECT COUNT(*) c FROM return_requests WHERE status='Ожидает'")->fetch_assoc()["c"]);

}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">

    <div class="container">

        <a class="navbar-brand" href="/library-system/">
            📚 Library System
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbar">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/library-system/">
                        <i class="bi bi-house-door-fill"></i> Главная
                    </a>
                </li>

                <?php if (isset($_SESSION["user_id"])): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/library-system/admin/index.php">
                            <i class="bi bi-speedometer2"></i> Панель
                        </a>
                    </li>

                    <!-- ===================== КАТАЛОГ ===================== -->
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-collection-fill"></i> Каталог
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item" href="/library-system/books/index.php">
                                    <i class="bi bi-book-half"></i> Книги
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/library-system/authors/index.php">
                                    <i class="bi bi-person-lines-fill"></i> Авторы
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/library-system/genres/index.php">
                                    <i class="bi bi-tags-fill"></i> Жанры
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a class="dropdown-item" href="/library-system/readers/index.php">
                                    <i class="bi bi-people-fill"></i> Читатели
                                </a>
                            </li>

                        </ul>

                    </li>

                    <!-- ===================== ОПЕРАЦИИ ===================== -->
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-arrow-left-right"></i> Операции
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item" href="/library-system/admin/requests/index.php">
                                    <i class="bi bi-envelope-paper-fill"></i> Заявки
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/library-system/borrowings/index.php">
                                    <i class="bi bi-journal-check"></i> Журнал выдач
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/library-system/return_requests/index.php">
                                    <i class="bi bi-arrow-return-left"></i> Возврат книг
                                </a>
                            </li>

                        </ul>

                    </li>

                    <!-- ===================== СИСТЕМА ===================== -->
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear-fill"></i> Система
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item" href="/library-system/admin/reports/index.php">
                                    <i class="bi bi-bar-chart-fill"></i> Отчёты
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="/library-system/admin/settings/index.php">
                                    <i class="bi bi-gear-fill"></i> Настройки
                                </a>
                            </li>

                            <?php if ($_SESSION["role"] == "superadmin"): ?>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a class="dropdown-item" href="/library-system/users/index.php">
                                        <i class="bi bi-people"></i> Администраторы
                                    </a>
                                </li>

                            <?php endif; ?>

                        </ul>

                    </li>

                <?php endif; ?>

            </ul>

            <div class="d-flex">

                <?php if (isset($_SESSION["user_id"])): ?>

                    <!-- ===================== УВЕДОМЛЕНИЯ ===================== -->
                    <div class="dropdown me-3">

                        <a href="#" class="btn btn-outline-light position-relative" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">

                            <i class="bi bi-bell-fill"></i>

                            <?php if ($notifTotal > 0): ?>

                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $notifTotal > 99 ? "99+" : $notifTotal ?>
                                </span>

                            <?php endif; ?>

                        </a>

                        <div class="dropdown-menu dropdown-menu-end shadow p-0" style="width:340px; max-height:420px; overflow-y:auto;">

                            <div class="px-3 py-2 border-bottom bg-light fw-bold">
                                <i class="bi bi-bell-fill text-primary"></i>
                                Уведомления
                                <?php if ($notifTotal > 0): ?>
                                    <span class="badge bg-danger float-end"><?= $notifTotal ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($notifTotal === 0): ?>

                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-check2-circle fs-3 d-block mb-2"></i>
                                    Новых уведомлений нет
                                </div>

                            <?php else: ?>

                                <?php if (!empty($notifPendingRequests)): ?>

                                    <h6 class="dropdown-header">🟡 Заявки на ожидании</h6>

                                    <?php foreach ($notifPendingRequests as $n): ?>
                                        <a class="dropdown-item small py-2" href="/library-system/admin/requests/index.php">
                                            <strong><?= htmlspecialchars($n["full_name"]) ?></strong> — <?= htmlspecialchars($n["title"]) ?>
                                            <br><span class="text-muted"><?= date("d.m.Y", strtotime($n["request_date"])) ?></span>
                                        </a>
                                    <?php endforeach; ?>

                                    <div class="dropdown-divider"></div>

                                <?php endif; ?>

                                <?php if (!empty($notifOverdue)): ?>

                                    <h6 class="dropdown-header">🔴 Просроченные книги</h6>

                                    <?php foreach ($notifOverdue as $n): ?>
                                        <a class="dropdown-item small py-2" href="/library-system/borrowings/index.php">
                                            <strong><?= htmlspecialchars($n["full_name"]) ?></strong> — <?= htmlspecialchars($n["title"]) ?>
                                            <br><span class="text-danger">Срок: <?= date("d.m.Y", strtotime($n["due_date"])) ?></span>
                                        </a>
                                    <?php endforeach; ?>

                                    <div class="dropdown-divider"></div>

                                <?php endif; ?>

                                <?php if (!empty($notifPendingReturns)): ?>

                                    <h6 class="dropdown-header">↩ Заявки на возврат</h6>

                                    <?php foreach ($notifPendingReturns as $n): ?>
                                        <a class="dropdown-item small py-2" href="/library-system/return_requests/index.php">
                                            <strong><?= htmlspecialchars($n["full_name"]) ?></strong> — <?= htmlspecialchars($n["title"]) ?>
                                        </a>
                                    <?php endforeach; ?>

                                <?php endif; ?>

                            <?php endif; ?>

                        </div>

                    </div>

                    <span class="navbar-text text-white me-3">

                        👋 <?= htmlspecialchars($_SESSION["username"]) ?>

                    </span>

                    <a href="/library-system/logout.php" class="btn btn-danger">

                        <i class="bi bi-box-arrow-right"></i>

                        Выход

                    </a>

                <?php else: ?>

                    <a href="/library-system/login.php" class="btn btn-light">

                        🔑 Войти

                    </a>

                <?php endif; ?>

            </div>

        </div>

    </div>

</nav>
<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "reader") {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

$user_id = $_SESSION["user_id"];

/* ==========================
   Получаем информацию
========================== */

$stmt = $conn->prepare("
SELECT
    readers.*,
    users.username
FROM readers
LEFT JOIN users
ON readers.user_id = users.id
WHERE readers.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$reader = $stmt->get_result()->fetch_assoc();

if (!$reader) {
    die("Профиль не найден.");
}

/* ==========================
   Сохранение профиля
========================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);

    $profileErrors = [];

    if ($fullname === "" || mb_strlen($fullname) < 2) {
        $profileErrors[] = "Укажите ФИО (не менее 2 символов).";
    }

    if ($phone !== "" && !preg_match('/^[0-9+\-\s()]{5,20}$/', $phone)) {
        $profileErrors[] = "Телефон может содержать только цифры, пробелы, +, - и скобки.";
    }

    if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profileErrors[] = "Некорректный формат email.";
    }

    if (empty($profileErrors)) {

    $stmt = $conn->prepare("
        UPDATE readers
        SET
            full_name=?,
            phone=?,
            email=?
        WHERE user_id=?
    ");

    $stmt->bind_param(
        "sssi",
        $fullname,
        $phone,
        $email,
        $user_id
    );

    if ($stmt->execute()) {

        header("Location: profile.php?saved=1");
        exit;

    }

    } else {

        // Persist введённых значений, чтобы форма их не потеряла
        $reader["full_name"] = $fullname;
        $reader["phone"] = $phone;
        $reader["email"] = $email;

    }

}

/* ==========================
   Статистика
========================== */

$books = $conn->query("
SELECT COUNT(*) total
FROM borrowings
WHERE reader_id=" . $reader["id"] . "
AND status='Выдана'
")->fetch_assoc()["total"];

$history = $conn->query("
SELECT COUNT(*) total
FROM borrowings
WHERE reader_id=" . $reader["id"])
    ->fetch_assoc()["total"];

$requests = $conn->query("
SELECT COUNT(*) total
FROM requests
WHERE reader_id=" . $reader["id"])
    ->fetch_assoc()["total"];

/* ==========================
   Аватар
========================== */

$avatar = "👤";

if (!empty($reader["full_name"])) {

    $avatar = mb_strtoupper(
        mb_substr(
            $reader["full_name"],
            0,
            1
        )
    );

}

/* ==========================
   Проверяем заполненность
========================== */

$profileIncomplete = false;

if (
    trim($reader["phone"]) == "" ||
    trim($reader["email"]) == "" ||
    trim($reader["full_name"]) == trim($reader["username"])
) {

    $profileIncomplete = true;

}

include "../includes/header.php";
include "navbar.php";
?>

<div class="container mt-5 mb-5">

    <div class="row g-4">

        <!-- ================== ЛЕВАЯ КОЛОНКА: ПРОФИЛЬ ================== -->
        <div class="col-lg-8">

            <div class="card border-0 shadow-lg mb-4 overflow-hidden">

                <div class="card-body p-0">

                    <div class="text-center text-white gradient-blue" style="padding:45px;">

                        <div class="mx-auto d-flex align-items-center justify-content-center"
                            style="
                                width:130px;
                                height:130px;
                                border-radius:50%;
                                background:white;
                                color:#0d6efd;
                                font-size:55px;
                                font-weight:bold;
                                box-shadow:0 5px 15px rgba(0,0,0,.25);
                            ">
                            <?= $avatar ?>
                        </div>

                        <h2 class="mt-4 fw-bold mb-1">
                            <?= htmlspecialchars($reader["full_name"]) ?>
                        </h2>

                        <p class="mb-0 fs-5 opacity-75">
                            @<?= htmlspecialchars($reader["username"]) ?>
                        </p>

                    </div>

                    <div class="p-4">

                        <?php if (isset($_GET["saved"])): ?>
                            <div class="alert alert-success d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Данные успешно сохранены.</span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($profileErrors)): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle-fill"></i>
                                <?= implode("<br>", $profileErrors) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($profileIncomplete): ?>
                            <div class="alert alert-warning">
                                <h5 class="d-flex align-items-center gap-2">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Профиль заполнен не полностью
                                </h5>
                                <p class="mb-0">
                                    Для корректной работы библиотеки рекомендуем заполнить настоящее ФИО,
                                    телефон и электронную почту.
                                </p>
                            </div>
                        <?php endif; ?>

                        <h4 class="mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-person-fill"></i>
                            Личные данные
                        </h4>

                        <form method="POST">

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ФИО</label>
                                    <input type="text" name="fullname" class="form-control form-control-lg"
                                        value="<?= htmlspecialchars($reader["full_name"]) ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Логин</label>
                                    <input type="text" class="form-control form-control-lg"
                                        value="<?= htmlspecialchars($reader["username"]) ?>" disabled>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Телефон</label>
                                    <input type="text" name="phone" class="form-control form-control-lg"
                                        pattern="[0-9+\-\s()]{5,20}" title="Только цифры, пробелы, +, - и скобки"
                                        placeholder="+7 (___) ___-__-__"
                                        value="<?= htmlspecialchars($reader["phone"]) ?>">
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">E-mail</label>
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        placeholder="example@mail.com"
                                        value="<?= htmlspecialchars($reader["email"]) ?>">
                                </div>

                            </div>

                            <hr>

                            <div class="d-flex flex-wrap gap-3">

                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Сохранить изменения
                                </button>

                                <a href="change_password.php" class="btn btn-warning btn-lg">
                                    <i class="bi bi-shield-lock-fill"></i>
                                    Сменить пароль
                                </a>

                                <a href="index.php" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-house-fill"></i>
                                    На главную
                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
        <!-- ================== /ЛЕВАЯ КОЛОНКА ================== -->

        <!-- ================== ПРАВАЯ КОЛОНКА: СТАТИСТИКА ================== -->
        <div class="col-lg-4">

            <div class="dashboard-card gradient-blue card border-0 shadow-lg text-white mb-4">
                <div class="card-body text-center p-4">
                    <i class="bi bi-book-fill" style="font-size:50px;"></i>
                    <h1 class="display-4 fw-bold mt-2 mb-0"><?= $books ?></h1>
                    <p class="mb-0 fs-5">Книг на руках</p>
                </div>
            </div>

            <div class="dashboard-card gradient-green card border-0 shadow-lg text-white mb-4">
                <div class="card-body text-center p-4">
                    <i class="bi bi-clock-history" style="font-size:50px;"></i>
                    <h1 class="display-4 fw-bold mt-2 mb-0"><?= $history ?></h1>
                    <p class="mb-0 fs-5">Всего прочитано</p>
                </div>
            </div>

            <div class="dashboard-card gradient-orange card border-0 shadow-lg text-white mb-4">
                <div class="card-body text-center p-4">
                    <i class="bi bi-envelope-paper-fill" style="font-size:50px;"></i>
                    <h1 class="display-4 fw-bold mt-2 mb-0"><?= $requests ?></h1>
                    <p class="mb-0 fs-5">Мои заявки</p>
                </div>
            </div>

        </div>
        <!-- ================== /ПРАВАЯ КОЛОНКА ================== -->

    </div>

    <!-- ================== АККОРДЕОН: ПОДСКАЗКИ ================== -->
    <div class="mt-4">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white">
                <button class="btn btn-link text-decoration-none fw-bold w-100 text-start p-0 d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="collapse" data-bs-target="#hintsCollapse"
                    aria-expanded="false" aria-controls="hintsCollapse">
                    <i class="bi bi-info-circle-fill text-primary"></i>
                    Подсказки
                    <i class="bi bi-chevron-down ms-auto"></i>
                </button>
            </div>

            <div class="collapse" id="hintsCollapse">
                <div class="card-body">

                    <p>
                        📖 После изменения личных данных нажмите
                        <strong>«Сохранить изменения»</strong>.
                    </p>
                    <hr>
                    <p>
                        📚 Все оформленные книги можно посмотреть
                        в разделе <strong>«Мои книги»</strong>.
                    </p>
                    <hr>
                    <p class="mb-0">
                        🔒 Если забыли пароль —
                        его можно изменить в любое время.
                    </p>

                </div>
            </div>

        </div>

    </div>
    <!-- ================== /АККОРДЕОН ================== -->

</div>

<style>
#hintsCollapse ~ .card-body,
.card-header .bi-chevron-down {
    transition: transform .25s ease;
}
button[aria-expanded="true"] .bi-chevron-down {
    transform: rotate(180deg);
}
</style>

<?php include "../includes/footer.php"; ?>

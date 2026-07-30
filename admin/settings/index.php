<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/db.php";

// Сохранение
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $borrow_days = (int)$_POST["borrow_days"];

    $stmt = $conn->prepare("
    UPDATE settings
    SET borrow_days=?
    WHERE id=1
    ");

    $stmt->bind_param("i", $borrow_days);
    $stmt->execute();

    header("Location: index.php?saved=1");
    exit;

}

// Получаем настройки
$settings = $conn->query("
SELECT *
FROM settings
LIMIT 1
")->fetch_assoc();

include "../../includes/header.php";
include "../../includes/navbar.php";
?>

<div class="container mt-5 mb-5" style="max-width:700px;">

    <h2 class="mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-gear-fill text-primary"></i>
        Настройки библиотеки
    </h2>

    <?php if (isset($_GET["saved"])): ?>

        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            Настройки успешно сохранены.
        </div>

    <?php endif; ?>

    <div class="card border-0 shadow-lg">

        <div class="card-body p-4">

            <form method="post">

                <label class="form-label fw-bold">
                    <i class="bi bi-calendar-range text-primary"></i>
                    Срок выдачи книг
                </label>

                <p class="text-muted small mb-3">
                    Количество дней, на которое книга выдаётся читателю по умолчанию.
                    Именно на эту величину рассчитывается дата возврата при выдаче.
                </p>

                <div class="input-group input-group-lg mb-4">

                    <input
                        type="number"
                        name="borrow_days"
                        class="form-control"
                        min="1"
                        max="365"
                        value="<?= (int) $settings["borrow_days"] ?>"
                        required>

                    <span class="input-group-text">дней</span>

                </div>

                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle-fill"></i>
                    Сохранить
                </button>

            </form>

        </div>

    </div>

</div>

<?php include "../../includes/footer.php"; ?>

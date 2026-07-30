<?php
require_once "../config/db.php";
include "../includes/header.php";
include "../includes/navbar.php";

if (!isset($_GET['id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Книга не найдена.</div></div>";
    include "../includes/footer.php";
    exit;
}

$id = (int)$_GET['id'];

$sql = "
SELECT
    books.*,
    authors.full_name,
    genres.name
FROM books
INNER JOIN authors ON books.author_id = authors.id
INNER JOIN genres ON books.genre_id = genres.id
WHERE books.id = $id
";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Книга не найдена.</div></div>";
    include "../includes/footer.php";
    exit;
}

$book = $result->fetch_assoc();

// Сколько экземпляров сейчас на руках
$issued = $conn->query("
    SELECT COUNT(*) total
    FROM borrowings
    WHERE book_id = $id
    AND status = 'Выдана'
")->fetch_assoc()["total"];

$available = max(0, (int)$book["copies"] - (int)$issued);

// Может ли текущий пользователь оформить заявку
$isReader = isset($_SESSION["user_id"]) && $_SESSION["role"] == "reader";
?>

<div class="container mt-5 mb-5">

    <div class="card border-0 shadow-lg mb-4">

        <div class="card-body p-4">

            <div class="row g-4">

                <!-- ===================== ОБЛОЖКА ===================== -->
                <div class="col-md-4">

                    <div class="book-cover" style="height:480px;">

                        <?php if (!empty($book["image"])): ?>

                            <img
                                src="../assets/images/<?= htmlspecialchars($book["image"]) ?>"
                                class="book-image"
                                alt="<?= htmlspecialchars($book["title"]) ?>">

                        <?php else: ?>

                            <img
                                src="../assets/images/no-cover.png"
                                class="book-image"
                                alt="Нет обложки">

                        <?php endif; ?>

                    </div>

                </div>

                <!-- ===================== ИНФОРМАЦИЯ ===================== -->
                <div class="col-md-8">

                    <h2 class="fw-bold mb-1"><?= htmlspecialchars($book['title']) ?></h2>

                    <p class="text-muted fs-5 mb-4">

                        <i class="bi bi-person-fill"></i>
                        <?= htmlspecialchars($book['full_name']) ?>

                    </p>

                    <ul class="list-group list-group-flush mb-4">

                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><i class="bi bi-tags-fill text-primary"></i> Жанр</span>
                            <span class="fw-bold"><?= htmlspecialchars($book['name']) ?></span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><i class="bi bi-calendar-event text-primary"></i> Год издания</span>
                            <span class="fw-bold"><?= htmlspecialchars($book['year_published']) ?></span>
                        </li>

                        <?php if (!empty($book['publisher'])): ?>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><i class="bi bi-building text-primary"></i> Издательство</span>
                            <span class="fw-bold"><?= htmlspecialchars($book['publisher']) ?></span>
                        </li>
                        <?php endif; ?>

                        <?php if (!empty($book['isbn'])): ?>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><i class="bi bi-upc-scan text-primary"></i> ISBN</span>
                            <span class="fw-bold"><?= htmlspecialchars($book['isbn']) ?></span>
                        </li>
                        <?php endif; ?>

                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><i class="bi bi-stack text-primary"></i> Всего экземпляров</span>
                            <span class="fw-bold"><?= (int)$book['copies'] ?></span>
                        </li>

                    </ul>

                    <?php if ($available > 0): ?>

                        <div class="alert alert-success d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i>
                            В наличии: <strong><?= $available ?></strong> экз.
                        </div>

                    <?php else: ?>

                        <div class="alert alert-danger d-flex align-items-center gap-2">
                            <i class="bi bi-x-circle-fill"></i>
                            Все экземпляры сейчас на руках
                        </div>

                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-3 mt-3">

                        <?php if ($isReader): ?>

                            <a
                                href="request.php?book_id=<?= $book['id'] ?>"
                                class="btn btn-lg <?= $available > 0 ? 'btn-primary' : 'btn-secondary disabled' ?>">

                                <i class="bi bi-envelope-paper-fill"></i>
                                Оформить заявку

                            </a>

                        <?php elseif (!isset($_SESSION["user_id"])): ?>

                            <a href="../login.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Войдите, чтобы оформить заявку
                            </a>

                        <?php endif; ?>

                        <button type="button" onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left"></i>
                            Назад
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- ===================== ОПИСАНИЕ ===================== -->
    <?php if (!empty($book['description'])): ?>

        <div class="card border-0 shadow mb-4">

            <div class="card-body p-4">

                <h4 class="mb-3">
                    <i class="bi bi-file-text-fill text-primary"></i>
                    Описание
                </h4>

                <p class="mb-0" style="line-height:1.7;">
                    <?= nl2br(htmlspecialchars($book['description'])) ?>
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php
include "../includes/footer.php";
?>

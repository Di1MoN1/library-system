<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

/*
|--------------------------------------------------------------------------
| Параметры поиска и фильтров
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");
$genreFilter = $_GET["genre"] ?? "";
$availability = $_GET["availability"] ?? "";
$sort = $_GET["sort"] ?? "newest";

/*
|--------------------------------------------------------------------------
| Список жанров для фильтра
|--------------------------------------------------------------------------
*/

$genres = $conn->query("SELECT * FROM genres ORDER BY name");

/*
|--------------------------------------------------------------------------
| Построение запроса
|--------------------------------------------------------------------------
*/

$sql = "
SELECT
    books.*,
    authors.full_name AS author_name,
    genres.name AS genre_name
FROM books
LEFT JOIN authors ON books.author_id = authors.id
LEFT JOIN genres ON books.genre_id = genres.id
WHERE 1=1
";

$params = [];
$types = "";

if ($search !== "") {

    $sql .= " AND (books.title LIKE ? OR authors.full_name LIKE ?) ";

    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";

}

if ($genreFilter !== "" && ctype_digit($genreFilter)) {

    $sql .= " AND books.genre_id = ? ";
    $params[] = (int) $genreFilter;
    $types .= "i";

}

if ($availability === "available") {

    $sql .= " AND books.copies > 0 ";

} elseif ($availability === "unavailable") {

    $sql .= " AND books.copies = 0 ";

}

switch ($sort) {

    case "title":
        $sql .= " ORDER BY books.title ASC";
        break;

    case "author":
        $sql .= " ORDER BY authors.full_name ASC";
        break;

    default:
        $sql .= " ORDER BY books.id DESC";

}

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

        <h2>📚 Управление книгами</h2>

        <a href="add.php" class="btn btn-success">
            ➕ Добавить книгу
        </a>

    </div>

    <!-- ================= ПОИСК И ФИЛЬТРЫ ================= -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-4">

                    <label class="form-label fw-bold">
                        <i class="bi bi-search"></i>
                        Поиск
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Название или автор..."
                        value="<?= htmlspecialchars($search) ?>">

                </div>

                <div class="col-md-2">

                    <label class="form-label fw-bold">Жанр</label>

                    <select name="genre" class="form-select">

                        <option value="">Все жанры</option>

                        <?php $genres->data_seek(0); ?>
                        <?php while ($g = $genres->fetch_assoc()): ?>

                            <option
                                value="<?= $g["id"] ?>"
                                <?= ($genreFilter == $g["id"]) ? "selected" : "" ?>>

                                <?= htmlspecialchars($g["name"]) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="col-md-2">

                    <label class="form-label fw-bold">Наличие</label>

                    <select name="availability" class="form-select">

                        <option value="">Все книги</option>

                        <option value="available" <?= $availability === "available" ? "selected" : "" ?>>
                            Есть в наличии
                        </option>

                        <option value="unavailable" <?= $availability === "unavailable" ? "selected" : "" ?>>
                            Нет в наличии
                        </option>

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label fw-bold">Сортировка</label>

                    <select name="sort" class="form-select">

                        <option value="newest" <?= $sort === "newest" ? "selected" : "" ?>>
                            Сначала новые
                        </option>

                        <option value="title" <?= $sort === "title" ? "selected" : "" ?>>
                            По названию (А-Я)
                        </option>

                        <option value="author" <?= $sort === "author" ? "selected" : "" ?>>
                            По автору (А-Я)
                        </option>

                    </select>

                </div>

                <div class="col-md-1 d-grid">

                    <button type="submit" class="btn btn-primary" title="Найти">
                        <i class="bi bi-search"></i>
                    </button>

                </div>

            </form>

            <?php if ($search !== "" || $genreFilter !== "" || $availability !== ""): ?>

                <div class="mt-3 d-flex align-items-center gap-3 flex-wrap">

                    <span class="text-muted">
                        Найдено: <strong><?= $totalFound ?></strong>
                    </span>

                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                        Сбросить фильтры
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">

            <tr>

                <th>ID</th>

                <th>Обложка</th>

                <th>Название</th>

                <th>Автор</th>

                <th>Жанр</th>

                <th>Год</th>

                <th>Экземпляров</th>

                <th width="170">Действия</th>

            </tr>

        </thead>

        <tbody>

<?php if ($totalFound === 0): ?>

    <tr>
        <td colspan="8" class="text-center text-muted py-4">
            Ничего не найдено по заданным критериям.
        </td>
    </tr>

<?php endif; ?>

<?php while($book = $result->fetch_assoc()) : ?>

<tr>

<td><?= $book["id"] ?></td>

<td width="100">

<?php if(!empty($book["image"])): ?>

<img src="../assets/images/<?= htmlspecialchars($book["image"]) ?>"
     width="70"
     class="img-thumbnail">

<?php else: ?>

<span class="text-muted">Нет</span>

<?php endif; ?>

</td>

<td><?= htmlspecialchars($book["title"]) ?></td>

<td><?= htmlspecialchars($book["author_name"]) ?></td>

<td><?= htmlspecialchars($book["genre_name"]) ?></td>

<td><?= $book["year_published"] ?></td>

<td><?= $book["copies"] ?></td>

<td>

<a href="edit.php?id=<?= $book["id"] ?>"
class="btn btn-warning btn-sm">

✏️

</a>

<a href="delete.php?id=<?= $book["id"] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Удалить книгу?')">

🗑️

</a>

</td>

</tr>

<?php endwhile; ?>

        </tbody>

    </table>

</div>

<?php include "../includes/footer.php"; ?>

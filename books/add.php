<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

/* Получаем авторов */
$authors = $conn->query("SELECT * FROM authors ORDER BY full_name");

/* Получаем жанры */
$genres = $conn->query("SELECT * FROM genres ORDER BY name");

$message = "";

/* Если нажали кнопку */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $author = $_POST["author"];
    $genre = $_POST["genre"];
    $year = trim($_POST["year"]);
    $description = trim($_POST["description"]);
    $isbn = trim($_POST["isbn"]);
    $publisher = trim($_POST["publisher"]);
    $copies = trim($_POST["copies"]);

    $errors = [];

    // Название
    if ($title === "") {
        $errors[] = "Укажите название книги.";
    }

    // Автор / жанр
    if ($author === "" || !ctype_digit((string)$author)) {
        $errors[] = "Выберите автора из списка.";
    }

    if ($genre === "" || !ctype_digit((string)$genre)) {
        $errors[] = "Выберите жанр из списка.";
    }

    // Год издания: число в разумном диапазоне
    $currentYear = (int) date("Y");

    if ($year === "" || !ctype_digit($year) || (int)$year < 1450 || (int)$year > $currentYear) {
        $errors[] = "Год издания должен быть числом от 1450 до {$currentYear}.";
    }

    // Количество экземпляров: целое неотрицательное число
    if ($copies === "" || !ctype_digit($copies) || (int)$copies < 0 || (int)$copies > 10000) {
        $errors[] = "Количество экземпляров должно быть целым числом от 0 до 10000.";
    }

    // ISBN: необязателен, но если указан — только цифры/дефисы, 10-17 символов
    if ($isbn !== "" && !preg_match('/^[0-9\-]{10,17}$/', $isbn)) {
        $errors[] = "ISBN может содержать только цифры и дефисы (10–17 символов).";
    }

    // Обложка: проверяем реальный MIME-тип, а не только расширение
    $image = "";

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

        $allowedTypes = ["image/jpeg", "image/png", "image/webp", "image/gif"];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES["image"]["tmp_name"]);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes)) {

            $errors[] = "Обложка должна быть изображением (JPEG, PNG, WEBP или GIF).";

        } else {

            $image = time() . "_" . basename($_FILES["image"]["name"]);

            move_uploaded_file(
                $_FILES["image"]["tmp_name"],
                "../assets/images/" . $image
            );

        }

    }

    if (empty($errors)) {

    $stmt = $conn->prepare("
        INSERT INTO books
        (
            title,
            author_id,
            genre_id,
            year_published,
            description,
            image,
            isbn,
            publisher,
            copies
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "siiissssi",
        $title,
        $author,
        $genre,
        $year,
        $description,
        $image,
        $isbn,
        $publisher,
        $copies
    );

    if ($stmt->execute()) {

        header("Location: index.php");
        exit;

    } else {

        $message = "Ошибка добавления книги.";

    }

    } else {

        $message = implode("<br>", $errors);

    }

}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>➕ Добавить книгу</h3>

</div>

<div class="card-body">

<?php if($message!=""): ?>

<div class="alert alert-danger">

<?= $message ?>

</div>

<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label>Название</label>

<input
type="text"
name="title"
class="form-control"
value="<?= htmlspecialchars($_POST["title"] ?? "") ?>"
required>

</div>

<div class="mb-3">

<label>Автор</label>

<select
name="author"
class="form-select"
required>

<option value="">Выберите автора</option>

<?php while($a=$authors->fetch_assoc()): ?>

<option value="<?= $a["id"] ?>" <?= (isset($_POST["author"]) && $_POST["author"] == $a["id"]) ? "selected" : "" ?>>

<?= htmlspecialchars($a["full_name"]) ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="mb-3">

<label>Жанр</label>

<select
name="genre"
class="form-select"
required>

<option value="">Выберите жанр</option>

<?php while($g=$genres->fetch_assoc()): ?>

<option value="<?= $g["id"] ?>" <?= (isset($_POST["genre"]) && $_POST["genre"] == $g["id"]) ? "selected" : "" ?>>

<?= htmlspecialchars($g["name"]) ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="row">

<div class="col-md-6">

<label>Год</label>

<input
type="number"
name="year"
class="form-control"
min="1450"
max="<?= date("Y") ?>"
value="<?= htmlspecialchars($_POST["year"] ?? "") ?>"
required>

</div>

<div class="col-md-6">

<label>Количество</label>

<input
type="number"
name="copies"
value="<?= htmlspecialchars($_POST["copies"] ?? "1") ?>"
min="0"
max="10000"
class="form-control"
required>

</div>

</div>

<br>

<div class="mb-3">

<label>ISBN</label>

<input
type="text"
name="isbn"
class="form-control"
placeholder="978-0-000-00000-0"
pattern="[0-9\-]{10,17}"
title="Только цифры и дефисы, 10-17 символов"
value="<?= htmlspecialchars($_POST["isbn"] ?? "") ?>">

</div>

<div class="mb-3">

<label>Издательство</label>

<input
type="text"
name="publisher"
class="form-control"
value="<?= htmlspecialchars($_POST["publisher"] ?? "") ?>">

</div>

<div class="mb-3">

<label>Описание</label>

<textarea
name="description"
class="form-control"
rows="5"><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>

</div>
<div class="mb-3">

    <label class="form-label">Обложка книги</label>

    <input
        type="file"
        name="image"
        class="form-control"
        accept="image/*">

</div>

<button class="btn btn-success">

Сохранить книгу

</button>

<a href="index.php" class="btn btn-secondary">

Назад

</a>

</form>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
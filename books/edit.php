<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET["id"];

/* Получаем книгу */
$stmt = $conn->prepare("SELECT * FROM books WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    die("Книга не найдена.");
}

/* Авторы */
$authors = $conn->query("SELECT * FROM authors ORDER BY full_name");

/* Жанры */
$genres = $conn->query("SELECT * FROM genres ORDER BY name");

$message = "";

/* Сохранение */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $author = (int)$_POST["author"];
    $genre = (int)$_POST["genre"];
    $year = trim($_POST["year"]);
    $isbn = trim($_POST["isbn"]);
    $publisher = trim($_POST["publisher"]);
    $copies = trim($_POST["copies"]);
    $description = trim($_POST["description"]);

    $errors = [];

    if ($title === "") {
        $errors[] = "Укажите название книги.";
    }

    $currentYear = (int) date("Y");

    if ($year === "" || !ctype_digit($year) || (int)$year < 1450 || (int)$year > $currentYear) {
        $errors[] = "Год издания должен быть числом от 1450 до {$currentYear}.";
    }

    if ($copies === "" || !ctype_digit($copies) || (int)$copies < 0 || (int)$copies > 10000) {
        $errors[] = "Количество экземпляров должно быть целым числом от 0 до 10000.";
    }

    if ($isbn !== "" && !preg_match('/^[0-9\-]{10,17}$/', $isbn)) {
        $errors[] = "ISBN может содержать только цифры и дефисы (10–17 символов).";
    }

    $image = $book["image"];

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

        $allowedTypes = ["image/jpeg", "image/png", "image/webp", "image/gif"];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES["image"]["tmp_name"]);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes)) {

            $errors[] = "Обложка должна быть изображением (JPEG, PNG, WEBP или GIF).";

        } else {

            $image = time() . "_" . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], "../assets/images/" . $image);

        }

    }

    if (empty($errors)) {

    $stmt = $conn->prepare("
        UPDATE books SET
            title=?,
            author_id=?,
            genre_id=?,
            year_published=?,
            description=?,
            image=?,
            isbn=?,
            publisher=?,
            copies=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "siiissssii",
        $title,
        $author,
        $genre,
        $year,
        $description,
        $image,
        $isbn,
        $publisher,
        $copies,
        $id
    );

    if ($stmt->execute()) {

        header("Location: index.php");
        exit;

    } else {

        $message = "Ошибка при сохранении.";

    }

    } else {

        $message = implode("<br>", $errors);

        // Чтобы форма показала введённые (пусть и некорректные) значения
        $book["title"] = $title;
        $book["author_id"] = $author;
        $book["genre_id"] = $genre;
        $book["year_published"] = $year;
        $book["isbn"] = $isbn;
        $book["publisher"] = $publisher;
        $book["copies"] = $copies;
        $book["description"] = $description;

    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>✏️ Редактирование книги</h3>

</div>

<div class="card-body">

<?php if($message!=""): ?>

<div class="alert alert-danger">

<?= $message ?>

</div>

<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">Название</label>

<input
type="text"
name="title"
class="form-control"
value="<?= htmlspecialchars($book["title"]) ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">Автор</label>

<select name="author" class="form-select">

<?php while($a=$authors->fetch_assoc()): ?>

<option
value="<?= $a["id"] ?>"
<?= $book["author_id"]==$a["id"] ? "selected" : "" ?>>

<?= htmlspecialchars($a["full_name"]) ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">Жанр</label>

<select name="genre" class="form-select">

<?php while($g=$genres->fetch_assoc()): ?>

<option
value="<?= $g["id"] ?>"
<?= $book["genre_id"]==$g["id"] ? "selected" : "" ?>>

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
value="<?= htmlspecialchars($book["year_published"]) ?>"
required>

</div>

<div class="col-md-6">

<label>Количество</label>

<input
type="number"
name="copies"
class="form-control"
min="0"
max="10000"
value="<?= htmlspecialchars($book["copies"]) ?>"
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
pattern="[0-9\-]{10,17}"
title="Только цифры и дефисы, 10-17 символов"
value="<?= htmlspecialchars($book["isbn"]) ?>">

</div>

<div class="mb-3">

<label>Издательство</label>

<input
type="text"
name="publisher"
class="form-control"
value="<?= htmlspecialchars($book["publisher"]) ?>">

</div>

<div class="mb-3">

<label>Описание</label>

<textarea
name="description"
rows="6"
class="form-control"><?= htmlspecialchars($book["description"]) ?></textarea>

</div>
<hr><div class="mb-3"><label class="form-label">Текущая обложка</label><br><?php if(!empty($book["image"])): ?><img src="../assets/images/<?= htmlspecialchars($book["image"]) ?>" class="img-thumbnail mb-3" style="max-width:220px;"><?php else: ?><div class="alert alert-secondary">Обложка отсутствует</div><?php endif; ?><label class="form-label">Заменить обложку</label><input type="file" name="image" class="form-control" accept="image/*"><small class="text-muted">Если файл не выбран — останется текущая обложка.</small></div>
<button class="btn btn-warning">

💾 Сохранить изменения

</button>

<a href="index.php" class="btn btn-secondary">

Отмена

</a>

</form>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
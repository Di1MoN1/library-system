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

$stmt = $conn->prepare("SELECT * FROM genres WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$genre = $stmt->get_result()->fetch_assoc();

if (!$genre) {
    die("Жанр не найден.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);

    if ($name != "") {

        $stmt = $conn->prepare("
            UPDATE genres
            SET name=?
            WHERE id=?
        ");

        $stmt->bind_param("si", $name, $id);

        if ($stmt->execute()) {

            header("Location: index.php");
            exit;

        } else {

            $error = "Ошибка сохранения.";

        }

    } else {

        $error = "Введите название жанра.";

    }

}

include "../includes/header.php";
include "../includes/navbar.php";
?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>✏️ Редактирование жанра</h3>

</div>

<div class="card-body">

<?php if($error!=""): ?>

<div class="alert alert-danger">

<?= $error ?>

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Название жанра

</label>

<input
type="text"
name="name"
class="form-control"
value="<?= htmlspecialchars($genre["name"]) ?>"
required>

</div>

<button class="btn btn-warning">

💾 Сохранить

</button>

<a href="index.php" class="btn btn-secondary">

Отмена

</a>

</form>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
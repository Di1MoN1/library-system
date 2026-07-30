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

/* Проверяем, есть ли книги этого жанра */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM books
    WHERE genre_id=?
");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

if ($result["total"] > 0) {

    die("
    <div style='font-family:Arial;padding:30px'>
        <h3>❌ Нельзя удалить жанр.</h3>
        <p>В этом жанре есть книги.</p>
        <a href='index.php'>← Вернуться</a>
    </div>
    ");

}

/* Удаляем жанр */
$stmt = $conn->prepare("
    DELETE FROM genres
    WHERE id=?
");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
?>
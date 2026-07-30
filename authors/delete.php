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

/* Проверяем, есть ли книги у автора */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM books
    WHERE author_id=?
");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

if ($result["total"] > 0) {

    die("
    <div style='font-family:Arial;padding:30px'>
        <h3>❌ Нельзя удалить автора.</h3>
        <p>У этого автора есть книги в библиотеке.</p>
        <a href='index.php'>← Вернуться</a>
    </div>
    ");

}

/* Удаляем автора */
$stmt = $conn->prepare("
    DELETE FROM authors
    WHERE id=?
");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
?>
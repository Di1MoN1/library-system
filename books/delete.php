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

/* Получаем изображение книги */
$stmt = $conn->prepare("SELECT image FROM books WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $book = $result->fetch_assoc();

    if (!empty($book["image"])) {

        $file = "../assets/images/" . $book["image"];

        if (file_exists($file)) {
            unlink($file);
        }

    }

}

/* Удаляем книгу */
$stmt = $conn->prepare("DELETE FROM books WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
?>
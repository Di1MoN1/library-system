<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "library_db";

// Подключение к базе данных
$conn = new mysqli($host, $user, $password, $database);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Устанавливаем кодировку UTF-8
$conn->set_charset("utf8");
?>
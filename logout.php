<?php
session_start();

/* Удаляем все данные сессии */
$_SESSION = [];

/* Уничтожаем сессию */
session_destroy();

/* Возвращаемся на страницу входа */
header("Location: login.php");
exit;
?>
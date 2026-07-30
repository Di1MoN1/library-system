<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "reader") {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

include "../includes/header.php";
include "navbar.php";

// Поиск
$search = "";
$type = "all";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

$sort = $_GET['sort'] ?? 'title';
$sortColumn = ($sort === 'author') ? 'authors.full_name' : 'books.title';

// SQL
$sql = "
SELECT
    books.*,
    authors.full_name,
    genres.name
FROM books
INNER JOIN authors
ON books.author_id = authors.id

INNER JOIN genres
ON books.genre_id = genres.id
";

// Поиск
if ($search != "") {

    $search = $conn->real_escape_string($search);

    switch ($type) {

        case "title":
            $sql .= " WHERE books.title LIKE '%$search%'";
            break;

        case "author":
            $sql .= " WHERE authors.full_name LIKE '%$search%'";
            break;

        case "genre":
            $sql .= " WHERE genres.name LIKE '%$search%'";
            break;

        default:

            $sql .= "
            WHERE
                books.title LIKE '%$search%'
                OR authors.full_name LIKE '%$search%'
                OR genres.name LIKE '%$search%'
            ";

    }

}

$sql .= " ORDER BY $sortColumn ASC";

$result = $conn->query($sql);

?>

<div class="container mt-5">

<h1 class="mb-4">

📚 Каталог книг

</h1>

<form method="GET" class="mb-4">

<div class="row g-2">

<div class="col-md-4">

<input
type="text"
name="search"
class="form-control"
placeholder="Введите название, автора или жанр..."
value="<?= htmlspecialchars($search) ?>">

</div>

<div class="col-md-3">

<select
name="type"
class="form-select">

<option value="all" <?= $type=="all"?"selected":"" ?>>

Искать везде

</option>

<option value="title" <?= $type=="title"?"selected":"" ?>>

По названию

</option>

<option value="author" <?= $type=="author"?"selected":"" ?>>

По автору

</option>

<option value="genre" <?= $type=="genre"?"selected":"" ?>>

По жанру

</option>

</select>

</div>

<div class="col-md-3">

<select name="sort" class="form-select">

<option value="title" <?= $sort=="title"?"selected":"" ?>>
Сортировка: по названию (А-Я)
</option>

<option value="author" <?= $sort=="author"?"selected":"" ?>>
Сортировка: по автору (А-Я)
</option>

</select>

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">

🔍 Найти

</button>

</div>

</div>

</form>

<div class="row">

<?php

if($result->num_rows>0){

    while($row=$result->fetch_assoc()){

        include "book-card.php";

    }

}else{

?>

<div class="col-12">

<div class="alert alert-warning text-center">

Книги не найдены.

</div>

</div>

<?php

}

?>

</div>

</div>

<?php include "../includes/footer.php"; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">

<div class="container">

<a class="navbar-brand" href="index.php">

📚 Кабинет читателя

</a>

<button class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#navbar">

<span class="navbar-toggler-icon"></span>

</button>

<div class="collapse navbar-collapse" id="navbar">

<ul class="navbar-nav me-auto">

<li class="nav-item">

<a class="nav-link" href="index.php">

🏠 Главная

</a>

</li>

<li class="nav-item">

<a class="nav-link" href="catalog.php">

📚 Каталог

</a>

</li>

<li class="nav-item">

<a class="nav-link" href="my_books.php">

📖 Мои книги

</a>

</li>

<li class="nav-item">

<a class="nav-link" href="requests.php">

📝 Мои заявки

</a>

</li>

<li class="nav-item">

<a class="nav-link" href="profile.php">

👤 Профиль

</a>

</li>

</ul>

<ul class="navbar-nav">

<li class="nav-item">

<span class="navbar-text me-3">

<?= htmlspecialchars($_SESSION["username"]) ?>

</span>

</li>

<li class="nav-item">

<a class="btn btn-danger btn-sm"

href="../logout.php">

Выход

</a>

</li>

</ul>

</div>

</div>

</nav>
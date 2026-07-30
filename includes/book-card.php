<div class="col-lg-3 col-md-4 col-sm-6 mb-4">

    <div class="card book-card h-100">

        <div class="book-cover">

            <?php if (!empty($row["image"])): ?>

                <img
                    src="assets/images/<?= htmlspecialchars($row["image"]) ?>"
                    class="book-image"
                    alt="<?= htmlspecialchars($row["title"]) ?>">

            <?php else: ?>

                <img
                    src="assets/images/no-cover.png"
                    class="book-image"
                    alt="Нет обложки">

            <?php endif; ?>

        </div>

        <div class="card-body d-flex flex-column">

            <h5 class="book-title">
                <?= htmlspecialchars($row["title"]) ?>
            </h5>

            <div class="book-info">

                <p>
                    <strong>Автор:</strong><br>
                    <?= htmlspecialchars($row["full_name"]) ?>
                </p>

                <p>
                    <strong>Жанр:</strong><br>
                    <?= htmlspecialchars($row["name"]) ?>
                </p>

                <p>
                    <strong>Год:</strong>
                    <?= $row["year_published"] ?>
                </p>

            </div>

            <div class="mt-auto">

                <a
                    href="books/details.php?id=<?= $row["id"] ?>"
                    class="btn btn-primary w-100">

                    Подробнее

                </a>

            </div>

        </div>

    </div>

</div>
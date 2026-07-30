<div class="col-lg-3 col-md-4 col-sm-6 mb-4">

    <div class="card book-card h-100">

        <div class="book-cover">

            <?php if (!empty($row["image"])): ?>

                <img
                    src="../assets/images/<?= htmlspecialchars($row["image"]) ?>"
                    class="book-image"
                    alt="<?= htmlspecialchars($row["title"]) ?>">

            <?php else: ?>

                <img
                    src="../assets/images/no-cover.png"
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

                <?php if($row["copies"]>0): ?>

                    <span class="badge bg-success w-100 mb-3">
                        В наличии: <?= $row["copies"] ?>
                    </span>

                <?php else: ?>

                    <span class="badge bg-danger w-100 mb-3">
                        Нет экземпляров
                    </span>

                <?php endif; ?>

                <a
                    href="book.php?id=<?= $row["id"] ?>"
                    class="btn btn-primary w-100 mb-2">

                    Подробнее

                </a>

                <?php if($row["copies"]>0): ?>

                    <a
                        href="request.php?book_id=<?= $row["id"] ?>"
                        class="btn btn-success w-100">

                        Оформить заявку

                    </a>

                <?php else: ?>

                    <button
                        class="btn btn-secondary w-100"
                        disabled>

                        Нет экземпляров

                    </button>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>
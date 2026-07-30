<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/db.php";

$sql = "
SELECT

DATE_FORMAT(issue_date,'%Y-%m') AS month,

COUNT(*) AS total

FROM borrowings

GROUP BY DATE_FORMAT(issue_date,'%Y-%m')

ORDER BY month ASC
";

$result = $conn->query($sql);

$rows = [];
$months = [];
$totals = [];

while ($row = $result->fetch_assoc()) {

    $date = DateTime::createFromFormat('Y-m', $row["month"]);

    $rows[] = [
        "label" => $date->format('m.Y'),
        "total" => (int) $row["total"],
    ];

    $months[] = $date->format('m.Y');
    $totals[] = (int) $row["total"];

}

// Для таблицы удобнее смотреть от новых к старым
$rowsDesc = array_reverse($rows);

include "../../includes/header.php";
include "../../includes/navbar.php";
?>

<div class="container mt-5 mb-5">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2 class="d-flex align-items-center gap-2">
        <i class="bi bi-graph-up text-warning"></i>
        Статистика выдач по месяцам
    </h2>

    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
        Назад
    </a>

</div>

<?php if (empty($rows)): ?>

    <div class="card border-0 shadow">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            Пока нет данных о выдачах.
        </div>
    </div>

<?php else: ?>

    <div class="card border-0 shadow mb-4">

        <div class="card-body">

            <canvas id="statsChart" height="90"></canvas>

        </div>

    </div>

    <div class="card border-0 shadow">

        <div class="card-body">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-dark">

                    <tr>

                        <th>Месяц</th>

                        <th width="280">Количество выдач</th>

                    </tr>

                </thead>

                <tbody>

                    <?php

                    $maxTotal = max($totals);

                    foreach ($rowsDesc as $row):

                    ?>

                    <tr>

                        <td class="fw-bold"><?= $row["label"] ?></td>

                        <td>

                            <div class="d-flex align-items-center gap-2">

                                <div class="progress flex-grow-1" style="height:10px;">
                                    <div
                                        class="progress-bar bg-warning"
                                        style="width: <?= round(($row["total"] / $maxTotal) * 100) ?>%;">
                                    </div>
                                </div>

                                <span class="badge bg-warning text-dark"><?= $row["total"] ?></span>

                            </div>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <script>
    window.addEventListener("load", function () {

        const canvas = document.getElementById("statsChart");

        if (!canvas || typeof Chart === "undefined") return;

        new Chart(canvas, {
            type: "line",
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: "Количество выдач",
                    data: <?= json_encode($totals) ?>,
                    borderColor: "#fd7e14",
                    backgroundColor: "rgba(253,126,20,0.15)",
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: "#fd7e14"
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

    });
    </script>

<?php endif; ?>

</div>

<?php include "../../includes/footer.php"; ?>

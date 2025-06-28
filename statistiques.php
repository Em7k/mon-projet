<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'formulaire_db');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Statistiques Commandes
$totalCommandes = $conn->query("SELECT COUNT(*) AS total FROM commande")->fetch_assoc()['total'];
$totalValidees = $conn->query("SELECT COUNT(*) AS validees FROM commande WHERE statut = 'Validée'")->fetch_assoc()['validees'];
$totalEnAttente = $conn->query("SELECT COUNT(*) AS attente FROM commande WHERE statut IS NULL OR statut = 'En attente'")->fetch_assoc()['attente'];

$statistiquesPlats = $conn->query("
    SELECT nom_plat, COUNT(*) AS nb_commandes 
    FROM commande 
    GROUP BY nom_plat 
    ORDER BY nb_commandes DESC
");

$labels = [];
$values = [];
while ($row = $statistiquesPlats->fetch_assoc()) {
    $labels[] = $row['nom_plat'];
    $values[] = $row['nb_commandes'];
}

// Statistiques Réservations
$totalReservations = $conn->query("SELECT COUNT(*) AS total FROM reservations")->fetch_assoc()['total'];
$reservations = $conn->query("
    SELECT DATE(date) as date, COUNT(*) as total
    FROM reservations
    GROUP BY DATE(date)
    ORDER BY DATE(date) ASC
");

$reservationLabels = [];
$reservationCounts = [];
while ($row = $reservations->fetch_assoc()) {
    $reservationLabels[] = $row['date'];
    $reservationCounts[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques des Commandes & Réservations</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="assets/css/stylet.css" rel="stylesheet">
    <style>
        .card-stat {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }
        .card-stat h3 {
            font-size: 2rem;
            margin: 0;
        }
        .card-stat p {
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">📊 Statistiques Globales</h2>

    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card-stat">
                <h3><?= $totalCommandes ?></h3>
                <p>Total des commandes</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-stat">
                <h3><?= $totalValidees ?></h3>
                <p>Commandes validées</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-stat">
                <h3><?= $totalEnAttente ?></h3>
                <p>Commandes en attente</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-stat">
                <h3><?= $totalReservations ?></h3>
                <p>Total des réservations</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Camembert des plats -->
        <div class="col-md-6 mb-4">
            <h5 class="text-center">Répartition des plats commandés</h5>
            <canvas id="platChart"></canvas>
        </div>

        <!-- Barres des statuts -->
        <div class="col-md-6 mb-4">
            <h5 class="text-center">Statut des commandes</h5>
            <canvas id="statutChart"></canvas>
        </div>

        <!-- Courbe des réservations -->
        <div class="col-md-12 mb-4">
            <h5 class="text-center">Évolution des Réservations</h5>
            <canvas id="reservationChart"></canvas>
        </div>
    </div>
</div>

<footer id="footer" class="footer dark-background mt-5">
    <div class="container">
        <div class="row gy-3">
            <div class="col-lg-3 col-md-6 d-flex">
                <i class="bi bi-geo-alt icon"></i>
                <div class="address">
                    <h4>Adresse</h4>
                    <p>136 Avenue Habib Bourguiba Km Soukra 2038<br>Tunisie, Tunis</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 d-flex">
                <i class="bi bi-telephone icon"></i>
                <div>
                    <h4>Contact</h4>
                    <p><strong>Phone:</strong> <span>+21652076472</span><br>
                    <strong>Email:</strong> <span>feliciakisukika@gmail.com</span></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 d-flex">
                <i class="bi bi-clock icon"></i>
                <div>
                    <h4>Heures d'ouverture</h4>
                    <p><strong>Lun-Sam :</strong> 12h - 00h<br><strong>Dimanche :</strong> Fermé</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4>Suivez-nous</h4>
                <div class="social-links d-flex">
                    <a href="https://x.com/twitter?lang=en" class="twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://www.facebook.com/" class="facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/" class="instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.linkedin.com/in/felly-kisukika-kayana-b086b8252" class="linkedin"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="container copyright text-center mt-4">
        <p>© <strong class="px-1 sitename">Kisukika Kayana Fellycia</strong></p>
    </div>
</footer>

<!-- Scripts -->
<script>
    const labels = <?= json_encode($labels) ?>;
    const values = <?= json_encode($values) ?>;
    const reservationLabels = <?= json_encode($reservationLabels) ?>;
    const reservationCounts = <?= json_encode($reservationCounts) ?>;

    // Camembert des plats
    const ctx1 = document.getElementById('platChart').getContext('2d');
    new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Commandes par plat',
                data: values,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                    '#FF9F40', '#C9CBCF', '#009688', '#795548', '#CDDC39'
                ]
            }]
        },
        options: { responsive: true }
    });

    // Statuts commandes
    const ctx2 = document.getElementById('statutChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Validées', 'En attente'],
            datasets: [{
                label: 'Nombre de commandes',
                data: [<?= $totalValidees ?>, <?= $totalEnAttente ?>],
                backgroundColor: ['#28a745', '#ffc107']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }
        }
    });

    // Réservations (courbe)
    const ctx3 = document.getElementById('reservationChart').getContext('2d');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: reservationLabels,
            datasets: [{
                label: 'Nombre de réservations',
                data: reservationCounts,
                fill: false,
                borderColor: '#007bff',
                tension: 0.2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }
        }
    });
</script>
</body>
</html>

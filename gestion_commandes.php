<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'formulaire_db');

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$parPage = $limit;

// Filtres
$nom_client = isset($_GET['nom_client']) ? $conn->real_escape_string($_GET['nom_client']) : '';
$statut = isset($_GET['statut']) ? $conn->real_escape_string($_GET['statut']) : '';

// Construction dynamique des filtres
$conditions = [];
if (!empty($nom_client)) {
    $conditions[] = "nom_client LIKE '%$nom_client%'";
}
if (!empty($statut)) {
    $conditions[] = "statut = '$statut'";
}
$where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

// Total filtré
$resultTotal = $conn->query("SELECT COUNT(*) AS total FROM commande $where");
$rowTotal = $resultTotal->fetch_assoc();
$total = $rowTotal['total'];
$pages = ceil($total / $limit);

// Récupération des commandes avec filtres et pagination
$sql = "SELECT * FROM commande $where ORDER BY id ASC LIMIT $parPage OFFSET $offset";
$result = $conn->query($sql);

// Génération de la base de l'URL pour la pagination
$get_params = $_GET;
unset($get_params['page']);
$query_string = http_build_query($get_params);
$base_url = '?' . $query_string . (strlen($query_string) > 0 ? '&' : '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
    <title>Gestion des Réservations</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
      <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/stylet.css" rel="stylesheet">
  <style>
    .pagination {
      display: flex;
      justify-content: center;
      margin-top: 20px;
      gap: 5px;
    }
    .pagination a, .pagination span {
      padding: 6px 12px;
      border: 1px solid #ddd;
      color: #000;
      text-decoration: none;
      border-radius: 4px;
    }
    .pagination a:hover {
      background-color: #007bff;
      color: white;
      border-color: #007bff;
    }
    .pagination .active {
      background-color: #007bff;
      color: white;
      border-color: #007bff;
      pointer-events: none;
    }
    .pagination .disabled {
      color: #ccc;
      pointer-events: none;
      border-color: #ddd;
    }
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
    }

    main.container {
      flex: 1;
      padding-bottom: 20px;
    }

    footer.footer {
      background-color: #222;
      color: white;
      padding: 30px 0;
    }
    .table td {
  vertical-align: middle;
}

  </style>
</head>
<body>
<main class="container">
  <h2 class="my-4 text-center">Gestion des commandes</h2>

  <!-- Formulaire de filtrage -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-md-4">
      <input type="text" name="nom_client" class="form-control" placeholder="Rechercher par nom du client" value="<?= htmlspecialchars($_GET['nom_client'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <select name="statut" class="form-select">
        <option value="">-- Statut --</option>
        <option value="Validée" <?= (isset($_GET['statut']) && $_GET['statut'] === 'Validée') ? 'selected' : '' ?>>Validée</option>
        <option value="En attente" <?= (isset($_GET['statut']) && $_GET['statut'] === 'En attente') ? 'selected' : '' ?>>En attente</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary">Filtrer</button>
    </div>
  </form>

  <!-- Tableau des commandes -->
  <table class="table table-bordered">
    <thead>
      <tr class="table-primary">
        <th>ID</th>
        <th>Nom du plat</th>
        <th>Client</th>
        <th>Téléphone</th>
        <th>Quantité</th>
        <th>Date</th>
        <th>Statut</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['nom_plat']) ?></td>
          <td><?= htmlspecialchars($row['nom_client']) ?></td>
          <td><?= htmlspecialchars($row['telephone']) ?></td>
          <td><?= htmlspecialchars($row['quantite']) ?></td>
          <td><?= htmlspecialchars($row['date_commande']) ?></td>
         <td>
  <?php if (($row['statut'] ?? 'En attente') === 'Validée'): ?>
    <span class="badge bg-success">Validée</span>
  <?php else: ?>
    <span class="badge bg-warning text-dark">En attente</span>
  <?php endif; ?>
</td>

          <td class="d-flex gap-2">
  <?php if (($row['statut'] ?? 'En attente') !== 'Validée'): ?>
    <a href="valider_commande.php?id=<?= $row['id'] ?>" class="btn btn-outline-success btn-sm" onclick="return confirm('Valider cette commande ?')">
      <i class="bi bi-check-circle"></i> Valider
    </a>
  <?php endif; ?>
  <a href="supprimer_commande.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette commande ?')">
    <i class="bi bi-trash"></i> Supprimer
  </a>
</td>

        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="8" class="text-center">Aucune commande enregistrée.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <nav class="pagination" aria-label="Pagination navigation">
    <?php if ($page > 1): ?>
      <a href="<?= $base_url ?>page=<?= $page - 1 ?>">&lt;</a>
    <?php else: ?>
      <span class="disabled">&lt;</span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <?php if ($i == $page): ?>
        <span class="active"><?= $i ?></span>
      <?php else: ?>
        <a href="<?= $base_url ?>page=<?= $i ?>"><?= $i ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $pages): ?>
      <a href="<?= $base_url ?>page=<?= $page + 1 ?>">&gt;</a>
    <?php else: ?>
      <span class="disabled">&gt;</span>
    <?php endif; ?>
  </nav>
</main>

<footer id="footer" class="footer dark-background">
    <div class="container">
      <div class="row gy-3">
        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-geo-alt icon"></i>
          <div class="address">
            <h4>Adresse</h4>
            <p>136 Avenue Habib Bourguiba Km Soukra 2038</p>
            <p>Tunisie, Tunis</p>
            <p></p>
          </div>

        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-telephone icon"></i>
          <div>
            <h4>Contact</h4>
            <p>
              <strong>Phone:</strong> <span>+21652076472</span><br>
              <strong>Email:</strong> <span>feliciakisukika@gmail.com</span><br>
            </p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-clock icon"></i>
          <div>
            <h4>Heures d'ouverture</h4>
            <p>
              <strong>Lun-Sam :</strong> <span>12h - 00h</span><br>
              <strong>Dimanche :</strong> <span>Fermé</span>
            </p>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <h4>Suivez-nous</h4>
          <div class="social-links d-flex">
            <a href="https://x.com/twitter?lang=en" class="twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="https://www.facebook.com/" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/" class="instagram"><i class="bi bi-instagram"></i></a>
            <a href="https://www.linkedin.com/in/felly-kisukika-kayana-b086b8252?lipi=urn%3Ali%3Apage%3Ad_flagship3_profile_view_base_contact_details%3BA6x9OiD2RAClrtozSEbMZA%3D%3D" class="linkedin"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright by</span> <strong class="px-1 sitename">Kisukika kayana Fellycia</strong> <span></span></p>
      
    </div>

  </footer>
   <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
</body>
</html>

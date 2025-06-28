<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$dbname = 'formulaire_db'; 
$username = 'root';  
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Suppression ou validation via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_id'])) {
            $idToDelete = (int) $_POST['delete_id'];
            $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");
            $stmt->bindParam(':id', $idToDelete, PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['message'] = "Réservation supprimée avec succès.";
            header("Location: text.php"); 
            exit();
        }

        if (isset($_POST['validate_id'])) {
            $idToValidate = (int) $_POST['validate_id'];
            $stmt = $pdo->prepare("UPDATE reservations SET confirmed = 1 WHERE id = :id");
            $stmt->bindParam(':id', $idToValidate, PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['message'] = "Réservation validée avec succès.";
            header("Location: text.php"); 
            exit();
        }
    }

    // Pagination
    $limit = 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Filtres
    $where = [];
    if (!empty($_GET['status'])) {
        $status = $_GET['status'] === 'confirmed' ? 1 : 0;
        $where[] = "confirmed = " . (int)$status;
    }
    if (!empty($_GET['date'])) {
        $where[] = "date = " . $pdo->quote($_GET['date']);
    }
    if (!empty($_GET['search'])) {
        $search = $pdo->quote('%' . $_GET['search'] . '%');
        $where[] = "(name LIKE $search OR email LIKE $search)";
    }

    $whereClause = count($where) ? "WHERE " . implode(" AND ", $where) : "";

    $stmt = $pdo->query("SELECT COUNT(*) FROM reservations $whereClause");
    $total = $stmt->fetchColumn();
    $pages = ceil($total / $limit);

    $stmt = $pdo->query("SELECT * FROM reservations $whereClause ORDER BY id ASC LIMIT $limit OFFSET $offset");
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
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
    
</head>
<body>
    <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="home.html" class="logo d-flex align-items-center me-auto me-xl-0">
        <h1 class="sitename">Les délices de Gomez</h1>
        <span>.</span>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Gestion Réservations<br></a></li>
          <li class="dropdown"><a href="#"><span>Gestion des Commandes</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="statistiques.php">Statistiques</a></li>
              <li><a href="gestion_commandes.php">Gestion des Commandes</a></li>
            </ul>
          </li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>
   
<main class="main">
    <section id="about" class="about section">
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert-success">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="filters">
        <form method="get">
            <label>Statut :</label>
            <select name="status">
                <option value="">-- Tous --</option>
                <option value="pending" <?= isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                <option value="confirmed" <?= isset($_GET['status']) && $_GET['status'] === 'confirmed' ? 'selected' : '' ?>>Validée</option>
            </select>
            <label>Date :</label>
            <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
            <label>Recherche :</label>
            <input type="text" name="search" placeholder="Nom ou Email" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit">Filtrer</button>
        </form>
    </div>

    <?php if (count($reservations) === 0): ?>
        <p>Aucune réservation trouvée.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Personnes</th>
                    <th>Message</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                   <tr class="<?= $res['confirmed'] ? 'confirmed' : 'pending' ?>">

                        <td><?= htmlspecialchars($res['id']) ?></td>
                        <td><?= htmlspecialchars($res['name']) ?></td>
                        <td><?= htmlspecialchars($res['email']) ?></td>
                        <td><?= htmlspecialchars($res['phone']) ?></td>
                        <td><?= htmlspecialchars($res['date']) ?></td>
                        <td><?= htmlspecialchars($res['time']) ?></td>
                        <td><?= htmlspecialchars($res['people']) ?></td>
                        <td><?= nl2br(htmlspecialchars($res['message'])) ?></td>
                        <td>
                            <?= $res['confirmed'] 
                                ? '<span class="badge bg-success">Validée</span>' 
                                : '<span class="badge bg-warning text-dark">En attente</span>' ?>
                        </td>
                        <td class="actions">
                            <?php if (!$res['confirmed']): ?>
                                <form method="post" onsubmit="return confirm('Valider cette réservation ?')">
                                    <input type="hidden" name="validate_id" value="<?= $res['id'] ?>">
                                    <button type="submit" class="validate"><i class="bi bi-check-circle"></i> Valider</button>
                                </form>
                            <?php endif; ?>
                            <form method="post" onsubmit="return confirm('Supprimer cette réservation ?')">
                                <input type="hidden" name="delete_id" value="<?= $res['id'] ?>">
                                <button type="submit" class="delete"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

        <div class="footer-bar">
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?><?= isset($_GET['date']) ? '&date=' . $_GET['date'] : '' ?><?= isset($_GET['search']) ? '&search=' . $_GET['search'] : '' ?>">&laquo; </a>
                <?php endif; ?>

                <?php if ($page < $pages): ?>
                    <a href="?page=<?= $page + 1 ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?><?= isset($_GET['date']) ? '&date=' . $_GET['date'] : '' ?><?= isset($_GET['search']) ? '&search=' . $_GET['search'] : '' ?>"> &raquo;</a>
                <?php endif; ?>
            </div>
            <div>
                <a href="logout.php" class="logout-link">Se déconnecter</a>
            </div>
        </div>
    <?php endif; ?>
    </section>
    

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

<?php
// Connexion PDO
$pdo = new PDO('mysql:host=localhost;dbname=formulaire_db', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

if (!isset($_GET['id'])) {
    die('Commande non spécifiée');
}

$id = (int)$_GET['id'];

// Récupérer la commande
$stmt = $pdo->prepare("SELECT * FROM commande WHERE id = ?");
$stmt->execute([$id]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    die('Commande introuvable');
}

$platSelectionne = $commande['nom_plat'] ?? '';

$plats = [
    ['nom' => 'Raviolis Maison à la Truffe', 'image' => 'assets/img/menu/Di5.jpg'],
    ['nom' => 'Rôti d\'Agneau aux Herbes', 'image' => 'assets/img/menu/Di6.jpg'],
    ['nom' => 'Saint-Jacques en Croûte de Sésame', 'image' => 'assets/img/menu/Di4.jpg'],
    ['nom' => 'Chateaubriand au Poivre', 'image' => 'assets/img/menu/Di3.jpg'],
    ['nom' => 'Lobster Roll', 'image' => 'assets/img/menu/Di2.jpeg'],
    ['nom' => 'Filet Mignon Sauce Truffe', 'image' => 'assets/img/menu/Di1.avif'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Commande</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
   <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    .galerie {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  max-width: 700px;
  margin: 20px auto;
}
    .plat { margin: 10px; text-align: center; cursor: pointer; }
    .plat img { width: 200px; height: 150px; object-fit: cover; border: 2px solid transparent; }
    .plat img.selected { border-color: red; }
    form {
  margin-bottom: 60px; /* espace entre formulaire et footer */
}

button[type="submit"] {
  margin-top: 20px;
}
/* Conteneur centré */
  .form-main {
    max-width: 400px;
    margin: 0 auto;
    text-align: center; /* centre le texte des éléments enfants */
  }

  /* Pour que les champs restent alignés à gauche */
  .form-main .mb-3 {
    text-align: left;
  }

  </style>
</head>
<body>
  <div class="container mt-5">
    <h1 class="text-center">Modifier Commande</h1>
    <div class="galerie">
      <?php foreach ($plats as $plat): ?>
        <div class="plat" onclick="selectPlat('<?= htmlspecialchars($plat['nom']) ?>', this)">
          <img src="<?= htmlspecialchars($plat['image']) ?>" 
               class="<?= ($plat['nom'] === $platSelectionne) ? 'selected' : '' ?>" 
               alt="<?= htmlspecialchars($plat['nom']) ?>">
          <div><?= htmlspecialchars($plat['nom']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Formulaire avec le champ caché dedans -->
    <form method="POST" action="update_commande.php">
      <input type="hidden" name="id" value="<?= $id ?>">
      <input type="hidden" name="nom_plat" id="nom_plat" value="<?= htmlspecialchars($platSelectionne) ?>">

      <div class="mb-3">
        <label for="nom_client">Nom </label>
        <input type="text" class="form-control" id="nom_client" name="nom_client" required value="<?= htmlspecialchars($commande['nom_client']) ?>">
      </div>

      <div class="mb-3">
        <label for="telephone">Téléphone </label>
        <input type="text" class="form-control" id="telephone" name="telephone" required value="<?= htmlspecialchars($commande['telephone']) ?>">
      </div>

      <div class="mb-3">
        <label for="quantite">Quantité </label>
        <input type="number" class="form-control" id="quantite" name="quantite" min="1" required value="<?= htmlspecialchars($commande['quantite']) ?>">
      </div>

      <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
  </div>

  <script>
    function selectPlat(nomPlat, element) {
      document.getElementById('nom_plat').value = nomPlat;

      const images = document.querySelectorAll('.galerie .plat img');
      images.forEach(img => img.classList.remove('selected'));

      const img = element.querySelector('img');
      img.classList.add('selected');
    }
  </script>
   <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
  <footer id="footer" class="footer dark-background">
    <div class="container">
      <div class="row gy-3">
        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-geo-alt icon"></i>
          <div class="address">
            <h4>Adresse</h4>
            <p>136 Avenue Habib Bourguiba Km Soukra 2038</p>
            <p>Tunisie, Tunis</p>
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
      <p>© <span>Copyright by</span> <strong class="px-1 sitename">Kisukika kayana Fellycia</strong></p>
    </div>
  </footer>
</body>
</html>

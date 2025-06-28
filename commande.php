<?php
$plats = [
    ['nom' => 'Raviolis Maison à la Truffe', 'image' => 'assets/img/menu/Di5.jpg'],
    ['nom' => 'Rôti d\'Agneau aux Herbes', 'image' => 'assets/img/menu/Di6.jpg'],
    ['nom' => 'Saint-Jacques en Croûte de Sésame', 'image' => 'assets/img/menu/Di4.jpg'],
    ['nom' => 'Chateaubriand au Poivre', 'image' => 'assets/img/menu/Di3.jpg'],
    ['nom' => 'Lobster Roll', 'image' => 'assets/img/menu/Di2.jpeg'],
    ['nom' => 'Filet Mignon Sauce Truffe', 'image' => 'assets/img/menu/Di1.avif'],
];

$platSelectionne = $_GET['plat'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Les délices de Gomez</title>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin><
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .galerie {
          display: flex;
          flex-wrap: nowrap;
          justify-content: center;
          max-width: 680px;
          margin-bottom: 20px;
          margin-left: auto;
          margin-right: auto;
        }
        .plat {
          margin: 10px;
          text-align: center;
          cursor: pointer;
          width: 200px;
          flex-shrink: 0;
        }
        .plat img {
          width: 200px;
          height: 150px;
          border: 2px solid transparent;
          transition: border-color 0.3s;
          object-fit: cover;
        }
        .plat img:hover {
          border-color: #f08;
        }
        .plat img.selected {
          border-color: #f00 !important;
        }
        .plat-name {
          margin-top: 5px;
          font-weight: bold;
          font-size: 16px;
          color: #333;
        }
        form {
          max-width: 400px;
          margin: auto;
          margin-bottom: 60px; /* Espace entre formulaire et footer */
        }
        button[type="submit"] {
          width: 100%;
          padding: 10px;
          background: #007bff; /* bleu */
          border: none;
          color: white;
          font-weight: bold;
          cursor: pointer;
          border-radius: 4px;
          transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
          background: #0056b3; /* bleu foncé au survol */
        }
    </style>
</head>
<body>

  <h1 style="text-align:center;">Choisissez un plat </h1>

  <?php
  // Affichage en 2 lignes de 3 plats
  for ($row = 0; $row < 2; $row++) {
      echo '<div class="galerie">';
      for ($i = $row * 3; $i < ($row + 1) * 3; $i++) {
          if (!isset($plats[$i])) break;
          $plat = $plats[$i];
          ?>
          <div class="plat">
            <a href="?plat=<?= urlencode($plat['image']) ?>">
              <img
                src="<?= htmlspecialchars($plat['image']) ?>"
                alt="<?= htmlspecialchars($plat['nom']) ?>"
                class="<?= $plat['image'] === $platSelectionne ? 'selected' : '' ?>"
                title="<?= htmlspecialchars($plat['nom']) ?>"
              >
            </a>
            <div class="plat-name"><?= htmlspecialchars($plat['nom']) ?></div>
          </div>
          <?php
      }
      echo '</div>';
  }
  ?>

  <?php if ($platSelectionne): ?>
    <?php
    $platTrouve = null;
    foreach ($plats as $plat) {
        if ($plat['image'] === $platSelectionne) {
            $platTrouve = $plat;
            break;
        }
    }
    ?>

    <?php if ($platTrouve): ?>
      <h2 style="text-align:center;">Commander : <?= htmlspecialchars($platTrouve['nom']) ?></h2>
      <div style="text-align:center;">
        <img src="<?= htmlspecialchars($platTrouve['image']) ?>" alt="Photo du plat" width="300" height="225" style="border-radius:8px;"><br><br>
      </div>

      <form method="POST" action="create_commande.php">
        <input type="hidden" name="nom_plat" value="<?= htmlspecialchars($platTrouve['nom']) ?>">

        <label>Nom </label><br>
        <input type="text" name="nom_client" required style="width:100%; padding:6px;"><br><br>

        <label>Téléphone </label><br>
        <input type="text" name="telephone" required style="width:100%; padding:6px;"><br><br>

        <label>Quantité </label><br>
        <input type="number" name="quantite" min="1" value="1" style="width:100%; padding:6px;"><br><br>

        <button type="submit">Commander</button>
      </form>
    <?php else: ?>
      <p>Plat non trouvé.</p>
    <?php endif; ?>

  <?php else: ?>
    <p style="text-align:center;">Veuillez sélectionner un plat en cliquant sur une photo ci-dessus.</p>
  <?php endif; ?>

  <footer id="footer" class="footer dark-background">
    <div class="container">
      <div class="row gy-3">
        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-geo-alt icon"></i>
          <div class="address">
            <h4>Adresse</h4>
            <p>136 Avenue toko</p>
            <p>kinshasa, RDC</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-telephone icon"></i>
          <div>
            <h4>Contact</h4>
            <p>
              <strong>Phone:</strong> <span>+243815226902</span><br>
              <strong>Email:</strong> <span>emmanuelkisukika@gmail.com</span><br>
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

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>

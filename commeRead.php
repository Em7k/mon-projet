<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=formulaire_db', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

// Récupérer toutes les commandes
$stmt = $pdo->query("SELECT * FROM commande ORDER BY id ASC");
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Liste des commandes</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Les délices de Gomez</title>
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
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 50px; /* espace entre tableau et footer */
        }
        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #000000; /* noir */
            color: white;
        }
        a.edit-link {
            background-color: #007bff;
            color: white;
            padding: 5px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        a.edit-link:hover {
            background-color: #007bff;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        /* Footer full width */
        footer#footer {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            background-color: #222; /* ou ta couleur "dark-background" */
            color: white;
            padding: 40px 0;
        }
        footer#footer .container {
            max-width: 1140px; /* Bootstrap container max width */
            margin: 0 auto;
        }
    </style>
</head>
<body>

<main>
    <h1>Liste des commandes</h1>

    <?php if (count($commandes) === 0): ?>
        <p>Aucune commande trouvée.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plat</th>
                    <th>Nom client</th>
                    <th>Téléphone</th>
                    <th>Quantité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['id']) ?></td>
                        <td><?= htmlspecialchars($commande['nom_plat']) ?></td>
                        <td><?= htmlspecialchars($commande['nom_client']) ?></td>
                        <td><?= htmlspecialchars($commande['telephone']) ?></td>
                        <td><?= htmlspecialchars($commande['quantite']) ?></td>
                        <td>
                            <a class="edit-link" href="edit_commande.php?id=<?= urlencode($commande['id']) ?>">Modifier</a>
                            <a class="delete-link" href="delete_commande.php?id=<?= urlencode($commande['id']) ?>" onclick="return confirm('Voulez-vous vraiment supprimer cette commande ?');" style="background-color: #dc3545; color: white; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-weight: bold; margin-left: 8px;">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
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
      <p>© <span>Copyright by</span> <strong class="px-1 sitename">Kisukika musitu emmanuel</strong></p>
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

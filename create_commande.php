<?php
$pdo = new PDO('mysql:host=localhost;dbname=formulaire_db', 'root', '');
$stmt = $pdo->prepare("INSERT INTO commande (nom_plat, nom_client, telephone, quantite) VALUES (?, ?, ?, ?)");
$stmt->execute([
    $_POST['nom_plat'],
    $_POST['nom_client'],
    $_POST['telephone'],
    $_POST['quantite']
]);

header("Location: commeRead.php");
exit;

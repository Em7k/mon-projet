<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = new PDO('mysql:host=localhost;dbname=formulaire_db', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Récupérer les données du formulaire
    $id = (int) $_POST['id'];
    $nom_client = trim($_POST['nom_client'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $quantite = (int) ($_POST['quantite'] ?? 1);
    $nom_plat = trim($_POST['nom_plat'] ?? '');

    // Mettre à jour la commande
    $stmt = $pdo->prepare("UPDATE commande SET nom_client = ?, telephone = ?, quantite = ?, nom_plat = ? WHERE id = ?");
    $stmt->execute([$nom_client, $telephone, $quantite, $nom_plat, $id]);

    // Rediriger vers la liste ou confirmation
    header("Location: commeRead.php?update=success");
    exit;
} else {
    echo "Méthode non autorisée.";
}

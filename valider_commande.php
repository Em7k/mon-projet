<?php
// valider_commande.php

$conn = new mysqli('localhost', 'root', '', 'formulaire_db');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Met à jour le statut en 'Validée'
    $sql = "UPDATE commande SET statut='Validée' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: gestion_commandes.php"); // Remplace par le nom de ta page principale
        exit;
    } else {
        echo "Erreur lors de la validation : " . $conn->error;
    }
} else {
    echo "ID non spécifié.";
}
?>

<?php
$conn = new mysqli('localhost', 'root', '', 'formulaire_db');
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$id = intval($_GET['id']);
$conn->query("DELETE FROM commande WHERE id = $id");

header('Location: gestion_commandes.php');
exit;
?>

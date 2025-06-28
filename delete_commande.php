<?php
$pdo = new PDO('mysql:host=localhost;dbname=formulaire_db', 'root', '');
$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM commande WHERE id = ?");
$stmt->execute([$id]);

header("Location: commeRead.php");
exit;

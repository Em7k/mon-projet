<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=formulaire_db", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = 'admin';
    $email = 'admin@admin.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);

    echo "Compte admin ajouté avec succès !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

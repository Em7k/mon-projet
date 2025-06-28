<?php
session_start();
$success = '';
$error = '';
try {
    $pdo = new PDO("mysql:host=localhost;dbname=formulaire_db", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = 'client'; 
        if ($username && $email && $password) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->rowCount() > 0) {
                $error = "Nom d'utilisateur ou email déjà utilisé.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$username, $email, $hashedPassword, $role])) {
                    $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                } else {
                    $error = "Erreur lors de l'inscription.";
                }
            }
        } else {
            $error = "Veuillez remplir tous les champs.";
        }
    }

} catch (PDOException $e) {
    $error = "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link href="assets/css/style.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>

<body>
    <div class="card">
        <h2>Inscription</h2>

        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label for="username">Nom </label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email </label>
            <input type="email" name="email" id="email" required>

            <label for="password">Mot de passe </label>
            <input type="password" name="password" id="password" required>

            <button type="submit">S'inscrire</button>
        </form>

        <div class="login-link">
            <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</body>
</html>

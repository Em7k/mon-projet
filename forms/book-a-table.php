<?php
session_start();

$host = 'localhost';
$dbname = 'formulaire_db'; 
$username = 'root';  
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les données
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $phone   = $_POST['phone'] ?? '';
    $date    = $_POST['date'] ?? '';
    $time    = $_POST['time'] ?? '';
    $people  = $_POST['people'] ?? '';
    $message = $_POST['message'] ?? '';

    // Insertion dans reservations
    $stmt = $pdo->prepare("INSERT INTO reservations (name, email, phone, date, time, people, message) 
                           VALUES (:name, :email, :phone, :date, :time, :people, :message)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':time', $time);
    $stmt->bindParam(':people', $people);
    $stmt->bindParam(':message', $message);

    if ($stmt->execute()) {
        // Vérifier si l'utilisateur existe déjà (par email ici)
        $checkUser = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $checkUser->execute([':email' => $email]);

        if ($checkUser->rowCount() === 0) {
            // Hash du mot de passe (ici on utilise le téléphone comme mdp temporaire)
            $hashedPassword = password_hash($phone, PASSWORD_DEFAULT);

            // Créer le compte utilisateur dans users
            $addUser = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'client')");
            $addUser->execute([
                ':username' => $name,
                ':email'    => $email,
                ':password' => $hashedPassword
            ]);
        }
        // Démarrer session
        $_SESSION['user'] = $email;
        $_SESSION['role'] = 'client';

        // Redirection
        header("Location: home.html");
        exit();
    } else {
        echo "Une erreur est survenue lors de l'enregistrement.";
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

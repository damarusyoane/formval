<?php 
echo "Bonjour DAMARUS";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="scripts/validate.js" defer></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6">Inscription</h2>
        <form id="signupForm" action="signup.php" method="POST">
            <div class="mb-4">
                <label for="nom" class="block text-gray-700">Nom</label>
                <input type="text" name="nom" id="nom" class="w-full p-2 border border-gray-300 rounded" required>
                <span id="nomError" class="text-red-500 text-sm"></span>
            </div>
            <div class="mb-4">
                <label for="prenom" class="block text-gray-700">Prénom</label>
                <input type="text" name="prenom" id="prenom" class="w-full p-2 border border-gray-300 rounded" required>
                <span id="prenomError" class="text-red-500 text-sm"></span>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">E-mail</label>
                <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded" required>
                <span id="emailError" class="text-red-500 text-sm"></span>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Mot de passe</label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded" required>
                <span id="passwordError" class="text-red-500 text-sm"></span>
            </div>
            <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">S'inscrire</button>
        </form>
    </div>
</body>
</html>





<?php
session_start();

// Établir une connexion avec la base de données
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'sign';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    // Vérifier si l'e-mail existe dans la base de données
    $req = $conn->prepare("SELECT * FROM client WHERE email = ?");
    $req->execute([$email]);
    $user = $req->fetch();

    if ($user) {
        // Générer un token unique
        $token = bin2hex(random_bytes(50)); // Token sécurisé
        $expires = date("Y-m-d H:i:s", time() + 3600); // Expire dans 1 heure

        // Stocker le token dans la base de données
        $stmt = $conn->prepare("UPDATE client SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
        $stmt->execute([$token, $expires, $email]);

        // Envoyer un e-mail avec le lien de réinitialisation
        $reset_link = "http://localhost:8080/reset.php?token=$token";
        $subject = "Réinitialisation de votre mot de passe";
        $message = "Cliquez sur ce lien pour réinitialiser votre mot de passe : $reset_link";
        $headers = "From: no-reply@D-CARS.com";

        if (mail($email, $subject, $message, $headers) === false) {
            error_log("Mail failed to send to $email");
        } else {
            echo "<div class='mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>Un lien de réinitialisation a été envoyé à votre adresse e-mail.</div>";
        }
    }
     else {
        echo "<div class='mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>Aucun compte associé à cet e-mail.</div>";
    }
 }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6">Réinitialisation du mot de passe</h2>
        <form action="request_reset.php" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-gray-700">E-mail</label>
                <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded">
            </div>
            <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Envoyer le lien de réinitialisation</button>
        </form>
    </div>
</body>
</html>
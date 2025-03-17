<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    // Valider l'adresse e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>Adresse e-mail invalide.</div>";
        exit;
    }

    // Vérifier si l'e-mail existe dans la base de données
    $req = $conn->prepare("SELECT * FROM client WHERE email = ?");
    $req->execute([$email]);
    $user = $req->fetch();

    if ($user) {
        // Générer un token unique
        $token = bin2hex(random_bytes(50)); // Token sécurisé
        $expires = date("Y-m-d H:i:s", time() + 3*3600); // Expire dans 1 heure

        // Stocker le token dans la base de données
        $stmt = $conn->prepare("UPDATE client SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
        $stmt->execute([$token, $expires, $email]);

        // Debug: Check if the token was saved
        $req = $conn->prepare("SELECT reset_token, reset_token_expires FROM client WHERE email = ?");
        $req->execute([$email]);
        $result = $req->fetch(PDO::FETCH_ASSOC);

       if ($result && $result['reset_token'] === $token) {
           echo "Token saved successfully. Expires at: " . $result['reset_token_expires'];
        } else {
               echo "Token not saved.";
            }

        $mail = new PHPMailer(true);

        try {
            // Paramètres du serveur SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Serveur SMTP (ex: Gmail)
            $mail->SMTPAuth   = true; // Activer l'authentification SMTP
            $mail->Username   = 'damarusngankou@gmail.com'; // Votre adresse e-mail
            $mail->Password   = 'btss mbkp ydjr thov'; // Votre mot de passe ou mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer le chiffrement TLS
            $mail->Port       = 587; // Port SMTP (587 pour TLS)

            // Désactiver la vérification SSL (développement uniquement)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            // Destinataires
            $mail->setFrom('no-reply@D-CARS.com', 'D-CARS'); // Expéditeur
            $mail->addAddress($email); // Destinataire

            // Contenu de l'e-mail
            $reset_link = "http://localhost:8080/reset.php?token=$token";
            $mail->isHTML(true); // Activer le format HTML
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body    = "Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='$reset_link'>Réinitialiser le mot de passe</a>";
            $mail->AltBody = "Cliquez sur ce lien pour réinitialiser votre mot de passe : $reset_link";

            // Envoyer l'e-mail
            $mail->send();
            echo "<div class='mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded'>Un lien de réinitialisation a été envoyé à votre adresse e-mail.</div>";
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'e-mail : " . $e->getMessage());
            echo "<div class='mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>Échec de l'envoi de l'e-mail. Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>Aucun compte associé à cet e-mail.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-[url('/assets/background1.jpg')] bg-cover bg-center bg-no-repeat w-full relative">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6">Password reset</h2>
        <form action="request_reset.php" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-gray-700">E-mail</label>
                <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded" required>
            </div>
            <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Send reset link</button>
        </form>
    </div>
</body>
</html>

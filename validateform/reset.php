<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Vérifier si le token est valide et n'a pas expiré
    $req = $conn->prepare("SELECT * FROM client WHERE reset_token = ? AND reset_token_expires > NOW()");
    $req->execute([$token]);
    $user = $req->fetch();

    if ($user) {
        // Afficher le formulaire pour définir un nouveau mot de passe
        if (isset($_POST['submit'])) {
            $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

            // Mettre à jour le mot de passe et effacer le token
            $stmt = $conn->prepare("UPDATE client SET motdepasse = ?, reset_token = NULL, reset_token_expires = NULL WHERE email = ?");
            $stmt->execute([$new_password, $user['email']]);

            echo "<div class='mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded'>Votre mot de passe a été réinitialisé avec succès.</div>";
        }
    } else {
        echo "<div class='mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>Lien de réinitialisation invalide ou expiré.</div>";
        exit; // Stop further execution if the token is invalid or expired
    }
} else {
    echo "<div class='mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>Token manquant.</div>";
    exit; // Stop further execution if no token is provided
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
<body class="flex items-center justify-center h-screen bg-[url('/assets/background1.jpg')] bg-cover bg-center bg-no-repeat w-full relative">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6">Password reset</h2>
        <form action="reset.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
            <div class="mb-4">
                <label for="new_password" class="block text-gray-700">New Password</label>
                <input type="password" name="new_password" id="new_password" class="w-full p-2 border border-gray-300 rounded" required>
            </div>
            <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Reset Password</button>
        </form>
    </div>
</body>
</html>
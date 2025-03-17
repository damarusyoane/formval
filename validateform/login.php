<?php
// session ouverte 
session_start();
// etablir une connexion avec la base de donneees
$host='localhost';
$user='root';
$password='';
$dbname='sign';

// check connexion avec la base de donnees
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

// traitement du formulaire d'inscription
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // verifie si le client existe deja
    $req = $conn->prepare("SELECT * FROM client WHERE email=?");
    $req->execute([$email]);
    $user = $req->fetch();


    if ($user && password_verify($password, $user['motdepasse'])) {
        // Demarre une session et redirige le client
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        header("Location: dashboard.php"); // redirige le client a son tableau de bord
        exit();
    } else {
        echo "<div class='mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>E-mail ou mot de passe incorrect.</div>";
    }
    setcookie('email_client', $email, time() + 7 * 24 * 3600, null, null, false, true);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-[url('/assets/background5.jpg')] bg-cover bg-center bg-no-repeat w-full relative">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6">Login</h2>
        <form action="login.php" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-gray-700">E-mail</label>
                <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded">
            </div>
            <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Login</button>
        </form>
        <p class="mt-4 text-center">
            <a href="request_reset.php" class="text-blue-500 hover:underline">Forgot password ?</a>
        </p>
        <p class="mt-4 text-center">Don't have an account? <a href="signup.php" class="text-blue-500">Signup here</a>.</p>
    </div>
</body>
</html>

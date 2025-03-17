<?php 
session_start();
// verifier si le client est deja connecter
if(!isset($_SESSION['nom'])){
    header('Location: login.php');
    exit();
}
//affiche les infos du client
$nom= $_SESSION['nom'];
$prenom= $_SESSION['prenom'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6">Welcome to your place, <?php echo $nom , $prenom; ?> !</h2>
        <p>You have been connected succesfuly.</p>
        <a href="logout.php" class="mt-4 block text-center text-blue-500 hover:underline">Logout</a>
    </div>
</body>
</html>
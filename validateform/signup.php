<?php
    // etablir une connnexion avec la base de donnees
    $host='localhost';
    $user='root';
    $password='';
    $dbname='sign';

    // check connexion avec la base de donnees
   try{
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }
   catch(PDOException $e){
    echo "Erreur de connexion : " . $e->getMessage();
   }

   //traitement du formulaire d'inscription
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $prenom= $_POST['prenom'];
    $email=$_POST['email'];
    // hasher le mot de passe  pour plus de protection
    $password= password_hash($_POST['password'], PASSWORD_BCRYPT);
   
   // verifie si le client existe deja
   $req = $conn->prepare("SELECT * FROM client WHERE email = :email");
   $req->execute(['email'=> $email]);
   if ($req->rowCount()>0){
    echo "<div class='mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>Cet e-mail est déjà utilisé.</div>";
   }
   else{
    // insere le client dans la base de donnees
    $req=$conn->prepare("INSERT INTO client (nom, prenom, email, motdepasse) VALUES (:nom,:prenom,:email,:motdepasse)");
    $req->execute(['nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'motdepasse' => $password]);
    echo "<div class='mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded'>Inscription réussie !</div>";
   }
 header("Refresh: 2; url=login.php");
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class=" flex items-center justify-center h-screen bg-[url('/assets/background2.jpg')] bg-cover bg-center bg-no-repeat w-full relative">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6">Signup</h2>
        <form id="signupForm" action="signup.php" method="POST">
            <div class="mb-4">
                <label for="nom" class="block text-gray-700">Name</label>
                <input type="text" name="nom" id="nom" class="w-full p-2 border border-gray-300 rounded">
                <span id="nomError" class="text-red-500 text-sm"></span>
            </div>
            <div class="mb-4">
                <label for="prenom" class="block text-gray-700">Surname</label>
                <input type="text" name="prenom" id="prenom" class="w-full p-2 border border-gray-300 rounded">
                <span id="prenomError" class="text-red-500 text-sm"></span>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">E-mail</label>
                <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded">
                <span id="emailError" class="text-red-500 text-sm"></span>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded">
                <span id="motdepasseError" class="text-red-500 text-sm"></span>
            </div>
            <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Signup</button>
        </form>
        <p class="mt-4 text-center">Already have an account? <a href="login.php" class="text-blue-500">Login here</a>.</p>
    </div>
    <script src="./validate.js"></script>
</body>
</html>

<?php
// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Récupérez les données du formulaire
  $name = $_POST["name"];
  $email = $_POST["email"];
  $message = $_POST["message"];

  // Créez un objet email
  $to = "ymbouebe@gmail.com";
  $subject = "Nouveau message de formulaire";
  $body = "Nom: $name\nEmail: $email\nMessage: $message";

  // Envoie l'email
  mail($to, $subject, $body);

  // Affichez un message de confirmation
  echo "Merci pour votre message !";
} else {
  // Affichez le formulaire
  ?>
  <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
    <label for="name">Nom :</label>
    <input type="text" id="name" name="name"><br><br>
    <label for="email">Email :</label>
    <input type="email" id="email" name="email"><br><br>
    <label for="message">Message :</label>
    <textarea id="message" name="message"></textarea><br><br>
    <input type="submit" value="Envoyer">
  </form>
  <?php
}
?>
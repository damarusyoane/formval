$(document).ready(function(){
    $('#signupForm').on('submit', function(e){
        // messages d'Erreur
        $('#nomError').text('');
        $('#prenomError').text('');
        $('#emailError').text('');
        $('#motdepasseError').text('');

        // validation du formulaire
       
          let nom= $('#nom').val().trim();
          let prenom= $('#prenom').val().trim();
          let email= $('#email').val().trim();
          let password= $('#password').val().trim();

          //si le formulaire est valide
          let isvalid=true;
            if (nom==''){
                $('#nomError').text('Vous devrez entrer un nom');
                isvalid=false;
            }
            if (prenom==''){
                $('#prenomError').text('Vous devrez entrer un prenom');
                isvalid=false;
            }
            if(email==''){
                $('#emailError').text('Vous devrez entrer un prenom');
                isvalid=false;
            } else if(!validateEmail(email)){
                $('#emailError').text("Format d'email invalide");
                isvalid=false;
            } 
            if(password==''){
                $('#motdepasseError').text('Mot de passe requis');
                isvalid=false;
            } else if(password.length<8){
                $('#motdepasseError').text('Le mot de passe doit contenir au moins 8 caracteres');
                isvalid=false;
            }
            // si le formulaire n'est pas valide on ne soummet pas
            if(!isvalid){
                e.preventDefault();
            }
    });
    // valider l'email
    function validateEmail(email){
            let regex= /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              return regex.test(email);
            }
});
// Gestion du formulaire de connexion

$(document).ready(function() {
  $(document).on('submit', '#loginForm', function(e) {
    e.preventDefault();


    const formData = {
        email: $('input[name="email"]').val(),
        mot_de_passe: $('input[name="mot_de_passe"]').val()
    };

    $.ajax({
        url: './login_traitement.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                console.log("connexion OKKKK");
                //$('#loginMessage').css('color', 'green').text('Bienvenue ' + response.pseudo + ' !');
                chargerContenu('dashboard','Darts Board'); 
            } else {
                $('#loginMessage').css('color', 'red').text(response.error);
            }
        },
        error: function () {
            $('#loginMessage').css('color', 'red').text("Erreur serveur.");
        }
    });
});
  });
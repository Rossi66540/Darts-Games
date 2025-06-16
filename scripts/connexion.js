// Gestion du formulaire de connexion
$(document).on('submit', '#loginForm', function (e) {
    e.preventDefault(); // Empêche le rechargement de la page

    const formData = {
        email: $('input[name="email"]').val(),
        mot_de_passe: $('input[name="mot_de_passe"]').val()
    };

    $.ajax({
        url: 'login_traitement.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $('#loginMessage').css('color', 'green').text('Connexion réussie !');
                // Tu peux rediriger ou charger un autre contenu
                chargerContenu('profil'); // ou dashboard, etc.
            } else {
                $('#loginMessage').css('color', 'red').text(response.message);
            }
        },
        error: function () {
            $('#loginMessage').css('color', 'red').text('Erreur serveur.');
        }
    });
});

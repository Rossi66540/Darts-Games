if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').then(registration => {
    console.log('Service Worker enregistré avec succès');
  }).catch(error => {
    console.log('Échec de l’enregistrement du Service Worker :', error);
  });
}

$(document).ready(function () {
  // Chargement par défaut
  chargerContenu('accueil');

  // Navigation
  $('nav button').on('click', function () {
    let page = $(this).data('page');
    chargerContenu(page);
  });

});

function afficherMenuSelonConnexion() {
  if (isConnected) {
    $('#menu_connecte').removeClass('hidden');
    $('#menu_non_connecte').addClass('hidden');
  } else {
    $('#menu_connecte').addClass('hidden');
    $('#menu_non_connecte').removeClass('hidden');
  }
}

  function chargerContenu(page,titre) { 
    $('#contenu').html('<p>Chargement...</p>');
    $.ajax({
      url: 'contenus/' + page + '.php',
      method: 'GET',
      success: function (data) {
        $('#contenu').html(data);
        $('#titre').html(titre);
        afficherMenuSelonConnexion(); // Met à jour les menus à chaque chargement de contenu
      },
      error: function () {
        $('#contenu').html('<p>Erreur de chargement.</p>');
      }
    });
  }
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
    let titre = $(this).data('titre');
    chargerContenu(page, titre);
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

function chargerContenu(page, titre) {
  console.log('laaaaa');
  console.log(page);
  $('#contenu').html('<p>Chargement...</p>');
  $.ajax({
    url: 'contenus/' + page + '.php',
    method: 'GET',
    success: function (data) {
      console.log(data);
      $('#contenu').html(data);
      $('#titre').html(titre);
      afficherMenuSelonConnexion(); // Met à jour les menus à chaque chargement de contenu
    },
    error: function () {
      $('#contenu').html('<p>Erreur de chargement.</p>');
    }
  });
}


function genererTableauNom(nbrJoueur, idTableJoueur) {
  console.log("nbrJoueur");
  console.log(nbrJoueur);
  let nbrJrs = nbrJoueur;
  $('#' + idTableJoueur).empty();

  $.ajax({
    url: './models/get_users.php', // <-- ce fichier doit retourner un JSON de type [{id_user: 1, pseudo: "Alice"}, ...]
    type: 'GET',
    dataType: 'json',
    success: function (data) {
      for (let i = 1; i <= nbrJrs; i++) {
        let select = '<select class="pseudo" id="in_pseudo_' + i + '" style="width:160px;">';
        select += '<option value=""> - - - </option>';
        data.forEach(function (user) {
          let selected = "";
          if (i == 1) {
            if (user.pseudo == nomUser) {
              selected = "selected";
            }
          }
          select += '<option value="' + user.pseudo + '" data-id="' + user.id + '" ' + selected + '>' + user.pseudo + '</option>';
        });
        select += '</select>';

        let html = '<tr><td>Joueur ' + i + '</td><td>' + select + '</td></tr>';
        $('#' + idTableJoueur).append(html);
      }

      $('#in_pseudo_1').focus();
    },
    error: function (e) {
      console.log(e);
      alert("Erreur lors du chargement des joueurs.");
    }
  });

  /*for (i = 1; i <= nbrJrs; i++) {
      let html = '<tr><td>Joueur ' + i + '</td><td><input class="pseudo" style="width:150px;" id="in_pseudo_' + i + '"/></td></tr>';
      $('#tableJoueurs').append(html);
  }*/
  $('#in_pseudo_1').focus();
}


function affichageModePartie(){
  $('#footer_principal').hide();
}
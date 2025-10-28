<?php
session_start(); // Obligatoire AVANT toute sortie HTML

$pseudo = $_SESSION['pseudo'] ?? null;
$isConnected = !empty($pseudo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/styleX01.css" />
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="manifest" href="/mono/asset/manifest.json">

    <!-- Icône de l'application -->
    <link rel="apple-touch-icon" href="favicon.ico">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- Couleur de fond de la barre de statut -->
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Darts-Games">

</head>

<body>
    <?php
    include 'config.php';
    ?>

    <!--<div id="divTitre">
        <h1 id="titre"></h1>
    </div>-->

    <main id="contenu">
        <!-- Le contenu chargé dynamiquement apparaîtra ici -->
    </main>

    <script>
        var isConnected = <?= json_encode($isConnected); ?>;
        var nomUser = getCookie("session");
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="script.js"></script>
    <script src="scripts/connexion.js"></script>
    <script src="scripts/inscription.js"></script>
    <script>
        // Enregistrement du service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js').then(reg => {
                console.log('Service Worker enregistré ✅');
            }).catch(err => {
                console.error('Erreur SW:', err);
            });
        }
    </script>
</body>

<footer id="footer_principal">
    <nav id="menu_non_connecte" class="<?= $isConnected ? 'hidden' : '' ?>">
        <!-- onclick="chargerContenu('connexion','Connexion')"    
        onclick="chargerContenu('inscription','Inscription')"-->
        <button data-page="connexion" data-titre="Connexion">Connexion <i class="fa-solid fa-right-to-bracket"></i></button>
        <button data-page="inscription" data-titre="Inscription">Inscription <i class="fa-solid fa-user-plus"></i></button>
    </nav>
    <nav id="menu_connecte" class="<?= $isConnected ? '' : 'hidden' ?>">
        <button data-titre="Jeux" data-page='dashboard'>Jeux <img src="./style/flechettes.png" style="width:20px;height:20px;"> </button>
        <button data-titre="Statistiques" data-page='stats'>Statistiques <i class="fa-solid fa-trophy"></i> </button>
        <button data-titre="" data-page='deconnexion'>Déconnexion <i class="fa-solid fa-right-from-bracket"></i></button>
        <!--onclick="chargerContenu('dashboard','Darts Board')"
            onclick="chargerContenu('stats','Statistiques')"
            onclick="deconnecter()"-->
    </nav>
</footer>

</html>

<script>
    $(document).ready(function() {
        if (isConnected) {
            // Charger automatiquement le dashboard
            chargerContenu('dashboard', 'Jeux');
        } else {
            // Sinon afficher la page de connexion par défaut (facultatif)
            chargerContenu('connexion', 'Connexion');
        }
    });

    // Gestion du clic sur le bouton "Déconnexion"

    $(document).ready(function() {
        $(document).on('click', '[data-page="deconnexion"]', function(e) {
            e.preventDefault();

            $.ajax({
                url: './logout_traitement.php',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        isConnected = false;
                        console.log("Déconnexion réussie ✅");

                        // Masquer le menu connecté et afficher celui non connecté
                        //$('#menu_connecte').addClass('hidden');
                        //$('#menu_non_connecte').removeClass('hidden');
                        afficherMenuSelonConnexion();    
                        chargerContenu('connexion','Connexion');   
                        
                        
                    } else {
                        alert("Erreur lors de la déconnexion : " + (response.message || ''));
                    }
                },
                error: function() {
                    alert("Erreur serveur pendant la déconnexion.");
                }
            });
        });
    });
</script>
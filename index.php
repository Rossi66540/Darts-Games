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
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="manifest" href="asset/manifest.json">

  <!-- Icône de l'application -->
  <link rel="apple-touch-icon" href="favicon.ico">

  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <!-- Couleur de fond de la barre de statut -->
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="Darts-Games">

</head>

<body>
    <?php
        include 'config.php';
    ?>

    <div id="divTitre">
        <h1 id="titre"></h1>
    </div>
    
    <main id="contenu">
        <!-- Le contenu chargé dynamiquement apparaîtra ici -->
    </main>

    <script>
        const isConnected = <?= json_encode($isConnected); ?>;
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

    <footer>
        <nav id="menu_non_connecte" class="<?= $isConnected ? 'hidden' : '' ?>">
            <button onclick="chargerContenu('connexion','Connexion')">Connexion</button>
            <button onclick="chargerContenu('inscription','Inscription')">Inscription</button>
        </nav>
        <nav id="menu_connecte" class="<?= $isConnected ? '' : 'hidden' ?>">
            <button onclick="chargerContenu('dashboard','Darts Board')">Dashboard</button>
            <button onclick="chargerContenu('stats','Statistiques')">Statistiques</button>
            <button onclick="deconnecter()">Déconnexion</button>
        </nav>
    </footer>

</html>
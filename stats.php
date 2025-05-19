<?php

include 'config.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$idUser = $_COOKIE['idUser'];

$reqPerso = "SELECT
        jeu.nom, 
        IFNULL(count(parties.id),' - ') as joues,        
        SUM(IF(parties.id_gagnant = scores.id_user,1,0)) as gagnes,
        MAX(scores.total) as top

    FROM    
        jeu    
    LEFT JOIN parties ON parties.type = jeu.id
    LEFT JOIN scores ON scores.id_partie = parties.id


    WHERE
        scores.id_user = " . $idUser . "

    GROUP BY 
        jeu.id
    ";

$statsPerso = [];
$result = mysqli_query($mysqli, $reqPerso);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $statsPerso[] = $row;
    }
    mysqli_free_result($result);
} else {
    echo "Erreur SQL : " . mysqli_error($mysqli);
}


$reqGlobal = "SELECT
        jeu.id as type,
        jeu.nom, 
        count(parties.id) as joues,                
        MAX(IFNULL(scores.total,0)) as top

    FROM    
        jeu    
    LEFT JOIN parties   ON parties.type = jeu.id
    LEFT JOIN scores    ON scores.id_partie = parties.id
    LEFT JOIN user      ON user.id = scores.id_user     
    
    GROUP BY 
        jeu.id
    ";

$statsGlobal = [];
$result = mysqli_query($mysqli, $reqGlobal);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pseudoMeilleur = getTopJoueur($row["type"]);
        $row['pseudoTop'] = $pseudoMeilleur;
        $statsGlobal[] = $row;
    }
    mysqli_free_result($result);
} else {
    echo "Erreur SQL : " . mysqli_error($mysqli);
}


function getTopJoueur($type)
{

    $DB_HOST = 'localhost';
$DB_USER = 'fvasnmcf_root';
// $DB_PORT = 3306;
$DB_PASS = 'Darts66540!';
$DB_NAME = 'fvasnmcf_darts-games';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

    $req = "SELECT
        user.pseudo,
        MAX(scores.total) as top, 
        DATE_FORMAT(parties.date,'%d/%m/%Y') as date

    FROM    
        jeu    
    INNER JOIN parties  ON parties.type = jeu.id
    INNER JOIN scores   ON scores.id_partie = parties.id
    INNER JOIN user     ON user.id = scores.id_user

    WHERE
        jeu.id= " . $type . "   
    ";

    $info = "";
    $result = mysqli_query($mysqli, $req);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $info = $row['pseudo'] . ' (' . $row['date'] . ')';
        mysqli_free_result($result);
    }
    return $info;
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Darts-Games</title>
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">

    <!-- Icône de l'application -->
    <link rel="apple-touch-icon" href="favicon.ico">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- Couleur de fond de la barre de statut -->
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Darts-Games">
</head>



<body>
    <script src="./libs/jquery3.7.js"></script>


    <div id="statPerso">
        <h1>Stats de <?= $_COOKIE['session'] ?> :</h1>

        <table class="tableStat">
            <thead>
                <tr>
                    <th>Jeu</th>
                    <th>Parties Jouées</th>
                    <th>Parties Gagnées</th>
                    <th>% Victoire</th>
                    <th>Top Score</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($statsPerso as $stat) {
                    $ligne = "<tr><td>" . $stat['nom'] . "</td><td>" . $stat['joues'] . "</td><td>" . $stat['gagnes'] . "</td><td>";
                    $ligne .= round($stat['gagnes'] / $stat['joues'] * 100, 2) . "%";
                    $ligne .= "</td><td>" . $stat['top'] . "</td></tr>";

                    echo $ligne;
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="statGlobale">
        <h1>Stats Générales</h1>
        <table class="tableStat">
            <thead>
                <tr>
                    <th>Jeu</th>
                    <th>Parties Jouées</th>
                    <th>Top Score</th>
                    <th>Top Joueur</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($statsGlobal as $stat) {
                    $ligne = "<tr><td>" . $stat['nom'] . "</td><td>" . $stat['joues'] . "</td><td>" . $stat['top'] . "</td><td>" . $stat['pseudoTop'] . "</td></tr>";

                    echo $ligne;
                }
                ?>
            </tbody>
        </table>

    </div>




</body>
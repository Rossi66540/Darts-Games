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
    u.pseudo, 
    s.total AS top, 
    DATE_FORMAT(p.date, '%d/%m/%Y') AS date
FROM 
    scores s
INNER JOIN parties p ON s.id_partie = p.id
INNER JOIN jeu j ON p.type = j.id
INNER JOIN user u ON s.id_user = u.id
WHERE 
    j.id = " . $type . "
ORDER BY 
    s.total DESC
LIMIT 1";


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
        <h1>Stats</h1>



        <select id="selectUser" onChange='chargeStat()'>

        </select>

        <table class="tableStat" id="tbStatPerso">
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


    <script>
        var nomUser = getCookie("session");
        var idUser = "<?= $idUser ?>";

         function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        $(document).ready(function() {
            genererSelect(idUser);
            chargeStat();
        });


        function genererSelect(idUser) {

            $('#selectUser').empty();
            $.ajax({
                url: '/models/get_users.php', // <-- ce fichier doit retourner un JSON de type [{id_user: 1, pseudo: "Alice"}, ...]
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    let options = "";
                    options += '<option value=""> - - - </option>';
                    data.forEach(function(user) {
                        let selected = "";
                        if (user.pseudo == nomUser) {
                            selected = "selected";
                        }
                        options += '<option value="' + user.id + '" data-id="' + user.id + '" ' + selected + '>' + user.pseudo + '</option>';
                    });
                    $('#selectUser').append(options);

                }
            });
        }

        function chargeStat() {
            idUser = $('#selectUser').val();
            console.log(idUser);
            $('#tbStatPerso tbody').empty();
            $.ajax({
                url: '/models/get_stat_perso.php',
                type: 'GET',
                data: {
                    idUser: idUser
                }, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let html = '';

                        response.data.forEach(function(stat) {
                            let taux = (stat.joues !== ' - ' && stat.joues > 0) ?
                                (parseInt(stat.gagnes) / parseInt(stat.joues) * 100).toFixed(2) + '%' :
                                '-';

                            html += "<tr><td>" + stat.nom + "</td><td>" + stat.joues + "</td><td>" + stat.gagnes + "</td><td>" + taux + "</td><td>" + stat.top + "</td></tr>";
                        });

                        $('#tbStatPerso tbody').html(html);
                    } else {
                        console.error('Erreur SQL :', response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX :', error);
                }
            });

        }
    </script>




</body>
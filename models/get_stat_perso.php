<?php
// get_stats_perso.php

header('Content-Type: application/json');
include '../config.php';

$idUser = intval($_GET['idUser'] ?? 0); // ou $_POST['idUser'] si tu envoies en POST

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
        scores.id_user = $idUser
    GROUP BY 
        jeu.id";

$statsPerso = [];
$result = mysqli_query($mysqli, $reqPerso);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $statsPerso[] = $row;
    }
    mysqli_free_result($result);
    echo json_encode(['success' => true, 'data' => $statsPerso]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($mysqli)]);
}

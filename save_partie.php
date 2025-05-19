<?php
include 'config.php';

$json = file_get_contents('php://input');

// Convertit le JSON en tableau associatif PHP
$data = json_decode($json, true);

$type_jeu = $data['type'] ?? null;
$id_gagnant = $data['id_gagnant'] ?? null;
$nbr_joueur = $data['nbr_joueur'] ?? null;
$scores = $data['scores'] ?? [];
$resultats = $data['resultats'];

$date_partie = date('Y-m-d H:i:s');

// Enregistrer la partie
$stmt_partie = $mysqli->prepare("
    INSERT INTO parties (nb_joueurs,type,date,id_gagnant)
    VALUES (?, ?, ?, ?)
");

if (!$stmt_partie) {
    die("Erreur préparation partie : " . $mysqli->error);
}

$stmt_partie->bind_param("issi", $nbr_joueur, $type_jeu, $date_partie, $id_gagnant);

if (!$stmt_partie->execute()) {
    die("Erreur insertion partie : " . $stmt_partie->error);
}

$id_partie = $stmt_partie->insert_id;
$stmt_partie->close();

// Préparer l'insertion des scores
$stmt_score = $mysqli->prepare("
    INSERT INTO scores (id_partie, id_user, total, place, details)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt_score) {
    die("Erreur préparation scores : " . $mysqli->error);
}

// Enregistrer tous les scores
foreach ($resultats as $score) {


    $stmt_score->bind_param(
        "iiiis",
        $id_partie,
        $score['id_user'],
        $score['total'],
        $score['place'],
        json_encode($score['details'])
    );

    if (!$stmt_score->execute()) {
        echo "Erreur score user {$score['id_user']} : " . $stmt_score->error . "<br>";
    }
}

$stmt_score->close();
$mysqli->close();

// echo "✅ Partie enregistrée avec succès (ID : $id_partie)";
?>
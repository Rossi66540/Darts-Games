<?php
// Toujours démarrer la session avant d'y accéder
session_start();

// Supprimer toutes les variables de session
$_SESSION = [];

// Supprimer le cookie utilisateur (si défini dans login)
if (isset($_COOKIE['idUser'])) {
    setcookie("idUser", "", time() - 3600, "/");
}

// Détruire complètement la session
session_destroy();

// Répondre au front-end
echo json_encode(["success" => true, "message" => "Déconnexion réussie."]);
?>
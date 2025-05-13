<?php
header('Content-Type: application/json');

include 'config.php';
// Vérification des données POST
$token = $_POST['token'] ?? null;
$nouveau_mdp = $_POST['mot_de_passe'] ?? null;

if (!$token || !$nouveau_mdp) {
  echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
  exit;
}


// Rechercher le token dans la base de données
$stmt = $mysqli->prepare("SELECT * FROM reinitialisations WHERE token = ? AND expiration > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

if ($user_data) {
    // Récupérer l'ID de l'utilisateur associé au token
    $id_utilisateur = $user_data['id_utilisateur'];  // Assume que 'id_utilisateur' est stocké dans la table 'reinitialisations'

    // Hasher le mot de passe
    $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);

    // Mettre à jour le mot de passe dans la table 'utilisateurs'
    $stmt = $mysqli->prepare("UPDATE user SET mdp = ? WHERE id = ?");
    $stmt->bind_param("si", $hash, $id_utilisateur);
    $stmt->execute();
    $stmt->close();

    // Invalider le token (le supprimer de la table 'reinitialisations')
    $stmt = $mysqli->prepare("DELETE FROM reinitialisations WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Token invalide ou expiré.']);
}

// Fermer la connexion à la base de données
$mysqli->close();
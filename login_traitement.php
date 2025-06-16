<?php
// Connexion à la base de données
include 'config.php'; // Assurez-vous que le fichier de configuration contient les bonnes informations de connexion.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données envoyées
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // Validation des champs
    if (empty($email) || empty($mot_de_passe)) {
        echo json_encode(["success" => false, "error" => "Tous les champs doivent être remplis."]);
        exit();
    }

    // Rechercher l'utilisateur dans la base de données
    $stmt = $mysqli->prepare("SELECT id,pseudo, mdp FROM user WHERE mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // L'utilisateur existe, vérifier le mot de passe
        $user = $result->fetch_assoc();

        if (password_verify($mot_de_passe, $user['mdp'])) {
            // Le mot de passe est correct, créer une session
            session_start();
            $_SESSION['pseudo'] = $user['pseudo'];
            

            // Répondre avec succès et pseudo de l'utilisateur
            echo json_encode(["success" => true, "id" => $user['id'], "pseudo" => $user['pseudo']]);
            
        } else {
            // Mot de passe incorrect
            echo json_encode(["success" => false, "error" => "Mot de passe incorrect."]);
        }
    } else {
        // Utilisateur non trouvé
        echo json_encode(["success" => false, "error" => "Utilisateur non trouvé."]);
    }

    $stmt->close();
    $mysqli->close();
} else {
    echo json_encode(["success" => false, "error" => "Méthode de requête invalide."]);
}
?>

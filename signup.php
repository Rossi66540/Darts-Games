<?php
// signup.php
include 'config.php';

// Si la requête est en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données via POST
    $nom = $_POST["nom"] ?? "";
    $prenom = $_POST["prenom"] ?? "";
    $pseudo = $_POST["pseudo"] ?? "";
    $email = $_POST["email"] ?? "";
    $mot_de_passe = $_POST["mot_de_passe"] ?? "";

    // Validation des champs
    if (empty($nom) || empty($prenom) || empty($pseudo) || empty($email) || empty($mot_de_passe)) {
        http_response_code(400);
        echo json_encode(["error" => "Tous les champs doivent être remplis."]);
        exit();
    }

    // Hash du mot de passe
    $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT);

   // Connexion à la base de données avec MySQLi
try {
    // Préparer la requête SQL sans l'ID (auto-incrément)
    $stmt = $mysqli->prepare("INSERT INTO user (nom, prenom, pseudo, mail, mdp, is_actif) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        throw new Exception("Erreur lors de la préparation de la requête : " . $mysqli->error);
    }

    // Lier les paramètres avec les bons types
    $isActif = true;  // ou 1 si booléen
    $stmt->bind_param("sssssi", $nom, $prenom, $pseudo, $email, $hashedPassword, $isActif);

    // Exécuter la requête
    $success = $stmt->execute();

    if ($success) {       
        header("Location: dashboard.html");

        exit();  // Assurez-vous d'arrêter l'exécution du script après la redirection
    } else {
        // Redirection vers la page d'inscription en cas d'erreur
        header("Location: signup.html");
        exit();  // Assurez-vous d'arrêter l'exécution du script après la redirection
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage(); // Affiche l'erreur capturée
    echo json_encode([
        "error" => "Erreur lors de l'exécution de la requête",
        "message" => $e->getMessage(), 
        "code" => $e->getCode(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
}
}

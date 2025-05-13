<?php
header('Content-Type: application/json');

include 'config.php';

// Vérifie la connexion
if ($mysqli->connect_error) {
  echo json_encode(['success' => false, 'message' => "Erreur de connexion : " . $mysqli->connect_error]);
  exit;
}

// Récupération de l'email
$email = $_POST['email'] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['success' => false, 'message' => "Email invalide."]);
  exit;
}

// Vérifie si l'utilisateur existe
$stmt = $mysqli->prepare("SELECT * FROM user WHERE mail = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
  echo json_encode(['success' => false, 'message' => "Aucun compte trouvé avec cet email."]);
  exit;
}

// Génère un token sécurisé
$token = bin2hex(random_bytes(32));
$expiration = date('Y-m-d H:i:s', time() + 3600); // expiration +1h

// Insère le token dans la table `reinitialisations`
$stmt = $mysqli->prepare("INSERT INTO reinitialisations (id_utilisateur,email, token, expiration) VALUES (? ,? , ?, ?)");
$stmt->bind_param("ssss",$user['id'], $email, $token, $expiration);
$stmt->execute();
$stmt->close();

// // Crée le lien de réinitialisation
// $lien = "https://www.darts-games.infinityfreeapp.com/reset_mdp.php?token=" . $token;

// // Envoie de l'email
// $objet = "Réinitialisation de votre mot de passe";
// $message = "Bonjour,\n\nVoici le lien pour réinitialiser votre mot de passe :\n\n$lien\n\nCe lien expire dans 1 heure.";
// $headers = "From: no-reply@votre-site.fr";

// if (mail($email, $objet, $message, $headers)) {
//   echo json_encode(['success' => true]);
// } else {
//   echo json_encode(['success' => false, 'message' => "Échec de l’envoi de l’email."]);
// }

// Crée le lien de réinitialisation
$lien = "https://www.darts-games.infinityfreeapp.com/reset_mdp.php?token=" . $token;

// Préparer l'email
$to_email = $email;
$to_name = "Utilisateur"; // Si tu veux inclure un nom
$from_email = "darts.games66@gmail.com";
$from_name = "Darts Games";
$subject = "Réinitialisation de votre mot de passe";
$body = "Bonjour,\n\nVoici le lien pour réinitialiser votre mot de passe :\n\n$lien\n\nCe lien expire dans 1 heure.";

// Appel du script send_email.php via cURL
$url = 'https://www.darts-games.infinityfreeapp.com/send_mail.php'; // URL de ton fichier PHP
$data = [
    'to_email'   => $to_email,
    'to_name'    => $to_name,
    'from_email' => $from_email,
    'from_name'  => $from_name,
    'subject'    => $subject,
    'body'       => $body,
];

// Initialisation de cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Désactiver la vérification SSL
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Exécution de la requête cURL
$response = curl_exec($ch);

// Vérification de l'exécution
if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Erreur de cURL : ' . curl_error($ch)]);
} else {
    $response_data = json_decode($response, true);
    if ($response_data['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur dans l\'envoi de l\'email']);
    }
}

// Fermeture de la connexion cURL
curl_close($ch);
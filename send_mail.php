<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Chargement de PHPMailer
require 'libs/PHPMailer-master/src/PHPMailer.php';
require 'libs/PHPMailer-master/src/SMTP.php';
require 'libs/PHPMailer-master/src/Exception.php';

// Récupération des paramètres (POST ou GET)
$to_email   = $_POST['to_email']   ?? $_GET['to_email']   ?? null;
$to_name    = $_POST['to_name']    ?? $_GET['to_name']    ?? '';
$from_email = $_POST['from_email'] ?? $_GET['from_email'] ?? 'darts.games66@gmail.com';
$from_name  = $_POST['from_name']  ?? $_GET['from_name']  ?? 'Expéditeur';
$subject    = $_POST['subject']    ?? $_GET['subject']    ?? '(Pas de sujet)';
$body       = $_POST['body']       ?? $_GET['body']       ?? '(Pas de message)';

if (!$to_email) {
    exit("Erreur : adresse du destinataire manquante.");
}

$mail = new PHPMailer(true);

try {
    // Configuration SMTP Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'darts.games66@gmail.com'; // Adresse Gmail
    $mail->Password   = 'qqfp mmvc fxyg vksb';     // Mot de passe d'application
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->CharSet = 'UTF-8';

    // Détails de l'e-mail
    $mail->setFrom($from_email, $from_name);
    $mail->addAddress($to_email, $to_name);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    echo 'Message envoyé avec succès.';
} catch (Exception $e) {
    echo "Erreur d'envoi : {$mail->ErrorInfo}";
}
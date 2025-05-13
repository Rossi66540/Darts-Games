<?php
// Vérifier si un token est fourni dans l’URL
$token = $_GET['token'] ?? null;

if (!$token) {
  echo "Lien invalide ou expiré.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Nouveau mot de passe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./style/login.css" />
</head>
<body>
  <form id="newPasswordForm">
    <h1>Nouveau mot de passe</h1>
    <input type="hidden" id="token" value="<?= htmlspecialchars($token) ?>">

    <label>Nouveau mot de passe :
      <input type="password" id="nouveau_mdp" required>
    </label><br>

    <label>Confirmer le mot de passe :
      <input type="password" id="confirm_mdp" required>
    </label><br>

    <button type="submit">Réinitialiser</button>
    <p><a href="login.html">← Retour à la connexion</a></p>
  </form>

  <script>
    document.getElementById("newPasswordForm").addEventListener("submit", async function(e) {
      e.preventDefault();

      const mdp = document.getElementById("nouveau_mdp").value;
      const confirm = document.getElementById("confirm_mdp").value;
      const token = document.getElementById("token").value;

      if (mdp !== confirm) {
        alert("Les mots de passe ne correspondent pas.");
        return;
      }

      const formData = new FormData();
      formData.append("token", token);
      formData.append("mot_de_passe", mdp);

      const response = await fetch("valider_reset.php", {
        method: "POST",
        body: formData
      });

      const result = await response.json();
      if (result.success) {
        alert("Mot de passe modifié avec succès !");
        window.location.href = "login.html";
      } else {
        alert("Erreur : " + result.message);
      }
    });
  </script>
</body>
</html>

<form id="loginForm">
    <h1>Connexion</h1>
    <label>Email : <input type="email" name="email" required></label><br>
    <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br>
    <p>
        <a href="#" onclick="chargerContenu('forgot')" style="font-size: 0.9em;">Mot de passe oublié ?</a>
    </p>
    <button type="submit">Se connecter</button>
    <div id="loginMessage" style="color: red; margin-top: 10px;"></div>
</form>

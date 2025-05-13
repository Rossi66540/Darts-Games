// Vérifie la présence du cookie de session
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// Vérifie si la session est ouverte
const session = getCookie("session");

// Vérifie si l'utilisateur est sur une page autre que index.html
if (window.location.pathname !== "/index.html" && !session) {
    window.location.href = "index.html"; // Redirige vers index si la session n'est pas ouverte
}
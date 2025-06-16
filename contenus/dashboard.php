        <label for="games-list">
            Je vous propose différents jeux de fléchettes afin que vous n'ayez plus besoin de tenir les comptes des
            scores.
        </label><br>

        <ul id="games-list">
            <li>
                <button class="buttonGeneral" style="min-width: 180px;" onclick="window.location.href='horloge_compte.html';">
                    Horloge Comptée
                </button>
            </li>
            <li>
                <button class="buttonGeneral" style="min-width: 180px;" onclick="window.location.href='morpion.html';">
                    Morpion
                </button>
            </li>
            <li>
                <button class="buttonGeneral" style="min-width: 180px;" onclick="window.location.href='kapital.html';">
                    Kapital
                </button>
            </li>
            <li>
                <button class="buttonGeneral" style="min-width: 180px;" onclick="loadPage('kapital2.html')">
                    Test Dev
                </button>
            </li>
            <li>
                <button class="buttonGeneral" style="min-width: 180px;background-color:lightgreen" onclick="window.location.href='stats.php';">
                    Statistiques
                </button>
            </li>
        </ul>
    </div>
    <div id="piedPage" style="display:flex;">        
        <button class="buttonMini" onclick="logout()">Déconnexion</button>
        <button class="buttonMini" onclick="viderCache()">Vider Cache</button>
    </div>

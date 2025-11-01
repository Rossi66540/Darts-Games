<main id="contenu">

    <div id="modalRegles" class="modal hide">
        <div class="modal-content">
            <span class="close" >&times;</span>
            <h3>Règle :</h3>
            <p>a ecrire</p>

            </ul>

            <p>Bonne partie à vous !</p>

            <CENTER><button  class="buttonGeneral">Bien Compris, allons jouer !</button>
            </CENTER>
        </div>
    </div>

    <style>
        .enCours {
            background-color: yellow;
        }
    </style>


    <div id="config_X01">
        <label>Nbr de Sets Gagnants : </label>
        <select id="sl_leg">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
        </select>

        <label>Façon de finir :</label>
        <select id="sl_out" >
            <option value="0"> - - </option>
            <option value="1">Single Out</option>
            <option value="2">Double Out</option>
        </select>

        <label>Nombre Joueurs :</label>
        <input id="nbJrs" style="width:60px;" type="number" />

        <table id="tableJoueurs">
            <tbody></tbody>
        </table>

        <CENTER><button class="buttonGeneral"> LANCER PARTIE </button></CENTER>
    </div>

    <div id="tableau_jeu_x01" style="display:none;">
        <h3 id="infosPartie" style="text-align: center; font-size: 40px; margin: 5px; padding: 0px;"></h3>
        <table id="table_X01" class="table_x01">
            <thead>
                <tr style="width: 100%;">
                    <td style="width: 20%;"> Nom </td>
                    <td style="width: 50%;"> Lancé </td>
                    <td style="width: 30%;"> Infos </td>
                </tr>
            </thead>
            <tbody>

            </tbody>

        </table>
    </div>

    <div id="divProposition" class="rowFlex" style="justify-content: center;gap: 20px;">
    </div>

    <div id="divScore_x01" style="display:none;flex-direction:column;">
        <div id="scorePossible" style="margin-top:unset;">
            <div class="scoreLigne">
                <button class="buttonScoreX01">1</button>
                <button class="buttonScoreX01">2</button>
                <button class="buttonScoreX01">3</button>
                <button class="buttonScoreX01">4</button>
                <button class="buttonScoreX01">5</button>
                <button class="buttonScoreX01">6</button>
                <button class="buttonScoreX01">7</button>
            </div>
            <div class="scoreLigne">
                <button class="buttonScoreX01">8</button>
                <button class="buttonScoreX01">9</button>
                <button class="buttonScoreX01">10</button>
                <button class="buttonScoreX01">11</button>
                <button class="buttonScoreX01">12</button>
                <button class="buttonScoreX01">13</button>
                <button class="buttonScoreX01">14</button>
            </div>
            <div class="scoreLigne">
                <button class="buttonScoreX01">15</button>
                <button class="buttonScoreX01">16</button>
                <button class="buttonScoreX01">17</button>
                <button class="buttonScoreX01">18</button>
                <button class="buttonScoreX01">19</button>
                <button class="buttonScoreX01">20</button>
                <button class="buttonScoreX01">B</button>
            </div>
            <div class="scoreLigne">
                <button class="buttonScoreX01">0</button>
                <button class="buttonMode">Double</button>
                <button class="buttonMode">Triple</button>
                <button class="buttonCancel" style="background-color:red;">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="divFin" class="hide">
        <button class="buttonGeneral" onclick="window.location.href='index.html'"> Retour à l'accueil </button>        
    </div>

</main>
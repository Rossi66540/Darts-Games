<div id="modalRegles" class="modal hide">
    <div class="modal-content">
        <span class="close" onclick="cacherRegles()">&times;</span>
        <h3>Règle :</h3>
        <p>a ecrire</p>

        </ul>

        <p>Bonne partie à vous !</p>

        <CENTER><button onclick="cacherRegles()" class="buttonGeneral">Bien Compris, allons jouer !</button>
        </CENTER>
    </div>
</div>

<style>
    .enCours {
        background-color: yellow;
    }
</style>


<div id="config_301">
    <label>Nbr de Sets Gagnants </label>
    <select id="sl_leg">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
    </select>

    <label>Façon de finir :</label>
    <select id="sl_out" onchange="setTypeFinish()">
        <option value="0"> - - </option>
        <option value="1">Single Out</option>
        <option value="2">Double Out</option>
    </select>

    <label>Nombre Joueurs :</label>
    <input id="nbJrs" style="width:60px;" type="number" onkeyup="genererTableauNom($(this).val(),'tableJoueurs')" />

    <table id="tableJoueurs">
        <tbody></tbody>
    </table>

    <CENTER><button onclick="lancerPartie()" class="buttonGeneral"> LANCER PARTIE </button></CENTER>
</div>

<div id="tableau_jeu_x01">
    <h3 id="infosPartie">301 Double Out Set#1</h3>
    <table id="table_301" class="table_x01">
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

<div id="divProposition" class="scoreLigne">
</div>

<div id="divScore_x01" style="display:none;flex-direction:column;">
    <div id="scorePossible">
        <div class="scoreLigne">
            <button class="buttonScoreX01" onclick="saisieScore(1)">1</button>
            <button class="buttonScoreX01" onclick="saisieScore(2)">2</button>
            <button class="buttonScoreX01" onclick="saisieScore(3)">3</button>
            <button class="buttonScoreX01" onclick="saisieScore(4)">4</button>
            <button class="buttonScoreX01" onclick="saisieScore(5)">5</button>
            <button class="buttonScoreX01" onclick="saisieScore(6)">6</button>
            <button class="buttonScoreX01" onclick="saisieScore(7)">7</button>
        </div>
        <div class="scoreLigne">
            <button class="buttonScoreX01" onclick="saisieScore(8)">8</button>
            <button class="buttonScoreX01" onclick="saisieScore(9)">9</button>
            <button class="buttonScoreX01" onclick="saisieScore(10)">10</button>
            <button class="buttonScoreX01" onclick="saisieScore(11)">11</button>
            <button class="buttonScoreX01" onclick="saisieScore(12)">12</button>
            <button class="buttonScoreX01" onclick="saisieScore(13)">13</button>
            <button class="buttonScoreX01" onclick="saisieScore(14)">14</button>
        </div>
        <div class="scoreLigne">
            <button class="buttonScoreX01" onclick="saisieScore(15)">15</button>
            <button class="buttonScoreX01" onclick="saisieScore(16)">16</button>
            <button class="buttonScoreX01" onclick="saisieScore(17)">17</button>
            <button class="buttonScoreX01" onclick="saisieScore(18)">18</button>
            <button class="buttonScoreX01" onclick="saisieScore(19)">19</button>
            <button class="buttonScoreX01" onclick="saisieScore(20)">20</button>
            <button class="buttonScoreX01" onclick="saisieScore(25)">B</button>
        </div>
        <div class="scoreLigne">
            <button class="buttonScoreX01" onclick="saisieScore(0)">0</button>
            <button class="buttonMode" onclick="setMode(2,this)">Double</button>
            <button class="buttonMode" onclick="setMode(3,this)">Triple</button>
            <button class="buttonCancel" onclick="annulerScore()" style="background-color:red;">
                <i class="fa-solid fa-rotate-left"></i>
            </button>
        </div>
    </div>
</div>


<script>
    var debutScore = 301;
    var typeFinish;
    

    var joueurs = [];

    var nbrSetGagnant = 1;
    var setActuel = 0;
    var tourActuel = 0;
    var indiceFlechette = 1;
    var flechetteRestantes = 3;
    var idJoueurActuel = 0;
    var indiceJoueurActuel = 0;

    var flechettes = [];


    var resultats = [];
    var resultatsSets = [];


    // var totaux = [];

    var modeActuel = 1;

    var objectifActuel = -1; //CORRECT

    // var scoreEncours = 0;

    function lancerPartie() {
        affichageModePartie();
        $('#config_301').hide();
        $('#divScore_x01').show();

        nbrSetGagnant = $('#sl_leg').val();

        let nbrPartieMax = (nbrSetGagnant - 1) * $('#nbJrs').val() + 1;
        for (i = 1; i <= nbrPartieMax; i++) {
            resultatsSets[i] = null;
        }

        //tirage au sort des joueurs
        // Vide le tableau des joueurs
        joueurs = [];

        $('.pseudo').each(function() {
            let selectedOption = $(this).find('option:selected');
            let id_user = selectedOption.data('id');
            let pseudo = selectedOption.text();

            if (id_user !== undefined && id_user !== "") {
                joueurs.push({
                    id: id_user,
                    pseudo: pseudo,
                    score: debutScore,
                    lance: ["", "", ""],
                    moyenne: 0,
                });
            }
        });

        // Mélange aléatoirement les joueurs (algorithme de Fisher-Yates)
        for (let i = joueurs.length - 1; i > 0; i--) {
            let j = Math.floor(Math.random() * (i + 1));
            [joueurs[i], joueurs[j]] = [joueurs[j], joueurs[i]];
        }

        afficheInfosJoueurs(setActuel, tourActuel);

    }

    function afficheInfosJoueurs(indiceSet, indiceTour) {
        $('#table_301 tbody').empty();
        let firstToSet = false;
        joueurs.forEach(function(infoJoueur) {
            let trSelect = "";
            if (idJoueurActuel == infoJoueur.id) {
                trSelect = "enCours";
            }
            let indiceJoueur = joueurs.findIndex(j => j.id == infoJoueur.id);

            let scoreRestant = debutScore - calculScoreFait(setActuel, indiceJoueur);
            infoJoueur.score = scoreRestant;

            let html = "<tr class='" + trSelect + "'>";
            html += "<td> <div class='columnFlex'><label class='lb_pseudo'>" + infoJoueur.pseudo + "</label> <label class='lb_score'>" + infoJoueur.score + "</label> </div></td>";
            html += "<td><div class='rowFlex divRes'>";

            for (i = 1; i <= 3; i++) {
                //let res = resultats[indiceSet][indiceJoueur][indiceTour][i];
                let res = resultats?.[indiceSet]?.[indiceJoueur]?.[indiceTour]?.[i];

                let txtRes = "";
                if (res) {
                    if (res[0] == 2) {
                        txtRes = "D ";
                    }
                    if (res[0] == 3) {
                        txtRes = "T ";
                    }
                    txtRes += res[1];
                }
                let classDiv = "";
                if (txtRes == "" && !firstToSet) {
                    firstToSet = true;
                    classDiv = "divEnCours";
                }

                html += "<div class='divResultat " + classDiv + "'>";
                html += "<label>" + txtRes + "</label>";
                html += "</div>";
            }
            html += "</div></td>";
            html += "<td>" + infoJoueur.moyenne + "</td>";
            html += "</tr>";
            $('#table_301 tbody').append(html);
        });
    }

    function setMode(value, btn) {
        let isActif = false;

        if ($(btn).hasClass('active')) {
            isActif = true;
            modeActuel = 1;
        } else {
            modeActuel = value;
        }

        // Désélectionner tous les boutons d'action
        $('.buttonMode').removeClass('active');
        if (!isActif) {
            $(btn).addClass('active');
        }
    }

    function setTypeFinish() {
        typeFinish = $('#sl_out').val();
    }

    function saisieScore(valeur) {

        if (resultats[setActuel] == undefined) {
            resultats[setActuel] = [];
        }
        if (resultats[setActuel][indiceJoueurActuel] == undefined) {
            resultats[setActuel][indiceJoueurActuel] = [];
        }
        if (resultats[setActuel][indiceJoueurActuel][tourActuel] == undefined) {
            resultats[setActuel][indiceJoueurActuel][tourActuel] = [];
        }
        if (resultats[setActuel][indiceJoueurActuel][tourActuel][indiceFlechette] == undefined) {
            resultats[setActuel][indiceJoueurActuel][tourActuel][indiceFlechette] = [];
        }

        resultats[setActuel][indiceJoueurActuel][tourActuel][indiceFlechette] = [modeActuel, valeur];

        let finPartie = false;
        let finSet = false;
        let burst = false;
        let restant = parseInt(debutScore - calculScoreFait(setActuel, indiceJoueurActuel));

        console.log("res:" + restant);
        if (restant == 0) { //GAGNE   
            if (typeFinish == "2") {
                if (modeActuel == 2) {
                    finSet = true;
                } else {
                    burst = true;
                }
            } else {
                console.log("iciii");
                finSet = true;
            }
        } else {
            if (restant < 0) { //BURST
                burst = true;
            }
        }
        console.log(finSet);

        if (finSet) {
            resultatsSets[setActuel] = joueurs[indiceJoueurActuel].id;

            let idGagnantPartie = verifFinPartie();
            console.log("FIN DE GAME ? " + idGagnantPartie)
            if (idGagnantPartie) {
                console.log("FIN DE PARTIE A CODER  !!!!!!");
            } else {
                setActuel++;
                tourActuel = 0;
                indiceFlechette = 1;
                indiceJoueurActuel = 0;
                joueurs.forEach(j => j.score = debutScore);
            }

        } else {
            if (burst) {
                resultats[setActuel][indiceJoueurActuel][tourActuel][1] = [1, 0];
                resultats[setActuel][indiceJoueurActuel][tourActuel][2] = [1, 0];
                resultats[setActuel][indiceJoueurActuel][tourActuel][3] = [1, 0];

                joueurs[indiceJoueurActuel].score = debutScore - calculScoreFait(setActuel, indiceJoueurActuel);

                indiceFlechette = 1;
                indiceJoueurActuel++;

                if (indiceJoueurActuel >= joueurs.length) {
                    indiceJoueurActuel = 0;
                    tourActuel++;
                }
            } else {
                indiceFlechette++;
                if (indiceFlechette > 3) {
                    indiceFlechette = 1;
                    indiceJoueurActuel++;

                    // Si c'était le dernier joueur → nouveau tour
                    if (indiceJoueurActuel >= joueurs.length) {
                        indiceJoueurActuel = 0;
                        tourActuel++;
                    }
                }


            }
        }

        if (finPartie) {
            console.log('fin de partie faut coder le save !!!!');
        } else {
            afficheInfosJoueurs(setActuel, tourActuel);
        }
        setMode(1);

    }

    function annulerScore() {
        // === Cas début de partie : rien à annuler ===
        if (!resultats[setActuel] || (indiceFlechette === 1 && indiceJoueurActuel === 0 && tourActuel === 0)) {
            console.log("Aucun score à annuler.");
            return;
        }

        // === Étape 1 : reculer d'une fléchette ===
        indiceFlechette--;
        if (indiceFlechette < 1) {
            // on était à la première fléchette du joueur
            indiceFlechette = 3;
            indiceJoueurActuel--;

            if (indiceJoueurActuel < 0) {
                // on était au premier joueur → reculer d’un tour
                indiceJoueurActuel = joueurs.length - 1;
                if (tourActuel > 0) {
                    tourActuel--;
                } else {
                    // début de partie
                    tourActuel = 0;
                    indiceFlechette = 1;
                    indiceJoueurActuel = 0;
                    console.log("Aucun score à annuler (début de partie).");
                    return;
                }
            }
        }

        // === Étape 2 : récupérer la fléchette à annuler ===
        let flechette = resultats?.[setActuel]?.[indiceJoueurActuel]?.[tourActuel]?.[indiceFlechette];

        if (!flechette) {
            console.log("Aucune fléchette à annuler à cet emplacement.");
            return;
        }

        let mode = flechette[0];
        let valeur = parseInt(flechette[1]);

        // === Étape 3 : supprimer la fléchette ===
        delete resultats[setActuel][indiceJoueurActuel][tourActuel][indiceFlechette];
        console.log(`Annulé : Set ${setActuel}, Joueur ${indiceJoueurActuel}, Tour ${tourActuel}, Fléchette ${indiceFlechette}`);

        // === Étape 4 : gérer burst ===
        let restant = debutScore - calculScoreFait(setActuel, indiceJoueurActuel);
        if (restant > 0 && flechette[0] * valeur < 0) {
            // Si le dernier score annulé était un burst, rétablir le joueur
            // Ici tu peux remettre l'état normal si besoin
            // (dans notre modèle, le joueur va rejouer normalement)
        }

        // === Étape 5 : gérer fin de set / fin de partie ===
        // Si cette fléchette annulée avait fait gagner le set
        let scoreApresAnnulation = debutScore - calculScoreFait(setActuel, indiceJoueurActuel);
        if (scoreApresAnnulation > 0 && resultatsSets[setActuel] === joueurs[indiceJoueurActuel].id) {
            resultatsSets[setActuel] = null; // annuler le set gagné
            console.log("Set annulé !");
        }

        // Vérifie si la partie était finie et doit être réactivée
        let gagnantPartie = verifFinPartie();
        if (!gagnantPartie) {
            // Partie non finie après annulation
            finPartie = false;
        }

        // === Étape 6 : mettre à jour le score du joueur et l'affichage ===
        joueurs[indiceJoueurActuel].score = debutScore - calculScoreFait(setActuel, indiceJoueurActuel);

        // Affichage mis à jour
        afficheInfosJoueurs(setActuel, tourActuel);

        // Reset du mode
        setMode(1);
    }

    function verifFinPartie() {
        // Compte des sets gagnés par joueur
        let scoreSets = {};

        resultatsSets.forEach(function(idGagnant) {
            if (idGagnant) {
                scoreSets[idGagnant] = (scoreSets[idGagnant] || 0) + 1;
            }
        });

        // Vérifie si un joueur atteint le nombre de sets requis
        for (let id in scoreSets) {
            if (scoreSets[id] >= nbrSetGagnant) {
                return id; // Retourne l'id du joueur gagnant
            }
        }

        return null; // Partie pas encore finie
    }

    function calculScoreFait(indiceSet, indiceJoueur) {
        let totalFait = 0;

        // Vérifie que le set et le joueur existent
        if (!resultats[indiceSet] || !resultats[indiceSet][indiceJoueur]) {
            return 0;
        }

        let dataJoueur = resultats[indiceSet][indiceJoueur];

        // Parcourt tous les tours du joueur
        for (let tour in dataJoueur) {
            let dataTour = dataJoueur[tour];

            // Parcourt les 3 fléchettes du tour
            for (let i = 1; i <= 3; i++) {
                let flechette = dataTour[i];
                if (flechette && flechette.length === 2) {
                    let mode = flechette[0];
                    let valeur = parseInt(flechette[1]);
                    if (!isNaN(valeur)) {
                        totalFait += mode * valeur;
                    }
                }
            }
        }

        return totalFait;
    }
</script>
// x01.js

$(document).ready(function() {

    // === Modal règles ===
    $('#modalRegles .close, #modalRegles button.buttonGeneral').on('click', function() {
        cacherRegles();
    });

    // === Config X01 ===
    $('#sl_out').on('change', function() {
        setTypeFinish();
    });

    $('#nbJrs').on('keyup', function() {
        genererTableauNom($(this).val(), 'tableJoueurs');
    });

    $('#config_301 button.buttonGeneral').on('click', function() {
        lancerPartie();
    });

    // === Boutons Score ===
    $('#divScore_x01 .buttonScoreX01').each(function() {
        $(this).on('click', function() {
            let valeur = parseInt($(this).text()) || 25; // 'B' vaut 25
            saisieScore(valeur);
        });
    });

    $('#divScore_x01 .buttonMode').each(function() {
        $(this).on('click', function() {
            let mode = $(this).text() === 'Double' ? 2 : 3;
            setMode(mode, this);
        });
    });

    $('#divScore_x01 .buttonCancel').on('click', function() {
        annulerScore();
    });

});



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
var idGagnant = null;

// var totaux = [];

var modeActuel = 1;

var objectifActuel = -1; //CORRECT

// var scoreEncours = 0;

function lancerPartie() {
    affichageModePartie();
    $('#tableau_jeu_x01').show();
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

    $('.pseudo').each(function () {
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

    joueurs.forEach(function (infoJoueur) {
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


    let joueurActuel = joueurs[indiceJoueurActuel];
    console.log(joueurActuel);
    let isDouble = (typeFinish == "2") ? true : false;

    let finish = getPossibleFinish(joueurActuel.score, isDouble, 4 - indiceFlechette);
    console.log(finish);
    $('#divProposition').empty();
    if (finish) {
        finish.forEach(function (f) {
            let html = "<label style='font-size:30px;margin-bottom:unset;'>" + f + "</label>";
            $('#divProposition').append(html);
        });
    }

    $('#infosPartie').html(debutScore + " " + $('#sl_out option:selected').text() + " First To " + $('#sl_leg').val() + " / Set#" + (indiceSet + 1));

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
        if (idGagnantPartie) {
            idGagnant = idGagnantPartie;
            console.log("FIN DE PARTIE A CODER  !!!!!!");
            savePartie();
            alert('FIN DE LA PARTIE !');
            cacheDivSaisiePossible();
            $('#divFin').show();


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

    resultatsSets.forEach(function (idGagnant) {
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

function savePartie() {
    let type = 3;
    let nbrJoueur = joueurs.length;
    let maxScore = 0;
    let indiceGagnant = -1;

    let detailScores = [];
    joueurs.forEach(function (joueur, index) {
        let scoreJoueur = {
            id_user: joueur,
            total: totaux[index],
            place: "1",
            details: resultats[index]
        };
        detailScores.push(scoreJoueur);
    });

    let dataToSend = {
        type: type,
        nbr_joueur: nbrJoueur,
        id_gagnant: idGagnant,
        scores: totaux,
        resultats: detailScores // tableau des scores
    };

    // Appel AJAX en POST vers /save_parties.php
    fetch('/save_partie_x01.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' // On envoie du JSON
        },
        body: JSON.stringify(dataToSend)
    })
        .then(response => response.json()) // On attend une réponse JSON (à adapter si ce n'est pas JSON)
        .then(data => {
            console.log('Partie sauvegardée avec succès:', data);
            // ici tu peux gérer la suite après sauvegarde
        })
        .catch(error => {
            console.error('Erreur lors de la sauvegarde:', error);
        });

}

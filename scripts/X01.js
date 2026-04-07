// x01.js

$(document).ready(function () {

    // === Modal règles ===
    $('#modalRegles .close, #modalRegles button.buttonGeneral').on('click', function () {
        cacherRegles();
    });

    // === Config X01 ===
    $('#sl_out').on('change', function () {
        setTypeFinish();
    });

    // input blur
    $('#nbJrs').on('change', function () {
        genererTableauNom($(this).val(), 'tableJoueurs');
    });

    $('#config_X01 button.buttonGeneral').on('click', function () {
        lancerPartie();
    });



    // === Boutons Score ===
    $('#divScore_x01 .buttonScoreX01').each(function () {
        $(this).on('click', function () {
            let valeur = 0;
            //console.log($(this).text());
            if ($(this).text() == "B") {
                valeur = 25;
            } else {
                if ($(this).text() == 0) {
                    valeur = 0;
                } else {
                    valeur = parseInt($(this).text());
                }
            }

            //let valeur = parseInt($(this).text()) || 25; // 'B' vaut 25
            saisieScore(valeur);
        });
    });

    $('#divScore_x01 .buttonMode').each(function () {
        $(this).on('click', function () {
            let mode = $(this).text() === 'Double' ? 2 : 3;
            setMode(mode, this);
        });
    });

    $('#divScore_x01 .buttonCancel').on('click', function () {
        annulerScore();
    });

});


//var debutScore = 301;
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
    $('#config_X01').hide();
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
                infos: []
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
    $('#table_X01 tbody').empty();
    let firstToSet = false;

    joueurs.forEach(function (infoJoueur) {
        let trSelect = "";
        if (idJoueurActuel == infoJoueur.id) {
            trSelect = "enCours";
        }
        let indiceJoueur = joueurs.findIndex(j => j.id == infoJoueur.id);

        let scoreRestant = debutScore - calculScoreFait(setActuel, indiceJoueur);
        infoJoueur.score = scoreRestant;
        infoJoueur.infos = [];
        infoJoueur.infos.push("Sets: " + getNombreSetGagnes(infoJoueur.id));
        infoJoueur.infos.push("Moyenne: " + calculMoyenneJoueur(setActuel, indiceJoueur));



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
        html += "<td><div class='columnFlex'>";

        let infos = infoJoueur.infos;
        if (infos) {
            infos.forEach(function (info) {
                html += "<label>" + info + "</label>";
            });
        }

        html += "</div></td>";
        html += "</tr>";
        $('#table_X01 tbody').append(html);
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

function cacheDivSaisiePossible() {
    $('#divScore_x01').hide();
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
    // Sauvegarde du set d'origine pour savoir si on revient d'un set précédent
    const originalSet = setActuel;

    // 1) Trouver le dernier set non vide (à partir de setActuel)
    let setToCheck = setActuel;
    while (setToCheck >= 0 && (!resultats[setToCheck] || Object.keys(resultats[setToCheck]).length === 0)) {
        setToCheck--;
    }

    if (setToCheck < 0) {
        console.log("🚫 Aucun score à annuler.");
        return;
    }

    // On travaille sur ce set
    setActuel = setToCheck;

    // 2) Trouver le dernier tour présent dans ce set
    let maxTour = -1;
    Object.keys(resultats[setActuel]).forEach(jKey => {
        let tours = Object.keys(resultats[setActuel][jKey] || {}).map(k => parseInt(k)).filter(x => !isNaN(x));
        if (tours.length) {
            let maxJ = Math.max(...tours);
            if (maxJ > maxTour) maxTour = maxJ;
        }
    });

    if (maxTour === -1) {
        // pas de tour (très improbable vu la recherche du set non vide) : fallback
        console.log("Aucun tour trouvé dans le set détecté.");
        return;
    }

    tourActuel = maxTour;

    // 3) Dans ce tour, trouver le joueur qui a la fléchette la plus élevée (la dernière fléchette jouée)
    let dernierJoueurKey = null; // clé telle qu'elle est utilisée dans resultats[set] (doit correspondre à indice joueur)
    let derniereFlechette = -1;

    Object.keys(resultats[setActuel]).forEach(jKey => {
        const dataTour = resultats[setActuel][jKey][tourActuel];
        if (!dataTour) return;
        const fleches = Object.keys(dataTour).map(k => parseInt(k)).filter(x => !isNaN(x));
        if (!fleches.length) return;
        const maxF = Math.max(...fleches);
        if (maxF > derniereFlechette) {
            derniereFlechette = maxF;
            dernierJoueurKey = parseInt(jKey);
        }
    });

    if (dernierJoueurKey === null) {
        console.log("Aucune fléchette trouvée dans le tour détecté.");
        return;
    }

    // 4) Supprimer cette dernière fléchette
    delete resultats[setActuel][dernierJoueurKey][tourActuel][derniereFlechette];

    // Nettoyage : si le tour devient vide, supprimer le tour
    if (Object.keys(resultats[setActuel][dernierJoueurKey][tourActuel] || {}).length === 0) {
        delete resultats[setActuel][dernierJoueurKey][tourActuel];
    }
    // si le joueur n'a plus de tours dans ce set, le retirer du set
    if (Object.keys(resultats[setActuel][dernierJoueurKey] || {}).length === 0) {
        delete resultats[setActuel][dernierJoueurKey];
    }

    // Si le set était marqué gagné par ce joueur, annuler
    const idJoueurSupprime = joueurs[dernierJoueurKey] ? joueurs[dernierJoueurKey].id : null;
    if (idJoueurSupprime && resultatsSets[setActuel] === idJoueurSupprime) {
        resultatsSets[setActuel] = null;
    }

    // 5) Déterminer la nouvelle position (setActuel, tourActuel, indiceJoueurActuel, indiceFlechette)
    //   Cas principal : si on a supprimé une fléchette > 1 => on reste sur le même joueur et on recule d'une fléchette.
    if (derniereFlechette > 1) {
        // revenir d'une fléchette (3->2, 2->1)
        indiceJoueurActuel = dernierJoueurKey;
        indiceFlechette = derniereFlechette - 1;
        // tourActuel et setActuel restent tels quels
    } else {
        // dernièreFlechette === 1
        // règle : revenir au joueur précédent et positionner la fléchette à 3
        // précédent en ordre de jeu (cercle) :
        let prevPlayerIdx = (dernierJoueurKey - 1 + joueurs.length) % joueurs.length;

        // On cherche la dernière présence du prevPlayer (dans setActuel..0)
        let foundPrev = false;
        let prevSetFound = setActuel;
        let prevTourFound = -1;
        for (let s = setActuel; s >= 0 && !foundPrev; s--) {
            if (!resultats[s]) continue;
            const pdata = resultats[s][prevPlayerIdx];
            if (!pdata) continue;
            const toursPrev = Object.keys(pdata).map(k => parseInt(k)).filter(x => !isNaN(x));
            if (!toursPrev.length) continue;
            prevTourFound = Math.max(...toursPrev);
            prevSetFound = s;
            foundPrev = true;
            break;
        }

        if (foundPrev) {
            // positionner sur ce joueur précédent ; on met la fléchette à 3 (prochaine insertion remplira la 3)
            setActuel = prevSetFound;
            tourActuel = prevTourFound;
            indiceJoueurActuel = prevPlayerIdx;
            indiceFlechette = 3;
        } else {
            // le joueur précédent n'a aucune fléchette (aucun historique) :
            // on place le prev player dans le même set/tour courant en position fléchette=3
            indiceJoueurActuel = prevPlayerIdx;
            // si le tour courant existe on garde tourActuel sinon on calcule
            // on laisse tourActuel tel quel (ou 0 si absent)
            if (tourActuel < 0) tourActuel = 0;
            indiceFlechette = 3;
        }
    }

    // 6) Si on a supprimé la dernière fléchette du set et qu'il n'y a plus rien dans ce set,
    //    il est possible que setActuel doive descendre — on laisse la logique suivante recalculer les scores et l'affichage.
    //    (setActuel a déjà été fixé sur le dernier set non vide initialement).

    // 7) Recalcul des scores et maj de l'affichage
    joueurs.forEach((j, idx) => j.score = debutScore - calculScoreFait(setActuel, idx));
    idGagnant = verifFinPartie();

    console.log(`Annulation : Set ${setActuel}, Joueur ${indiceJoueurActuel}, Tour ${tourActuel}, Fléchette ${indiceFlechette}`);

    afficheInfosJoueurs(setActuel, tourActuel);
    setMode(1);
}


function getDernierTour(setVoulu){
    if (!resultats[setVoulu]) return null;

    let maxTour = -1;
    Object.keys(resultats[setActuel]).forEach(jKey => {
        let tours = Object.keys(resultats[setActuel][jKey] || {}).map(k => parseInt(k)).filter(x => !isNaN(x));
        if (tours.length) {
            let maxJ = Math.max(...tours);
            if (maxJ > maxTour) maxTour = maxJ;
        }
    });

    return maxTour;
    

}

function infoDerniereFlechetteSet(setVoulu){
    if (!resultats[setVoulu]) return null;

    let indiceJoueurCherche;
    let indiceTourCherche;
    let flechetteCherche;

    

    return [indiceJoueurCherche,indiceTourCherche,flechetteCherche];
}

function trouverInfoFlechePrecedente() {

    // on part de la position courante
    let setCherche = setActuel;
    let indiceJoueurCherche = indiceJoueurActuel;
    let tourCherche = tourActuel;
    let flechetteCherche = indiceFlechette; 

    if(indiceFlechette == 1){
        if(tourActuel == 0){
            if(indiceJoueurActuel == 0){
                if(setActuel != 0){
                    setCherche = setActuel - 1;
                    indiceJoueurCherche = 
                    tourCherche = 
                    flechetteCherche = 
                }else{

                }
            }

        }else{


        }
    }else{
        flechetteCherche--;
    }


    
    return [setCherche,indiceJoueurCherche,tourCherche,flechetteCherche]; // aucune flèche trouvée (début de partie)
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

function calculMoyenneJoueur(indiceSet, indiceJoueur) {
    // Vérifie que le set et le joueur existent
    if (!resultats[indiceSet] || !resultats[indiceSet][indiceJoueur]) {
        return 0;
    }

    let totalPoints = 0;
    let nbFlechettes = 0;

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
                if (!isNaN(valeur) && valeur > 0) {
                    totalPoints += mode * valeur;
                    nbFlechettes++;
                }
            }
        }
    }

    if (nbFlechettes === 0) return 0;

    let moyenne = totalPoints / nbFlechettes;
    return Math.round(moyenne * 100) / 100; // arrondi à 2 décimales
}

function getNombreSetGagnes(idJoueur) {
    if (!resultatsSets || resultatsSets.length === 0) {
        return 0;
    }

    let nbSets = 0;
    resultatsSets.forEach(function (idGagnant) {
        if (idGagnant === idJoueur) {
            nbSets++;
        }
    });

    return nbSets;
}

function getMinNbFlechetteGagnante(idJoueur) {
    let minF = Infinity;

    for (let s = 0; s < resultats.length; s++) {
        if (resultatsSets[s] !== idJoueur) continue; // set gagné par lui sinon skip

        let idxJ = joueurs.findIndex(j => j.id == idJoueur);
        if (idxJ < 0) continue;

        let nb = 0;
        let score = debutScore;

        let set = resultats[s][idxJ];
        if (!set) continue;

        // parcours tours → fléchettes
        for (let tour in set) {
            let dataTour = set[tour];

            for (let i = 1; i <= 3; i++) {
                let flechette = dataTour[i];
                if (!flechette) continue;

                let mode = flechette[0];
                let valeur = parseInt(flechette[1]) || 0;

                nb++; // 1 fléchette lancée
                score -= (mode * valeur);

                if (score == 0) {
                    // fin du leg
                    if (typeFinish == "2" && mode != 2) {
                        // pas double ⇒ pas valide ⇒ il burst
                        // donc ce set ne compte pas
                        nb = Infinity;
                    }
                    i = 4; // sortir des boucles
                    break;
                }
                if (score < 0) break;
            }
            if (score <= 0) break;
        }

        if (nb < minF) minF = nb;
    }

    return (minF === Infinity ? 0 : minF);
}

function savePartie() {
    let nbrJoueur = joueurs.length;
    let maxScore = 0;
    let indiceGagnant = -1;

    let detailScores = [];
    joueurs.forEach(function (joueur, index) {
        let resultatsPrecis = [];

        for (let s = 0; s < resultats.length; s++) {           // Tous les sets
            const set = resultats[s];
            if (!set) continue;                                 // Si le set n'existe pas
            const joueurSet = set[index] || [];                 // Récupère uniquement le joueur courant
            resultatsPrecis.push(joueurSet);                    // Ajoute ce set au tableau final
        }

        let isGagnant = false;
        if (joueur.id == idGagnant) {
            isGagnant = true;
        }

        let scoreJoueur = {
            id_user: joueur.id,
            nbr_set_gagne: getNombreSetGagnes(joueur.id),
            is_gagnant: isGagnant,
            min_flechette_gagnante: getMinNbFlechetteGagnante(joueur.id),
            details: resultatsPrecis,
            place: "1"
        };
        detailScores.push(scoreJoueur);
    });

    let dataToSend = {
        type: type,
        nbr_joueur: nbrJoueur,
        id_gagnant: idGagnant,
        nbr_set: nbrSetGagnant,
        type_finish: typeFinish,
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

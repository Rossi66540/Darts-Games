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

<div id="config_x01">
    
    <label>Type Jeu :</label>
    <select id="sl_type_jeu">
        <option value="3">301</option>
        <option value="4">501</option>
    </select>

    <label>Nbr de Legs</label>
    <select id="sl_leg">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>        
    </select>
        

    <label>Finish :</label>
    <select id="sl_out">        
        <option value="1">Single Out</option>
        <option value="2">Double Out</option>
    </select>


    <label>Nombre Joueurs :</label>
    <input id="nbJrs" style="width:60px;" type="number" onkeyup="genererTableauNom()" />
    
    <table id="tableJoueurs">
        <tbody></tbody>
    </table>

    <CENTER><button onclick="lancerPartie()" class="buttonGeneral"> LANCER PARTIE </button></CENTER>
</div>

<div id="tableau_jeu">

</div>


<div id="clavier"> 
    
</div>

<script>



</script>
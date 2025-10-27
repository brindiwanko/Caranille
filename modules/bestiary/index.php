<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On fait une recherche de tous les monstres dans le bestiaire du joueur
$monsterBestiaryQuery = $bdd->prepare("SELECT * FROM car_monsters, car_bestiary 
WHERE monsterId = bestiaryMonsterId
AND bestiaryCharacterId = ?");
$monsterBestiaryQuery->execute([$characterId]);
$monsterBestiaryRow = $monsterBestiaryQuery->rowCount();

//Si un ou plusieurs équipements ont été trouvé
if ($monsterBestiaryRow > 0)
{
    ?>
    
    <form method="POST" action="viewMonster.php">
        Entrée dans le bestiaire : <?php echo $monsterBestiaryRow ?> monstre(s) <select name="monsterId" class="form-control">

            <?php
            //on récupère les valeurs de chaque monstres qu'on va ensuite mettre dans le menu déroulant
            while ($monsterBestiary = $monsterBestiaryQuery->fetch())
            {
                //On récupère les informations du monstre
                $monsterId = $monsterBestiary['monsterId']; 
                $monsterName = $monsterBestiary['monsterName'];
                $bestiaryMonsterQuantity = $monsterBestiary['bestiaryMonsterQuantity'];
                ?>
                <option value="<?php echo $monsterId ?>"><?php echo "N°$monsterId - $monsterName ($bestiaryMonsterQuantity victoire(s))" ?></option>
                <?php
            }
            ?>

        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <center><input type="submit" name="viewMonster" class="btn btn-secondary btn-lg" value="Voir la fiche du monstre"></center>
    </form>
    
    <?php
}
//Si aucun monstre n'a été trouvé
else
{
    echo "Il y a actuellement aucun monstre dans votre bestiaire";
}
$monsterBestiaryQuery->closeCursor();

require_once("../../html/footer.php"); ?>
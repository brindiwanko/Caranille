<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On fait une recherche de tous les joueurs dans la base de donnée qui ont un Id différent du notre
$opponentQuery = $bdd->prepare("SELECT * FROM car_characters 
WHERE characterId != ?");
$opponentQuery->execute([$characterId]);
$opponentRow = $opponentQuery->rowCount();

//Si un ou plusieurs personnages ont été trouvé
if ($opponentRow > 0)
{
    ?>
    
    <form method="POST" action="selectedOpponent.php">
        Liste des joueurs : <select class="form-control" name="opponentCharacterId">
                
            <?php
            //On fait une boucle sur tous les résultats
            while ($opponent = $opponentQuery->fetch())
            {
                //On récupère les informations de l'adversaire
                $characterId = $opponent['characterId']; 
                $characterName = $opponent['characterName'];
                ?>
                <option value="<?php echo $characterId ?>"><?php echo $characterName ?></option>
                <?php
            }
            ?>
            
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" class="btn btn-secondary btn-lg" name="enter" value="Lancer le combat">
    </form>
    
    <?php
}
//Si aucun joueur n'a été trouvé
else
{
    echo "Il n'y a aucun autre joueur.";
}
$opponentQuery->closeCursor();

require_once("../../html/footer.php"); ?>
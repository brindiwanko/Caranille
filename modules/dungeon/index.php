<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'est pas dans un lieu on le redirige vers la carte du monde
if ($characterPlaceId == 0) { exit(header("Location: ../../modules/map/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On fait une recherche de tous les monstres limité
$monsterLimitedQueryList = $bdd->prepare("SELECT * FROM car_monsters, car_places, car_places_monsters
WHERE placeMonsterMonsterId = monsterId
AND placeMonsterPlaceId = placeId
AND monsterLimited = 'Yes'
AND monsterQuantity > 0
AND placeId = ?");
$monsterLimitedQueryList->execute([$placeId]);
$monsterLimitedQueryRow = $monsterLimitedQueryList->rowCount();

//Si plusieurs monstres ont été trouvé
if ($monsterLimitedQueryRow > 0)
{
    ?>
    
    <form method="POST" action="selectedMonster.php">
        Monstres temporaire : <select name="battleMonsterId" class="form-control">
                
            <?php
            //On fait une boucle sur tous les résultats
            while ($monsterLimited = $monsterLimitedQueryList->fetch())
            {
                //On récupère les informations du monstre
                $monsterLimitedId = $monsterLimited['monsterId']; 
                $monsterLimitedName = $monsterLimited['monsterName'];
                $monsterLimitedLevel = $monsterLimited['monsterLevel'];
                ?>
                <option value="<?php echo $monsterLimitedId ?>"><?php echo "Niveau $monsterLimitedLevel - $monsterLimitedName" ?></option>
                <?php
            }
            ?>

        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="enter" class="btn btn-secondary btn-lg" value="Lancer le combat">
    </form>

    <hr>
    
    <?php
}
$monsterLimitedQueryList->closeCursor();

//On fait une recherche de tous les monstres classiques
$monsterClassicQueryList = $bdd->prepare("SELECT * FROM car_monsters, car_places, car_places_monsters
WHERE placeMonsterMonsterId = monsterId
AND placeMonsterPlaceId = placeId
AND monsterLimited = 'No'
AND placeId = ?");
$monsterClassicQueryList->execute([$placeId]);
$monsterClassicQueryRow = $monsterClassicQueryList->rowCount();

//Si plusieurs monstres ont été trouvé
if ($monsterClassicQueryRow > 0)
{
    ?>
    
    <form method="POST" action="selectedMonster.php">
        Monstres classique : <select name="battleMonsterId" class="form-control">
                
            <?php
            //On fait une boucle sur tous les résultats
            while ($monsterClassic = $monsterClassicQueryList->fetch())
            {
                //On récupère les informations du monstre
                $monsterClassicId = $monsterClassic['monsterId']; 
                $monsterClassicName = $monsterClassic['monsterName'];
                $monsterClassicLevel = $monsterClassic['monsterLevel'];
                ?>
                <option value="<?php echo $monsterClassicId ?>"><?php echo "Niveau $monsterClassicLevel - $monsterClassicName" ?></option>
                <?php
            }
            ?>
 
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="enter" class="btn btn-secondary btn-lg" value="Lancer le combat">
    </form>
    
    <?php
}
//S'il n'y a aucun monstre de disponible on prévient le joueur
else
{
    echo "Il n'y a aucun monstre classique de disponible.";
}
$monsterClassicQueryList->closeCursor();

require_once("../../html/footer.php"); ?>
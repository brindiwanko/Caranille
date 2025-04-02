<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//On fait une requête dans la base de donnée pour récupérer toutes les classes
$raceQuery = $bdd->query("SELECT * FROM car_races");
?>

<form method="POST" action="manageRace.php">
    Liste des classes : <select name="adminRaceId" class="form-control">

        <?php        
        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
        while ($race = $raceQuery->fetch())
        {
            //On récupère les informations de la classe
            $adminRaceId = stripslashes($race['raceId']);
            $adminRaceName = stripslashes($race['raceName']);
            ?>
            <option value="<?php echo $adminRaceId ?>"><?php echo "$adminRaceName"; ?></option>
            <?php
        }
        $raceQuery->closeCursor();
        ?>
        
    </select>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" name="manage" class="btn btn-secondary btn-lg" value="Gérer la classe">
</form>

<hr>

<form method="POST" action="addRace.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">    
    <input type="submit" class="btn btn-secondary btn-lg" name="add" value="Créer une classe">
</form>

<?php require_once("../html/footer.php");
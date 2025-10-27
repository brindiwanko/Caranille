<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//On fait une recherche dans la base de donnée de toutes les lieux
$placeQuery = $bdd->query("SELECT * FROM car_places
ORDER by placeChapter");
$placeRow = $placeQuery->rowCount();

//S'il existe un ou plusieurs lieux on affiche le menu déroulant
if ($placeRow > 0) 
{
    ?>
    
    <form method="POST" action="managePlace.php">
        Liste des lieux : <select name="adminPlaceId" class="form-control">

            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($place = $placeQuery->fetch())
            {
                $adminPlaceId = $place['placeId'];
                $adminplaceName = $place['placeName'];
				$adminplaceChapter = $place['placeChapter'];
                ?>
                <option value="<?php echo $adminPlaceId ?>"><?php echo "Chapitre $adminplaceChapter - $adminplaceName"; ?></option>
                <?php
            }
            ?>
        
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="manage" class="btn btn-secondary btn-lg" value="Gérer le lieu">
    </form>
    
    <?php
}
//S'il n'y a aucun lieu on prévient le joueur
else
{
    echo "Il n'y a actuellement aucun lieu";
}
$placeQuery->closeCursor();
?>

<hr>

<form method="POST" action="addPlace.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="add" value="Créer un lieu">
</form>

<?php require_once("../html/footer.php");
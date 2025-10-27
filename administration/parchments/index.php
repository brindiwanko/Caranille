<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//On fait une recherche dans la base de donnée de tous les équipements
$parchmentQuery = $bdd->query("SELECT * FROM car_items, car_items_types
WHERE itemItemTypeId = itemTypeId
AND itemTypeName = 'Parchment'
ORDER by itemItemTypeId, itemName");
$parchmentRow = $parchmentQuery->rowCount();

//S'il existe un ou plusieurs parchemins on affiche le menu déroulant
if ($parchmentRow > 0) 
{
    ?>
    
    <form method="POST" action="manageParchment.php">
        Liste des parchemins : <select name="adminItemId" class="form-control">
                
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($parchment = $parchmentQuery->fetch())
            {
                //On récupère les informations du parchemin
                $adminItemId = $parchment['itemId'];
                $adminItemName = $parchment['itemName'];
                $adminItemTypeName = $parchment['itemTypeName'];
                $adminItemTypeNameShow = $parchment['itemTypeNameShow'];
                ?>
                <option value="<?php echo $adminItemId ?>"><?php echo "[$adminItemTypeNameShow] - $adminItemName"; ?></option>
                <?php
            }
            $parchmentQuery->closeCursor();
            ?>
            
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="manage" class="btn btn-secondary btn-lg" value="Gérer le parchemin">
    </form>
    
    <?php
}
//S'il n'y a aucun parchemin on préviens le joueur
else
{
    echo "Il n'y a actuellement aucun parchemin";
}
?>

<hr>

<form method="POST" action="addParchment.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="add" value="Créer un parchemin">
</form>

<?php require_once("../html/footer.php");
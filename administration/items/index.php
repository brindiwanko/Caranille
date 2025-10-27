<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//On fait une recherche dans la base de donnée de tous les équipements
$itemQuery = $bdd->query("SELECT * FROM car_items, car_items_types
WHERE itemItemTypeId = itemTypeId
AND itemTypeName = 'Item'
ORDER by itemItemTypeId, itemName");
$itemRow = $itemQuery->rowCount();

//S'il existe un ou plusieurs objet(s) on affiche le menu déroulant
if ($itemRow > 0) 
{
    ?>
    <form method="POST" action="manageItem.php">
        Liste des objets : <select name="adminItemId" class="form-control">
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($item = $itemQuery->fetch())
            {
                //On récupère les informations de l'objet
                $adminItemId = $item['itemId'];
                $adminItemName = $item['itemName'];
                $adminItemTypeName = $item['itemTypeName'];
                $adminItemTypeNameShow = $item['itemTypeNameShow'];
                ?>
                <option value="<?php echo $adminItemId ?>"><?php echo "[$adminItemTypeNameShow] - $adminItemName"; ?></option>
                <?php
            }
            $itemQuery->closeCursor();
            ?>
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="manage" class="btn btn-secondary btn-lg" value="Gérer l'objet">
    </form>
    <?php
}
//S'il n'y a actuellement aucun objet on prévient le joueur
else
{
    echo "Il n'y a actuellement aucun objet";
}
?>

<hr>

<form method="POST" action="addItem.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="add" value="Créer un objet">
</form>

<?php require_once("../html/footer.php");
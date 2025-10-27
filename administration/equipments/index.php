<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//On fait une recherche dans la base de donnée de tous les équipements
$equipmentQuery = $bdd->query("SELECT * FROM car_items, car_items_types
WHERE itemItemTypeId = itemTypeId
AND (itemTypeName = 'Armor' 
OR itemTypeName = 'Boots' 
OR itemTypeName = 'Gloves' 
OR itemTypeName = 'Helmet' 
OR itemTypeName = 'Weapon')
ORDER by itemItemTypeId, itemName");
$equipmentRow = $equipmentQuery->rowCount();

//S'il existe un ou plusieurs équipements on affiche le menu déroulant
if ($equipmentRow > 0) 
{
    ?>
    
    <form method="POST" action="manageEquipment.php">
        Liste des équipements : <select name="adminItemId" class="form-control">
                
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($equipment = $equipmentQuery->fetch())
            {
                //On récupère les informations de l'équipement
                $adminItemId = $equipment['itemId'];
                $adminItemName = $equipment['itemName'];
                $adminItemTypeName = $equipment['itemTypeName'];
                $adminItemTypeNameShow = $equipment['itemTypeNameShow'];
                ?>
                <option value="<?php echo $adminItemId ?>"><?php echo "[$adminItemTypeNameShow] - $adminItemName"; ?></option>
                <?php
            }
            $equipmentQuery->closeCursor();
            ?>
            
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="manage" class="btn btn-secondary btn-lg" value="Gérer l'équipement">
    </form>
    
    <?php
}
//S'il n'y a aucun équipement on préviens le joueur
else
{
    echo "Il n'y a actuellement aucun équipement";
}
?>

<hr>

<form method="POST" action="addEquipment.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="add" value="Créer un équipement">
</form>

<?php require_once("../html/footer.php");
<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
		//Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
		
        //On fait une requête pour avoir la liste des équipements du personnage
        $equipmentEquippedQuery = $bdd->prepare("SELECT * FROM  car_items, car_items_types, car_inventory 
        WHERE itemItemTypeId = itemTypeId
        AND itemId = inventoryItemId
        AND (itemTypeName = 'Armor' 
        OR itemTypeName = 'Boots' 
        OR itemTypeName = 'Gloves' 
        OR itemTypeName = 'Helmet' 
        OR itemTypeName = 'Weapon')
        AND inventoryEquipped = 1
        AND inventoryCharacterId = ?
		ORDER BY itemItemTypeId, itemName");
        $equipmentEquippedQuery->execute([$characterId]);
        $equipmentEquippedRow = $equipmentEquippedQuery->rowCount();
        
        //Si un ou plusieurs équipements ont été trouvé
        if ($equipmentEquippedRow > 0)
        {
            ?>
            
            <form method="POST" action="viewEquipment.php">
                Actuellement équippé : <select name="itemId" class="form-control">
                        
                    <?php
                    //on récupère les valeurs de chaque joueurs qu'on va ensuite mettre dans le menu déroulant
                    while ($equipmentEquipped = $equipmentEquippedQuery->fetch())
                    {
                        //On récupère les informations de l'équippement
                        $equipmentId = $equipmentEquipped['itemId']; 
                        $equipmentName = $equipmentEquipped['itemName'];
                        $equipmentQuantity = $equipmentEquipped['inventoryQuantity'];
                        $equipmentTypeName = $equipmentEquipped['itemTypeName'];
                        $equipmentTypeNameShow = $equipmentEquipped['itemTypeNameShow'];
                        ?>
                        <option value="<?php echo $equipmentId ?>"><?php echo "[$equipmentTypeNameShow] - $equipmentName (Quantité: $equipmentQuantity)" ?></option>
                        <?php
                    }
                    ?>
                        
                </select>
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <center><input type="submit" name="viewEquipment" class="btn btn-secondary btn-lg" value="Plus d'informations"></center>
            </form>
            
            <?php
        }
        //Si toutes les variables $_POST n'existent pas
        else
        {
            echo "Vous ne possédez aucun équipements équipé";
        }
        $equipmentEquippedQuery->closeCursor();

        echo "<hr>";

        //On fait une requête pour avoir la liste des équipements du personnage
        $equipmentNoEquippedQuery = $bdd->prepare("SELECT * FROM  car_items, car_items_types, car_inventory 
        WHERE itemItemTypeId = itemTypeId
        AND itemId = inventoryItemId
        AND (itemTypeName = 'Armor' 
        OR itemTypeName = 'Boots' 
        OR itemTypeName = 'Gloves' 
        OR itemTypeName = 'Helmet' 
        OR itemTypeName = 'Weapon')
        AND inventoryEquipped = 0
        AND inventoryCharacterId = ?
		ORDER BY itemItemTypeId, itemName");
        $equipmentNoEquippedQuery->execute([$characterId]);
        $equipmentNoEquippedRow = $equipmentNoEquippedQuery->rowCount();
        
        //Si un ou plusieurs équipements ont été trouvé
        if ($equipmentNoEquippedRow > 0)
        {
            ?>
            
            <form method="POST" action="viewEquipment.php">
                Actuellement non équippé : <select name="itemId" class="form-control">
                        
                    <?php
                    //on récupère les valeurs de chaque joueurs qu'on va ensuite mettre dans le menu déroulant
                    while ($equipmentNoEquipped = $equipmentNoEquippedQuery->fetch())
                    {
                        //On récupère les informations de l'équippement
                        $equipmentId = $equipmentNoEquipped['itemId']; 
                        $equipmentName = $equipmentNoEquipped['itemName'];
                        $equipmentQuantity = $equipmentNoEquipped['inventoryQuantity'];
                        $equipmentTypeName = $equipmentNoEquipped['itemTypeName'];
                        $equipmentTypeNameShow = $equipmentNoEquipped['itemTypeNameShow'];
                        ?>
                        <option value="<?php echo $equipmentId ?>"><?php echo "[$equipmentTypeNameShow] - $equipmentName (Quantité: $equipmentQuantity)" ?></option>
                        <?php
                    }
                    ?>
                        
                </select>
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <center><input type="submit" name="viewEquipment" class="btn btn-secondary btn-lg" value="Plus d'informations"></center>
            </form>
            
            <?php
        }
        //Si toutes les variables $_POST n'existent pas
        else
        {
            echo "Vous ne possédez aucun équipements non équipé";
        }
        $equipmentNoEquippedQuery->closeCursor();
        
    }
    //Si le token de sécurité n'est pas correct
    else
    {
        echo "Erreur : La session a expirée, veuillez réessayer";
    }
}
//Si toutes les variables $_POST n'existent pas
else 
{
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>
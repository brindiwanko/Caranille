<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");
?>

<p>Offrir un objet</p>

<form method="POST" action="offerItem.php">
    Liste des objets <select class="form-control" name="adminItemId">

        <?php
        //On fait une recherche dans la base de donnée tous les objets
        $itemQuery = $bdd->query("SELECT * FROM car_items, car_items_types
        WHERE itemItemTypeId = itemTypeId
        ORDER by itemItemTypeId, itemName");
        
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
    Quantité à offrir : <input type="number" class="form-control" name="adminItemQuantity" placeholder="Quantité à offrir" required>
    Liste des personnages <select class="form-control" name="adminCharacterId">
        <option value="0">Tous les joueurs</option>
        
        <?php
        //On fait une recherche dans la base de donnée tous les personnages
        $characterQuery = $bdd->query("SELECT * FROM car_characters
        ORDER by characterName");
        
        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
        while ($character = $characterQuery->fetch())
        {
            $adminCharacterId = $character['characterId'];
            $adminCharacterName =  $character['characterName'];
            ?>
            <option value="<?php echo $adminCharacterId ?>"><?php echo "$adminCharacterName"; ?></option>
            <?php
        }
        $characterQuery->closeCursor();
        ?>
    
    </select>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="offerItem" value="Offrir cet objet">
</form>

<?php require_once("../html/footer.php");
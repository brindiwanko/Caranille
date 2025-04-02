<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['marketId'])
&& isset($_POST['token'])
&& isset($_POST['finalBuy']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['marketId'])
        && $_POST['marketId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $marketId = htmlspecialchars(addslashes($_POST['marketId']));

            //On fait une requête pour vérifier si l'offre choisit existe
            $marketQuery = $bdd->prepare("SELECT * FROM car_market, car_characters, car_items
            WHERE marketCharacterId = characterId
            AND marketItemId = itemId
            AND marketId = ?");
            $marketQuery->execute([$marketId]);
            $marketRow = $marketQuery->rowCount();

            //Si l'offre existe
            if ($marketRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($market = $marketQuery->fetch())
                {
                    //On récupère toutes les informations de l'offre
                    $marketId = stripslashes($market['marketId']);
                    $marketCharacterId = stripslashes($market['characterId']);
                    $marketCharacterName = stripslashes($market['characterName']);
                    $marketItemId = stripslashes($market['itemId']);
                    $marketItemName = stripslashes($market['itemName']);
                    $marketSalePrice = stripslashes($market['marketSalePrice']);
                    $marketItemRaceId = stripslashes($market['itemRaceId']);
                    $marketItemLevel = stripslashes($market['itemLevel']);
                    $marketItemLevelRequired = stripslashes($market['itemLevelRequired']);
                    $marketItemName = stripslashes($market['itemName']);
                    $marketItemDescription = stripslashes($market['itemDescription']);
                    $marketItemHpEffect = stripslashes($market['itemHpEffect']);
                    $marketItemMpEffect = stripslashes($market['itemMpEffect']);
                    $marketItemStrengthEffect = stripslashes($market['itemStrengthEffect']);
                    $marketItemMagicEffect = stripslashes($market['itemMagicEffect']);
                    $marketItemAgilityEffect = stripslashes($market['itemAgilityEffect']);
                    $marketItemDefenseEffect = stripslashes($market['itemDefenseEffect']);
                    $marketItemDefenseMagicEffect = stripslashes($market['itemDefenseMagicEffect']);
                    $marketItemWisdomEffect = stripslashes($market['itemWisdomEffect']);
                }
                //Si le joueur à suffisament d'argent
                if ($characterGold >= $marketSalePrice)
                {
                    //On cherche à savoir si l'objet que le joueur va acheter appartient déjà au joueur
                    $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                    WHERE itemId = inventoryItemId
                    AND inventoryCharacterId = ?
                    AND itemId = ?");
                    $itemQuery->execute([$characterId, $marketItemId]);
                    $itemRow = $itemQuery->rowCount();

                    //Si le personne possède cet objet
                    if ($itemRow == 1) 
                    {
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($item = $itemQuery->fetch())
                        {
                            //On récupère les informations de l'inventaire
                            $inventoryId = stripslashes($item['inventoryId']);
                            $itemQuantity = stripslashes($item['inventoryQuantity']);
                            $inventoryEquipped = stripslashes($item['inventoryEquipped']);
                        }
                        $itemQuery->closeCursor();

                        //On met l'inventaire à jour
                        $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                        inventoryQuantity = inventoryQuantity + 1
                        WHERE inventoryId = :inventoryId");
                        $updateInventory->execute(array(
                        'inventoryId' => $inventoryId));
                        $updateInventory->closeCursor();
                    }
                    //Si le joueur ne possède pas encore cet équipement/objet
                    else
                    {
                        $addItem = $bdd->prepare("INSERT INTO car_inventory VALUES(
                        NULL,
                        :characterId,
                        :marketItemId,
                        '1',
                        '0')");
                        $addItem->execute([
                        'characterId' => $characterId,
                        'marketItemId' => $marketItemId]);
                        $addItem->closeCursor();
                    }
                    
                    //On ajoute l'argent au vendeur
                    $updatecharacter = $bdd->prepare("UPDATE car_characters SET
                    characterGold = characterGold + :marketSalePrice
                    WHERE characterId = :marketCharacterId");
                    $updatecharacter->execute(array(
                    'marketSalePrice' => $marketSalePrice,  
                    'marketCharacterId' => $marketCharacterId));
                    $updatecharacter->closeCursor();

                    //On supprime l'offre de vente
                    $marketDeleteQuery = $bdd->prepare("DELETE FROM car_market
                    WHERE marketId = ?");
                    $marketDeleteQuery->execute([$marketId]);
                    $marketDeleteQuery->closeCursor();

                    //On retire l'argent de la vente au personnage
                    $updatecharacter = $bdd->prepare("UPDATE car_characters SET
                    characterGold = characterGold - :marketSalePrice
                    WHERE characterId = :characterId");
                    $updatecharacter->execute(array(
                    'marketSalePrice' => $marketSalePrice,  
                    'characterId' => $characterId));
                    $updatecharacter->closeCursor();
                    ?>
                    
                    Vous venez d'acheter l'article <?php echo $marketItemName ?> vendu par <em><?php echo $marketCharacterName ?></em> pour <em><?php echo $marketSalePrice ?> Pièce(s) d'or</em>.<br />

                    <hr>

                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
                    </form>
                    
                    <?php
                }
                else
                {
                    ?>
                    
                    Vous n'avez pas assez d'argent

                    <hr>

                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
                }
            }
            //Si l'offre n'exite pas
            else
            {
                echo "Erreur : Cette offre n'existe pas";
            }
            $marketQuery->closeCursor(); 
        }
        //Si tous les champs numérique ne contiennent pas un nombre
        else
        {
            echo "Erreur : Les champs de type numérique ne peuvent contenir qu'un nombre entier";
        }
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
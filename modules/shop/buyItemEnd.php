<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'est pas dans un lieu on le redirige vers la carte du monde
if ($characterPlaceId == 0) { exit(header("Location: ../../modules/map/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['buyQuantity'])
&& isset($_POST['shopId'])
&& isset($_POST['itemId'])
&& isset($_POST['token'])
&& isset($_POST['finalBuy']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

       //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
       if (ctype_digit($_POST['buyQuantity'])
       && ctype_digit($_POST['shopId'])
       && ctype_digit($_POST['itemId'])
       && $_POST['shopId'] >= 1
       && $_POST['itemId'] >= 1)
       {
            //On récupère l'id du formulaire précédent
            $buyQuantity = htmlspecialchars($_POST['buyQuantity']);
            $shopId = htmlspecialchars($_POST['shopId']);
            $itemId = htmlspecialchars($_POST['itemId']);

            //On fait une requête pour vérifier si le magasin choisit existe
            $shopQuery = $bdd->prepare("SELECT * FROM car_shops 
            WHERE shopId = ?");
            $shopQuery->execute([$shopId]);
            $shopRow = $shopQuery->rowCount();

            //Si le magasin existe
            if ($shopRow == 1) 
            {
                //On fait une requête pour vérifier si l'objet choisit existe
                $itemQuery = $bdd->prepare("SELECT * FROM car_items 
                WHERE itemId = ?");
                $itemQuery->execute([$itemId]);
                $itemRow = $itemQuery->rowCount();

                //Si l'objet existe
                if ($itemRow == 1) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($item = $itemQuery->fetch())
                    {
                        //On récupère les informations de l'objet
                        $itemName = $item['itemName'];
                        $itemPurchasePrice = $item['itemPurchasePrice'];
                    }
                    //On fait une requête pour récupérer les informations de l'objet du magasin
                    $shopItemQuery = $bdd->prepare("SELECT * FROM car_shops_items
                    WHERE shopItemShopId = ?
                    AND shopItemItemId = ?");
                    $shopItemQuery->execute([$shopId, $itemId]);

                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($shopItem = $shopItemQuery->fetch())
                    {
                        //On récupère les informations du magasin
                        $itemDiscount = $shopItem['shopItemDiscount'];
                    }
                    $shopItemRow = $shopItemQuery->rowCount();

                    $discount = $itemPurchasePrice * $itemDiscount / 100;
                    $itemPurchasePrice = $itemPurchasePrice - $discount;

                    //On calcul le prix d'achat total en prenant en compte les quantité
                    $itemPurchasePrice = $itemPurchasePrice * $buyQuantity;

                    //Si le joueur à suffisament d'argent
                    if ($characterGold >= $itemPurchasePrice)
                    {
                        //On cherche à savoir si l'objet que le joueur va acheter appartient déjà au joueur
                        $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                        WHERE itemId = inventoryItemId
                        AND inventoryCharacterId = ?
                        AND itemId = ?");
                        $itemQuery->execute([$characterId, $itemId]);
                        $itemRow = $itemQuery->rowCount();

                        //Si le personne possède cet objet
                        if ($itemRow == 1) 
                        {
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($item = $itemQuery->fetch())
                            {
                                //On récupère les informations de l'inventaire
                                $inventoryId = $item['inventoryId'];
                                $itemQuantity = $item['inventoryQuantity'];
                                $inventoryEquipped = $item['inventoryEquipped'];
                            }
                            
                            //On met l'inventaire à jour
                            $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                            inventoryQuantity = inventoryQuantity + :buyQuantity
                            WHERE inventoryId = :inventoryId");
                            $updateInventory->execute(array(
                            'buyQuantity' => $buyQuantity,
                            'inventoryId' => $inventoryId));
                            $updateInventory->closeCursor(); 
                        }
                        //Si le joueur ne possède pas encore cet équipement/objet
                        else
                        {
                            $addItem = $bdd->prepare("INSERT INTO car_inventory VALUES(
                            NULL,
                            :characterId,
                            :itemId,
                            :buyQuantity,
                            '0')");
                            $addItem->execute([
                            'characterId' => $characterId,
                            'itemId' => $itemId,
                            'buyQuantity' => $buyQuantity]);
                            $addItem->closeCursor();  
                        }
                        $itemQuery->closeCursor();

                        //On retire l'argent de la vente au personnage
                        $updatecharacter = $bdd->prepare("UPDATE car_characters SET
                        characterGold = characterGold - :itemPurchasePrice
                        WHERE characterId = :characterId");
                        $updatecharacter->execute(array(
                        'itemPurchasePrice' => $itemPurchasePrice,  
                        'characterId' => $characterId));
                        $updatecharacter->closeCursor();
                        ?>
                        
                        Vous venez d'acheter l'article <?php echo $itemName ?> en <em><?php echo $buyQuantity ?> quantité</em> pour <?php echo $itemPurchasePrice ?> Pièce(s) d'or.

                        <hr>

                        <form method="POST" action="selectedShop.php">
                            <input type="hidden" name="shopId" value="<?php echo $shopId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
                        </form>
                        
                        <?php
                    }
                    else
                    {
                        ?>
                        
                        Vous n'avez pas assez d'argent.

                        <hr>

                        <form method="POST" action="selectedShop.php">
                            <input type="hidden" name="shopId" value="<?php echo $shopId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                        </form>
                        
                        <?php
                    }
                }
                //Si l'article n'exite pas
                else
                {
                    echo "Erreur : Cet article n'existe pas";
                }
                $itemQuery->closeCursor();
            }
            //Si le magasin n'exite pas
            else
            {
                echo "Erreur : Ce magasin n'existe pas";
            }
            $shopQuery->closeCursor();
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
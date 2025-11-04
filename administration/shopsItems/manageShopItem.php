<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminShopItemShopId'])
&& isset($_POST['token'])
&& isset($_POST['manage']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminShopItemShopId'])
        && $_POST['adminShopItemShopId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminShopItemShopId = htmlspecialchars($_POST['adminShopItemShopId']);

            //On fait une requête pour vérifier si le magasin choisit existe
            $shopQuery = $bdd->prepare("SELECT * FROM car_shops 
            WHERE shopId = ?");
            $shopQuery->execute([$adminShopItemShopId]);
            $shopRow = $shopQuery->rowCount();

            //Si le magasin existe
            if ($shopRow == 1)
            {
                //On fait une requête pour vérifier la liste des objets/équipement dans le magasin
                $placeShopQuery = $bdd->prepare("SELECT * FROM car_shops, car_items, car_items_types, car_shops_items
                WHERE itemItemTypeId = itemTypeId
                AND shopItemShopId = shopId
                AND shopItemItemId = itemId
                AND shopId = ?
                ORDER BY itemItemTypeId, itemName");
                $placeShopQuery->execute([$adminShopItemShopId]);
                $placeShopRow = $placeShopQuery->rowCount();

                //S'il existe un ou plusieurs objet dans le magasin on affiche le menu déroulant
                if ($placeShopRow > 0) 
                {
                    ?>

                    <form method="POST" action="editDeleteShopItem.php">
                        Article(s) en vente (Remise en %) : <select name="adminShopItemItemId" class="form-control">
                            
                            <?php
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($placeShop = $placeShopQuery->fetch())
                            {
                                //On récupère les informations des objets
                                $adminShopItemItemId = $placeShop['itemId'];
                                $adminShopItemItemName = $placeShop['itemName'];
                                $adminShopItemDiscount = $placeShop['shopItemDiscount'];
                                $adminItemTypeName = $placeShop['itemTypeName'];
                                $adminItemTypeNameShow = $placeShop['itemTypeNameShow'];
                                ?>
                                <option value="<?php echo $adminShopItemItemId ?>"><?php echo "[$adminItemTypeNameShow] - $adminShopItemItemName ($adminShopItemDiscount%)"; ?></option>
                                <?php
                            }
                            ?>
                            
                        </select>
                        <input type="hidden" name="adminShopItemShopId" value="<?php echo $adminShopItemShopId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="edit" class="btn btn-secondary btn-lg" value="Modifier la réduction">
                        <input type="submit" name="delete" class="btn btn-secondary btn-lg" value="Retirer l'article">
                    </form>
                    
                    <hr>

                    <?php
                }
                $placeShopQuery->closeCursor();

                //On fait une requête pour afficher la liste des objets du jeu qui ne sont pas dans le magasin
                $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_items_types
                WHERE itemItemTypeId = itemTypeId
                AND (SELECT COUNT(*) FROM car_shops_items
                WHERE shopItemShopId = ?
                AND shopItemItemId = itemId) = 0
                ORDER BY itemItemTypeId, itemName");
                $itemQuery->execute([$adminShopItemShopId]);
                $itemRow = $itemQuery->rowCount();
                //S'il existe un ou plusieurs objets on affiche le menu déroulant pour proposer au joueur d'en ajouter
                if ($itemRow > 0) 
                {
                    ?>
                    
                    <form method="POST" action="addShopItem.php">
                        Article(s) existant : <select name="adminShopItemItemId" class="form-control">
                                
                            <?php
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($item = $itemQuery->fetch())
                            {
                                //On récupère les informations des objets
                                $adminShopItemItemId = $item['itemId'];
                                $adminShopItemItemName = $item['itemName'];
                                $adminItemTypeName = $item['itemTypeName'];
                                $adminItemTypeNameShow = $item['itemTypeNameShow'];
                                ?>
                                <option value="<?php echo $adminShopItemItemId ?>"><?php echo "[$adminItemTypeNameShow] - $adminShopItemItemName"; ?></option>
                                <?php
                            }
                            ?>
                            
                        </select>
                        Réduction (De 0 à 100%) <input type="number" name="adminShopItemDiscount" class="form-control" placeholder="Réduction (De 0 à 100%)" value="0" required>
                        <input type="hidden" name="adminShopItemShopId" value="<?php echo $adminShopItemShopId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="add" class="btn btn-secondary btn-lg" value="Ajouter l'article">
                    </form>
                    
                    <?php
                }
                else
                {
                    echo "Il n'y a actuellement aucun article";
                }
                $itemQuery->closeCursor();
                ?>

                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
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
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
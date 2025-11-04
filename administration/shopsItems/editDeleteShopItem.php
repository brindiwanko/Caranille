<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['adminShopItemShopId'])
&& isset($_POST['adminShopItemItemId']))
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
        && ctype_digit($_POST['adminShopItemItemId'])
        && $_POST['adminShopItemShopId'] >= 1
        && $_POST['adminShopItemItemId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminShopItemShopId = htmlspecialchars($_POST['adminShopItemShopId']);
            $adminShopItemItemId = htmlspecialchars($_POST['adminShopItemItemId']);

            //On fait une requête pour vérifier si le magasin choisit existe
            $shopQuery = $bdd->prepare("SELECT * FROM car_shops 
            WHERE shopId = ?");
            $shopQuery->execute([$adminShopItemShopId]);
            $shopRow = $shopQuery->rowCount();

            //Si le magasin existe
            if ($shopRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($shop = $shopQuery->fetch())
                {
                    $adminShopItemShopName = $shop['shopName'];
                }

                //On fait une requête pour vérifier si l'objet choisit existe
                $itemQuery = $bdd->prepare("SELECT * FROM car_items 
                WHERE itemId = ?");
                $itemQuery->execute([$adminShopItemItemId]);
                $itemRow = $itemQuery->rowCount();

                //Si l'objet existe
                if ($itemRow == 1) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($item = $itemQuery->fetch())
                    {
                        $adminShopItemItemName = $item['itemName'];
                    }

                    //On fait une requête pour vérifier si l'objet n'est pas déjà dans ce magasin
                    $shopItemQuery = $bdd->prepare("SELECT * FROM car_shops_items
                    WHERE shopItemShopId = ?
                    AND shopItemItemId = ?");
                    $shopItemQuery->execute([$adminShopItemShopId, $adminShopItemItemId]);
                    $shopItemRow = $shopItemQuery->rowCount();

                    //Si l'objet est dans ce magasin
                    if ($shopItemRow == 1) 
                    {
                        //Si l'utilisateur à cliqué sur le bouton edit
                        if (isset($_POST['edit']))
                        {
                            //On récupère le taux de réduction de l'équipement/objet
                            while ($shopItem = $shopItemQuery->fetch())
                            {
                                $adminShopItemDiscount = $shopItem['shopItemDiscount'];
                            }
                            ?>
                            
                            <h4><?php echo $adminShopItemItemName ?></h4><br />
                            
                            <form method="POST" action="editShopItem.php">
                                Réduction (de 0 à 100%) : <input type="number" name="adminShopItemDiscount" class="form-control" placeholder="Réduction (de 0 à 100%)" value="<?php echo $adminShopItemDiscount ?>" required>
                                <input type="hidden" class="btn btn-secondary btn-lg" name="adminShopItemShopId" value="<?php echo $adminShopItemShopId ?>">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="adminShopItemItemId" value="<?php echo $adminShopItemItemId ?>">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                <input type="submit" class="btn btn-secondary btn-lg" name="finalEdit" value="Mettre à jour">
                            </form>
                            
                            <?php
                        }
                        //Si l'utilisateur à cliqué sur le bouton delete
                        elseif (isset($_POST['delete']))
                        {
                            ?>
                            
                            <p>ATTENTION</p>

                            Vous êtes sur le point de retirer l'article <em><?php echo $adminShopItemItemName ?></em> du magasin <em><?php echo $adminShopItemShopName ?></em>.<br />
                            Confirmez-vous ?

                            <hr>
                                
                            <form method="POST" action="deleteShopItem.php">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="adminShopItemShopId" value="<?php echo $adminShopItemShopId ?>">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="adminShopItemItemId" value="<?php echo $adminShopItemItemId ?>">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                <input type="submit" class="btn btn-secondary btn-lg" name="finalDelete" value="Je confirme">
                            </form>
                    
                            <hr>

                            <form method="POST" action="index.php">
                                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                            </form>
                            
                            <?php
                        }
                        //Si l'utilisateur n'a pas cliqué sur le bouton edit ou delete
                        else 
                        {
                            echo "Erreur : Tous les champs n'ont pas été remplis";
                        }
                    }
                    //Si l'objet n'exite pas
                    else
                    {
                        echo "Erreur : L'objet n'est pas dans ce magasin";
                    }
                    $shopItemQuery->closeCursor();
                }
                //Si l'objet existe pas
                else
                {
                    echo "Erreur : Objet indisponible";
                }
                $itemQuery->closeCursor();
            }
            //Si le magasin existe pas
            else
            {
                echo "Erreur : Magasin indisponible";
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
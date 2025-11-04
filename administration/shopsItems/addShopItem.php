<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminShopItemShopId'])
&& isset($_POST['adminShopItemItemId'])
&& isset($_POST['adminShopItemDiscount'])
&& isset($_POST['token'])
&& isset($_POST['add']))
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
        && ctype_digit($_POST['adminShopItemDiscount'])
        && $_POST['adminShopItemShopId'] >= 1
        && $_POST['adminShopItemItemId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminShopItemShopId = htmlspecialchars($_POST['adminShopItemShopId']);
            $adminShopItemItemId = htmlspecialchars($_POST['adminShopItemItemId']);
            $adminShopItemDiscount = htmlspecialchars($_POST['adminShopItemDiscount']);

            //Si la réduction est entre 0 et 100 on ajoute l'objet
            if ($adminShopItemDiscount >= 0 && $adminShopItemDiscount <= 100)
            {
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
                        //On récupère les informations du magasin
                        $adminShopItemShopPicture = $shop['shopPicture'];
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
                            ///On récupère les informations de l'objet
                            $adminShopItemItemPicture = $item['itemPicture'];
                            $adminShopItemItemName = $item['itemName'];
                        }
                        $itemQuery->closeCursor();
                
                        //On fait une requête pour vérifier si le monstre n'est pas déjà dans cette lieu
                        $shopItemQuery = $bdd->prepare("SELECT * FROM car_shops_items
                        WHERE shopItemShopId = ?
                        AND shopItemItemId = ?");
                        $shopItemQuery->execute([$adminShopItemShopId, $adminShopItemItemId]);
                        $shopItemRow = $shopItemQuery->rowCount();

                        //Si l'objet n'est pas dans ce magasin
                        if ($shopItemRow == 0) 
                        {
                            ?>
                
                            <p>ATTENTION</p> 

                            Vous êtes sur le point d'ajouter l'article <em><?php echo $adminShopItemItemName ?></em> dans le magasin <em><?php echo $adminShopItemShopName ?></em>.<br />
                            Confirmez-vous l'ajout ?

                            <hr>
                                
                            <form method="POST" action="addShopItemEnd.php">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="adminShopItemShopId" value="<?php echo $adminShopItemShopId ?>">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="adminShopItemItemId" value="<?php echo $adminShopItemItemId ?>">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="adminShopItemDiscount" value="<?php echo $adminShopItemDiscount ?>">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                <input type="submit" class="btn btn-secondary btn-lg" name="finalAdd" value="Je confirme">
                            </form>
                            
                            <hr>

                            <form method="POST" action="index.php">
                                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                            </form>
                            
                            <?php
                        }
                        //Si l'objet est déjà dans ce magasin
                        else
                        {
                            ?>
                            
                            Erreur : Cet objet est déjà dans ce magasin

                            <form method="POST" action="manageShopItem.php">
                                <input type="hidden" name="adminShopItemShopId" value="<?php echo $adminShopItemShopId ?>">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                <input type="submit" class="btn btn-secondary btn-lg" name="manage" value="Retour">
                            </form>
                            
                            <?php
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
            //Si la réduction de l'objet est inférieur à 0 ou est supérieur à 100
            else
            {
                ?>
                
                Erreur : Le taux de réduction doit être de 0 à 100
                
                <form method="POST" action="manageShopItem.php">
                    <input type="hidden" name="adminShopItemShopId" value="<?php echo $adminShopItemShopId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="manage" value="Continuer">
                </form>
                
                <?php
            }
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
    echo "Erreur : Tous les champs n'ont pas été rempli";
}

require_once("../html/footer.php");
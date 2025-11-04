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
&& isset($_POST['token'])
&& isset($_POST['finalDelete']))
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
                //On fait une requête pour vérifier si l'objet choisit existe
                $itemQuery = $bdd->prepare("SELECT * FROM car_items 
                WHERE itemId = ?");
                $itemQuery->execute([$adminShopItemItemId]);
                $itemRow = $itemQuery->rowCount();

                //Si l'objet existe
                if ($itemRow == 1) 
                {
                    //On fait une requête pour vérifier si l'objet est bien dans ce magasin
                    $shopItemQuery = $bdd->prepare("SELECT * FROM car_shops_items
                    WHERE shopItemShopId = ?
                    AND shopItemItemId = ?");
                    $shopItemQuery->execute([$adminShopItemShopId, $adminShopItemItemId]);
                    $shopItemRow = $shopItemQuery->rowCount();

                    //Si l'objet est dans ce magasin
                    if ($shopItemRow == 1) 
                    {
                        //On supprime l'équipement/objet du magasin de la base de donnée
                        $shopItemDeleteQuery = $bdd->prepare("DELETE FROM car_shops_items
                        WHERE shopItemItemId = ?");
                        $shopItemDeleteQuery->execute([$adminShopItemItemId]);
                        $shopItemDeleteQuery->closeCursor();
                        ?>

                        L'article a bien été retiré du magasin

                        <hr>
                            
                        <form method="POST" action="manageShopItem.php">
                            <input type="hidden" name="adminShopItemShopId" value="<?php echo $adminShopItemShopId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" class="btn btn-secondary btn-lg" name="manage" value="Continuer">
                        </form>
                        
                        <?php
                    }
                    //Si l'objet n'est pas dans ce magasin
                    else
                    {
                        echo "Impossible de retirer un équipement/objet qui ne fait pas parti de ce magasin";
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
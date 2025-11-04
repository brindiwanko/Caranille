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
&& isset($_POST['buy']))
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
                    $shopItemRow = $shopItemQuery->rowCount();

                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($shopItem = $shopItemQuery->fetch())
                    {
                        //On récupère les informations du magasin
                        $itemDiscount = $shopItem['shopItemDiscount'];
                    }
                    $shopItemQuery->closeCursor();

                    //On calcule le prix final de l'objet par rapport à la réduction
                    $discount = $itemPurchasePrice * $itemDiscount / 100;
                    $itemPurchasePrice = $itemPurchasePrice - $discount; 

                    //On calcul le prix d'achat total en prenant en compte les quantité
                    $itemPurchasePrice = $itemPurchasePrice * $buyQuantity;
                    ?>

                    <p>ATTENTION</p> 
                    
                    Vous êtes sur le point d'acheter l'article <em><?php echo $itemName ?></em> en <em><?php echo $buyQuantity ?> quantité</em> pour <em><?php echo $itemPurchasePrice ?> Pièce(s) d'or</em>.<br />
                    Confirmez-vous l'achat ?
                    
                    <hr>
                    
                    <form method="POST" action="buyItemEnd.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="buyQuantity" value="<?php echo $buyQuantity ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="shopId" value="<?php echo $shopId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="itemId" value="<?php echo $itemId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" class="btn btn-secondary btn-lg" name="finalBuy" value="Je confirme">
                    </form>
        
                    <hr>

                    <form method="POST" action="index.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
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
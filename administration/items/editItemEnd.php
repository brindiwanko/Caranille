<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminItemId'])
&& isset($_POST['adminItemPicture'])
&& isset($_POST['adminItemName'])
&& isset($_POST['adminItemDescription'])
&& isset($_POST['adminItemHpEffects'])
&& isset($_POST['adminItemMpEffect'])
&& isset($_POST['adminItemPurchasePrice'])
&& isset($_POST['adminItemSalePrice'])
&& isset($_POST['token'])
&& isset($_POST['finalEdit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminItemId'])
        && ctype_digit($_POST['adminItemHpEffects'])
        && ctype_digit($_POST['adminItemMpEffect'])
        && ctype_digit($_POST['adminItemPurchasePrice'])
        && ctype_digit($_POST['adminItemSalePrice'])
        && $_POST['adminItemId'] >= 1
        && $_POST['adminItemHpEffects'] >= 0
        && $_POST['adminItemMpEffect'] >= 0
        && $_POST['adminItemPurchasePrice'] >= 0
        && $_POST['adminItemSalePrice'] >= 0)
        {
            //On récupère les informations du formulaire
            $adminItemId = htmlspecialchars($_POST['adminItemId']);
            $adminItemPicture = htmlspecialchars($_POST['adminItemPicture']);
            $adminItemName = htmlspecialchars($_POST['adminItemName']);
            $adminItemDescription = htmlspecialchars($_POST['adminItemDescription']);
            $adminItemHpEffects = htmlspecialchars($_POST['adminItemHpEffects']);
            $adminItemMpEffect = htmlspecialchars($_POST['adminItemMpEffect']);
            $adminItemPurchasePrice = htmlspecialchars($_POST['adminItemPurchasePrice']);
            $adminItemSalePrice = htmlspecialchars($_POST['adminItemSalePrice']);

            //On fait une requête pour vérifier si l'objet choisit existe
            $itemQuery = $bdd->prepare("SELECT * FROM car_items 
            WHERE itemId = ?");
            $itemQuery->execute([$adminItemId]);
            $itemRow = $itemQuery->rowCount();

            //Si l'objet existe
            if ($itemRow == 1) 
            {
                //On met à jour l'objet dans la base de donnée
                $updateItems = $bdd->prepare("UPDATE car_items 
                SET itemPicture = :adminItemPicture,
                itemName = :adminItemName,
                itemDescription = :adminItemDescription,
                itemHpEffect = :adminItemHpEffects,
                itemMpEffect = :adminItemMpEffect,
                itemPurchasePrice = :adminItemPurchasePrice,
                itemSalePrice = :adminItemSalePrice
                WHERE itemId = :adminItemId");

                $updateItems->execute([
                'adminItemPicture' => $adminItemPicture,
                'adminItemName' => $adminItemName,
                'adminItemDescription' => $adminItemDescription,
                'adminItemHpEffects' => $adminItemHpEffects,
                'adminItemMpEffect' => $adminItemMpEffect,
                'adminItemPurchasePrice' => $adminItemPurchasePrice,
                'adminItemSalePrice' => $adminItemSalePrice,
                'adminItemId' => $adminItemId]);
                $updateItems->closeCursor();
                ?>

                L'objet a bien été mit à jour

                <hr>
                    
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si l'objet n'exite pas
            else
            {
                echo "Erreur : Cet objet n'existe pas";
            }
            $itemQuery->closeCursor();
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
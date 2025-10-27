<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminItemPicture'])
&& isset($_POST['adminItemName'])
&& isset($_POST['adminItemDescription'])
&& isset($_POST['adminItemHpEffects'])
&& isset($_POST['adminItemMpEffect'])
&& isset($_POST['adminItemStrengthEffect'])
&& isset($_POST['adminItemMagicEffect'])
&& isset($_POST['adminItemAgilityEffect'])
&& isset($_POST['adminItemDefenseEffect'])
&& isset($_POST['adminItemDefenseMagicEffect'])
&& isset($_POST['adminItemWisdomEffect'])
&& isset($_POST['adminItemProspectingEffect'])
&& isset($_POST['adminItemPurchasePrice'])
&& isset($_POST['adminItemSalePrice'])
&& isset($_POST['token'])
&& isset($_POST['finalAdd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminItemHpEffects'])
        && ctype_digit($_POST['adminItemMpEffect'])
        && ctype_digit($_POST['adminItemStrengthEffect'])
        && ctype_digit($_POST['adminItemMagicEffect'])
        && ctype_digit($_POST['adminItemAgilityEffect'])
        && ctype_digit($_POST['adminItemDefenseEffect'])
        && ctype_digit($_POST['adminItemDefenseMagicEffect'])
        && ctype_digit($_POST['adminItemWisdomEffect'])
        && ctype_digit($_POST['adminItemProspectingEffect'])
        && ctype_digit($_POST['adminItemPurchasePrice'])
        && ctype_digit($_POST['adminItemSalePrice'])
        && $_POST['adminItemHpEffects'] >= 0
        && $_POST['adminItemMpEffect'] >= 0
        && $_POST['adminItemStrengthEffect'] >= 0
        && $_POST['adminItemMagicEffect'] >= 0
        && $_POST['adminItemAgilityEffect'] >= 0
        && $_POST['adminItemDefenseEffect'] >= 0
        && $_POST['adminItemDefenseMagicEffect'] >= 0
        && $_POST['adminItemWisdomEffect'] >= 0
        && $_POST['adminItemProspectingEffect'] >= 0
        && $_POST['adminItemPurchasePrice'] >= 0
        && $_POST['adminItemSalePrice'] >= 0)
        {        
            //On récupère les informations du formulaire
            $adminItemPicture = htmlspecialchars($_POST['adminItemPicture']);
            $adminItemName = htmlspecialchars($_POST['adminItemName']);
            $adminItemDescription = htmlspecialchars($_POST['adminItemDescription']);
            $adminItemHpEffects = htmlspecialchars($_POST['adminItemHpEffects']);
            $adminItemMpEffect = htmlspecialchars($_POST['adminItemMpEffect']);
            $adminItemStrengthEffect = htmlspecialchars($_POST['adminItemStrengthEffect']);
            $adminItemMagicEffect = htmlspecialchars($_POST['adminItemMagicEffect']);
            $adminItemAgilityEffect = htmlspecialchars($_POST['adminItemAgilityEffect']);
            $adminItemDefenseEffect = htmlspecialchars($_POST['adminItemDefenseEffect']);
            $adminItemDefenseMagicEffect = htmlspecialchars($_POST['adminItemDefenseMagicEffect']);
            $adminItemWisdomEffect = htmlspecialchars($_POST['adminItemWisdomEffect']);
            $adminItemProspectingEffect = htmlspecialchars($_POST['adminItemProspectingEffect']);
            $adminItemPurchasePrice = htmlspecialchars($_POST['adminItemPurchasePrice']);
            $adminItemSalePrice = htmlspecialchars($_POST['adminItemSalePrice']);

            //On ajoute l'équipement dans la base de donnée
            $addItem = $bdd->prepare("INSERT INTO car_items VALUES(
            NULL,
            '7',
            '0',
            :adminItemPicture,
            :adminItemName,
            :adminItemDescription,
            '1',
            '1',
            :adminItemHpEffects,
            :adminItemMpEffect,
            :adminItemStrengthEffect,
            :adminItemMagicEffect,
            :adminItemAgilityEffect,
            :adminItemDefenseEffect,
            :adminItemDefenseMagicEffect,
            :adminItemWisdomEffect,
            :adminItemProspectingEffect,
            :adminItemPurchasePrice,
            :adminItemSalePrice)");
            $addItem->execute([
            'adminItemPicture' => $adminItemPicture,
            'adminItemName' => $adminItemName,
            'adminItemDescription' => $adminItemDescription,
            'adminItemHpEffects' => $adminItemHpEffects,
            'adminItemMpEffect' => $adminItemMpEffect,
            'adminItemStrengthEffect' => $adminItemStrengthEffect,
            'adminItemMagicEffect' => $adminItemMagicEffect,
            'adminItemAgilityEffect' => $adminItemAgilityEffect,
            'adminItemDefenseEffect' => $adminItemDefenseEffect,
            'adminItemDefenseMagicEffect' => $adminItemDefenseMagicEffect,
            'adminItemWisdomEffect' => $adminItemWisdomEffect,
            'adminItemProspectingEffect' => $adminItemProspectingEffect,
            'adminItemPurchasePrice' => $adminItemPurchasePrice,
            'adminItemSalePrice' => $adminItemSalePrice]);
            $addItem->closeCursor();
            ?>

            Le parchemin a bien été crée

            <hr>
                
            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
            </form>
            
            <?php
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
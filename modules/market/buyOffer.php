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
&& isset($_POST['buyOffer']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['marketId'])
        && $_POST['marketId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $marketId = htmlspecialchars($_POST['marketId']);

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
                    $marketId = $market['marketId'];
                    $marketCharacterName = $market['characterName'];
                    $marketItemId = $market['itemId'];
                    $marketItemName = $market['itemName'];
                    $marketSalePrice = $market['marketSalePrice'];
                    $marketItemclasseId = $market['itemclasseId'];
                    $marketItemLevel = $market['itemLevel'];
                    $marketItemLevelRequired = $market['itemLevelRequired'];
                    $marketItemName = $market['itemName'];
                    $marketItemDescription = $market['itemDescription'];
                    $marketItemHpEffect = $market['itemHpEffect'];
                    $marketItemMpEffect = $market['itemMpEffect'];
                    $marketItemStrengthEffect = $market['itemStrengthEffect'];
                    $marketItemMagicEffect = $market['itemMagicEffect'];
                    $marketItemAgilityEffect = $market['itemAgilityEffect'];
                    $marketItemDefenseEffect = $market['itemDefenseEffect'];
                    $marketItemDefenseMagicEffect = $market['itemDefenseMagicEffect'];
                    $marketItemWisdomEffect = $market['itemWisdomEffect'];
                }
                ?>
                
                <p>ATTENTION</p> 
                
                Vous êtes sur le point d'acheter l'article <em><?php echo $marketItemName ?></em> vendu par <em><?php echo $marketCharacterName ?></em> pour <em><?php echo $marketSalePrice ?> Pièce(s) d'or</em>.<br />
                Confirmez-vous l'achat ?

                <hr>

                <form method="POST" action="buyOfferEnd.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="marketId" value="<?php echo $marketId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="finalBuy" value="Je confirme">
                </form>

                <hr>

                <form method="POST" action="index.php">
                   <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
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
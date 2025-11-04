<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['itemId'])
&& isset($_POST['token'])
&& isset($_POST['use']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
		//Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['itemId'])
        && $_POST['itemId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $itemId = htmlspecialchars($_POST['itemId']);
    
            //On cherche à savoir si l'objet qui va se vendre appartient bien au joueur
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
                    //On récupère les informations du parchemin
                    $inventoryId = $item['inventoryId'];
                    $itemId = $item['itemId'];
                    $itemPicture = $item['itemPicture'];
                    $itemName = $item['itemName'];
                    $itemDescription = $item['itemDescription'];
                    $itemQuantity = $item['inventoryQuantity'];
                    $itemHpEffect = $item['itemHpEffect'];
                    $itemMpEffect = $item['itemMpEffect'];
                    $itemStrengthEffect = $item['itemStrengthEffect'];
                    $itemMagicEffect = $item['itemMagicEffect'];
                    $itemAgilityEffect = $item['itemAgilityEffect'];
                    $itemDefenseEffect = $item['itemDefenseEffect'];
                    $itemDefenseMagicEffect = $item['itemDefenseMagicEffect'];
                    $itemWisdomEffect = $item['itemWisdomEffect'];
                    $itemProspectingEffect = $item['itemProspectingEffect'];
                    $itemSalePrice = $item['itemSalePrice'];
                }
                ?>
    
                <p>ATTENTION</p> 
                Vous êtes sur le point d'utiliser le parchemin <em><?php echo $itemName ?>.<br />
                Confirmez-vous ?
                
                <hr>
    
                <form method="POST" action="useParchmentEnd.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="itemId" value="<?php echo $itemId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="useFinal" value="Je confirme">
                </form>
    
                <hr>
    
                <form method="POST" action="index.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            else
            {
                echo "Erreur : Impossible d'utiliser un parchemin que vous ne possédez pas.";
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
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>
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
&& isset($_POST['useFinal']))
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
                    //On récupère les informations de l'objet
                    $inventoryId = $item['inventoryId'];
                    $itemQuantity = $item['inventoryQuantity'];
                    $itemName = $item['itemName'];
                    $itemHpEffect = $item['itemHpEffect'];
                    $itemMpEffect = $item['itemMpEffect'];
                }
                
                //On applique les ajouts de l'objet sur les stats du joueur
                $characterHpMin = $characterHpMin + $itemHpEffect;
                $characterMpMin = $characterMpMin + $itemMpEffect;
                
                //Si les HP Minimum sont supérieur au HP Maximum
                if ($characterHpMin > $characterHpTotal)
                {
                    //Si c'est le cas $characterHpMin = $characterHpTotal
                    $characterHpMin = $characterHpTotal;
                }
                
                //Si les MP Minimum sont supérieur au MP Maximum
                if ($characterMpMin > $characterMpTotal)
                {
                    //Si c'est le cas $characterMpMin = $characterMpTotal
                    $characterMpMin = $characterMpTotal;
                }
    
                //Si le joueur possède plusieurs exemplaire de cet équipement/objet
                if ($itemQuantity > 1)
                {
                    //On met l'inventaire à jour
                    $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                    inventoryQuantity = inventoryQuantity - 1
                    WHERE inventoryId = :inventoryId");
                    $updateInventory->execute(array(
                    'inventoryId' => $inventoryId));
                    $updateInventory->closeCursor();
                }
                //Si le joueur ne possède cet équipement/objet que en un seul exemplaire
                else
                {
                    //On supprime l'objet de l'inventaire
                    $updateInventory = $bdd->prepare("DELETE FROM car_inventory
                    WHERE inventoryId = :inventoryId");
                    $updateInventory->execute(array(
                    'inventoryId' => $inventoryId));
                    $updateInventory->closeCursor();
                }
                //On met le personnage à jour
                $updatecharacter = $bdd->prepare("UPDATE car_characters SET
                characterHpMin = :characterHpMin,
                characterMpMin = :characterMpMin
                WHERE characterId = :characterId");
                $updatecharacter->execute(array(
                'characterHpMin' => $characterHpMin,
                'characterMpMin' => $characterMpMin,  
                'characterId' => $characterId));
                $updatecharacter->closeCursor();
                ?>
                
                Vous venez d'utiliser l'objet <?php echo $itemName ?>.
    
                <hr>
    
                <form method="POST" action="index.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
                </form>
                
                <?php
            }
            else
            {
                echo "Erreur : Impossible d'utiliser un objet que vous ne possédez pas.";
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
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
&& isset($_POST['saleQuantity'])
&& isset($_POST['sale']))
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
        && ctype_digit($_POST['saleQuantity'])
        && $_POST['itemId'] >= 1
        && $_POST['saleQuantity'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $itemId = htmlspecialchars($_POST['itemId']);
            $saleQuantity = htmlspecialchars($_POST['saleQuantity']);
    
            //On cherche à savoir si l'objet qui va se vendre appartient bien au joueur
            $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
            WHERE itemId = inventoryItemId
            AND inventoryCharacterId = ?
            AND itemId = ?");
            $itemQuery->execute([$characterId, $itemId]);
            $itemRow = $itemQuery->rowCount();
    
            //Si le personne possède cet objet dans la quantité
            if ($itemRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($item = $itemQuery->fetch())
                {
                    //On récupère les informations de l'objet
                    $inventoryId = $item['inventoryId'];
                    $itemQuantity = $item['inventoryQuantity'];
                    $itemName = $item['itemName'];
                    $itemSalePrice = $item['itemSalePrice'];
                    $inventoryEquipped = $item['inventoryEquipped'];
                }

                //On vérifie si le joueur a suffisament de quantité que ce qu'il souhaite vendre
                if ($itemQuantity >= $saleQuantity)
                {
                    //On calcul le prix de vente total en prenant en compte la quantité
                    $itemSalePriceTotal = $itemSalePrice * $saleQuantity;
                    ?>

                    <p>ATTENTION</p> 
                    Vous êtes sur le point de vendre l'équipement/objet <em><?php echo $itemName ?> en <em><?php echo $saleQuantity ?> quantité pour <?php echo $itemSalePriceTotal ?> Pièce(s) d'or.</em><br />
                    Confirmez-vous la vente ?

                    <hr>

                    <form method="POST" action="saleEnd.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="saleQuantity" value="<?php echo $saleQuantity ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="itemId" value="<?php echo $itemId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" class="btn btn-secondary btn-lg" name="finalSale" value="Je confirme">
                    </form>

                    <hr>

                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>

                    <?php
                }
                else
                {
                    echo "Erreur : Vous ne possedez pas autant de quantité que ce que vous avez saisit";
                }
            }
            else
            {
                echo "Erreur : Impossible de vendre un équipement/objet que vous ne possédez pas.";
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
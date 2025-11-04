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
&& isset($_POST['equip']))
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
    
            //On cherche à savoir si l'équipement que l'on va équipper appartient bien au joueur
            $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
            WHERE itemId = inventoryItemId
            AND inventoryCharacterId = ?
            AND itemId = ?");
            $itemQuery->execute([$characterId, $itemId]);
            $itemRow = $itemQuery->rowCount();
    
            //Si le joueur possède cet équipement
            if ($itemRow == 1) 
            {
                //On récupère les informations de l'équipement
                while ($item = $itemQuery->fetch())
                {
                    //On récupère les informations de l'équippement
                    $inventoryId = $item['inventoryId'];
                    $itemclasseId = $item['itemclasseId'];
                    $itemType = $item['itemItemTypeId'];
                    $itemName = $item['itemName'];
                }
                $itemQuery->closeCursor();
    
                //On vérifie si la classe du joueur lui permet de s'équiper de cet équipement, ou si celui-ci est pour toutes les classes
                if ($characterclasseId == $itemclasseId || $itemclasseId == 0)
                {
                    ?>
                    
                    <p>ATTENTION</p> 
                    Vous êtes sur le point de vous équipper de l'équipement <em><?php echo $itemName ?></em>.<br />
                    Confirmez-vous ?
                    
                    <hr>
        
                    <form method="POST" action="equipEnd.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="itemId" value="<?php echo $itemId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" class="btn btn-secondary btn-lg" name="finalEquip" value="Je confirme">
                    </form>
        
                    <hr>
        
                    <form method="POST" action="index.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
                }
                //Si la classe de l'objet est incompatible avec celle du joueur
                else
                {
                    ?>
                    
                    Votre classe ne vous permet pas de vous équiper de cet équipement
                    
                    <hr>
    
                    <form method="POST" action="index.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
                    </form>
                    
                    <?php
                }
            }
            //Si le joueur ne possède pas cet équipement
            else
            {
                echo "Erreur : Vous ne possedez pas cet équipement";
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
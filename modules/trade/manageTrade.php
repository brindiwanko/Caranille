<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si l'utilisateur à cliqué sur le bouton gérer l'échange
if (isset($_POST['tradeId'])
&& isset($_POST['token'])
&& isset($_POST['manageTrade']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
		//Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['tradeId'])
        && $_POST['tradeId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $tradeId = htmlspecialchars($_POST['tradeId']);
            
            //On fait une requête pour vérifier si cette demande existe et est bien attribué au joueur
            $tradeQuery = $bdd->prepare("SELECT * FROM car_trades
            WHERE (tradeCharacterOneId = ?
            OR tradeCharacterTwoId = ?)
            AND tradeId = ?");
            $tradeQuery->execute([$characterId, $characterId, $tradeId]);
            $tradeRow = $tradeQuery->rowCount();
            
            //Si cette échange existe et est attribuée au joueur
            if ($tradeRow > 0) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($trade = $tradeQuery->fetch())
                {
                    //On récupère les valeurs de la demande d'échange
                    $tradeId = $trade['tradeId'];
                    $tradeCharacterOneId = $trade['tradeCharacterOneId'];
                    $tradeCharacterTwoId = $trade['tradeCharacterTwoId'];
                    $tradeMessage = $trade['tradeMessage'];
                    $tradeLastUpdate = $trade['tradeLastUpdate'];
                }
                
                //Si la première personne de l'échange est le joueur on cherche à savoir qui est l'autre personnage
                if ($tradeCharacterOneId == $characterId)
                {
                    //On fait une requête pour vérifier la liste des conversations dans la base de données
                    $characterQuery = $bdd->prepare("SELECT * FROM car_characters
                    WHERE characterId = ?");
    
                    $characterQuery->execute([$tradeCharacterTwoId]);
                    
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($character = $characterQuery->fetch())
                    {
                        //On récupère les informations du personnage
                        $tradeCharacterId = $character['characterId'];
                        $tradeCharacterName = $character['characterName'];
                    }
                    $characterQuery->closeCursor(); 
                }
                //Si la seconde personne de l'échange est le joueur on cherche à savoir qui est l'autre personne
                else
                {
                    //On fait une requête pour vérifier la liste des conversations dans la base de données
                    $characterQuery = $bdd->prepare("SELECT * FROM car_characters
                    WHERE characterId = ?");
    
                    $characterQuery->execute([$tradeCharacterOneId]);
                    
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($character = $characterQuery->fetch())
                    {
                        //On récupère les informations du personnage
                        $tradeCharacterId = $character['characterId'];
                        $tradeCharacterName = $character['characterName'];
                    }
                    $characterQuery->closeCursor();
                }
                ?>
                
                <p><?php echo $tradeCharacterName ?></p>
                
                <?php
                //On fait une requête pour vérifier la liste des objets de l'échange en cours
                $tradeItemQuery = $bdd->prepare("SELECT * FROM car_trades_items, car_items
                WHERE tradeItemItemId = itemId
                AND tradeItemCharacterId = ?
                AND tradeItemTradeId = ?");
                $tradeItemQuery->execute([$tradeCharacterId, $tradeId]);
                $tradeItemRow = $tradeItemQuery->rowCount();
        
                //Si plusieurs objets ont été trouvée
                if ($tradeItemRow > 0)
                {
                    ?>
                
                    <form>
                        Liste des objets de <?php echo $tradeCharacterName ?> : <select name="itemId" class="form-control">
        
                            <?php
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeItem = $tradeItemQuery->fetch())
                            {
                                //on récupère les valeurs de chaque objets qu'on va ensuite mettre dans le menu déroulant
                                $tradeItemId = $tradeItem['itemId'];
                                $tradeItemName = $tradeItem['itemName'];
                                $tradeItemQuantity = $tradeItem['tradeItemItemQuantity'];
                                ?>
                                <option value="<?php echo $tradeItemId ?>"><?php echo "$tradeItemName ($tradeItemQuantity)" ?></option>
                                <?php
                            }
                            ?>
        
                        </select>
                    </form>
                    
                    <?php
                }
                else
                {
                   echo "Il n'y a actuellement aucun objet<br />"; 
                }
                $tradeItemQuery->closeCursor();
                
                //On fait une requête pour vérifier si le joueur a mit des pièces d'or dans l'échange
                $tradeGoldQuery = $bdd->prepare("SELECT * FROM car_trades_golds
                WHERE tradeGoldCharacterId = ?
                AND tradeGoldTradeId = ?");
                $tradeGoldQuery->execute([$tradeCharacterId, $tradeId]);
                $tradeGoldRow = $tradeGoldQuery->rowCount();
        
                //Si l'utilisateur a mit des pièces d'or dans l'échange on récupère combien il a mit
                if ($tradeGoldRow == 1)
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($tradeGold = $tradeGoldQuery->fetch())
                    {
                        //on récupère les valeurs de chaque magasins qu'on va ensuite mettre dans le menu déroulant
                        $tradeGoldQuantity = $tradeGold['tradeGoldQuantity'];
                    }
                }
                ?>
                
                Pièces d'or: <?php echo $tradeGoldQuantity ?>
                
                <hr>
                
                <p><?php echo $characterName ?></p>
                
                <?php
                $tradeItemQuery = $bdd->prepare("SELECT * FROM car_trades_items, car_items
                WHERE tradeItemItemId = itemId
                AND tradeItemCharacterId = ?
                AND tradeItemTradeId = ?");
                $tradeItemQuery->execute([$characterId, $tradeId]);
                $tradeItemRow = $tradeItemQuery->rowCount();
        
                //Si plusieurs objets ont été trouvée
                if ($tradeItemRow > 0)
                {
                    ?>
                
                    <form method="POST" action="removeItem.php">
                        Liste des objets de <?php echo $characterName ?> : <select name="tradeItemId" class="form-control">
        
                            <?php
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeItem = $tradeItemQuery->fetch())
                            {
                                //on récupère les valeurs de chaque objets qu'on va ensuite mettre dans le menu déroulant
                                $tradeItemId = $tradeItem['itemId'];
                                $tradeItemName = $tradeItem['itemName'];
                                $tradeItemQuantity = $tradeItem['tradeItemItemQuantity'];
                                ?>
                                <option value="<?php echo $tradeItemId ?>"><?php echo "$tradeItemName ($tradeItemQuantity)" ?></option>
                                <?php
                            }
                            ?>
        
                        </select>
                        <input type="hidden" name="tradeId" value="<?php echo $tradeId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="removeItem" class="btn btn-secondary btn-lg" value="Retirer l'objet">
                    </form>
                    
                    <?php
                }
                else
                {
                   echo "Il n'y a actuellement aucun objet<br />"; 
                }
                $tradeItemQuery->closeCursor();
                
                //On fait une requête pour vérifier si le joueur a mit des pièces d'or dans l'échange
                $tradeGoldQuery = $bdd->prepare("SELECT * FROM car_trades_golds
                WHERE tradeGoldCharacterId = ?
                AND tradeGoldTradeId = ?");
                $tradeGoldQuery->execute([$characterId, $tradeId]);
                $tradeGoldRow = $tradeGoldQuery->rowCount();
        
                //Si l'utilisateur a mit des pièces d'or dans l'échange on récupère combien il a mit
                if ($tradeGoldRow == 1)
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($tradeGold = $tradeGoldQuery->fetch())
                    {
                        //on récupère les valeurs de chaque magasins qu'on va ensuite mettre dans le menu déroulant
                        $tradeGoldQuantity = $tradeGold['tradeGoldQuantity'];
                    }
                }
                ?>
                
                Pièces d'or: <?php echo $tradeGoldQuantity ?>
                
                <hr>
                
                <form method="POST" action="addTradeItem.php">
                    <input type="hidden" name="tradeId" value="<?php echo $tradeId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="addTradeItem" value="Ajouter un objet">
                </form>
                
                <form method="POST" action="addGold.php">
                    <input type="hidden" name="tradeId" value="<?php echo $tradeId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="addGold" value="Modifier le nombre de pièces d'or">
                </form>
                
                <hr>
                
                <form method="POST" action="acceptTrade.php">
                    <input type="hidden" name="tradeDate" value="<?php echo $tradeLastUpdate ?>">
                    <input type="hidden" name="tradeId" value="<?php echo $tradeId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="acceptTrade" value="Accepter l'échange">
                </form>
                
                <form method="POST" action="declineTrade.php">
                    <input type="hidden" name="tradeId" value="<?php echo $tradeId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="declineTrade" value="Refuser l'échange">
                </form>
                
                <hr>
                
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="manage" value="Retour">
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
//Si tous les champs n'ont pas été rempli
else
{
    echo "Erreur : Tous les champs n'ont pas été rempli";
}
?>

<?php require_once("../../html/footer.php"); ?>
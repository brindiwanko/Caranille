<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['tradeId'])
&& isset($_POST['tradeDate'])
&& isset($_POST['token'])
&& isset($_POST['acceptTrade']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['tradeId'])
        && $_POST['tradeId'] >= 0)
        {
            //On récupère l'id du formulaire précédent
            $tradeId = htmlspecialchars($_POST['tradeId']);
            $tradeDate = htmlspecialchars($_POST['tradeDate']);
            
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
                    $tradeCharacterOneTradeAccepted = $trade['tradeCharacterOneTradeAccepted'];
                    $tradeCharacterTwoTradeAccepted = $trade['tradeCharacterTwoTradeAccepted'];
                }
                
                if ($tradeDate == $tradeLastUpdate)
                {
                    //Si la première personne de l'échange est le joueur
                    if ($tradeCharacterOneId == $characterId)
                    {
                        //On met à jour l'échange dans la base de donnée
                        $updateTrade = $bdd->prepare("UPDATE car_trades
                        SET tradeCharacterOneTradeAccepted = 'Yes'
                        WHERE tradeId = :tradeId");
                        $updateTrade->execute([
                        'tradeId' => $tradeId]);
                        $updateTrade->closeCursor();
                        
                        //Si l'autre joueur à déjà validé l'échange
                        if ($tradeCharacterTwoTradeAccepted == "Yes")
                        {
                            //On fait une requête pour récupérer la liste des objets que l'autre joueur à proposé
                            $tradeItemQuery = $bdd->prepare("SELECT * FROM car_trades_items
                            WHERE tradeItemCharacterId = ?
                            AND tradeItemTradeId = ?");
                            $tradeItemQuery->execute([$tradeCharacterTwoId, $tradeId]);
                            $tradeRow = $tradeItemQuery->rowCount();
                            
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeItem = $tradeItemQuery->fetch())
                            {
                                $tradeItemId = $tradeItem['tradeItemItemId'];
                                $tradeItemQuantity = $tradeItem['tradeItemItemQuantity'];
                                
                                //On fait une requête pour récupérer les informations de l'objet
                                $itemQuery = $bdd->prepare("SELECT * FROM car_items
                                WHERE itemId = ?");
                                $itemQuery->execute([$tradeItemId]);
                                
                                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                while ($item = $itemQuery->fetch())
                                {
                                    $itemName = $item['itemName'];
                                }
    
                                //On cherche à savoir si l'objet que le joueur va recevoir appartient déjà au joueur
                                $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                                WHERE itemId = inventoryItemId
                                AND inventoryCharacterId = ?
                                AND itemId = ?");
                                $itemQuery->execute([$tradeCharacterOneId, $tradeItemId]);
                                $itemRow = $itemQuery->rowCount();
                
                                //Si le personne possède cet objet
                                if ($itemRow == 1) 
                                {
                                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                    while ($item = $itemQuery->fetch())
                                    {
                                        //On récupère les informations de l'inventaire
                                        $itemId = $item['itemId'];
                                        $itemName = $item['itemName'];
                                        $inventoryId = $item['inventoryId'];
                                        $itemQuantity = $item['inventoryQuantity'];
                                        $inventoryEquipped = $item['inventoryEquipped'];
                                    }
                                    $itemQuery->closeCursor();
                                    
                                    //On met l'inventaire à jour
                                    $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                                    inventoryQuantity = inventoryQuantity + :tradeItemQuantity
                                    WHERE inventoryId = :inventoryId");
                                    $updateInventory->execute(array(
                                    'tradeItemQuantity' => $tradeItemQuantity,
                                    'inventoryId' => $inventoryId));
                                    $updateInventory->closeCursor();
                                }
                                else
                                {
                                    $addItem = $bdd->prepare("INSERT INTO car_inventory VALUES(
                                    NULL,
                                    :characterId,
                                    :tradeItemId,
                                    :tradeItemQuantity,
                                    '0')");
                                    $addItem->execute([
                                    'characterId' => $tradeCharacterOneId,
                                    'tradeItemId' => $tradeItemId,
                                    'tradeItemQuantity' => $tradeItemQuantity]);
                                    $addItem->closeCursor();
                                }
                                
                                echo "Vous avez reçu l'objet $itemName en $tradeItemQuantity exemplaire(s)<br />";
                            }
                            
                            //On fait une requête pour récupérer le montant de l'argent que l'autre joueur à proposé
                            $tradeGoldQuery = $bdd->prepare("SELECT * FROM car_trades_golds
                            WHERE tradeGoldCharacterId = ?
                            AND tradeGoldTradeId = ?");
                            $tradeGoldQuery->execute([$tradeCharacterTwoId, $tradeId]);
                            $tradeGoldRow = $tradeGoldQuery->rowCount();
                            
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeGold = $tradeGoldQuery->fetch())
                            {
                                $tradeGoldQuantity = $tradeGold['tradeGoldQuantity'];
                            }
                            
                            echo "Vous avez reçu $tradeGoldQuantity pièce(s) d'or<br />";
                            
                            //On ajoute l'argent au joueur
                            $updateCharacter = $bdd->prepare("UPDATE car_characters SET
                            characterGold = characterGold + :tradeGoldQuantity
                            WHERE characterId = :tradeCharacterOneId");
                            $updateCharacter->execute(array(
                            'tradeGoldQuantity' => $tradeGoldQuantity,  
                            'tradeCharacterOneId' => $tradeCharacterOneId));
                            $updateCharacter->closeCursor();
                            
                            
                            //On fait la même chose pour l'autre joueur
                            
                            //On fait une requête pour récupérer la liste des objets que l'autre joueur à proposé
                            $tradeItemQuery = $bdd->prepare("SELECT * FROM car_trades_items
                            WHERE tradeItemCharacterId = ?
                            AND tradeItemTradeId = ?");
                            $tradeItemQuery->execute([$tradeCharacterOneId, $tradeId]);
                            $tradeRow = $tradeItemQuery->rowCount();
                            
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeItem = $tradeItemQuery->fetch())
                            {
                                $tradeItemId = $tradeItem['tradeItemItemId'];
                                $tradeItemQuantity = $tradeItem['tradeItemItemQuantity'];
                                
                                //On fait une requête pour récupérer les informations de l'objet
                                $itemQuery = $bdd->prepare("SELECT * FROM car_items
                                WHERE itemId = ?");
                                $itemQuery->execute([$tradeItemId]);
                                
                                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                while ($item = $itemQuery->fetch())
                                {
                                    $itemName = $item['itemName'];
                                }
                                
                                //On cherche à savoir si l'objet que le joueur va recevoir appartient déjà au joueur
                                $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                                WHERE itemId = inventoryItemId
                                AND inventoryCharacterId = ?
                                AND itemId = ?");
                                $itemQuery->execute([$tradeCharacterTwoId, $tradeItemId]);
                                $itemRow = $itemQuery->rowCount();
                
                                //Si le personne possède cet objet
                                if ($itemRow == 1) 
                                {
                                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                    while ($item = $itemQuery->fetch())
                                    {
                                        //On récupère les informations de l'inventaire
                                        $itemId = $item['itemId'];
                                        $itemName = $item['itemName'];
                                        $inventoryId = $item['inventoryId'];
                                        $itemQuantity = $item['inventoryQuantity'];
                                        $inventoryEquipped = $item['inventoryEquipped'];
                                    }
                                    $itemQuery->closeCursor();
                                    
                                    //On met l'inventaire à jour
                                    $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                                    inventoryQuantity = inventoryQuantity + :tradeItemQuantity
                                    WHERE inventoryId = :inventoryId");
                                    $updateInventory->execute(array(
                                    'tradeItemQuantity' => $tradeItemQuantity,
                                    'inventoryId' => $inventoryId));
                                    $updateInventory->closeCursor();
                                }
                                else
                                {
                                    $addItem = $bdd->prepare("INSERT INTO car_inventory VALUES(
                                    NULL,
                                    :characterId,
                                    :tradeItemId,
                                    :tradeItemQuantity,
                                    '0')");
                                    $addItem->execute([
                                    'characterId' => $tradeCharacterTwoId,
                                    'tradeItemId' => $tradeItemId,
                                    'tradeItemQuantity' => $tradeItemQuantity]);
                                    $addItem->closeCursor();
                                }
                            }
                            
                            //On fait une requête pour récupérer le montant de l'argent que l'autre joueur à proposé
                            $tradeGoldQuery = $bdd->prepare("SELECT * FROM car_trades_golds
                            WHERE tradeGoldCharacterId = ?
                            AND tradeGoldTradeId = ?");
                            $tradeGoldQuery->execute([$tradeCharacterOneId, $tradeId]);
                            $tradeGoldRow = $tradeGoldQuery->rowCount();
                            
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeGold = $tradeGoldQuery->fetch())
                            {
                                $tradeGoldQuantity = $tradeGold['tradeGoldQuantity'];
                            }
                            
                            //On ajoute l'argent au joueur
                            $updateCharacter = $bdd->prepare("UPDATE car_characters SET
                            characterGold = characterGold + :tradeGoldQuantity
                            WHERE characterId = :tradeCharacterTwoId");
                            $updateCharacter->execute(array(
                            'tradeGoldQuantity' => $tradeGoldQuantity,  
                            'tradeCharacterTwoId' => $tradeCharacterTwoId));
                            $updateCharacter->closeCursor();
                            
                            $notificationDate = date('Y-m-d H:i:s');
                            $notificationMessage = "$characterName a accepté l'échange";
                            
                            //On envoie une notification au joueur
                            $addNotification = $bdd->prepare("INSERT INTO car_notifications VALUES(
                            NULL,
                            :tradeCharacterTwoId,
                            :notificationDate,
                            :notificationMessage,
                            'No')");
                            $addNotification->execute(array(
                            'tradeCharacterTwoId' => $tradeCharacterTwoId,  
                            'notificationDate' => $notificationDate,
                            'notificationMessage' => $notificationMessage));
                            $addNotification->closeCursor();
                            
                            //On supprime l'échange
                            $deleteTrade = $bdd->prepare("DELETE FROM car_trades 
                            WHERE tradeId = :tradeId");
                            $deleteTrade->execute(array('tradeId' => $tradeId));
                            $deleteTrade->closeCursor();
                            
                            $deleteTradeItems = $bdd->prepare("DELETE FROM car_trades_items
                            WHERE tradeItemTradeId = :tradeId");
                            $deleteTradeItems->execute(array('tradeId' => $tradeId));
                            $deleteTradeItems->closeCursor();
                            
                            $deleteTradeGolds = $bdd->prepare("DELETE FROM car_trades_golds
                            WHERE tradeGoldTradeId = :tradeId");
                            $deleteTradeGolds->execute(array('tradeId' => $tradeId));
                            $deleteTradeGolds->closeCursor();
                            ?>
                            
                            <hr>
                                            
                            L'échange a été terminé, vous avez reçu les objets et l'or proposé
                            
                            <hr>
            
                            <form method="POST" action="index.php">
                                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                            </form>
                            
                            <?php
                        }
                        else
                        {
                            ?>
                            
                            En attente de l'acceptation de l'autre joueur
                            
                            <hr>
            
                            <form method="POST" action="index.php">
                                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                            </form>
                            
                            <?php
                        }
                    }
                    //Si la seconde personne de l'échange est le joueur
                    else
                    {
                        //On met à jour le chapitre dans la base de donnée
                        $updateTrade = $bdd->prepare("UPDATE car_trades
                        SET tradeCharacterTwoTradeAccepted = 'Yes'
                        WHERE tradeId = :tradeId");
                        $updateTrade->execute([
                        'tradeId' => $tradeId]);
                        $updateTrade->closeCursor();
                        
                        //Si l'autre joueur à déjà validé l'échange
                        if ($tradeCharacterOneTradeAccepted == "Yes")
                        {
                            //On fait une requête pour récupérer la liste des objets que l'autre joueur à proposé
                            $tradeItemQuery = $bdd->prepare("SELECT * FROM car_trades_items
                            WHERE tradeItemCharacterId = ?
                            AND tradeItemTradeId = ?");
                            $tradeItemQuery->execute([$tradeCharacterOneId, $tradeId]);
                            $tradeRow = $tradeItemQuery->rowCount();
                            
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeItem = $tradeItemQuery->fetch())
                            {
                                $tradeItemId = $tradeItem['tradeItemItemId'];
                                $tradeItemQuantity = $tradeItem['tradeItemItemQuantity'];
                                
                                //On fait une requête pour récupérer les informations de l'objet
                                $itemQuery = $bdd->prepare("SELECT * FROM car_items
                                WHERE itemId = ?");
                                $itemQuery->execute([$tradeItemId]);
                                
                                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                while ($item = $itemQuery->fetch())
                                {
                                    $itemName = $item['itemName'];
                                }
                                
                                //On cherche à savoir si l'objet que le joueur va recevoir appartient déjà au joueur
                                $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                                WHERE itemId = inventoryItemId
                                AND inventoryCharacterId = ?
                                AND itemId = ?");
                                $itemQuery->execute([$tradeCharacterTwoId, $tradeItemId]);
                                $itemRow = $itemQuery->rowCount();
                
                                //Si le personne possède cet objet
                                if ($itemRow == 1) 
                                {
                                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                    while ($item = $itemQuery->fetch())
                                    {
                                        //On récupère les informations de l'inventaire
                                        $itemId = $item['itemId'];
                                        $itemName = $item['itemName'];
                                        $inventoryId = $item['inventoryId'];
                                        $itemQuantity = $item['inventoryQuantity'];
                                        $inventoryEquipped = $item['inventoryEquipped'];
                                    }
                                    $itemQuery->closeCursor();
                                    
                                    //On met l'inventaire à jour
                                    $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                                    inventoryQuantity = inventoryQuantity + :tradeItemQuantity
                                    WHERE inventoryId = :inventoryId");
                                    $updateInventory->execute(array(
                                    'tradeItemQuantity' => $tradeItemQuantity,
                                    'inventoryId' => $inventoryId));
                                    $updateInventory->closeCursor();
                                }
                                else
                                {
                                    $addItem = $bdd->prepare("INSERT INTO car_inventory VALUES(
                                    NULL,
                                    :characterId,
                                    :tradeItemId,
                                    :tradeItemQuantity,
                                    '0')");
                                    $addItem->execute([
                                    'characterId' => $tradeCharacterTwoId,
                                    'tradeItemId' => $tradeItemId,
                                    'tradeItemQuantity' => $tradeItemQuantity]);
                                    $addItem->closeCursor();
                                }
                                
                                echo "Vous avez reçu l'objet $itemName en $tradeItemQuantity exemplaire(s)<br />";
                            }
                            
                            //On fait une requête pour récupérer le montant de l'argent que l'autre joueur à proposé
                            $tradeGoldQuery = $bdd->prepare("SELECT * FROM car_trades_golds
                            WHERE tradeGoldCharacterId = ?
                            AND tradeGoldTradeId = ?");
                            $tradeGoldQuery->execute([$tradeCharacterOneId, $tradeId]);
                            $tradeGoldRow = $tradeGoldQuery->rowCount();
                            
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeGold = $tradeGoldQuery->fetch())
                            {
                                $tradeGoldQuantity = $tradeGold['tradeGoldQuantity'];
                            }
                            
                            echo "Vous avez reçu $tradeGoldQuantity pièce(s) d'or<br />";
                            
                            //On ajoute l'argent au joueur
                            $updateCharacter = $bdd->prepare("UPDATE car_characters SET
                            characterGold = characterGold + :tradeGoldQuantity
                            WHERE characterId = :tradeCharacterTwoId");
                            $updateCharacter->execute(array(
                            'tradeGoldQuantity' => $tradeGoldQuantity,  
                            'tradeCharacterTwoId' => $tradeCharacterTwoId));
                            $updateCharacter->closeCursor();
                            
                            //On fait la même chose pour l'autre joueur
                            
                            //On fait une requête pour récupérer la liste des objets que l'autre joueur à proposé
                            $tradeItemQuery = $bdd->prepare("SELECT * FROM car_trades_items
                            WHERE tradeItemCharacterId = ?
                            AND tradeItemTradeId = ?");
                            $tradeItemQuery->execute([$tradeCharacterTwoId, $tradeId]);
                            $tradeRow = $tradeItemQuery->rowCount();
                            
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeItem = $tradeItemQuery->fetch())
                            {
                                $tradeItemId = $tradeItem['tradeItemItemId'];
                                $tradeItemQuantity = $tradeItem['tradeItemItemQuantity'];
                                
                                //On fait une requête pour récupérer les informations de l'objet
                                $itemQuery = $bdd->prepare("SELECT * FROM car_items
                                WHERE itemId = ?");
                                $itemQuery->execute([$tradeItemId]);
                                
                                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                while ($item = $itemQuery->fetch())
                                {
                                    $itemName = $item['itemName'];
                                }
                                
                                //On cherche à savoir si l'objet que le joueur va recevoir appartient déjà au joueur
                                $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                                WHERE itemId = inventoryItemId
                                AND inventoryCharacterId = ?
                                AND itemId = ?");
                                $itemQuery->execute([$tradeCharacterOneId, $tradeItemId]);
                                $itemRow = $itemQuery->rowCount();
                
                                //Si le personne possède cet objet
                                if ($itemRow == 1) 
                                {
                                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                    while ($item = $itemQuery->fetch())
                                    {
                                        //On récupère les informations de l'inventaire
                                        $itemId = $item['itemId'];
                                        $itemName = $item['itemName'];
                                        $inventoryId = $item['inventoryId'];
                                        $itemQuantity = $item['inventoryQuantity'];
                                        $inventoryEquipped = $item['inventoryEquipped'];
                                    }
                                    $itemQuery->closeCursor();
                                    
                                    //On met l'inventaire à jour
                                    $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                                    inventoryQuantity = inventoryQuantity + :tradeItemQuantity
                                    WHERE inventoryId = :inventoryId");
                                    $updateInventory->execute(array(
                                    'tradeItemQuantity' => $tradeItemQuantity,
                                    'inventoryId' => $inventoryId));
                                    $updateInventory->closeCursor();
                                }
                                else
                                {
                                    $addItem = $bdd->prepare("INSERT INTO car_inventory VALUES(
                                    NULL,
                                    :characterId,
                                    :tradeItemId,
                                    :tradeItemQuantity,
                                    '0')");
                                    $addItem->execute([
                                    'characterId' => $tradeCharacterOneId,
                                    'tradeItemId' => $tradeItemId,
                                    'tradeItemQuantity' => $tradeItemQuantity]);
                                    $addItem->closeCursor();
                                }
                            }
                            
                            //On fait une requête pour récupérer le montant de l'argent que l'autre joueur à proposé
                            $tradeGoldQuery = $bdd->prepare("SELECT * FROM car_trades_golds
                            WHERE tradeGoldCharacterId = ?
                            AND tradeGoldTradeId = ?");
                            $tradeGoldQuery->execute([$tradeCharacterTwoId, $tradeId]);
                            $tradeGoldRow = $tradeGoldQuery->rowCount();
                            
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($tradeGold = $tradeGoldQuery->fetch())
                            {
                                $tradeGoldQuantity = $tradeGold['tradeGoldQuantity'];
                            }
                            
                            //On ajoute l'argent au joueur
                            $updateCharacter = $bdd->prepare("UPDATE car_characters SET
                            characterGold = characterGold + :tradeGoldQuantity
                            WHERE characterId = :tradeCharacterTwoId");
                            $updateCharacter->execute(array(
                            'tradeGoldQuantity' => $tradeGoldQuantity,  
                            'tradeCharacterTwoId' => $tradeCharacterOneId));
                            $updateCharacter->closeCursor();
                            
                            $notificationDate = date('Y-m-d H:i:s');
                            $notificationMessage = "$characterName a accepté l'échange";
                            
                            //On envoie une notification au joueur
                            $addNotification = $bdd->prepare("INSERT INTO car_notifications VALUES(
                            NULL,
                            :tradeCharacterOneId,
                            :notificationDate,
                            :notificationMessage,
                            'No')");
                            $addNotification->execute(array(
                            'tradeCharacterOneId' => $tradeCharacterOneId,  
                            'notificationDate' => $notificationDate,
                            'notificationMessage' => $notificationMessage));
                            $addNotification->closeCursor();
                            
                            //On supprime l'échange
                            $deleteTrade = $bdd->prepare("DELETE FROM car_trades 
                            WHERE tradeId = :tradeId");
                            $deleteTrade->execute(array('tradeId' => $tradeId));
                            $deleteTrade->closeCursor();
                            
                            $deleteTradeItems = $bdd->prepare("DELETE FROM car_trades_items
                            WHERE tradeItemTradeId = :tradeId");
                            $deleteTradeItems->execute(array('tradeId' => $tradeId));
                            $deleteTradeItems->closeCursor();
                            
                            $deleteTradeGolds = $bdd->prepare("DELETE FROM car_trades_golds
                            WHERE tradeGoldTradeId = :tradeId");
                            $deleteTradeGolds->execute(array('tradeId' => $tradeId));
                            $deleteTradeGolds->closeCursor();
                            ?>
                            
                            <hr>
                                            
                            L'échange a été terminé, vous avez reçu les objets et l'or proposé
                            
                            <hr>
            
                            <form method="POST" action="index.php">
                                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                            </form>
                            
                            <?php
                            
                        }
                        else
                        {
                            ?>
                            
                            En attente de l'acceptation de l'autre joueur
                            
                            <hr>
            
                            <form method="POST" action="index.php">
                                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                            </form>
                            
                            <?php
                        }
                    }
                }
                else
                {
                    echo "Il y a eu une modification de l'échange pendant la validation, veuillez retourner sur l'interface d'échange et recommencer";
                }
            }
            //Si la demande d'échange n'existe pas ou n'est pas attribué au joueur
            else
            {
                echo "Erreur : Cette demande d'échange n'existe pas où ne vous est pas attribuée";
            }
            $tradeQuery->closeCursor(); 
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

require_once("../../html/footer.php"); ?>
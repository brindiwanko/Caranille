<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['cancelTradeRequest']) 
|| isset($_POST['acceptTradeRequest']) 
|| isset($_POST['declineTradeRequest']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
        //Si l'utilisateur à cliqué sur le bouton annuler l'échange
        if (isset($_POST['tradeRequestId'])
        && isset($_POST['cancelTradeRequest']))
        {
            //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
            if (ctype_digit($_POST['tradeRequestId'])
            && $_POST['tradeRequestId'] >= 1)
            {
                //On récupère l'id du formulaire précédent
                $tradeRequestId = htmlspecialchars($_POST['tradeRequestId']);
                
                //On fait une requête pour vérifier si cette demande existe et est bien attribué au joueur
                $tradeRequestQuery = $bdd->prepare("SELECT * FROM car_trades_requests
                WHERE tradeRequestCharacterOneId = ?
                AND tradeRequestId = ?");
                $tradeRequestQuery->execute([$characterId, $tradeRequestId]);
                $tradeRequestRow = $tradeRequestQuery->rowCount();
                
                //Si cette demande existe et est attribuée au joueur
                if ($tradeRequestRow > 0) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($tradeRequest = $tradeRequestQuery->fetch())
                    {
                        //On récupère les valeurs de la demande d'échange
                        $tradeRequestId = $tradeRequest['tradeRequestId'];
                        $tradeRequestCharacterOneId = $tradeRequest['tradeRequestCharacterOneId'];
                        $tradeRequestCharacterTwoId = $tradeRequest['tradeRequestCharacterTwoId'];
                        $tradeRequestMessage = $tradeRequest['tradeRequestMessage'];
                    }
                    
                    //On supprime la demande d'échange
                    $tradeRequestDeleteQuery = $bdd->prepare("DELETE FROM car_trades_requests
                    WHERE tradeRequestId = ?");
                    $tradeRequestDeleteQuery->execute([$tradeRequestId]);
                    $tradeRequestDeleteQuery->closeCursor();
                    
                    $notificationDate = date('Y-m-d H:i:s');
                    $notificationMessage = "$characterName a annulé sa demande d'échange";
                    
                    //On envoie une notification au joueur
                    $addNotification = $bdd->prepare("INSERT INTO car_notifications VALUES(
                    NULL,
                    :tradeRequestCharacterTwoId,
                    :notificationDate,
                    :notificationMessage,
                    'No')");
                    $addNotification->execute(array(
                    'tradeRequestCharacterTwoId' => $tradeRequestCharacterTwoId,  
                    'notificationDate' => $notificationDate,
                    'notificationMessage' => $notificationMessage));
                    $addNotification->closeCursor();
                    ?>
                    
                    Votre demande d'échange a bien été annulée
                    
                    <hr>
                        
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
                }
                //Si la demande d'échange n'existe pas ou n'est pas attribué au joueur
                else
                {
                    echo "Erreur : Cette demande d'échange n'existe pas où ne vous est pas attribuée";
                }
                $tradeRequestQuery->closeCursor();
            }
            //Si tous les champs numérique ne contiennent pas un nombre
            else
            {
                echo "Erreur : Les champs de type numérique ne peuvent contenir qu'un nombre entier";
            }
        }
        
        //Si l'utilisateur à cliqué sur le bouton accepter l'échange
        if (isset($_POST['tradeRequestId'])
        && isset($_POST['acceptTradeRequest']))
        {
            //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
            if (ctype_digit($_POST['tradeRequestId'])
            && $_POST['tradeRequestId'] >= 1)
            {
                //On récupère l'id du formulaire précédent
                $tradeRequestId = htmlspecialchars($_POST['tradeRequestId']);
                
                //On fait une requête pour vérifier si cette demande existe et est bien attribué au joueur
                $tradeRequestQuery = $bdd->prepare("SELECT * FROM car_trades_requests
                WHERE tradeRequestCharacterTwoId = ?
                AND tradeRequestId = ?");
                $tradeRequestQuery->execute([$characterId, $tradeRequestId]);
                $tradeRequestRow = $tradeRequestQuery->rowCount();
                
                //Si cette demande existe et est attribuée au joueur
                if ($tradeRequestRow > 0) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($tradeRequest = $tradeRequestQuery->fetch())
                    {
                        //On récupère les valeurs de la demande d'échange
                        $tradeRequestId = $tradeRequest['tradeRequestId'];
                        $tradeRequestCharacterOneId = $tradeRequest['tradeRequestCharacterOneId'];
                        $tradeRequestCharacterTwoId = $tradeRequest['tradeRequestCharacterTwoId'];
                        $tradeRequestMessage = $tradeRequest['tradeRequestMessage'];
                    }
                    
                    //On crée une variable date
                    $date = date('Y-m-d H:i:s');
                    
                    //On ajoute l'échange dans la base de donnée
                    $addTrade = $bdd->prepare("INSERT INTO car_trades VALUES(
                    NULL,
                    :tradeRequestCharacterOneId,
                    :tradeRequestCharacterTwoId,
                    :tradeRequestMessage,
                    :tradeLastUpdate,
                    'No',
                    'No')");
                    $addTrade->execute([
                    'tradeRequestCharacterOneId' => $tradeRequestCharacterOneId,
                    'tradeRequestCharacterTwoId' => $tradeRequestCharacterTwoId,
                    'tradeRequestMessage' => $tradeRequestMessage,
                    'tradeLastUpdate' => $date]);
                    $addTrade->closeCursor();
                    
                    //On recherche l'Id de l'échange
                    $tradeQuery = $bdd->prepare("SELECT * FROM car_trades
                    WHERE tradeCharacterOneId = ?
                    AND tradeCharacterTwoId = ?");
                    $tradeQuery->execute([$tradeRequestCharacterOneId, $tradeRequestCharacterTwoId]);
    
                    //On fait une boucle pour récupérer toutes les information
                    while ($trade = $tradeQuery->fetch())
                    {
                        //On Stock l'id de l'échange
                        $tradeId = $trade['tradeId'];
                    }
                    $tradeQuery->closeCursor();
                    
                    //On ajoute 0 pièces d'or du joueur 1 sur l'échange
                    $addGoldTradeRequest = $bdd->prepare("INSERT INTO car_trades_golds VALUES(
                    NULL,
                    :tradeId,
                    :tradeCharacterId,
                    '0')");
                    $addGoldTradeRequest->execute([
                    'tradeId' => $tradeId,
                    'tradeCharacterId' => $tradeRequestCharacterOneId]);
                    $addGoldTradeRequest->closeCursor();
                    
                    //On ajoute 0 pièces d'or du joueur 2 sur l'échange
                    $addGoldTradeRequest = $bdd->prepare("INSERT INTO car_trades_golds VALUES(
                    NULL,
                    :tradeId,
                    :tradeCharacterId,
                    '0')");
                    $addGoldTradeRequest->execute([
                    'tradeId' => $tradeId,
                    'tradeCharacterId' => $tradeRequestCharacterTwoId]);
                    $addGoldTradeRequest->closeCursor();
                    
                    //On supprime la demande d'échange
                    $tradeRequestDeleteQuery = $bdd->prepare("DELETE FROM car_trades_requests
                    WHERE tradeRequestId = ?");
                    $tradeRequestDeleteQuery->execute([$tradeRequestId]);
                    $tradeRequestDeleteQuery->closeCursor();
                    
                    $notificationDate = date('Y-m-d H:i:s');
                    $notificationMessage = "$characterName a accepté votre demande d'échange";
                    
                    //On envoie une notification au joueur
                    $addNotification = $bdd->prepare("INSERT INTO car_notifications VALUES(
                    NULL,
                    :tradeRequestCharacterOneId,
                    :notificationDate,
                    :notificationMessage,
                    'No')");
                    $addNotification->execute(array(
                    'tradeRequestCharacterOneId' => $tradeRequestCharacterOneId,  
                    'notificationDate' => $notificationDate,
                    'notificationMessage' => $notificationMessage));
                    $addNotification->closeCursor();
                    ?>
                    
                    La demande d'échange a bien été acceptée
                    
                    <hr>
                    
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
                }
                //Si la demande d'échange n'existe pas ou n'est pas attribué au joueur
                else
                {
                    echo "Erreur : Cette demande d'échange n'existe pas où ne vous est pas attribuée";
                }
            }
            //Si tous les champs numérique ne contiennent pas un nombre
            else
            {
                echo "Erreur : Les champs de type numérique ne peuvent contenir qu'un nombre entier";
            }
            $tradeRequestQuery->closeCursor();
        }
        
        //Si l'utilisateur à cliqué sur le bouton refuser l'échange
        if (isset($_POST['tradeRequestId'])
        && isset($_POST['declineTradeRequest']))
        {
            //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
            if (ctype_digit($_POST['tradeRequestId'])
            && $_POST['tradeRequestId'] >= 1)
            {
                //On récupère l'id du formulaire précédent
                $tradeRequestId = htmlspecialchars($_POST['tradeRequestId']);
                
                //On fait une requête pour vérifier si cette demande existe et est bien attribué au joueur
                $tradeRequestQuery = $bdd->prepare("SELECT * FROM car_trades_requests
                WHERE tradeRequestCharacterTwoId = ?
                AND tradeRequestId = ?");
                $tradeRequestQuery->execute([$characterId, $tradeRequestId]);
                $tradeRequestRow = $tradeRequestQuery->rowCount();
                
                //Si cette demande existe et est attribuée au joueur
                if ($tradeRequestRow > 0) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($tradeRequest = $tradeRequestQuery->fetch())
                    {
                        //On récupère les valeurs de la demande d'échange
                        $tradeRequestId = $tradeRequest['tradeRequestId'];
                        $tradeRequestCharacterOneId = $tradeRequest['tradeRequestCharacterOneId'];
                        $tradeRequestCharacterTwoId = $tradeRequest['tradeRequestCharacterTwoId'];
                        $tradeRequestMessage = $tradeRequest['tradeRequestMessage'];
                    }
                    
                    //On supprime la demande d'échange
                    $tradeRequestDeleteQuery = $bdd->prepare("DELETE FROM car_trades_requests
                    WHERE tradeRequestId = ?");
                    $tradeRequestDeleteQuery->execute([$tradeRequestId]);
                    $tradeRequestDeleteQuery->closeCursor();
                    
                    $notificationDate = date('Y-m-d H:i:s');
                    $notificationMessage = "$characterName a refusé votre demande d'échange";
                    
                    //On envoie une notification au joueur
                    $addNotification = $bdd->prepare("INSERT INTO car_notifications VALUES(
                    NULL,
                    :tradeRequestCharacterOneId,
                    :notificationDate,
                    :notificationMessage,
                    'No')");
                    $addNotification->execute(array(
                    'tradeRequestCharacterOneId' => $tradeRequestCharacterOneId,  
                    'notificationDate' => $notificationDate,
                    'notificationMessage' => $notificationMessage));
                    $addNotification->closeCursor();
                    ?>
                    
                    La demande d'échange a bien été refusée
                    
                    <hr>
                        
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
                }
                //Si la demande d'échange n'existe pas ou n'est pas attribué au joueur
                else
                {
                    echo "Erreur : Cette demande d'échange n'existe pas où ne vous est pas attribuée";
                }
                $tradeRequestQuery->closeCursor();
            }
            //Si tous les champs numérique ne contiennent pas un nombre
            else
            {
                echo "Erreur : Les champs de type numérique ne peuvent contenir qu'un nombre entier";
            }
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
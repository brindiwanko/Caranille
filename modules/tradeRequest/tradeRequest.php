<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
		//Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On fait une recherche dans la base de donnée de tous les personnage avec qui on a pas de demande d'échange ou d'échange en cours
        $characterTradeQuery = $bdd->prepare("SELECT * FROM car_characters
        WHERE characterId != ?
        
        AND (SELECT COUNT(*) FROM car_trades_requests
        WHERE tradeRequestCharacterOneId = ?
        AND tradeRequestCharacterTwoId = characterId
        OR tradeRequestCharacterOneId = characterId
        AND tradeRequestCharacterTwoId = ?) = 0
        
        AND (SELECT COUNT(*) FROM car_trades
        WHERE tradeCharacterOneId = ?
        AND tradeCharacterTwoId = characterId
        OR tradeCharacterOneId = characterId
        AND tradeCharacterTwoId = ?) = 0
        ORDER by characterName");
        $characterTradeQuery->execute([$characterId, $characterId, $characterId, $characterId, $characterId]);
        $characterTradeRow = $characterTradeQuery->rowCount();
        
        //S'il y a au moins un autre joueur on affiche le menu
        if ($characterTradeRow > 0)
        {
            ?>
            
            <form method="POST" action="tradeRequestEnd.php">
                Liste des joueurs <select name="tradeCharacterId" class="form-control">
                    
                    <?php
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($characterTrade = $characterTradeQuery->fetch())
                    {
                        //On récupère les informations du personnage
                        $tradeCharacterId = $characterTrade['characterId'];
                        $tradeCharacterName = $characterTrade['characterName'];
                        
                        ?>
                        <option value="<?php echo $tradeCharacterId ?>"><?php echo $tradeCharacterName ?></option>
                        <?php
                    }
                    ?>
                
                </select>
                Message : <input type="text" name="tradeMessage" class="form-control" required>
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" name="addTrade" class="btn btn-secondary btn-lg" value="Envoyer la demande">
            </form>
            
            <?php
        }
        //Si il y a aucun autre joueur de disponible
        else
        {
            echo "Il n'y a actuellement aucun joueur de disponible pour faire un échange";
        }
        $characterTradeQuery->closeCursor();
        ?>
        
        <hr>
        
        <form method="POST" action="index.php">
            <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
        </form>
        
        <?php
        
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
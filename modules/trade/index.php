<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On fait une requête pour vérifier toutes les demandes d'échange en cours
$tradeQuery = $bdd->prepare("SELECT * FROM car_trades
WHERE (tradeCharacterOneId = ?
OR tradeCharacterTwoId = ?)");
$tradeQuery->execute([$characterId, $characterId]);
$tradeRow = $tradeQuery->rowCount();

//S'il existe un ou plusieurs demande d'échange en cours
if ($tradeRow > 0) 
{
    ?>
    
    <form method="POST" action="manageTrade.php">
        Echange en cours : <select name="tradeId" class="form-control">

            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($trade = $tradeQuery->fetch())
            {
                //On récupère les valeurs de la demande d'échange
                $tradeId = $trade['tradeId'];
                $tradeCharacterOneId = $trade['tradeCharacterOneId'];
                $tradeCharacterTwoId = $trade['tradeCharacterTwoId'];
                $tradeMessage = $trade['tradeMessage'];
                
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
                        $tradeCharacterName = $character['characterName'];
                    }
                    $characterQuery->closeCursor();  
                }
                ?>
                <option value="<?php echo $tradeId ?>"><?php echo "Echange entre $characterName et $tradeCharacterName" ?></option>
                <?php
            }
            ?>

        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="manageTrade" class="btn btn-secondary btn-lg" value="Gérer l'échange">
    </form>

    <?php
}
//S'il n'y a aucune offre de disponible on prévient le joueur
else
{
    echo "Il n'y a aucune demande d'échange en attente.";
}
$tradeQuery->closeCursor();

require_once("../../html/footer.php"); ?>
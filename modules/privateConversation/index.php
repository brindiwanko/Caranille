<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On fait une requête pour vérifier la liste des conversations dans la base de donnée
$privateConversationQuery = $bdd->prepare("SELECT * FROM car_private_conversation
WHERE (privateConversationCharacterOneId = ?
OR privateConversationCharacterTwoId = ?)");
$privateConversationQuery->execute([$characterId, $characterId]);
$privateConversationRow = $privateConversationQuery->rowCount();

//S'il existe une ou plusieurs conversation dans la messagerie privée
if ($privateConversationRow > 0) 
{
    ?>

    <form method="POST" action="showConversation.php">
        Liste de vos conversations : <select name="privateConversationId" class="form-control">
            
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($privateConversation = $privateConversationQuery->fetch())
            {
                //On récupère les informations de la conversation
                $privateConversationId = $privateConversation['privateConversationId'];
                $privateConversationCharacterOneId = $privateConversation['privateConversationCharacterOneId'];
                $privateConversationCharacterTwoId = $privateConversation['privateConversationCharacterTwoId'];
                
                //Si la première personne de la conversation est le joueur on cherche à savoir qui est l'autre personne
                if ($privateConversationCharacterOneId == $characterId)
                {
                    //On fait une requête pour récupérer le nom du personnage dans la base de donnée
                    $characterQuery = $bdd->prepare("SELECT * FROM car_characters
                    WHERE characterId = ?");
                    $characterQuery->execute([$privateConversationCharacterTwoId]);
                    
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($character = $characterQuery->fetch())
                    {
                        //On récupère les informations du personnage
                        $privateConversationCharacterName = $character['characterName'];
                    }
                    $characterQuery->closeCursor();
                }
                //Si la seconde personne de la conversation est le joueur on cherche à savoir qui est l'autre personne
                else
                {
                    //On fait une requête pour récupérer le nom du personnage dans la base de donnée
                    $characterQuery = $bdd->prepare("SELECT * FROM car_characters
                    WHERE characterId = ?");
                    $characterQuery->execute([$privateConversationCharacterOneId]);
                    
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($character = $characterQuery->fetch())
                    {
                        //On récupère les informations du personnage
                        $privateConversationCharacterName = $character['characterName'];
                    }
                    $characterQuery->closeCursor();
                }
                
                //On fait une requête pour vérifier le nombre de message non lu
                $privateConversationNumberQuery = $bdd->prepare("SELECT * FROM car_private_conversation_message
                WHERE privateConversationMessagePrivateConversationId = ?
                AND privateConversationMessageCharacterId != ?
                AND privateConversationMessageRead = 'No'");
                $privateConversationNumberQuery->execute([$privateConversationId, $characterId]);
                $privateConversationNumberRow = $privateConversationNumberQuery->rowCount();
                ?>
                <option value="<?php echo $privateConversationId ?>"><?php echo "$privateConversationCharacterName ($privateConversationNumberRow nouveau(x) message)" ?></option>
                <?php
            }
            ?>
            
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="showConversation" class="btn btn-secondary btn-lg" value="Afficher la conversation">
    </form>
    
    <hr>

    <?php
}
$privateConversationQuery->closeCursor();

//On fait une recherche dans la base de donnée de tous les personnage avec qui on a pas de conversation
$characterQuery = $bdd->prepare("SELECT * FROM car_characters
WHERE characterId != ?
AND (SELECT COUNT(*) FROM car_private_conversation
WHERE privateConversationCharacterOneId = ?
AND privateConversationCharacterTwoId = characterId
OR privateConversationCharacterOneId = characterId
AND privateConversationCharacterTwoId = ?) = 0
ORDER by characterName");
$characterQuery->execute([$characterId, $characterId, $characterId]);
$characterRow = $characterQuery->rowCount();

//S'il y a au moins un autre joueur on affiche le menu
if ($characterRow > 0)
{
    ?>
    
    <form method="POST" action="launchConversation.php">
        Liste des joueurs <select name="privateConversationCharacterId" class="form-control">
            
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($character = $characterQuery->fetch())
            {
                //On récupère les informations du personnage
                $privateConversationCharacterId = $character['characterId'];
                $privateConversationCharacterName = $character['characterName'];
                ?>
                <option value="<?php echo $privateConversationCharacterId ?>"><?php echo "$privateConversationCharacterName"; ?></option>
                <?php
            }
            ?>
        
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="launchConversation" class="btn btn-secondary btn-lg" value="Démarrer une conversation">
    </form>
    
    <?php
}
//Si il y a aucun autre joueur
else
{
    echo "Il n'y a actuellement aucun autre joueur";
}
$characterQuery->closeCursor();
?>

<hr>

<form method="POST" action="../../index.php">
    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
</form>

<?php require_once("../../html/footer.php"); ?>
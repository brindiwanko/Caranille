<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On fait une recherche dans la base de donnée des 20 derniers message du chat
$chatQuery = $bdd->query("SELECT * FROM car_chat, car_characters 
WHERE chatCharacterId = characterId
LIMIT 0, 20");
$chatRow = $chatQuery->rowCount();

//Si il y a des messages dans le chat on les affiches
if ($chatRow > 0)
{
    ?>
    
    <p>Affichage des 20 derniers messages</p>
    
    <table class="table">
        
        <tr>
            <td>
                Date/Heure
            </td>
            
            <td>
                Pseudo
            </td>
        
            <td>
                Message
            </td>
            
            <?php
            //Si le joueur est modérateur ou administrateur on lui donne la possibilité de vider entièrement le chat
            if ($accountAccess >= 1)
            {
                ?>
                
                <td>
                    Action
                </td>
                
                <?php
            }
            ?>
            
        </tr>
        
        <?php
        //On fait une boucle pour récupérer toutes les informations
        while ($chat = $chatQuery->fetch())
        {
            //On récupère les informations du chat
            $chatMessageId = $chat['chatMessageId'];
            $chatCharacterName = $chat['characterName'];
            $chatDateTime = $chat['chatDateTime'];
            $chatMessage = $chat['chatMessage'];
            ?>
            
            <tr>
                <td>
                    <?php echo (new DateTime($chatDateTime))->format('d-m-Y H:i') ?> 
                </td>
                
                <td>
                    <?php echo htmlspecialchars($chatCharacterName) ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($chatMessage) ?>
                </td>
                
                <?php
                //Si le joueur est modérateur ou administrateur on lui donne la possibilité de supprimer le message
                if ($accountAccess >= 1)
                {
                    ?>
                    
                    <td>
                        <form method="POST" action="deleteMessage.php">
                            <input type="hidden" name="chatMessageId" value="<?php echo $chatMessageId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit"name="deleteMessage"  class="btn btn-secondary btn-lg" value="X">
                        </form>
                    </td>
                    
                    <?php
                }
                ?>
                
            </tr>
            
        <?php
        }
        ?>
        
    </table>
    
    <?php
}
$chatQuery->closeCursor();
?>
     
<form method="POST" action="sendMessage.php">
    <input type="text" class="form-control" placeholder="message" name="chatMessage" required>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="sendMessage" value="Envoyer le message">
</form>

<hr>

<form method="POST" action="index.php">
    <input type="submit" class="btn btn-secondary btn-lg" name="refreshChat" value="Actualiser le chat">
</form>
<form method="POST" action="showAllMessages.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="showAllMessages" value="Afficher tous les messages">
</form>
 
<?php
//Si le joueur est modérateur ou administrateur on lui donne la possibilité de vider entièrement le chat
if ($accountAccess >= 1)
{
    ?>
    
    <form method="POST" action="clearChat.php">
    	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" class="btn btn-secondary btn-lg" name="clearChat" value="Vider le chat">
    </form>
    
    <?php
}

require_once("../../html/footer.php"); ?>
<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['privateConversationId'])
&& isset($_POST['token'])
&& isset($_POST['showAllConversation']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
         
        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['privateConversationId'])
        && $_POST['privateConversationId'] >= 0)
        {
            //On récupère l'id du formulaire précédent
            $privateConversationId = htmlspecialchars($_POST['privateConversationId']);
            
            //On vérifie si le joueur est bien dans cette conversation
            $privateConversationQuery = $bdd->prepare("SELECT * FROM car_private_conversation
            WHERE (privateConversationCharacterOneId = ?
            OR privateConversationCharacterTwoId = ?)
            AND privateConversationId = ?");
            $privateConversationQuery->execute([$characterId, $characterId, $privateConversationId]);
            $privateConversationRow = $privateConversationQuery->rowCount();

            //Si la conversation existe
            if ($privateConversationRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($privateConversation = $privateConversationQuery->fetch())
                {
                    //On récupère les informations de la conversation
                    $privateConversationId = $privateConversation['privateConversationId'];
                    $privateConversationCharacterOneId = $privateConversation['privateConversationCharacterOneId'];
                    $privateConversationCharacterTwoId = $privateConversation['privateConversationCharacterTwoId'];
                }
                
                //Si la première personne de la conversation est le joueur on cherche à savoir qui est l'autre personnage
                if ($privateConversationCharacterOneId == $characterId)
                {
                    //On fait une requête pour vérifier la liste des conversations dans la base de données
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
                    //On fait une requête pour vérifier la liste des conversations dans la base de données
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
                ?>
                
                <p>Conversation avec <?php echo $privateConversationCharacterName ?> (Tous les messages)</p>

                <?php
                //On fait une recherche dans la base de donnée des 20 derniers message de la conversation
                $privateConversationMessageQuery = $bdd->prepare("SELECT * FROM car_private_conversation_message
                WHERE privateConversationMessagePrivateConversationId = ?");
                $privateConversationMessageQuery->execute([$privateConversationId]);
                $privateConversationMessageRow = $privateConversationMessageQuery->rowCount();
                
                //Si il y a déjà eu au moins un message
                if ($privateConversationMessageRow > 0)
                {
                    ?>
                                        
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
                        </tr>
                        
                        <?php
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($privateConversationMessage = $privateConversationMessageQuery->fetch())
                        {
                            //On récupère les informations du message de la discution
                            $privateConversationMessageCharacterId = $privateConversationMessage['privateConversationMessageCharacterId'];
                            $privateConversationMessageDateTime = $privateConversationMessage['privateConversationMessageDateTime'];
                            $privateConversationMessage = $privateConversationMessage['privateConversationMessage'];
                            
                            //On vérifie si l'auteur du message est l'autre joueur
                            if ($privateConversationMessageCharacterId != $characterId)
                            {
                                //Si le message provient de l'autre joueur on vérifie si il est non lu
                                if ($privateConversationMessageRead == "No")
                                {
                                    //On peut enfin le mettre lu car on vient de le lire
                                    $updatePrivateConversationMessage = $bdd->prepare("UPDATE car_private_conversation_message
                                    SET privateConversationMessageRead = 'Yes'
                                    WHERE privateConversationMessageId = :privateConversationMessageId");
                                    $updatePrivateConversationMessage->execute([
                                    'privateConversationMessageId' => $privateConversationMessageId]);
                                    $updatePrivateConversationMessage->closeCursor();
                                }
                            }
                            
                            //Si l'id de la personne qui a posté le message et celui du personnage sinon il s'agira de l'autre personnage
                            if ($privateConversationMessageCharacterId == $characterId)
                            {
                                $privateConversationCharacterName = $characterName;
                            }
                            else
                            {
                                //On fait une requête pour vérifier la liste des conversations dans la base de données
                                $characterQuery = $bdd->prepare("SELECT * FROM car_characters
                                WHERE characterId = ?");
                                $characterQuery->execute([$privateConversationMessageCharacterId]);
                                
                                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                while ($character = $characterQuery->fetch())
                                {
                                    //On récupère les informations du personnage
                                    $privateConversationCharacterName = $character['characterName'];
                                }
                                $characterQuery->closeCursor(); 
                            }
                            ?>
                            
                            <tr>
                                
                                <td>
                                    <?php echo (new DateTime($privateConversationMessageDateTime))->format('d-m-Y H:i') ?> 
                                </td>
                                
                                <td>
                                    <?php echo $privateConversationCharacterName ?>
                                </td>
                                
                                <td>
                                    <?php echo $privateConversationMessage ?>
                                </td>
                                
                            </tr>
                            
                        <?php
                        }
                        ?>
                    
                    </table>
                    
                    <?php
                }
                $privateConversationMessageQuery->closeCursor();
                ?>
                
                <form method="POST" action="showConversation.php">
                    <input type="hidden" name="privateConversationId" value="<?php echo $privateConversationId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="showConversation" value="Retour à la conversation">
                </form>

                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                <?php
            }
            //Si la conversation n'exite pas ou que le joueur n'y a pas accès
            else
            {
                echo "Erreur : Cette conversation n'existe pas ou vous n'en faite pas parti";
            }
            $privateConversationQuery->closeCursor();  
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

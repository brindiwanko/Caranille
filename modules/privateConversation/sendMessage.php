<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['privateConversationMessage'])
&& isset($_POST['privateConversationId'])
&& isset($_POST['token'])
&& isset($_POST['sendMessage']))
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
            //On récupère les informations du formulaire
            $privateConversationId = htmlspecialchars($_POST['privateConversationId']);
            $privateConversationMessage = htmlspecialchars($_POST['privateConversationMessage']);
            
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
                //On définit une date pour le message
                $date = date('Y-m-d H:i:s');
                
                //On ajoute le message dans la base de donnée
                $addMessage = $bdd->prepare("INSERT INTO car_private_conversation_message VALUES(
                NULL,
                :privateConversationId,
                :characterId,
                :date,
                :privateConversationMessage,
                'No')");
                $addMessage->execute([
                'privateConversationId' => $privateConversationId,
                'characterId' => $characterId,
                'date' => $date,
                'privateConversationMessage' => $privateConversationMessage,]);
                $addMessage->closeCursor();
                ?>
                
                Le message a bien été envoyé
                
                <hr>
                
                <form method="POST" action="showConversation.php">
                    <input type="hidden" name="privateConversationId" value="<?php echo $privateConversationId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="showConversation" value="Retour">
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
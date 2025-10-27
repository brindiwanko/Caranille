<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['privateConversationCharacterId'])
&& isset($_POST['token'])
&& isset($_POST['launchConversation']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['privateConversationCharacterId'])
        && $_POST['privateConversationCharacterId'] >= 0)
        {
            //On récupère l'id du formulaire précédent
            $privateConversationCharacterId = htmlspecialchars($_POST['privateConversationCharacterId']);
            
            //On fait une requête pour vérifier si le personnage choisit existe
            $characterQuery = $bdd->prepare("SELECT * FROM car_characters 
            WHERE characterId = ?");
            $characterQuery->execute([$privateConversationCharacterId]);
            $characterRow = $characterQuery->rowCount();

            //Si le compte existe
            if ($characterRow == 1) 
            {
                //On vérifie si il n'y a pas déjà une conversation avec ce joueur
                $privateConversationQuery = $bdd->prepare("SELECT * FROM car_private_conversation
                WHERE (privateConversationCharacterOneId = ?
                AND privateConversationCharacterTwoId = ?
                OR privateConversationCharacterOneId = ?
                AND privateConversationCharacterTwoId = ?)");
                $privateConversationQuery->execute([$characterId, $privateConversationCharacterId, $privateConversationCharacterId, $characterId]);
                $privateConversationRow = $privateConversationQuery->rowCount();
                
                //Si aucune conversation n'existe
                if ($privateConversationRow == 0)
                {
                    //On crée la conversation
                    $addPrivateConversation = $bdd->prepare("INSERT INTO car_private_conversation VALUES(
                    NULL,
                    :characterId,
                    :conversationCharacterTwoId)");
                    $addPrivateConversation->execute([
                    'characterId' => $characterId,
                    'conversationCharacterTwoId' => $privateConversationCharacterId]);
                    $addPrivateConversation->closeCursor();
                    ?>
                    
                    La conversation a bien été crée

                    <hr>
                    
                    <form method="POST" action="index.php">
                        <input type="submit" name="edit" class="btn btn-secondary btn-lg" value="Retour">
                    </form>
                    
                    <?php
                }
                //Si une conversation exixte déjà avec ce joueur
                else
                {
                    ?>
                    
                    Une conversation existe déjà avec ce joueur
                    
                    <hr>
                    
                    <form method="POST" action="index.php">
                        <input type="submit" name="back" class="btn btn-secondary btn-lg" value="Retour">
                    </form>
                    
                    <?php
                    
                }
                $privateConversationQuery->closeCursor();
            }
            //Si le personnage n'existe pas
            else
            {
                echo "Erreur : Ce personnage n'existe pas";
            }
            $characterQuery->closeCursor(); 
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

<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminBattleInvitationId'])
&& isset($_POST['token'])
&& isset($_POST['finalDelete']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminBattleInvitationId'])
        && $_POST['adminBattleInvitationId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminBattleInvitationId = htmlspecialchars($_POST['adminBattleInvitationId']);

            //On fait une requête pour vérifier si l'invitation de combat choisit existe
            $battleInvitationQuery = $bdd->prepare("SELECT * FROM car_battles_invitations 
            WHERE battleInvitationId = ?");
            $battleInvitationQuery->execute([$adminBattleInvitationId]);
            $battleInvitationRow = $battleInvitationQuery->rowCount();

            //Si l'invitation de combat existe
            if ($battleInvitationRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($battleInvitation = $battleInvitationQuery->fetch())
                {
                    //On récupère les informations de l'invitation
                    $adminBattleInvitationId = $battleInvitation['battleInvitationId'];
                    $adminBattleInvitationMonsterId = $battleInvitation['battleInvitationMonsterId'];
                }
                
                //On fait une requête pour vérifier si le monstre choisit existe
                $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters
                WHERE monsterId = ?");
                $monsterQuery->execute([$adminBattleInvitationMonsterId]);
                $monsterRow = $monsterQuery->rowCount();
        
                //Si le monstre existe
                if ($monsterRow == 1) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($monster = $monsterQuery->fetch())
                    {
                        $adminBattleInvitationMonsterName = $monster['monsterName'];
                    }
                }
                
                //On fait une requête pour vérifier si l'objet choisit existe
                $battleInvitationCharacterQuery = $bdd->prepare("SELECT * FROM car_battles_invitations_characters, car_characters
                WHERE battleInvitationCharacterCharacterId = characterId
                AND battleInvitationCharacterBattleInvitationId = ?");
                $battleInvitationCharacterQuery->execute([$adminBattleInvitationId]);
                
                //On fait une recherche dans la base de donnée de toutes les lieux
                while ($battleInvitationCharacter = $battleInvitationCharacterQuery->fetch())
                {
                    $adminCharacterId = $battleInvitationCharacter['characterId'];
                    
                    $notificationDate = date('Y-m-d H:i:s');
                    $notificationMessage = "ATTENTION : L'invitation de combat contre $adminBattleInvitationMonsterName a été annulée.";
                    
                    //On envoie une notification au joueur
                    $addNotification = $bdd->prepare("INSERT INTO car_notifications VALUES(
                    NULL,
                    :adminCharacterId,
                    :notificationDate,
                    :notificationMessage,
                    'No')");
                    $addNotification->execute(array(
                    'adminCharacterId' => $adminCharacterId,  
                    'notificationDate' => $notificationDate,
                    'notificationMessage' => $notificationMessage));
                    $addNotification->closeCursor();
                }
                
                //On supprime l'invitation de combat de la base de donnée
                $battleInvitationDeleteQuery = $bdd->prepare("DELETE FROM car_battles_invitations
                WHERE battleInvitationId = ?");
                $battleInvitationDeleteQuery->execute([$adminBattleInvitationId]);
                $battleInvitationDeleteQuery->closeCursor();
                
                //On supprime les invitation envoyées aux joueurs de la base de donnée
                $battleInvitationCharacterDeleteQuery = $bdd->prepare("DELETE FROM car_battles_invitations_characters
                WHERE battleInvitationCharacterBattleInvitationId = ?");
                $battleInvitationCharacterDeleteQuery->execute([$adminBattleInvitationId]);
                $battleInvitationCharacterDeleteQuery->closeCursor();
                ?>

                L'invitation de combat a bien été supprimée

                <hr>
                    
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si l'invitation de combat n'existe pas
            else
            {
                echo "Erreur : Cette invitation de combat n'existe pas";
            }
            $battleInvitationQuery->closeCursor();
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
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
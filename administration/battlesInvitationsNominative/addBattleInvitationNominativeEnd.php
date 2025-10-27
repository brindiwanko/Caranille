<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminBattleInvitationCharacterId'])
&& isset($_POST['adminBattleInvitationMonsterId'])
&& isset($_POST['adminBattleInvitationPicture'])
&& isset($_POST['adminBattleInvitationeName'])
&& isset($_POST['adminBattleInvitationDescription'])
&& isset($_POST['finalAdd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminBattleInvitationCharacterId'])
        && ctype_digit($_POST['adminBattleInvitationMonsterId'])
        && $_POST['adminBattleInvitationCharacterId'] >= 0
        && $_POST['adminBattleInvitationMonsterId'] >= 0)
        {
            //On récupère les informations du formulaire
            $adminBattleInvitationCharacterId = htmlspecialchars($_POST['adminBattleInvitationCharacterId']);
            $adminBattleInvitationMonsterId = htmlspecialchars($_POST['adminBattleInvitationMonsterId']);
            $adminBattleInvitationPicture = htmlspecialchars($_POST['adminBattleInvitationPicture']);
            $adminBattleInvitationeName = htmlspecialchars($_POST['adminBattleInvitationeName']);
            $adminBattleInvitationDescription = htmlspecialchars($_POST['adminBattleInvitationDescription']);
            $date = date('Y-m-d H:i:s');
            
            //On ajoute l'invitation de combat dans la base de donnée
            $addInvitationBattle = $bdd->prepare("INSERT INTO car_battles_invitations VALUES(
            NULL,
            :adminBattleInvitationMonsterId,
            :adminBattleInvitationPicture,
            :adminBattleInvitationeName,
            :adminBattleInvitationDescription,
            :date,
            :date)");
            $addInvitationBattle->execute([
            'adminBattleInvitationMonsterId' => $adminBattleInvitationMonsterId,
            'adminBattleInvitationPicture' => $adminBattleInvitationPicture,
            'adminBattleInvitationeName' => $adminBattleInvitationeName,
            'adminBattleInvitationDescription' => $adminBattleInvitationDescription,
            'date' => $date,
            'date' => $date]);
            $addInvitationBattle->closeCursor();
            
            //On fait une requête pour récupérer l'Id de l'invitation faite à l'instant
            $battleInvitationQuery = $bdd->query("SELECT * FROM car_battles_invitations 
            ORDER BY battleInvitationId DESC 
            LIMIT 1");
            
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($battleInvitation = $battleInvitationQuery->fetch())
            {
                //On récupère les informations de l'invitation
                $adminBattleInvitationId = $battleInvitation['battleInvitationId'];
            }
            
            //On fait une requête pour vérifier si le monstre choisit existe
            $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters, car_monsters_categories
            WHERE monsterCategory = monsterCategoryId
            AND monsterId = ?");
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
            
            //On ajoute l'invitation de combat dans la base de donnée
            $addInvitationBattleCharacter = $bdd->prepare("INSERT INTO car_battles_invitations_characters VALUES(
            NULL,
            :adminBattleInvitationId,
            :adminBattleInvitationCharacterId)");
            $addInvitationBattleCharacter->execute([
            'adminBattleInvitationId' => $adminBattleInvitationId,
            'adminBattleInvitationCharacterId' => $adminBattleInvitationCharacterId]);
            $addInvitationBattleCharacter->closeCursor();
            
            $notificationDate = date('Y-m-d H:i:s');
            $notificationMessage = "Félicitation : Vous avez reçu une invitation de combat contre le monstre $adminBattleInvitationMonsterName.<br />Rendez-vous dans le menu Personnage -> Invitation de combat pour y prendre part.";
            
            //On envoie une notification au joueur
            $addNotification = $bdd->prepare("INSERT INTO car_notifications VALUES(
            NULL,
            :adminBattleInvitationCharacterId,
            :notificationDate,
            :notificationMessage,
            'No')");
            $addNotification->execute(array(
            'adminBattleInvitationCharacterId' => $adminBattleInvitationCharacterId,  
            'notificationDate' => $notificationDate,
            'notificationMessage' => $notificationMessage));
            $addNotification->closeCursor();

            echo "L'invitation de combat a bien été envoyée";
            ?>
            
            <hr>
                
            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
            </form>
                
            <?php
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

require_once("../html/footer.php");
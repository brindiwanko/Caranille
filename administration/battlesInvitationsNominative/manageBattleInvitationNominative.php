<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['battleInvitationId'])
&& isset($_POST['token'])
&& isset($_POST['manage']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['battleInvitationId'])
        && $_POST['battleInvitationId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminBattleInvitationId = htmlspecialchars($_POST['battleInvitationId']);

            //On fait une requête pour vérifier si l'objet choisit existe
            $battleInvitationQuery = $bdd->prepare("SELECT * FROM car_battles_invitations
            WHERE battleInvitationId = ?");
            $battleInvitationQuery->execute([$adminBattleInvitationId]);
            $battleInvitationRow = $battleInvitationQuery->rowCount();

            //Si l'invitation de combat existe
            if ($battleInvitationRow == 1) 
            {
                //On fait une recherche dans la base de donnée de toutes les lieux
                while ($battleInvitation = $battleInvitationQuery->fetch())
                {
                    $adminBattleInvitationName = $battleInvitation['battleInvitationName'];
                    $adminBattleInvitationDescription = $battleInvitation['battleInvitationDescription'];
                }
                
                ?>
                
                <p><em><?php echo $adminBattleInvitationName ?></em></p>
                
                <p><em><?php echo $adminBattleInvitationDescription ?></em></p>
                
                <p>Joueur(s) invité : </p>
                
                <?php
                
                //On fait une requête pour vérifier si l'objet choisit existe
                $battleInvitationCharacterQuery = $bdd->prepare("SELECT * FROM car_battles_invitations_characters, car_characters
                WHERE battleInvitationCharacterCharacterId = characterId
                AND battleInvitationCharacterBattleInvitationId = ?");
                $battleInvitationCharacterQuery->execute([$adminBattleInvitationId]);
                
                //On fait une recherche dans la base de donnée de toutes les lieux
                while ($battleInvitationCharacter = $battleInvitationCharacterQuery->fetch())
                {
                    $battleInvitationCharacterId = $battleInvitationCharacter['characterId'];
                    $battleInvitationCharacterName = $battleInvitationCharacter['characterName'];
                    
                    echo "$battleInvitationCharacterName est invité <br />";
                }
                ?>
                
                <p>Que souhaitez-vous faire ?</p>
                
                <hr>
                    
                <form method="POST" action="deleteBattleInvitationNominative.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="adminBattleInvitationId" value="<?php echo $adminBattleInvitationId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="delete" value="Supprimer l'invitation">
                </form>
                
                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si l'invitation de combat n'exite pas
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
<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['battleInvitationCharacterId'])
&& isset($_POST['open']))
{
    //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
    if (ctype_digit($_POST['battleInvitationCharacterId'])
    && $_POST['battleInvitationCharacterId'] >= 1)
    {
        //On récupère l'id du formulaire précédent
        $battleInvitationCharacterId = htmlspecialchars($_POST['battleInvitationCharacterId']);

        //On fait une requête pour vérifier si l'invitation de combat choisit existe
        $battleInvitationQuery = $bdd->prepare("SELECT * FROM car_battles_invitations, car_battles_invitations_characters, car_monsters
		WHERE battleInvitationId = battleInvitationCharacterBattleInvitationId
		AND battleInvitationMonsterId = monsterId
		AND battleInvitationCharacterId = ?
		AND battleInvitationCharacterCharacterId = ?");
        $battleInvitationQuery->execute([$battleInvitationCharacterId, $characterId]);
        $battleInvitationRow = $battleInvitationQuery->rowCount();

        //Si l'invitation de combat existe
        if ($battleInvitationRow == 1) 
        {
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($battleInvitation = $battleInvitationQuery->fetch())
            {
                //On récupère les informations de l'invitation de combat
                $battleInvitationName = $battleInvitation['battleInvitationName'];
                $battleInvitationDescription = nl2br(htmlspecialchars($battleInvitation['battleInvitationDescription']));
                $battleInvitationMonsterName = $battleInvitation['monsterName'];
                $battleInvitationMonsterLevel = $battleInvitation['monsterLevel'];
            }
            ?>

            <p>Nom : <?php echo $battleInvitationName ?></p>
            <p>Description : <?php echo $battleInvitationDescription ?></p>
            <p>Niveau recommandé : <?php echo $battleInvitationMonsterLevel ?></p>

            <hr>
            
            ATTENTION : Vous êtes sur le point de lancer un combat contre <?php echo $battleInvitationMonsterName ?><br /><br />
            
            Si vous perdez ou fuyez le combat vous ne pourrez pas le recommencer à moins de recevoir une nouvelle invitation<br /><br />
            
            Que souhaitez-vous faire ?
            
            <hr>
                
            <form method="POST" action="launchInvitationEnd.php">
                <input type="hidden" class="btn btn-secondary btn-lg" name="battleInvitationCharacterId" value="<?php echo $battleInvitationCharacterId ?>">
                <input type="submit" class="btn btn-secondary btn-lg" name="launch" value="Lancer le combat">
            </form>
            
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
//Si toutes les variables $_POST n'existent pas
else
{
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../../html/footer.php");
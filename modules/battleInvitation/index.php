<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On fait une recherche dans la base de donnée de toutes les invitations de combat
$battleInvitationQuery = $bdd->prepare("SELECT * FROM car_battles_invitations, car_battles_invitations_characters, car_monsters
WHERE battleInvitationId = battleInvitationCharacterBattleInvitationId
AND battleInvitationMonsterId = monsterId
AND battleInvitationCharacterCharacterId = ?");
$battleInvitationQuery->execute([$characterId]);
$battleInvitationRow = $battleInvitationQuery->rowCount();

//S'il existe une ou plusieurs invitation de combat on affiche le menu déroulant
if ($battleInvitationRow > 0) 
{
    ?>
    
    <form method="POST" action="launchInvitation.php">
        Liste de vos invitations : <select class="form-control" name="battleInvitationCharacterId" >

            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($battleInvitation = $battleInvitationQuery->fetch())
            {
                //On récupère les informations de l'invitation de combat
                $battleInvitationId = $battleInvitation['battleInvitationId'];
                $battleInvitationName = $battleInvitation['battleInvitationName'];
                $battleInvitationMonsterName = $battleInvitation['monsterName'];
                $battleInvitationMonsterLevel = $battleInvitation['monsterLevel'];
                $battleInvitationCharacterId = $battleInvitation['battleInvitationCharacterId'];
                ?>
                <option value="<?php echo $battleInvitationCharacterId ?>"><?php echo "$battleInvitationName (Monstre : $battleInvitationMonsterName - Niveau : $battleInvitationMonsterLevel)"; ?></option>
                <?php
            }
            ?>
        
        </select>
        <input type="submit" class="btn btn-secondary btn-lg" name="open" value="Ouvrir l'invitation">
    </form>
    
    <?php
}
//S'il n'y a aucune invitation de combat on prévient le joueur
else
{
    echo "Erreur : Vous n'avez aucune invitation de combat";
}
$battleInvitationQuery->closeCursor();

require_once("../../html/footer.php"); ?>
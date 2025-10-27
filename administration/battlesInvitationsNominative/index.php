<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//On fait une recherche dans la base de donnée de toutes les lieux
$battleInvitationQuery = $bdd->query("SELECT * FROM car_battles_invitations, car_monsters
WHERE battleInvitationMonsterId = monsterId");
$battleInvitationRow = $battleInvitationQuery->rowCount();

//S'il existe une ou plusieurs invitation de combat on affiche le menu déroulant
if ($battleInvitationRow > 0) 
{
    ?>
    
    <form method="POST" action="manageBattleInvitationNominative.php">
        Liste des invitations : <select name="battleInvitationId" class="form-control">

            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($battleInvitation = $battleInvitationQuery->fetch())
            {
                $adminBattleInvitationId = $battleInvitation['battleInvitationId'];
                $adminBattleInvitationName = $battleInvitation['battleInvitationName'];
                $adminBattleInvitationMonsterName = $battleInvitation['monsterName'];
                ?>
                <option value="<?php echo $adminBattleInvitationId ?>"><?php echo "$adminBattleInvitationName ($adminBattleInvitationMonsterName)"; ?></option>
                <?php
            }
            ?>
        
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="manage" class="btn btn-secondary btn-lg" value="Gérer l'invitation de combat">
    </form>
    
    <?php
}
//S'il n'y a aucune invitation de combat on prévient le joueur
else
{
    echo "Il n'y a actuellement aucune invitation de combat";
}
$battleInvitationQuery->closeCursor();
?>

<hr>

<form method="POST" action="addBattleInvitationNominative.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="add" value="Envoyer une invitation de combat">
</form>

<?php require_once("../html/footer.php");
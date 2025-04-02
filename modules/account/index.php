<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");
?>

<?php echo $accountPseudo ?><br />

<hr>

Dernière connexion : <?php echo (new DateTime($accountLastConnection))->format('d-m-Y H:i') ?><br />
Dernière action : <?php echo (new DateTime($accountLastAction))->format('d-m-Y H:i') ?><br />
Accès : <?php echo $accountAccess ?><br />

<hr>

<form method="POST" action="changePassword.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" name="changePassword" class="btn btn-secondary btn-lg" value="Changer le mot de passe"><br>
</form>

<?php require_once("../../html/footer.php"); ?>
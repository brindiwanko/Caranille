<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../modules/login/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

?>

Changer votre adresse email<br />

<hr>

<form method="POST" action="changeEmailEnd.php">
    Email : <input type="text" class="form-control" name="accountEmail" value="<?php echo $accountEmail ?>" required>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" name="edit" class="btn btn-secondary btn-lg" value="Modifier l'adresse email"><br>
</form>

<?php

require_once("../../html/footer.php"); ?>
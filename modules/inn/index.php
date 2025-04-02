<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'est pas dans un lieu on le redirige vers la carte du monde
if ($characterPlaceId == 0) { exit(header("Location: ../../modules/map/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");
?>

<p><img src="<?php echo $placePicture ?>" height="100" width="100"></p>

Bienvenue à l'auberge, ici vous allez pouvoir vous reposer pour <?php echo $placePriceInn; ?> pièce(s) d'or

<hr>

<form method="POST" action="sleep.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" name="sleep" class="btn btn-secondary btn-lg" value="Se reposer">
</form>

<?php require_once("../../html/footer.php"); ?>
<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");
?>

<h1>Points de compétences</h1>
Votre personnage possède <?php echo $characterSkillPoints; ?> PC (Point de compétences)

<form method="POST" action="addHp.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="addHp" class="btn btn-secondary btn-lg" value="+10 HP">
</form>

<form method="POST" action="addMp.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="addMp" class="btn btn-secondary btn-lg" value="+1 MP">
</form>

<form method="POST" action="addStrength.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="addStrength" class="btn btn-secondary btn-lg" value="+1 en force">
</form>

<form method="POST" action="addMagic.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="addMagic" class="btn btn-secondary btn-lg" value="+1 en magie">
</form>

<form method="POST" action="addAgility.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="addAgility" class="btn btn-secondary btn-lg" value="+1 en agilité">
</form>

<form method="POST" action="addDefense.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="addDefense" class="btn btn-secondary btn-lg" value="+1 en défense">
</form>

<form method="POST" action="addDefenseMagic.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="addDefenseMagic" class="btn btn-secondary btn-lg" value="+1 en défense magique">
</form>

<form method="POST" action="addWisdom.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="addWisdom" class="btn btn-secondary btn-lg" value="+1 en sagesse">
</form>

<form method="POST" action="addProspecting.php">
	<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="addWisdom" class="btn btn-secondary btn-lg" value="+1 en prospection">
</form>

<?php require_once("../../html/footer.php"); ?>
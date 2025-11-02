<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");
?>

<hr>
Informations
<hr>

<p><img src="<?php echo $characterPicture ?>" height="100" width="100"></p>

Pseudo : <?php echo $characterName ?><br />
Classe : <?php echo $characterclasseName ?><br />

<hr>
Statistiques
<hr>

Niveau : <?php echo $characterLevel ?><br />
HP : <?php echo "$characterHpMin/$characterHpTotal" ?><br />
MP : <?php echo "$characterMpMin/$characterMpTotal" ?><br />
Force : <?php echo $characterStrengthTotal ?><br />
Magie : <?php echo $characterMagicTotal ?><br />
Agilité : <?php echo $characterAgilityTotal ?><br />
Défense : <?php echo $characterDefenseTotal ?><br />
Défense Magique : <?php echo $characterDefenseMagicTotal ?><br />
Sagesse : <?php echo $characterWisdomTotal ?><br />
Prospection : <?php echo $characterProspectingTotal ?><br />
Défaite(s) en arène : <?php echo $characterArenaDefeate ?><br />
Victoire(s) en arène : <?php echo $characterArenaVictory ?><br />
Expérience : <?php echo "$characterExperience/$experienceLevel" ?><br />
Prochain niveau dans : <?php echo $experienceRemaining ?><br />
Experience total : <?php echo $characterExperienceTotal ?><br />
Argent : <?php echo $characterGold ?><br />

<hr>
Equipements
<hr>

<?php echo $itemArmorNameShow ?> : <?php echo $equipmentArmorName ?><br />
<?php echo $itemBootsNameShow ?> : <?php echo $equipmentBootsName ?><br />
<?php echo $itemGlovesNameShow ?> : <?php echo $equipmentGlovesName ?><br />
<?php echo $itemHelmetNameShow ?> : <?php echo $equipmentHelmetName ?><br />
<?php echo $itemWeaponNameShow ?> : <?php echo $equipmentWeaponName ?><br />

<?php require_once("../../html/footer.php"); ?>
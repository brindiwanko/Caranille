<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On recherche le chapitre de l'histoire du joueur
$chapterQuery = $bdd->prepare("SELECT * FROM car_chapters
WHERE chapterId = ?");
$chapterQuery->execute([$characterChapter]);
$chapterRow = $chapterQuery->rowCount();

//Si le chapitre du joueur existe
if ($chapterRow == 1)
{
	//On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
	while ($chapter = $chapterQuery->fetch())
	{
		//On récupère les informations du chapitre
		$chapterId = stripslashes($chapter['chapterId']);
		$chapterTitle = stripslashes($chapter['chapterTitle']);
		$chapterOpening = stripslashes(nl2br($chapter['chapterOpening']));
		$chapterEnding = stripslashes(nl2br($chapter['chapterEnding']));
	}
	?>
	
	Chapitre <?php echo $chapterId; ?> - <?php echo $chapterTitle; ?>
	
	<hr>
	
	<?php echo $chapterOpening; ?>
		
	<hr>
	
    <form method="POST" action="launchStory.php">
        <input type="submit" class="btn btn-secondary btn-lg" name="continue" value="Continuer">
    </form>
    
	<?php
}
else 
{
	echo "Il n'y a actuellement aucun nouveau chapitre";
}
$chapterQuery->closeCursor();
?>

<?php require_once("../../html/footer.php"); ?>
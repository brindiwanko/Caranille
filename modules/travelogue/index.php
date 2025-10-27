<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On fait une recherche de tous les chapitres dans le carnet de voyage du joueur
$chapterQuery = $bdd->prepare("SELECT * FROM car_chapters
WHERE chapterId < ?");
$chapterQuery->execute([$characterChapter]);
$chapterRow = $chapterQuery->rowCount();

//Si un ou plusieurs chapitres ont été trouvé
if ($chapterRow > 0)
{
    ?>
    
    <form method="POST" action="viewChapter.php">
        Liste des chapitres : <select name="chapterId" class="form-control">

            <?php
            //on récupère les valeurs de chaque monstres qu'on va ensuite mettre dans le menu déroulant
            while ($chapter = $chapterQuery->fetch())
            {
                //On récupère les informations du monstre
                $chapterId = $chapter['chapterId']; 
                $chapterTitle = $chapter['chapterTitle'];
                ?>
                <option value="<?php echo $chapterId ?>"><?php echo "Chapitre $chapterId - $chapterTitle"; ?></option>
                <?php
            }
            ?>

        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <center><input type="submit" name="viewChapter" class="btn btn-secondary btn-lg" value="Voir le chapitre"></center>
    </form>
    
    <?php
}
//Si aucun chapitre n'a été trouvé
else
{
    echo "Il y a actuellement aucun chapitre dans votre carnet de voyage";
}
$chapterQuery->closeCursor();

require_once("../../html/footer.php"); ?>
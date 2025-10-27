<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['chapterId'])
&& isset($_POST['token'])
&& isset($_POST['viewChapter']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if(ctype_digit($_POST['chapterId'])
        && $_POST['chapterId'] >= 1)
        {
            //On récupère les valeurs du formulaire dans une variable
            $chapterId = htmlspecialchars($_POST['chapterId']);
            
            //On fait une requête pour vérifier si le chapitre entré est bien dans le carnet de voyage du joueur
            $chapterQuery = $bdd->prepare("SELECT * FROM car_chapters
            WHERE chapterId < ?
            AND chapterId = ?");
            $chapterQuery->execute([$characterChapter, $chapterId]);
            $chapterRow = $chapterQuery->rowCount();
            
            //Si le chapitre est bien dans le carnet de voyage du joueur
            if ($chapterRow == 1)
            {
                //on récupère les valeurs de chaque monstres qu'on va ensuite mettre dans le menu déroulant
                while ($chapter = $chapterQuery->fetch())
                {
                    //On récupère les informations du monstre
                    $chapterId = $chapter['chapterId']; 
                    $chapterTitle = $chapter['chapterTitle'];
                    $chapterOpening = nl2br(htmlspecialchars($chapter['chapterOpening']));
                    $chapterEnding = nl2br(htmlspecialchars($chapter['chapterEnding']));
                }
                ?>

                <p>Introduction</p>
                <?php echo $chapterOpening ?>

                <hr>

                <p>Conclusion</p>
                <?php echo $chapterEnding ?>

                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
                </form>

                <?php
            }
            //Si aucun chapitre a été trouvé
            else
            {
                echo "Erreur : Chapitre introuvable";
            }
            $chapterQuery->closeCursor();
        }
        //Si tous les champs numérique ne contiennent pas un nombre
        else
        {
            echo "Erreur : Les champs de type numérique ne peuvent contenir qu'un nombre entier";
        }
    }
    //Si le token de sécurité n'est pas correct
    else
    {
        echo "Erreur : La session a expirée, veuillez réessayer";
    }
}
//Si toutes les variables $_POST n'existent pas
else 
{
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>
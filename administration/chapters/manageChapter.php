<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminChapterId'])
&& isset($_POST['token'])
&& isset($_POST['manage']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminChapterId'])
        && $_POST['adminChapterId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminChapterId = htmlspecialchars($_POST['adminChapterId']);

            //On fait une requête pour vérifier si le chapitre choisit existe
            $chapterQuery = $bdd->prepare("SELECT * FROM car_chapters 
            WHERE chapterId = ?
            ORDER By chapterId");
            $chapterQuery->execute([$adminChapterId]);
            $chapterRow = $chapterQuery->rowCount();

            //Si le chapitre existe
            if ($chapterRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($chapter = $chapterQuery->fetch())
                {
                    //On récupère les informations du chapitre
                    $adminChapterId = $chapter['chapterId'];
                    $adminChapterTitle = $chapter['chapterTitle'];
                }
                ?>

                Que souhaitez-vous faire du chapitre <em><?php echo "$adminChapterId - $adminChapterTitle"; ?></em> ?

                <hr>
                    
                <form method="POST" action="editChapter.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="adminChapterId" value="<?php echo $adminChapterId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="edit" value="Afficher/Modifier le chapitre">
                </form>
                
                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>

                <?php
            }
            //Si le chapitre n'exite pas
            else
            {
                echo "Erreur : Chapitre indisponible";
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
//Si l'utilisateur n'a pas cliqué sur le bouton manage
else
{
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
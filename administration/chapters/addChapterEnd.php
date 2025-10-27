<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminChapterMonsterId'])
&& isset($_POST['adminChapterTitle'])
&& isset($_POST['adminChapterOpening'])
&& isset($_POST['adminChapterEnding'])
&& isset($_POST['finalAdd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminChapterMonsterId'])
        && $_POST['adminChapterMonsterId'] >= 1)
        {
            //On récupère les informations du formulaire
            $adminChapterMonsterId = htmlspecialchars($_POST['adminChapterMonsterId']);
            $adminChapterTitle = htmlspecialchars($_POST['adminChapterTitle']);
            $adminChapterOpening = htmlspecialchars($_POST['adminChapterOpening']);
            $adminChapterEnding = htmlspecialchars($_POST['adminChapterEnding']);
            
            //On ajoute le chapitre dans la base de donnée
            $addChapter = $bdd->prepare("INSERT INTO car_chapters VALUES(
            NULL,
            :adminChapterMonsterId,
            :adminChapterTitle,
            :adminChapterOpening,
            :adminChapterEnding)");
            $addChapter->execute([
            'adminChapterMonsterId' => $adminChapterMonsterId,
            'adminChapterTitle' => $adminChapterTitle,
            'adminChapterOpening' => $adminChapterOpening,
            'adminChapterEnding' => $adminChapterEnding]);
            $addChapter->closeCursor();
            ?>

            Le chapitre a bien été crée

            <hr>
                
            <form method="POST" action="index.php">
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
            </form>
            
            <?php
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
//Si tous les champs n'ont pas été rempli
else
{
    echo "Erreur : Tous les champs n'ont pas été rempli";
}

require_once("../html/footer.php");
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
&& isset($_POST['edit']))
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
            WHERE chapterId = ?");
            $chapterQuery->execute([$adminChapterId]);
            $chapterRow = $chapterQuery->rowCount();

            //Si le chapitre existe
            if ($chapterRow == 1) 
            {
                //On fait une boucle pour récupérer toutes les information
                while ($chapter = $chapterQuery->fetch())
                {
                    //On récupère les informations du chapitre
                    $adminChapterId = $chapter['chapterId'];
                    $adminChapterMonsterId = $chapter['chapterMonsterId'];
                    $adminChapterTitle = $chapter['chapterTitle'];
                    $adminChapterOpening = $chapter['chapterOpening'];
                    $adminChapterEnding = $chapter['chapterEnding'];
                }

                //On récupère les informations du monstre du chapitre
                $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters 
                WHERE monsterId = ?");
                $monsterQuery->execute([$adminChapterMonsterId]);
                
                //On fait une boucle pour récupérer toutes les information
                while ($monster = $monsterQuery->fetch())
                {
                    //On récupère les informations du monstre
                    $adminMonsterId = $monster['monsterId'];
                    $adminMonsterName = $monster['monsterName'];
                }
                $monsterQuery->closeCursor();
                ?>

                <p>Informations du chapitre</p>
                
                <form method="POST" action="editChapterEnd.php">
                    Monstre du chapitre <select name="adminChapterMonsterId" class="form-control">
                        <option selected="selected" value="<?php echo $adminMonsterId ?>"><?php echo "N°$adminMonsterId - $adminMonsterName" ?>
                        
                        <?php
                        //On rempli le menu déroulant avec la liste des monstres disponible sans afficher celui juste au dessus
                        $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters
                        WHERE monsterId != ?");
                        $monsterQuery->execute([$adminChapterMonsterId]);
                        $monsterRow = $monsterQuery->rowCount();
                        
                        //S'il y a au moins un monstre de disponible on les affiches dans le menu déroulant
                        if ($monsterRow >= 1)
                        {
                            //On fait une boucle sur tous les résultats
                            while ($monster = $monsterQuery->fetch())
                            {
                                //On récupère les informations du monstre
                                $adminMonsterId = $monster['monsterId']; 
                                $adminMonsterName = $monster['monsterName'];
                                ?>
                                <option value="<?php echo $adminMonsterId ?>"><?php echo "N°$adminMonsterId - $adminMonsterName" ?></option>
                                <?php
                            }
                        }
                        $monsterQuery->closeCursor();
                        ?>
                        
                    </select>
                    Titre : <input type="text" name="adminChapterTitle" class="form-control" placeholder="Titre" value="<?php echo $adminChapterTitle ?>" required>
                    Introduction :  <br> <textarea class="form-control" name="adminChapterOpening" id="adminChapterOpening" rows="3" required><?php echo $adminChapterOpening; ?></textarea>
                    Conclusion :  <br> <textarea class="form-control" name="adminChapterEnding" id="adminChapterEnding" rows="3" required><?php echo $adminChapterEnding; ?></textarea>
                    <input type="hidden" name="adminChapterId" value="<?php echo $adminChapterId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input name="finalEdit" class="btn btn-secondary btn-lg" type="submit" value="Modifier">
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
                echo "Erreur : Impossible de modifier un chapitre qui n'existe pas";
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
//Si l'utilisateur n'a pas cliqué sur le bouton edit
else
{
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
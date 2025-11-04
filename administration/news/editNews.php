<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminNewsId'])
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
        if (ctype_digit($_POST['adminNewsId'])
        && $_POST['adminNewsId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminNewsId = htmlspecialchars($_POST['adminNewsId']);

            //On fait une requête pour vérifier si la news choisie existe
            $newsQuery = $bdd->prepare("SELECT * FROM car_news 
            WHERE newsId = ?");
            $newsQuery->execute([$adminNewsId]);
            $newsRow = $newsQuery->rowCount();

            //Si la news existe
            if ($newsRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($news = $newsQuery->fetch())
                {
                    //On récupère les informations de la news
                    $adminNewsId = $news['newsId'];
                    $adminNewsPicture = $news['newsPicture'];
                    $adminNewsTitle = $news['newsTitle'];
                    $adminNewsMessage = $news['newsMessage'];
                }
                ?>

                <p><img src="<?php echo $adminNewsPicture ?>" height="100" width="100"></p>

                <p>Informations de la news</p>

                <form method="POST" action="editNewsEnd.php">
                    Image : <input type="text" name="adminNewsPicture" class="form-control" placeholder="Image" value="<?php echo $adminNewsPicture ?>" required>
                    Titre : <input type="text" name="adminNewsTitle" class="form-control" placeholder="Titre" value="<?php echo $adminNewsTitle ?>"required>
                    Message : <br> <textarea class="form-control" name="adminNewsMessage" id="adminNewsMessage" rows="3" required><?php echo $adminNewsMessage ?></textarea>
                    <input type="hidden" name="adminNewsId" value="<?php echo $adminNewsId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input name="finalEdit" class="btn btn-secondary btn-lg" type="submit" value="Modifier">
                </form>
                
                <hr>
                
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si la news n'exite pas
            else
            {
                echo "Erreur : Cette news n'existe pas";
            }
            $newsQuery->closeCursor();
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
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
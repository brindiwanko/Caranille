<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

if(isset($_POST['token'])
&& isset($_FILES['picture']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        $dossier = '../../img/items/';
        $fichier = basename($_FILES['picture']['name']);
        $taille_maxi = 1000000;
        $taille = filesize($_FILES['picture']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
        $extension = strrchr($_FILES['picture']['name'], '.'); 
        
        //Si l'extension du fichier n'est pas celle voulue
        if(!in_array($extension, $extensions))
        {
            $erreur = "Le fichier doit être une image";
        }
        //Si la taille de l'image est plus grande que la taille maximal
        if($taille>$taille_maxi)
        {
            $erreur = "La taille de l'image est trop grosse";
        }
        //Si il n'y a pas d'erreur sur l'image
        if(!isset($erreur))
        {
            //On supprime les accents et caractères spéciaux
            $fichier = strtr($fichier, 
            'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
            'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
            $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);

            //Si le fichier à bien été déplacé dans le dossier final
            if(move_uploaded_file($_FILES['picture']['tmp_name'], $dossier . $fichier))
            {
                ?>

                Le fichier a bien été envoyé
                
                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>

                <?php
            }
            //Si le fichier n'a pas été déplacé dans le dossier final
            else
            {
                ?>

                Une erreur s'est produite lors du déplacement de l'image
                
                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>

                <?php
            }
        }
        //Si il y a une erreur sur l'image
        else
        {
            ?>

            <?php echo $erreur ?>
            
            <hr>

            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
            </form>

            <?php
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
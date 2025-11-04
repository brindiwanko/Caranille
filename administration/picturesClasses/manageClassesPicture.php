<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['viewPicture']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On récupère le nom de l'image du formulaire précédent
        $adminFile = htmlspecialchars($_POST['pictureFile']);
        ?>
        
        <p><img src="../../img/classes/<?php echo $adminFile ?>"></p>

        Pour utiliser cette image veuillez copier/coller le texte ci-dessous dans le champs image : <br/>

        ../../img/classes/<?php echo $adminFile ?>
        
        <hr>

        <form method="POST" action="index.php">
            <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
        </form>
        
        <?php
    }
    //Si le token de sécurité n'est pas correct
    else
    {
        echo "Erreur : La session a expirée, veuillez réessayer";
    }
}
else if (isset($_POST['token'])
&& isset($_POST['deletePicture']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On récupère le nom de l'image du formulaire précédent
        $adminFile = htmlspecialchars($_POST['pictureFile']);

        if ($adminFile != "default.png")
        {
            ?>

            <p>ATTENTION</p> 

            Vous êtes sur le point de supprimer cette image : 
        
            <p><img src="../../img/classes/<?php echo $adminFile ?>"></p>
        
            Confirmez-vous la suppression ?
        
            <hr>
                
            <form method="POST" action="deleteClassesPicture.php">
                <input type="hidden" class="btn btn-secondary btn-lg" name="pictureFile" value="<?php echo $adminFile ?>">
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" class="btn btn-secondary btn-lg" name="finalDelete" value="Je confirme la suppression">
            </form>
            
            <hr>
        
            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
            </form>
        
            <?php
        }
        else
        {
            ?>

            Erreur : Il est impossible de supprimer l'image par défaut
        
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
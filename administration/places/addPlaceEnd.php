<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminplacePicture'])
&& isset($_POST['adminplaceName'])
&& isset($_POST['adminplaceDescription'])
&& isset($_POST['adminplacePriceInn'])
&& isset($_POST['adminplaceChapter'])
&& isset($_POST['adminplaceAccess'])
&& isset($_POST['token'])
&& isset($_POST['finalAdd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
    
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminplacePriceInn'])
        && ctype_digit($_POST['adminplaceChapter'])
        && $_POST['adminplacePriceInn'] >= 0
        && $_POST['adminplaceChapter'] >= 1)
        {
            //On récupère les informations du formulaire
            $adminplacePicture = htmlspecialchars($_POST['adminplacePicture']);
            $adminplaceName = htmlspecialchars($_POST['adminplaceName']);
            $adminplaceDescription = htmlspecialchars($_POST['adminplaceDescription']);
            $adminplacePriceInn = htmlspecialchars($_POST['adminplacePriceInn']);
            $adminplaceChapter = htmlspecialchars($_POST['adminplaceChapter']);
            $adminplaceAccess = htmlspecialchars($_POST['adminplaceAccess']);

            //On ajoute le lieu dans la base de donnée
            $addPlace = $bdd->prepare("INSERT INTO car_places VALUES(
            NULL,
            :adminplacePicture,
            :adminplaceName,
            :adminplaceDescription,
            :adminplacePriceInn,
            :adminplaceChapter,
            :adminplaceAccess)");
            $addPlace->execute([
            'adminplacePicture' => $adminplacePicture,
            'adminplaceName' => $adminplaceName,
            'adminplaceDescription' => $adminplaceDescription,
            'adminplacePriceInn' => $adminplacePriceInn,
            'adminplaceChapter' => $adminplaceChapter,
            'adminplaceAccess' => $adminplaceAccess]);
            $addPlace->closeCursor();
            ?>

            le lieu a bien été crée

            <hr>
                
            <form method="POST" action="index.php">
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
//Si toutes les variables $_POST n'existent pas
else
{
    echo "Erreur : Tous les champs n'ont pas été rempli";
}

require_once("../html/footer.php");
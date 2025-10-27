<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminPlaceId'])
&& isset($_POST['adminplacePicture'])
&& isset($_POST['adminplaceName'])
&& isset($_POST['adminplaceDescription'])
&& isset($_POST['adminplacePriceInn'])
&& isset($_POST['adminplaceChapter'])
&& isset($_POST['adminplaceAccess'])
&& isset($_POST['token'])
&& isset($_POST['finalEdit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminPlaceId'])
        && ctype_digit($_POST['adminplacePriceInn'])
        && ctype_digit($_POST['adminplaceChapter'])
        && $_POST['adminPlaceId'] >= 1
        && $_POST['adminplacePriceInn'] >= 0
        && $_POST['adminplaceChapter'] >= 1)
        {
            //On récupère les informations du formulaire
            $adminPlaceId = htmlspecialchars($_POST['adminPlaceId']);
            $adminplacePicture = htmlspecialchars($_POST['adminplacePicture']);
            $adminplaceName = htmlspecialchars($_POST['adminplaceName']);
            $adminItemDescription = htmlspecialchars($_POST['adminplaceDescription']);
            $adminplacePriceInn = htmlspecialchars($_POST['adminplacePriceInn']);
            $adminplaceChapter = htmlspecialchars($_POST['adminplaceChapter']);
            $adminplaceAccess = htmlspecialchars($_POST['adminplaceAccess']);

            //On fait une requête pour vérifier si le lieu choisit existe
            $placeQuery = $bdd->prepare("SELECT * FROM car_places 
            WHERE placeId = ?");
            $placeQuery->execute([$adminPlaceId]);
            $placeRow = $placeQuery->rowCount();

            //Si le lieu existe
            if ($placeRow == 1) 
            {
                //Si le lieu est ou devient innaccessible
                if ($adminplaceAccess == "No")
                {
                    //On ejecte tous les joueurs présents dedans
                    $updateCharacter = $bdd->prepare("UPDATE car_characters 
                    SET characterPlaceId = 0
                    WHERE characterPlaceId = :adminPlaceId");
                    $updateCharacter->execute([
                    'adminPlaceId' => $adminPlaceId]);
                    $updateCharacter->closeCursor();
                }
                //On met à jour le lieu dans la base de donnée
                $updatePlace = $bdd->prepare("UPDATE car_places 
                SET placePicture = :adminplacePicture,
                placeName = :adminplaceName,
                placeDescription = :adminItemDescription,
                placePriceInn = :adminplacePriceInn,
                placeChapter = :adminplaceChapter,
                placeAccess = :adminplaceAccess
                WHERE placeId = :adminPlaceId");
                $updatePlace->execute([
                'adminplacePicture' => $adminplacePicture,
                'adminplaceName' => $adminplaceName,
                'adminItemDescription' => $adminItemDescription,
                'adminplacePriceInn' => $adminplacePriceInn,
                'adminplaceChapter' => $adminplaceChapter,
                'adminplaceAccess' => $adminplaceAccess,
                'adminPlaceId' => $adminPlaceId]);
                $updatePlace->closeCursor();
                ?>

                le lieu a bien été mit à jour

                <hr>
                    
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si le lieu n'exite pas
            else
            {
                echo "Erreur : Cette lieu n'existe pas";
            }
            $placeQuery->closeCursor();
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
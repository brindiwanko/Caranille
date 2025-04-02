<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminRacePicture'])
&& isset($_POST['adminRaceName'])
&& isset($_POST['adminRaceDescription'])
&& isset($_POST['adminRaceHpBonus'])
&& isset($_POST['adminRaceMpBonus'])
&& isset($_POST['adminRaceStrengthBonus'])
&& isset($_POST['adminRaceMagicBonus'])
&& isset($_POST['adminRaceAgilityBonus'])
&& isset($_POST['adminRaceDefenseBonus'])
&& isset($_POST['adminRaceDefenseMagicBonus'])
&& isset($_POST['adminRaceWisdomBonus'])
&& isset($_POST['adminRaceProspectingBonus'])
&& isset($_POST['token'])
&& isset($_POST['finalAdd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si l'id de la race récupéré dans le formulaire est en entier positif
        if (ctype_digit($_POST['adminRaceHpBonus'])
        && ctype_digit($_POST['adminRaceMpBonus'])
        && ctype_digit($_POST['adminRaceStrengthBonus'])
        && ctype_digit($_POST['adminRaceMagicBonus'])
        && ctype_digit($_POST['adminRaceAgilityBonus'])
        && ctype_digit($_POST['adminRaceDefenseBonus'])
        && ctype_digit($_POST['adminRaceDefenseMagicBonus'])
        && ctype_digit($_POST['adminRaceWisdomBonus'])
        && ctype_digit($_POST['adminRaceProspectingBonus'])
        && $_POST['adminRaceHpBonus'] >= 0
        && $_POST['adminRaceMpBonus'] >= 0
        && $_POST['adminRaceStrengthBonus'] >= 0
        && $_POST['adminRaceMagicBonus'] >= 0
        && $_POST['adminRaceAgilityBonus'] >= 0
        && $_POST['adminRaceDefenseBonus'] >= 0
        && $_POST['adminRaceDefenseMagicBonus'] >= 0
        && $_POST['adminRaceWisdomBonus'] >= 0
        && $_POST['adminRaceProspectingBonus'] >= 0)
        {
            //On récupère les informations du formulaire
            $adminRacePicture = htmlspecialchars(addslashes($_POST['adminRacePicture']));
            $adminRaceName = htmlspecialchars(addslashes($_POST['adminRaceName']));
            $adminRaceDescription = htmlspecialchars(addslashes($_POST['adminRaceDescription']));
            $adminRaceHpBonus = htmlspecialchars(addslashes($_POST['adminRaceHpBonus']));
            $adminRaceMpBonus = htmlspecialchars(addslashes($_POST['adminRaceMpBonus']));
            $adminRaceStrengthBonus = htmlspecialchars(addslashes($_POST['adminRaceStrengthBonus']));
            $adminRaceMagicBonus = htmlspecialchars(addslashes($_POST['adminRaceMagicBonus']));
            $adminRaceAgilityBonus = htmlspecialchars(addslashes($_POST['adminRaceAgilityBonus']));
            $adminRaceDefenseBonus = htmlspecialchars(addslashes($_POST['adminRaceDefenseBonus']));
            $adminRaceDefenseMagicBonus = htmlspecialchars(addslashes($_POST['adminRaceDefenseMagicBonus']));
            $adminRaceWisdomBonus = htmlspecialchars(addslashes($_POST['adminRaceWisdomBonus']));
            $adminRaceProspectingBonus = htmlspecialchars(addslashes($_POST['adminRaceProspectingBonus']));
            
            //On ajoute la race dans la base de donnée
            $addRace = $bdd->prepare("INSERT INTO car_races VALUES(
            NULL,
            :adminRacePicture,
            :adminRaceName,
            :adminRaceDescription,
            :adminRaceHpBonus,
            :adminRaceMpBonus,
            :adminRaceStrengthBonus,
            :adminRaceMagicBonus,
            :adminRaceAgilityBonus,
            :adminRaceDefenseBonus,
            :adminRaceDefenseMagicBonus,
            :adminRaceWisdomBonus,
            :adminRaceProspectingBonus)");
            $addRace->execute([
            'adminRacePicture' => $adminRacePicture,
            'adminRaceName' => $adminRaceName,
            'adminRaceDescription' => $adminRaceDescription,
            'adminRaceHpBonus' => $adminRaceHpBonus,
            'adminRaceMpBonus' => $adminRaceMpBonus,
            'adminRaceStrengthBonus' => $adminRaceStrengthBonus,
            'adminRaceMagicBonus' => $adminRaceMagicBonus,
            'adminRaceAgilityBonus' => $adminRaceAgilityBonus,
            'adminRaceDefenseBonus' => $adminRaceDefenseBonus,
            'adminRaceDefenseMagicBonus' => $adminRaceDefenseMagicBonus,
            'adminRaceWisdomBonus' => $adminRaceWisdomBonus,
            'adminRaceProspectingBonus' => $adminRaceProspectingBonus]);
            $addRace->closeCursor();
            ?>
            
            La classe a bien été crée

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
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");

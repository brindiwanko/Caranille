<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminclassePicture'])
&& isset($_POST['adminclasseName'])
&& isset($_POST['adminclasseDescription'])
&& isset($_POST['adminclasseHpBonus'])
&& isset($_POST['adminclasseMpBonus'])
&& isset($_POST['adminclassestrengthBonus'])
&& isset($_POST['adminclasseMagicBonus'])
&& isset($_POST['adminclasseAgilityBonus'])
&& isset($_POST['adminclasseDefenseBonus'])
&& isset($_POST['adminclasseDefenseMagicBonus'])
&& isset($_POST['adminclasseWisdomBonus'])
&& isset($_POST['adminclasseProspectingBonus'])
&& isset($_POST['token'])
&& isset($_POST['finalAdd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si l'id de la classe récupéré dans le formulaire est en entier positif
        if (ctype_digit($_POST['adminclasseHpBonus'])
        && ctype_digit($_POST['adminclasseMpBonus'])
        && ctype_digit($_POST['adminclassestrengthBonus'])
        && ctype_digit($_POST['adminclasseMagicBonus'])
        && ctype_digit($_POST['adminclasseAgilityBonus'])
        && ctype_digit($_POST['adminclasseDefenseBonus'])
        && ctype_digit($_POST['adminclasseDefenseMagicBonus'])
        && ctype_digit($_POST['adminclasseWisdomBonus'])
        && ctype_digit($_POST['adminclasseProspectingBonus'])
        && $_POST['adminclasseHpBonus'] >= 0
        && $_POST['adminclasseMpBonus'] >= 0
        && $_POST['adminclassestrengthBonus'] >= 0
        && $_POST['adminclasseMagicBonus'] >= 0
        && $_POST['adminclasseAgilityBonus'] >= 0
        && $_POST['adminclasseDefenseBonus'] >= 0
        && $_POST['adminclasseDefenseMagicBonus'] >= 0
        && $_POST['adminclasseWisdomBonus'] >= 0
        && $_POST['adminclasseProspectingBonus'] >= 0)
        {
            //On récupère les informations du formulaire
            $adminclassePicture = htmlspecialchars($_POST['adminclassePicture']);
            $adminclasseName = htmlspecialchars($_POST['adminclasseName']);
            $adminclasseDescription = htmlspecialchars($_POST['adminclasseDescription']);
            $adminclasseHpBonus = htmlspecialchars($_POST['adminclasseHpBonus']);
            $adminclasseMpBonus = htmlspecialchars($_POST['adminclasseMpBonus']);
            $adminclassestrengthBonus = htmlspecialchars($_POST['adminclassestrengthBonus']);
            $adminclasseMagicBonus = htmlspecialchars($_POST['adminclasseMagicBonus']);
            $adminclasseAgilityBonus = htmlspecialchars($_POST['adminclasseAgilityBonus']);
            $adminclasseDefenseBonus = htmlspecialchars($_POST['adminclasseDefenseBonus']);
            $adminclasseDefenseMagicBonus = htmlspecialchars($_POST['adminclasseDefenseMagicBonus']);
            $adminclasseWisdomBonus = htmlspecialchars($_POST['adminclasseWisdomBonus']);
            $adminclasseProspectingBonus = htmlspecialchars($_POST['adminclasseProspectingBonus']);
            
            //On ajoute la classe dans la base de donnée
            $addclasse = $bdd->prepare("INSERT INTO car_classes VALUES(
            NULL,
            :adminclassePicture,
            :adminclasseName,
            :adminclasseDescription,
            :adminclasseHpBonus,
            :adminclasseMpBonus,
            :adminclassestrengthBonus,
            :adminclasseMagicBonus,
            :adminclasseAgilityBonus,
            :adminclasseDefenseBonus,
            :adminclasseDefenseMagicBonus,
            :adminclasseWisdomBonus,
            :adminclasseProspectingBonus)");
            $addclasse->execute([
            'adminclassePicture' => $adminclassePicture,
            'adminclasseName' => $adminclasseName,
            'adminclasseDescription' => $adminclasseDescription,
            'adminclasseHpBonus' => $adminclasseHpBonus,
            'adminclasseMpBonus' => $adminclasseMpBonus,
            'adminclassestrengthBonus' => $adminclassestrengthBonus,
            'adminclasseMagicBonus' => $adminclasseMagicBonus,
            'adminclasseAgilityBonus' => $adminclasseAgilityBonus,
            'adminclasseDefenseBonus' => $adminclasseDefenseBonus,
            'adminclasseDefenseMagicBonus' => $adminclasseDefenseMagicBonus,
            'adminclasseWisdomBonus' => $adminclasseWisdomBonus,
            'adminclasseProspectingBonus' => $adminclasseProspectingBonus]);
            $addclasse->closeCursor();
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

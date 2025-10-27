<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminNewsPicture'])
&& isset($_POST['adminNewsTitle'])
&& isset($_POST['adminNewsMessage'])
&& isset($_POST['token'])
&& isset($_POST['finalAdd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On récupère les informations du formulaire
        $adminNewsPicture = htmlspecialchars($_POST['adminNewsPicture']);
        $adminNewsTitle = htmlspecialchars($_POST['adminNewsTitle']);
        $adminNewsMessage = htmlspecialchars($_POST['adminNewsMessage']);
        $adminNewsAccountPseudo = $accountPseudo;
        $adminNewsDate = date('Y-m-d H:i:s');

        //On met à jour l'équipement dans la base de donnée
        $addNews = $bdd->prepare("INSERT INTO car_news VALUES(
        NULL,
        :adminNewsPicture,
        :adminNewsTitle,
        :adminNewsMessage,
        :adminNewsAccountPseudo,
        :adminNewsDate)");
        $addNews->execute([
        'adminNewsPicture' => $adminNewsPicture,
        'adminNewsTitle' => $adminNewsTitle,
        'adminNewsMessage' => $adminNewsMessage,
        'adminNewsAccountPseudo' => $adminNewsAccountPseudo,
        'adminNewsDate' => $adminNewsDate]);
        $addNews->closeCursor();
        ?>

        La news a bien été publiée

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
//Si toutes les variables $_POST n'existent pas
else
{
    echo "Erreur : Tous les champs n'ont pas été rempli";
}

require_once("../html/footer.php");
<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminAccountId'])
&& isset($_POST['adminAccountAccess'])
&& isset($_POST['token'])
&& isset($_POST['finalEdit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminAccountId'])
        && ctype_digit($_POST['adminAccountAccess'])
        && $_POST['adminAccountId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminAccountId = htmlspecialchars($_POST['adminAccountId']);

            //On fait une requête pour vérifier si le compte choisit existe
            $accountQuery = $bdd->prepare("SELECT * FROM car_accounts 
            WHERE accountId = ?");
            $accountQuery->execute([$adminAccountId]);
            $account = $accountQuery->rowCount();
            $accountQuery->closeCursor();

            //Si le compte existe
            if ($account == 1) 
            {
                //On récupère les informations du formulaire
                $adminAccountId = htmlspecialchars($_POST['adminAccountId']);
                $adminAccountAccess =  htmlspecialchars($_POST['adminAccountAccess']);

                //On met à jour le compte dans la base de donnée
                $updateAccount = $bdd->prepare("UPDATE car_accounts 
                SET accountAccess = :adminAccountAccess
                WHERE accountId = :adminAccountId");

                $updateAccount->execute([
                'adminAccountAccess' => $adminAccountAccess,
                'adminAccountId' => $adminAccountId]);
                $updateAccount->closeCursor();
                ?>

                Le compte a bien été mit à jour

                <hr>
                    
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si le compte n'existe pas
            else
            {
                echo "Erreur : Ce compte n'existe pas";
            }
            $accountQuery->closeCursor();
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
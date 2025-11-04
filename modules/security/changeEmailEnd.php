<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../modules/login/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['accountEmail'])
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

        //On récupère les valeurs du formulaire dans une variable
        $accountEmail = htmlspecialchars($_POST['accountEmail']);

        //On fait une requête pour vérifier si l'adresse email est déjà utilisé
        $emailQuery = $bdd->prepare("SELECT * FROM car_accounts 
        WHERE accountId != ?
        AND accountEmail = ?");
        $emailQuery->execute([$accountId, $accountEmail]);
        $emailRow = $emailQuery->rowCount();
        $emailQuery->closeCursor();

        //Si l'adresse email est disponible
        if ($emailRow == 0) 
        {
            //On met à jour l'adresse email dans la base de donnée
            $updateAccount = $bdd->prepare("UPDATE car_accounts SET 
            accountEmail = :accountEmail
            WHERE accountId = :accountId");
            $updateAccount->execute(array(
            'accountEmail' => $accountEmail,
            'accountId' => $accountId));
            $updateAccount->closeCursor();
            ?>
            
            L'adresse email a bien été mise à jour

            <hr>

            <form method="POST" action="index.php">
                <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Continuer">
            </form>
                
            <?php
        }
        else
        {
            ?>

            L'adresse email est déjà utilisée

            <hr>

            <form method="POST" action="changeEmail.php">
                <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Continuer">
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
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>
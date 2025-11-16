<?php 
require_once("../../kernel/kernel.php");
require_once("../../kernel/security/passwordManager.php");
require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['accountCode'])
&& isset($_POST['accountPassword'])
&& isset($_POST['accountPasswordConfirm'])
&& isset($_POST['changePasswordEnd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On récupère les valeurs du formulaire dans une variable
        $accountCode = htmlspecialchars($_POST['accountCode']);
        $accountPassword = $_POST['accountPassword'];
        $accountPasswordConfirm = $_POST['accountPasswordConfirm'];

        //On vérifie si les deux mots de passes sont identiques
        if ($accountPassword == $accountPasswordConfirm) 
        {
            //On fait une requête pour vérifier si une demande de vérification est en cours
            $accountForgetPasswordQuery = $bdd->prepare("SELECT * FROM car_forgets_passwords 
            WHERE accountForgetPasswordEmailCode = ?");
            $accountForgetPasswordQuery->execute([$accountCode]);
            $accountForgetPasswordRow = $accountForgetPasswordQuery->rowCount();

            //Si une vérification est en cours
            if ($accountForgetPasswordRow == 1) 
            {
                //Dans ce cas on boucle pour récupérer le tableau retourné par la base de donnée pour récupérer les informations du compte
                while ($accountForgetPassword = $accountForgetPasswordQuery->fetch())
                {
                    //On récupère les informations de la demande de vérification
                    $accountForgetPasswordId = $accountForgetPassword['accountForgetPasswordId'];
                    $accountForgetPasswordAccountId = $accountForgetPassword['accountForgetPasswordAccountId'];
                    $accountForgetPasswordEmailAdress = $accountForgetPassword['accountForgetPasswordEmailAdress'];
                }

                //On hash le mot de passe soumis
                $accountPasswordHash = PasswordManager::hashPassword($accountPassword);
                
                //On supprime la demande de réinitialisation du mot de passe
                $deleteForgetPasswordQuery = $bdd->prepare("DELETE FROM car_forgets_passwords
                WHERE accountForgetPasswordId = :accountForgetPasswordId");
                $deleteForgetPasswordQuery->execute(array(
                'accountForgetPasswordId' => $accountForgetPasswordId));
                $deleteForgetPasswordQuery->closeCursor();

                //On met à jour le mot de passe dans la base de donnée
                $updateAccount = $bdd->prepare("UPDATE car_accounts 
                SET accountPassword = :accountPassword
                WHERE accountId = :accountForgetPasswordAccountId");
                $updateAccount->execute(array(
                'accountPassword' => $accountPasswordHash,
                'accountForgetPasswordAccountId' => $accountForgetPasswordAccountId));
                $updateAccount->closeCursor();
                ?>

                Votre mot de passe a bien été changé

                <hr>

                <form method="POST" action="../../modules/login/index.php">
                    <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Se connecter">
                </form>

                <?php
            }
            //Si le pseudo est déjà utilisé
            else 
            {
                ?>

                Erreur : Ce code ne correspond à aucune demande de réinitialisation de mot de passe

                <hr>

                <form method="POST" action="../../modules/forgetPassword/enterCode.php">
                    <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Recommencer">
                </form>

                <?php
            }
            $accountForgetPasswordQuery->closeCursor();
        }
        //Si les deux mots de passe ne sont pas identique
        else 
        {
            echo "Les deux mots de passe ne sont pas identiques";
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

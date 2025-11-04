<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['accountCode'])
&& isset($_POST['resetPassword']))
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

        //On fait une requête pour vérifier si une demande de vérification est en cours
        $accountForgetPasswordQuery = $bdd->prepare("SELECT * FROM car_forgets_passwords 
        WHERE accountForgetPasswordEmailCode = ?");
        $accountForgetPasswordQuery->execute([$accountCode]);
        $accountForgetPasswordRow = $accountForgetPasswordQuery->rowCount();

        //Si une vérification est en cours
        if ($accountForgetPasswordRow == 1) 
        {
            ?>

            Veuillez saisir un nouveau mot de passe et le confirmer

            <hr>

            <form method="POST" action="../../modules/forgetPassword/changePasswordEnd.php">
                Mot de passe : <input type="password" class="form-control" name="accountPassword" required>
                Confirmez : <input type="password" class="form-control" name="accountPasswordConfirm" required>
                <input type="hidden" class="btn btn-secondary btn-lg" name="accountCode" value="<?php echo $accountCode ?>">
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" name="changePasswordEnd" class="btn btn-secondary btn-lg" value="Se connecter">
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
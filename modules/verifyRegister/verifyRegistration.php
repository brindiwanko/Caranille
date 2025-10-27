<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['accountEmail']) 
&& isset($_POST['codeAccountVerification']))
{
    //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
    if (ctype_digit($_POST['codeAccountVerification'])
    && $_POST['codeAccountVerification'] >= 0)
    {
        //On récupère les valeurs du formulaire dans une variable
        $accountEmail = htmlspecialchars($_POST['accountEmail']);
        $codeAccountVerification = htmlspecialchars($_POST['codeAccountVerification']);

        //On fait une requête pour vérifier si une demande de vérification est en cours
        $accountVerificationQuery = $bdd->prepare("SELECT * FROM car_accounts_verifications 
        WHERE accountVerificationEmailAdresse = ?
        AND accountVerificationEmailCode = ?");
        $accountVerificationQuery->execute([$accountEmail, $codeAccountVerification]);
        $accountVerificationRow = $accountVerificationQuery->rowCount();

        //Si une vérification est en cours
        if ($accountVerificationRow == 1) 
        {
            //Dans ce cas on boucle pour récupérer le tableau retourné par la base de donnée pour récupérer les informations du compte
            while ($accountVerification = $accountVerificationQuery->fetch())
            {
                //On récupère les informations de la demande de vérification
                $accountVerificationId = $accountVerification['accountVerificationId'];
                $accountVerificationAccountId = $accountVerification['accountVerificationAccountId'];
            }

            //On supprime la demande de vérification du compte
            $deleteVerificationAccountQuery = $bdd->prepare("DELETE FROM car_accounts_verifications
            WHERE accountVerificationId = :accountVerificationId");
            $deleteVerificationAccountQuery->execute(array(
            'accountVerificationId' => $accountVerificationId));
            $deleteVerificationAccountQuery->closeCursor();

            //On débloque le compte
            $updateAccount = $bdd->prepare("UPDATE car_accounts SET 
            accountStatus = 0,
            accountReason = 'None'
            WHERE accountId = :accountVerificationAccountId");
            $updateAccount->execute(array(
            'accountVerificationAccountId' => $accountVerificationAccountId));
            $updateAccount->closeCursor();

            ?>

            Votre inscription est maintenant confirmée, vous pouvez maintenant vous connecter

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

            Erreur : Aucune demande de vérification en cours

            <hr>

            <form method="POST" action="../../modules/register/index.php">
                <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Recommencer">
            </form>

            <?php
        }
        $accountVerificationQuery->closeCursor();
    }
    //Si tous les champs numérique ne contiennent pas un nombre
    else
    {
        echo "Erreur : Les champs de type numérique ne peuvent contenir qu'un nombre entier";
    }
}
//Si toutes les variables $_POST n'existent pas
else 
{
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>
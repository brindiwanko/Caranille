<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['accountPseudo'])
&& isset($_POST['accountEmail'])
&& isset($_POST['token'])
&& isset($_POST['continue']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On récupère les valeurs du formulaire dans une variable
        $accountPseudo = htmlspecialchars($_POST['accountPseudo']);
        $accountEmail = htmlspecialchars($_POST['accountEmail']);

        //On fait une requête pour vérifier si la combinaison pseudo et adresse Email est bonne
        $accountQuery = $bdd->prepare("SELECT * FROM car_accounts 
        WHERE accountPseudo = ?
        AND accountEmail = ?");
        $accountQuery->execute([$accountPseudo, $accountEmail]);
        $accountRow = $accountQuery->rowCount();

        //Si le pseudo est disponible
        if ($accountRow == 1) 
        {
            //On fait une boucle pour récupérer les résultats
            while ($account = $accountQuery->fetch())
            {
                //On récupère les informations du compte
                $accountId = $account['accountId'];
            }

            //On génère un code
            $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

            $codeForgetPassword = "";
            for($i=0;$i<20;$i++)
            {
                $codeForgetPassword .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
            }

            $date = time();

            $codeForgetPasswordFinal = $accountPseudo.$date.$codeForgetPassword;

            $addForgetPassword = $bdd->prepare("INSERT INTO car_forgets_passwords VALUES(
            NULL,
            :accountId,
            :accountEmail,
            :codeForgetPasswordFinal)");
            $addForgetPassword->execute([
            'accountId' => $accountId,
            'accountEmail' => $accountEmail,
            'codeForgetPasswordFinal' => $codeForgetPasswordFinal]);
            $addForgetPassword->closeCursor();

            $from = "noreply@caranille.com";

            $to = $accountEmail;
            
            $subject = "Mot de passe oublié";
            
            $message = "Voici votre code à saisir dans Mon compte -> Code pour réinitialiser votre nouveau mot de passe : \n\n$codeForgetPasswordFinal\n\nSi vous n'êtes pas à l'origine de cette demande veuillez ne pas tenir compte de ce mail.";
            
            $headers = "From:" . $from;
            
            mail($to,$subject,$message, $headers);
            ?>

            Un code vous a été envoyé, veuillez cliquer sur "Saisir le code" ci-dessous afin de continuer

            <hr>

            <form method="POST" action="enterCode.php">
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" name="enterCode" class="btn btn-secondary btn-lg" value="Saisir le code">
            </form>
                
            <?php
        }
        //Si la combinaison pseudo/email est incorrecte
        else 
        {
            ?>

            Erreur : La combinaison pseudo/email est incorrecte

            <hr>

            <form method="POST" action="../../modules/forgetPassword/index.php">
                <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Recommencer">
            </form>

            <?php
        }
        $accountQuery->closeCursor();
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

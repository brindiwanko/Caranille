<?php 
require_once("../../kernel/kernel.php");
require_once("../../kernel/security/passwordManager.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['oldPassword']) 
&& isset($_POST['newPassword'])
&& isset($_POST['confirmNewPassword'])
&& isset($_POST['token'])
&& isset($_POST['changePasswordEnd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
        //On récupère les valeurs du formulaire dans une variable
        $accountOldPassword = $_POST['oldPassword'];
        $accountNewPassword = $_POST['newPassword'];
        $accountConfirmNewPassword = $_POST['confirmNewPassword'];
    
        //On vérifie si les deux nouveaux mots de passes sont identiques
        if ($accountNewPassword == $accountConfirmNewPassword) 
        {
            //On récupère le hash actuel de l'utilisateur
            $accountQuery = $bdd->prepare("SELECT accountPassword FROM car_accounts 
            WHERE accountId = ?");
            $accountQuery->execute([$accountId]);
            
            if ($accountQuery->rowCount() == 1) 
            {
                $account = $accountQuery->fetch();
                $storedPassword = $account['accountPassword'];
                
                //On vérifie si le mot de passe soumis est correct
                if (PasswordManager::verifyPassword($accountOldPassword, $storedPassword))
                {
                    //On hash le mot de passe soumis
                    $accountNewPasswordHash = PasswordManager::hashPassword($accountNewPassword);
                    
                    //On met à jour le mot de passe dans la base de donnée
                    $updateAccount = $bdd->prepare("UPDATE car_accounts 
                    SET accountPassword = :newPassword
                    WHERE accountId = :accountId");
                    $updateAccount->execute(array(
                    'newPassword' => $accountNewPasswordHash,
                    'accountId' => $accountId));
                    $updateAccount->closeCursor();
                ?>
                
                Le mot de passe a bien été mit à jour
                     
                <hr>
            
                <form method="POST" action="../security/index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
                }
                else
                {
                    echo "L'ancien mot de passe saisit est incorrect";
                }
            }
            else
            {
                echo "Erreur lors de la récupération du compte";
            }
            $accountQuery->closeCursor();
        }
        //Si les deux mots de passe ne sont pas identique
        else 
        {
            ?>
            
            Les deux mots de passe entré ne sont pas identiques
            
            <hr>
            
            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
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

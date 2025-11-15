<?php 
require_once("../../kernel/kernel.php");
require_once("../../kernel/security/passwordManager.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['accountPseudo']) 
&& isset($_POST['accountPassword'])
&& isset($_POST['token'])
&& isset($_POST['login']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //Récupération des valeurs des champs
        $accountPseudo = htmlspecialchars($_POST['accountPseudo']);
        $accountPassword = $_POST['accountPassword'];
    
        //On fait une requête pour récupérer les informations du compte
        $accountQuery = $bdd->prepare("SELECT * FROM car_accounts 
        WHERE accountPseudo = ?");
        $accountQuery->execute([$accountPseudo]);
        $accountRow = $accountQuery->rowCount();
    
        //S'il y a un résultat de trouvé, on vérifie le mot de passe
        if ($accountRow == 1)
        {
            //Dans ce cas on boucle pour récupérer le tableau retourné par la base de donnée
            $account = $accountQuery->fetch();
            
            //On récupère le hash stocké
            $storedPassword = $account['accountPassword'];
            
            //On vérifie si le mot de passe soumis est correct
            if (PasswordManager::verifyPassword($accountPassword, $storedPassword))
            {
                //On récupère les informations du compte comme l'id et les accès (joueur, modérateur, administrateur)
                $accountId = $account['accountId'];
                $accountAccess = $account['accountAccess'];
                $accountStatus = $account['accountStatus'];
                $accountReason = $account['accountReason'];

                //Si le joueur peut se connecter
                if ($accountStatus == 0)
                {
                    //Si le jeu est ouvert au public
                    if ($gameAccess == "Opened")
                    {
                        //On définit une date pour mettre à jour la dernière connexion du compte
                        $date = date('Y-m-d H:i:s');
                        
                        //On créer une session qui ne contiendra que l'id du compte
                        $_SESSION['account']['id'] = $account['accountId'];
                        $accountId = $_SESSION['account']['id'];

                        //On régénère l'ID de session pour éviter les attaques de fixation de session
                        session_regenerate_id(true);

                        //On met la date de connexion à jour
                        $updateAccount = $bdd->prepare("UPDATE car_accounts SET 
                        accountLastConnection = :accountLastConnection
                        WHERE accountId = :accountId");
                        $updateAccount->execute(array(
                        'accountLastConnection' => $date,   
                        'accountId' => $accountId));
                        $updateAccount->closeCursor();
                        
                        header("Location: ../../index.php");
                    }
                    //Si le jeu est fermé au public
                    else
                    {
                        //Si le joueur est administrateur il peut se connecter
                        if ($accountAccess == 2)
                        {
                            //On définit une date pour mettre à jour la dernière connexion du compte
                            $date = date('Y-m-d H:i:s');
                            
                            //On créer une session qui ne contiendra que l'id du compte
                            $_SESSION['account']['id'] = $account['accountId'];
                            $accountId = $_SESSION['account']['id'];

                            //On régénère l'ID de session pour éviter les attaques de fixation de session
                            session_regenerate_id(true);

                            //On met la date de connexion à jour
                            $updateAccount = $bdd->prepare("UPDATE car_accounts SET 
                            accountLastConnection = :accountLastConnection
                            WHERE accountId = :accountId");
                            $updateAccount->execute(array(
                            'accountLastConnection' => $date,   
                            'accountId' => $accountId));
                            $updateAccount->closeCursor();
                            
                            header("Location: ../../index.php");
                        }
                        //Si le joueur n'est pas administrateur on lui refuse l'accès
                        else
                        {
                            ?>

                            Une maintenance est actuellement en cours, merci de réessayer plus tard.

                            <hr>

                            <form method="POST" action="../../modules/main/index.php">
                                <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Retourner à l'accueil">
                            </form>

                            <?php
                        }
                    }
                }
                //Si le joueur ne peut se connecter
                else
                {
                    ?>

                    Vous êtes actuellement banni pour la raison suivante : <?php echo "$accountReason" ?>

                    <hr>

                    <form method="POST" action="../../modules/main/index.php">
                        <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Recommencer">
                    </form>

                    <?php
                }
            }
            else
            {
                echo "Mauvais Pseudo/Mot de passe";
            }
        }
        //S'il n'y a aucun résultat de trouvé c'est que le pseudo n'existe pas
        else
        {
            echo "Mauvais Pseudo/Mot de passe";
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

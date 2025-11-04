<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['changePassword']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        ?>
        
        <form method="POST" action="changePasswordEnd.php">
            Ancien mot de passe : <input type="password" class="form-control" name="oldPassword" required>
            Nouveau mot de passe : <input type="password" class="form-control" name="newPassword" required>
            Confirmez : <input type="password" class="form-control" name="confirmNewPassword" required>
            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
            <input type="submit" class="btn btn-secondary btn-lg" name="changePasswordEnd" value="Modifier le mot de passe">
        </form>
            
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
	echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>
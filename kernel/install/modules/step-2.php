<?php require_once("../html/header.php"); 

//Si le token de sécurité est correct
if ($_POST['token'] == $_SESSION['token'])
{
    //On supprime le token de l'ancien formulaire
    $_SESSION['token'] = NULL;
    
    //Comme il y a un nouveau formulaire on régénère un nouveau token
    $_SESSION['token'] = bin2hex(random_bytes(32));
    ?>

    <p><h4>Etape 2/4 - Configuration de la base de donnée (Mysql)</h4></p>

    <form method="POST" action="step-3.php">
        Nom de la base de donnée : <input type="text" class="form-control" name="databaseName" required>
        Adresse de la base de donnée : <input type="text" class="form-control" name="databaseHost" required>
        Nom de l'utilisateur : <input type="text" class="form-control" name="databaseUser" required>
        Mot de passe : <input type="password" class="form-control" name="databasePassword">
        Port (3306 par défaut) : <input type="number" class="form-control" name="databasePort" value="3306" required>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" class="btn btn-secondary btn-lg" name="install" value="Continuer">
    </form>

    <?php 
}
//Si le token de sécurité n'est pas correct
else
{
    echo "Erreur : La session a expirée, veuillez réessayer";
}
require_once("../html/footer.php"); ?>
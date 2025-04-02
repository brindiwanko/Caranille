<?php require_once("../html/header.php");

if (isset($_POST['databaseName'])
&& isset($_POST['databaseHost'])
&& isset($_POST['databaseUser'])
&& isset($_POST['databasePassword'])
&& isset($_POST['databasePort']))
{
	//Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
		//Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = uniqid();
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['databasePort'])
        && $_POST['databasePort'] >= 0)
        {
			//On récupère les valeurs du formulaire dans une variable
		    $databaseName = htmlspecialchars(addslashes($_POST['databaseName']));
		    $databaseHost = htmlspecialchars(addslashes($_POST['databaseHost']));
		    $databaseUser = htmlspecialchars(addslashes($_POST['databaseUser']));
		    $databasePassword = htmlspecialchars(addslashes($_POST['databasePassword']));
		    $databasePort = htmlspecialchars(addslashes($_POST['databasePort']));
		
		    //On créer le fichier config.php et on y écrit dedans les informations de connexion SQL
		    $openSql = fopen('../../config.php', 'w');
		    fwrite($openSql, "
		    <?php
		    //Version of Caranille
		    \$version = \"1.10.4\";
		    \$dsn = 'mysql:dbname=$databaseName;host=$databaseHost;port=$databasePort';
		    \$user = '$databaseUser';
		    \$password = '$databasePassword';
		
		    //LAUNCH THE CONNECTION
		    try 
		    {
		        \$bdd = new PDO(\$dsn, \$user, \$password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		        \$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    } 
		    catch (PDOException \$e) 
		    {		
				?>	

				<h3>Les informations de connexion à la base de données sont incorrect !</h3><br />

				<b>Caranille était en cours d'installation ?</b><br /><br />
				
				Supprimer le fichier \"config.php\" dans le dossier \"kernel\" du site et retournez sur la page d'accueil du site

				<hr>

				<b>Caranille est déjà installé ?</b><br /><br />

				Vérifiez le fichier \"config.php\" dans le dossier \"kernel\" du site et retournez sur la page d'accueil du site

				<?php

				exit();
		    }
		    ?>");
		    fclose($openSql);
		
		    //On inclue le fichier précédement crée
		    include("../../config.php");
		
		    $bdd->query(file_get_contents('../sql/ddb.sql'));
		    ?>

			<p><h4>Etape 3/4 - Choix d'installation</h4></p>

		    <form method="POST" action="step-4.php">
				Type d'installation : <select class="form-control" name="installationType">
					<option value="fullInstall">Complète (avec exemples)</option>
					<option value="minInstall">Minimale (sans exemples)</option>
				</select>
				<input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
		        <input type="submit" class="btn btn-secondary btn-lg" name="continuer" value="Continuer">
		    </form>
		    
		    <?php
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
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../html/footer.php"); ?>
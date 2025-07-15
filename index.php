<?php
$config = 'kernel/config.php';

//Si le fichier existe on redirige le joueur vers le jeu
if (file_exists($config)) 
{
	header("Location: modules/main/index.php");
	exit();
}
//Sinon on redirige l'utilisateur vers l'accueil du site
else
{
	header("Location: kernel/install/modules/step-1.php");
	exit();
}
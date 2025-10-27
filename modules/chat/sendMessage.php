<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

//Si les variables $_POST suivantes existent
if (isset($_POST['chatMessage'])
&& isset($_POST['token'])
&& isset($_POST['sendMessage']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
	    //On récupère les informations du formulaire
	    $chatMessage = htmlspecialchars($_POST['chatMessage']);
	    
	    //On définit une date pour le message
	    $date = date('Y-m-d H:i:s');
	    
	    //On ajoute le message dans la base de donnée
	    $addMessage = $bdd->prepare("INSERT INTO car_chat VALUES(
	    NULL,
	    :characterId,
	    :date,
	    :chatMessage)");
	    $addMessage->execute([
	    'characterId' => $characterId,
	    'date' => $date,
	    'chatMessage' => $chatMessage]);
	    $addMessage->closeCursor();
	
	    //On redirige le joueur vers le chat
	    header("Location: index.php");
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
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../../html/footer.php"); ?>
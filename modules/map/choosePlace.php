<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur est déjà dans un lieu on le redirige vers le lieu
if ($characterPlaceId >= 1) { exit(header("Location: ../../modules/place/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

//Si les variables $_POST suivantes existent
if (isset($_POST['placeId'])
&& isset($_POST['token'])
&& isset($_POST['enter']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['placeId'])
        && $_POST['placeId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $placeId = htmlspecialchars($_POST['placeId']);

            //On fait une requête pour vérifier si le joueur peut accèder à le lieu choisie
            $placeQuery = $bdd->prepare("SELECT * FROM car_places
            WHERE placeChapter <= ?
            AND placeId = ?");
            $placeQuery->execute([$characterChapter, $placeId]);
            $placeRow = $placeQuery->rowCount();

            //Si le lieu existe pour le joueur
            if ($placeRow >= 1) 
            {
            	while ($placeList = $placeQuery->fetch())
            	{
	                //on récupère les valeurs de chaque lieux qu'on va ensuite mettre dans le menu déroulant
	                $placeId = $placeList['placeId']; 
	                $placeName = $placeList['placeName'];
	                $placeChapter = $placeList['placeChapter'];
	                $placeAccess = $placeList['placeAccess'];
            	}
            	//On vérifie si le lieu est accessible
            	if ($placeAccess == "Yes")
            	{
            		//On met le personnage à jour
	                $updatecharacter = $bdd->prepare("UPDATE car_characters SET
	                characterPlaceId = :characterPlaceId
	                WHERE characterId = :characterId");
	                $updatecharacter->execute(array(
	                'characterPlaceId' => $placeId, 
	                'characterId' => $characterId));
	                $updatecharacter->closeCursor();
	
	                header("Location: ../../modules/place/index.php");
            	}
            	//Si le lieu n'est pas accessible
            	else
            	{
            		//On vérifie si le jour est administrateur si oui il peut rentrer
            		if ($accountAccess == 2)
            		{
            			//On met le personnage à jour
		                $updatecharacter = $bdd->prepare("UPDATE car_characters SET
		                characterPlaceId = :characterPlaceId
		                WHERE characterId = :characterId");
		                $updatecharacter->execute(array(
		                'characterPlaceId' => $placeId, 
		                'characterId' => $characterId));
		                $updatecharacter->closeCursor();
		
		                header("Location: ../../modules/place/index.php");
            		}
            		//Sinon il peut pas rentrer
            		else
            		{
            			?>
            			
            			Ce lieu est actuellement inaccessible
            			
            			<hr>
	    
						<form method="POST" action="index.php">
						    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
						</form>
						
						<?php
            		}
            	}
                
            }
            //Si le lieu n'exite pas pour le joueur on le prévient
            else
            {
                echo "le lieu choisie est invalide";
            }
            $placeQuery->closeCursor();
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

require_once("../../html/footer.php"); ?>
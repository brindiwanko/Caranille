<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'est pas dans un lieu on le redirige vers la carte du monde
if ($characterPlaceId == 0) { exit(header("Location: ../../modules/map/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['sleep']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //Si le personnage a assez d'argent pour se soigner
        if ($characterGold >= $placePriceInn) 
        {
            $updateAccount = $bdd->prepare("UPDATE car_characters
            SET characterGold = characterGold - :placePriceInn,
            characterHpMin = characterHpTotal,
            characterMpMin = characterMpTotal
            WHERE characterId = :characterId");
            $updateAccount->execute([
            'placePriceInn' => $placePriceInn,
            'characterId' => $characterId]);
            $updateAccount->closeCursor();        
            ?>
			
            Votre personnage à récupéré toutes ses forces !
    
            <hr>
            
            <form method="POST" action="../../modules/place/index.php">
                <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
            </form>
            
            <?php
        }
        //Si le personnage n'a pas assez d'argent pour se soigner
        else
        {
            ?>
            
            Vous n'avez pas assez d'argent
            
            <hr>
            
            <form method="POST" action="../../modules/place/index.php">
                <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
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
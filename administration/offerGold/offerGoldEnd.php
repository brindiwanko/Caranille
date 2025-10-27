<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminCharacterId'])
&& isset($_POST['token'])
&& isset($_POST['adminOfferGold']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminCharacterId'])
        && ctype_digit($_POST['adminOfferGold'])
        && $_POST['adminCharacterId'] >= 0
        && $_POST['adminOfferGold'] >= 0)
        {
            //On récupère les informations du formulaire précédent
            $adminCharacterId = htmlspecialchars($_POST['adminCharacterId']);
            $adminOfferGold = htmlspecialchars($_POST['adminOfferGold']);
            
            //Si l'experience à offrir est pour tous les joueurs
            if ($adminCharacterId == 0)
            {
                //On fait une requêtes pour récupérer chaque personnage
                $characterQuery = $bdd->query("SELECT * FROM car_characters
                ORDER by characterName");
                
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($character = $characterQuery->fetch())
                {
                    //On récupère l'id et le nom du personnage
                    $adminCharacterId = $character['characterId'];
                    $adminCharacterName =  $character['characterName'];
                    
                    //On ajoute les pièces d'or au personnage
                    $updateCharacter = $bdd->prepare("UPDATE car_characters SET
                    characterGold = characterGold + :adminOfferGold
                    WHERE characterId = :adminCharacterId");
                    $updateCharacter->execute(array(
                    'adminOfferGold' => $adminOfferGold,
                    'adminCharacterId' => $adminCharacterId));
                    $updateCharacter->closeCursor();
                }
                $characterQuery->closeCursor();
                ?>

                Vous venez d'offrir <em><?php echo $adminOfferGold ?></em> pièce(s) d'or à tous les joueurs</em>.<br />
                
                <hr>
                
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                    
                <?php
            }
            //Si l'experience à offrir est pour un seul joueur
            else
            {
                //On fait une requête pour vérifier si le personnage existe
                $characterQuery = $bdd->prepare("SELECT * FROM car_characters 
                WHERE characterId = ?");
                $characterQuery->execute([$adminCharacterId]);
                $characterRow = $characterQuery->rowCount();
        
                //Si le personnage existe
                if ($characterRow == 1)
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($character = $characterQuery->fetch())
                    {
                        $adminCharacterName = $character['characterName'];
                    }
                    
                    //On ajoute les pièces d'or au personnage
                    $updateCharacter = $bdd->prepare("UPDATE car_characters SET
                    characterGold = characterGold + :adminOfferGold
                    WHERE characterId = :adminCharacterId");
                    $updateCharacter->execute(array(
                    'adminOfferGold' => $adminOfferGold,
                    'adminCharacterId' => $adminCharacterId));
                    $updateCharacter->closeCursor();
                    ?>
                    
                    Vous venez d'offrir <em><?php echo $adminOfferGold ?></em> pièce(s) d'or au joueur <em><?php echo $adminCharacterName ?></em>
                    
                    <hr>
        
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
                }
                //Si le compte n'existe pas
                else
                {
                    echo "Erreur : Ce compte n'existe pas";
                }
                $accountQuery->closeCursor();
            }
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
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
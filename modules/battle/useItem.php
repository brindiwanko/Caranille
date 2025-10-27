<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il n'y a actuellement pas de combat on redirige le joueur vers l'accueil
if ($battleRow == 0) { exit(header("Location: ../../modules/main/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['useItem']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if(ctype_digit($_POST['itemId'])
        && $_POST['itemId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $itemId = htmlspecialchars($_POST['itemId']);
            
            //On fait une requête pour vérifier si l'objet choisit existe
            $itemQuery = $bdd->prepare("SELECT * FROM car_items 
            WHERE itemId = ?");
            $itemQuery->execute([$itemId]);
            $itemRow = $itemQuery->rowCount();
    
            //Si l'objet existe
            if ($itemRow == 1) 
            {
                //On cherche à savoir si l'objet que le joueur va utiliser lui appartient bien
                $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                WHERE itemId = inventoryItemId
                AND inventoryCharacterId = ?
                AND itemId = ?");
                $itemQuery->execute([$characterId, $itemId]);
                $itemRow = $itemQuery->rowCount();
        
                //Si le personne possède cet objet
                if ($itemRow == 1) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($item = $itemQuery->fetch())
                    {
                        //On récupère les informations de l'objet
                        $inventoryId = $item['inventoryId'];
                        $itemId = $item['itemId'];
                        $itemName = $item['itemName'];
                        $itemDescription = $item['itemDescription'];
                        $itemQuantity = $item['inventoryQuantity'];
                        $itemHpEffect = $item['itemHpEffect'];
                        $itemMpEffect = $item['itemMpEffect'];
                    }
                    $itemQuery->closeCursor();
                    
                    echo "$characterName vient d'utiliser l'objet $itemName<br />";
                    echo "+ $itemHpEffect HP<br />";
                    echo "+ $itemMpEffect MP<br /><br />";
                
                    //On met à jour les HP et MP du joueur
                    $characterHpMin = $characterHpMin + $itemHpEffect;
                    $characterMpMin = $characterMpMin + $itemMpEffect;
                    
                    //Si les HP Minimum sont supérieur au HP Maximum
                    if ($characterHpMin > $characterHpTotal)
                    {
                        //Si c'est le cas $characterHpMin = $characterHpTotal
                        $characterHpMin = $characterHpTotal;
                    }
                    
                    //Si les MP Minimum sont supérieur au MP Maximum
                    if ($characterMpMin > $characterMpTotal)
                    {
                        //Si c'est le cas $characterMpMin = $characterMpTotal
                        $characterMpMin = $characterMpTotal;
                    }
                
                    //Si l'adversaire à plus de puissance physique ou autant que de magique il fera une attaque physique
                    if ($opponentStrength >= $opponentMagic)
                    {
                        //On calcule les dégats de l'adversaire
                        $totalDamagesOpponent = $opponentStrength - $characterDefenseTotal;

                        echo "$opponentName lance une attaque physique<br />";
                    }
                    //Sinon il fera une attaque magique
                    else
                    {
                        //On vérifie si l'adversaire a suffisament de MP pour faire une attaque magique
                        $mpNeed = round($opponentMagic / 10);
                        if ($battleOpponentMpRemaining >= $mpNeed)
                        {
                            //On calcule les dégats de l'adversaire
                            $totalDamagesOpponent = $opponentMagic * 2 - $characterDefenseMagic;

                            //On met les MP de l'adversaire à jour
                            $battleOpponentMpRemaining = $battleOpponentMpRemaining - $mpNeed;

                            echo "$opponentName lance une attaque magique<br />";
                        }
                        //Si l'adversaire n'a pas assez de MP pour faire une attaque magique il fera une attaque physique
                        else
                        {
                            //On calcule les dégats de l'adversaire
                            $totalDamagesOpponent = $opponentStrength - $characterDefenseTotal;

                            echo "$opponentName n'a plus assez de MP, il lance une attaque physique<br />";
                        }
                    }
                
                    //Si l'adversaire a fait des dégats négatif ont bloque à zéro pour ne pas soigner le personnage (Car moins et moins fait plus)
                    if ($totalDamagesOpponent < 0)
                    {
                        $totalDamagesOpponent = 0;
                    }
                
                    //On vérifie si le joueur esquive l'attaque de l'adversaire
                    if ($characterAgilityTotal > $opponentAgility)
                    {
                        $totalDifference = $characterAgilityTotal - $opponentAgility;
                        $percentage = $totalDifference/$characterAgilityTotal * 100;
                
                        //Si la différence est de plus de 50% on bloque pour ne pas rendre le joueur intouchable
                        if ($percentage > 50)
                        {
                            $percentage = 50;
                        }
                
                        //On génère un nombre entre 0 et 100 (inclus)
                        $result = mt_rand(0, 101);
                
                        //Si le nombre généré est inférieur ou égal le joueur esquive l'attaque, on met donc $totalDamagesOpponent à 0
                        if ($result <= $percentage)
                        {
                            $totalDamagesOpponent = 0;
                            echo "$characterName a esquivé l'attaque de $opponentName<br />";
                        }
                        //Sinon le joueur subit l'attaque
                        else
                        {
                            echo "$opponentName a fait $totalDamagesOpponent point(s) de dégat à $characterName<br />";
                        }
                    }
                    //Si le joueur a moins d'agilité que l'adversaire il subit l'attaque
                    else
                    {
                        echo "$opponentName a fait $totalDamagesOpponent point(s) de dégat à $characterName<br />";
                    }
                    
                    //On met à jour la vie du joueur et de l'adversaire
                    $battleOpponentHpRemaining = $battleOpponentHpRemaining;
                    $characterHpMin = $characterHpMin - $totalDamagesOpponent;
                
                    //On met le personnage à jour dans la base de donnée
                    $updateCharacter = $bdd->prepare("UPDATE car_characters
                    SET characterHpMin = :characterHpMin,
                    characterMpMin = :characterMpMin
                    WHERE characterId = :characterId");
                    $updateCharacter->execute([
                    'characterHpMin' => $characterHpMin,
                    'characterMpMin' => $characterMpMin,
                    'characterId' => $characterId]);
                    $updateCharacter->closeCursor();
                    
                    //Si le joueur possède plusieurs exemplaire de l'objet utilisé
                    if ($itemQuantity > 1)
                    {
                        //On met l'inventaire à jour
                        $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                        inventoryQuantity = inventoryQuantity - 1
                        WHERE inventoryId = :inventoryId");
                        $updateInventory->execute(array(
                        'inventoryId' => $inventoryId));
                        $updateInventory->closeCursor();
                    }
                    //Si le joueur possède l'objet utilisé en un seul exemplaire
                    else
                    {
                        //On supprime l'objet de l'inventaire
                        $updateInventory = $bdd->prepare("DELETE FROM car_inventory
                        WHERE inventoryId = :inventoryId");
                        $updateInventory->execute(array(
                        'inventoryId' => $inventoryId));
                        $updateInventory->closeCursor();
                    }
                
                    //On met l'adversaire à jour dans la base de donnée
                    $updateBattle = $bdd->prepare("UPDATE car_battles
                    SET battleOpponentHpRemaining = :battleOpponentHpRemaining,
                    battleOpponentMpRemaining = :battleOpponentMpRemaining
                    WHERE battleId = :battleId");
                    $updateBattle->execute([
                    'battleOpponentHpRemaining' => $battleOpponentHpRemaining,
                    'battleOpponentMpRemaining' => $battleOpponentMpRemaining,
                    'battleId' => $battleId]);
                    $updateBattle->closeCursor();
                    
                    //Si le joueur ou l'adversaire a moins ou a zéro HP on redirige le joueur vers la page des récompenses
                    if ($characterHpMin <= 0 || $battleOpponentHpRemaining <= 0)
                    {
                        ?>
                                    
                        <hr>
                        
                        <form method="POST" action="rewards.php">
                            <input type="submit" class="btn btn-secondary btn-lg" name="escape" value="Continuer"><br />
                        </form>
                        
                        <?php
                    }
                
                    //Si l'adversaire et le joueur ont plus de zéro HP on continue le combat
                    if ($battleOpponentHpRemaining > 0 && $characterHpMin > 0 )
                    {
                        ?>
                                
                        <hr>
                
                        <form method="POST" action="index.php">
                            <input type="submit" class="btn btn-secondary btn-lg" name="magic" value="Continuer"><br>
                        </form>
                        
                        <?php
                    }
                }
                else
                {
                    echo "Erreur : Impossible d'utiliser un objet que vous ne possédez pas.";
                }
            }
            //Si l'objet n'existe pas
            else
            {
                echo "Erreur : Objet indisponible";
            }
            $itemQuery->closeCursor();
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
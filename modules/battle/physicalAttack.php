<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il n'y a actuellement pas de combat on redirige le joueur vers l'accueil
if ($battleRow == 0) { exit(header("Location: ../../modules/main/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['attack']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
    
        //On calcule les dégats du joueur
        $totalDamagesCharacter = $characterStrengthTotal - $opponentDefense;

        //Si le joueur a fait des dégats négatif ont bloque à zéro pour ne pas soigner l'adversaire
        if ($totalDamagesCharacter < 0)
        {
            $totalDamagesCharacter = 0;
        }

        echo "$characterName lance une attaque physique<br />";

        //On vérifie si l'adversaire esquive l'attaque du joueur
        if ($opponentAgility > $characterAgilityTotal)
        {
            $totalDifference = $opponentAgility - $characterAgilityTotal;
            $percentage = $totalDifference/$opponentAgility * 100;
    
            //Si la différence est de plus de 50% on bloque pour ne pas rendre l'adversaire intouchable
            if ($percentage > 50)
            {
                $percentage = 50;
            }
    
            //On génère un nombre entre 0 et 100 (inclus)
            $result = mt_rand(0, 101);
    
            //Si le nombre généré est inférieur ou égal l'adversaire esquive l'attaque, on met donc $totalDamagesCharacter à 0
            if ($result <= $percentage)
            {
                $totalDamagesCharacter = 0;
                echo "$opponentName a esquivé l'attaque de $characterName<br /><br />";
            }
            //Sinon l'adversaire subit l'attaque
            else
            {
                echo "$characterName a infligé $totalDamagesCharacter point(s) de dégat à $opponentName<br /><br />";
            }
        }
        //Si l'adversaire a moins d'agilité que le joueur il subit l'attaque
        else
        {
            echo "$characterName a infligé $totalDamagesCharacter point(s) de dégat à $opponentName<br /><br />";
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
    
        //Si l'adversaire a fait des dégats négatif ont bloque à zéro pour ne pas soigner le personnage
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
                echo "$characterName a esquivé l'attaque de $opponentName<br /><br />";
            }
            //Sinon le joueur subit l'attaque
            else
            {
                echo "$opponentName a infligé $totalDamagesOpponent point(s) de dégat à $characterName<br /><br />";
            }
        }
        //Si le joueur a moins d'agilité que l'adversaire il subit l'attaque
        else
        {
            echo "$opponentName a infligé $totalDamagesOpponent point(s) de dégat à $characterName<br /><br />";
        }
        
        //On met à jour la vie du joueur et de l'adversaire
        $battleOpponentHpRemaining = $battleOpponentHpRemaining - $totalDamagesCharacter;
        $characterHpMin = $characterHpMin - $totalDamagesOpponent;
    
        //On met le personnage à jour dans la base de donnée
        $updateCharacter = $bdd->prepare("UPDATE car_characters
        SET characterHpMin = :characterHpMin
        WHERE characterId = :characterId");
        $updateCharacter->execute([
        'characterHpMin' => $characterHpMin,
        'characterId' => $characterId]);
        $updateCharacter->closeCursor();
    
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
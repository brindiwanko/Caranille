<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'est pas dans un lieu on le redirige vers la carte du monde
if ($characterPlaceId == 0) { exit(header("Location: ../../modules/map/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['battleMonsterId']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['battleMonsterId'])
        && $_POST['battleMonsterId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $opponentId = htmlspecialchars($_POST['battleMonsterId']);
    
            //On fait une requête pour vérifier si le monstre est bien disponible dans le lieu du joueur
            $opponentQuery = $bdd->prepare("SELECT * FROM car_monsters, car_places, car_places_monsters
            WHERE placeMonsterMonsterId = monsterId
            AND placeMonsterPlaceId = placeId
            AND monsterId = ?
            AND placeId = ?");
            $opponentQuery->execute([$opponentId, $placeId]);
            $opponentRow = $opponentQuery->rowCount();
    
            //Si le monstre existe
            if ($opponentRow == 1) 
            {
                while ($opponent = $opponentQuery->fetch())
                {
                    //On récupère les informations du monstre
                    $opponentHp = $opponent['monsterHp'];
                    $opponentMp = $opponent['monsterMp'];
                    $monsterLimited = $opponent['monsterLimited'];
                    $monsterQuantity = $opponent['monsterQuantity'];
                }
                $opponentQuery->closeCursor();

                //Si le monstre n'est pas limité on lance le combat
                if ($monsterLimited == "No")
                {
                    //Insertion du combat dans la base de donnée avec les données
                    $addBattle = $bdd->prepare("INSERT INTO car_battles VALUES(
                    NULL,
                    :characterId,
                    :opponentId,
                    'Dungeon',
                    :opponentHp,
                    :opponentMp)");
                    $addBattle->execute([
                    'characterId' => $characterId,
                    'opponentId' => $opponentId,
                    'opponentHp' => $opponentHp,
                    'opponentMp' => $opponentMp]);
                    $addBattle->closeCursor();

                    //On met à jour les stats du monstre
                    $updateMonsterStats = $bdd->prepare("UPDATE car_monsters 
                    SET monsterQuantityBattle = monsterQuantityBattle + 1
                    WHERE monsterId = :opponentId");
                    $updateMonsterStats->execute(['opponentId' => $opponentId]);
                    $updateMonsterStats->closeCursor();  
                    
                    //On définit une date
                    $date = date('Y-m-d H:i:s');
    
                    //Insertion des stats du combat dans la base de donnée avec les données
                    $addBattleStats = $bdd->prepare("INSERT INTO car_monsters_battles_stats VALUES(
                    NULL,
                    :monsterBattleStatsMonsterId,
                    :monsterBattleStatsCharacterId,
                    'LaunchBattle',
                    :monsterBattleStatsDateTime)");
                    $addBattleStats->execute([
                    'monsterBattleStatsMonsterId' => $opponentId,
                    'monsterBattleStatsCharacterId' => $characterId,
                    'monsterBattleStatsDateTime' => $date]);
                    $addBattleStats->closeCursor();

                    //On redirige le joueur vers le combat
                    header("Location: ../../modules/battle/index.php");
                }
                //Si le monstre est limité
                else
                {
                    //On vérifie si il en reste et si c'est le cas on lance le combat
                    if ($monsterQuantity > 0)
                    {
                        //Insertion du combat dans la base de donnée avec les données
                        $addBattle = $bdd->prepare("INSERT INTO car_battles VALUES(
                        NULL,
                        :characterId,
                        :opponentId,
                        'Dungeon',
                        :opponentHp,
                        :opponentMp)");
                        $addBattle->execute([
                        'characterId' => $characterId,
                        'opponentId' => $opponentId,
                        'opponentHp' => $opponentHp,
                        'opponentMp' => $opponentMp]);
                        $addBattle->closeCursor();

                        //On met le monstre à jour dans la base de donnée
                        $updateMonster = $bdd->prepare("UPDATE car_monsters 
                        SET monsterQuantity = monsterQuantity - 1
                        WHERE monsterId = :opponentId");
                        $updateMonster->execute([
                        'opponentId' => $opponentId]);
                        $updateMonster->closeCursor();

                        //On redirige le joueur vers le combat
                        header("Location: ../../modules/battle/index.php");
                    }
                    //Sinon on prévient le joueur
                    else
                    {
                        ?>

                        Ce monstre n'est plus disponible !

                        <form method="POST" action="index.php">
                            <input type="submit" name="back" class="btn btn-secondary btn-lg" value="Retour"><br />
                        </form>

                        <?php
                    }
                }                
            }
            //Si le monstre n'exite pas
            else
            {
                echo "Erreur : Monstre indisponible";
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
    echo "Erreur : Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>
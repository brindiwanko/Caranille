<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

//Si les variables $_POST suivantes existent
if (isset($_POST['continue']))
{
    //On vérifie si le chapitre existe
    $chapterQuery = $bdd->prepare("SELECT * FROM car_chapters
    WHERE chapterId = ?");
    $chapterQuery->execute([$characterChapter]);
    $chapterRow = $chapterQuery->rowCount();
    
    //Si le chapitre existe
    if ($chapterRow == 1)
    {
        //On fait une requête pour récupérer le monstre du chapitre
        $opponentQuery = $bdd->prepare("SELECT * FROM car_monsters, car_chapters
        WHERE monsterId = chapterMonsterId
        AND chapterId = ?");
        $opponentQuery->execute([$characterChapter]);
        $opponentRow = $opponentQuery->rowCount();

        //Si le monstre existe
        if ($opponentRow == 1) 
        {
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($opponent = $opponentQuery->fetch())
            {
                //On récupère les informations du monstre
                $opponentId = $opponent['monsterId'];
                $opponentHp = $opponent['monsterHp'];
                $opponentMp = $opponent['monsterMp'];
            }

            //Insertion du combat dans la base de donnée avec les données
            $addBattle = $bdd->prepare("INSERT INTO car_battles VALUES(
            NULL,
            :characterId,
            :opponentId,
            'Story',
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

            //On redirige le joueur vers le combat du monstre
            header("Location: ../../modules/battle/index.php");
        }
        //Si le monstre n'exite pas
        else
        {
            echo "Erreur : Ce monstre n'existe pas";
        }
    }
    //Si le chapitre n'exite pas
    {
        echo "Erreur : Le chapitre demandé n'existe pas";
    }
    $opponentQuery->closeCursor(); 
}
//Si toutes les variables $_POST n'existent pas
else
{
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../../html/footer.php"); ?>
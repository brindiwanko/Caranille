<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il n'y a actuellement pas de combat on redirige le joueur vers l'accueil
if ($battleRow == 0) { exit(header("Location: ../../modules/main/index.php")); }

require_once("../../html/header.php");

//Si le monstre et le joueur on 0 HP
if ($battleOpponentHpRemaining <= 0 && $characterHpMin <= 0)
{
    //On prévient le joueur qu'il y a un match nul
    ?>
    
    <p>Match Nul !</p>
    
    <?php
    
    //On soigne le personnage et ont le met à jour dans la base de donnée
    $updateCharacter = $bdd->prepare("UPDATE car_characters
    SET characterHpMin = characterHpTotal,
    characterMpMin = characterMpTotal
    WHERE characterId = :characterId");
    $updateCharacter->execute([
    'characterId' => $characterId]);
    $updateCharacter->closeCursor();

    //On détruit le combat en cours
    $deleteBattle = $bdd->prepare("DELETE FROM car_battles 
    WHERE battleId = :battleId");
    $deleteBattle->execute(array('battleId' => $battleId));
    $deleteBattle->closeCursor();

    //Si il ne s'agit pas d'un combat d'arène on met à jour les stats du combat
    if ($battleType != "Arena")
    {
        //On met à jour les stats du monstre
        $updateMonsterStats = $bdd->prepare("UPDATE car_monsters 
        SET monsterQuantityDraw = monsterQuantityDraw + 1
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
        'DrawBattle',
        :monsterBattleStatsDateTime)");
        $addBattleStats->execute([
        'monsterBattleStatsMonsterId' => $opponentId,
        'monsterBattleStatsCharacterId' => $characterId,
        'monsterBattleStatsDateTime' => $date]);
        $addBattleStats->closeCursor();
    }
    ?>

    <hr>

    <form method="POST" action="../../modules/dungeon/index.php">
        <input type="submit" name="escape" class="btn btn-secondary btn-lg" value="Continuer"><br />
    </form>
    
    <?php
}

//Si le monstre a moins ou a zéro HP et que le joueur à plus de 0 hp
if ($battleOpponentHpRemaining <= 0 && $characterHpMin > 0)
{
    //Si il ne s'agit pas d'un combat d'arène on met à jour les stats du combat
    if ($battleType != "Arena")
    {
        //On calcule l'expérience bonus que le joueur va recevoir en fonction de sa sagesse
        $wisdomBonus = round($opponentExperience * $characterWisdomTotal / 100);
        //On l'ajoute à l'experience de base du monstre
        $opponentExperience = $opponentExperience + $wisdomBonus;
        //On calcul le bonus d'expérience du jeu
        $experienceBonus = round($opponentExperience * $gameExperienceBonus / 100);
        //On l'ajoute à l'expérience de base du monstre
        $opponentExperience = $opponentExperience + $experienceBonus;

        //On calcule l'argent bonus que le joueur va recevoir en fonction de sa prospection
        $prospectingBonus = round($opponentGold * $characterProspectingTotal / 100);
        //On l'ajoute à l'argent de base du monstre
        $opponentGold = $opponentGold + $prospectingBonus;
        //On calcul le bonus d'expérience du jeu
        $gameGoldBonus = round($opponentGold * $gameGoldBonus / 100);
        //On l'ajoute à l'argent de base du monstre
        $opponentGold = $opponentGold + $gameGoldBonus;
    }
    ?>
    
    <p><?php echo $characterName; ?> remporte le combat !</p>
    Récompenses : <br />
    
    <?php
    //S'il s'agit d'un combat de Donjon, de mission ou d'histoire
    if ($battleType == "Dungeon"
    || $battleType == "Mission"
    || $battleType == "Story"
    || $battleType == "battleInvitation")
    {
        ?>

        -<?php echo $opponentExperience; ?> point(s) d'experience<br />
        -<?php echo $opponentGold; ?> pièce(s) d'or<br />
        
        <?php
    
        //On recherche dans la base de donnée les objets et équipements que ce monstre peut faire gagner
        $opponentDropQuery = $bdd->prepare("SELECT * FROM car_monsters, car_items, car_monsters_drops
        WHERE monsterDropMonsterId = monsterId
        AND monsterDropItemId = itemId
        AND monsterDropMonsterId = ?");
        $opponentDropQuery->execute([$opponentId]);
        $opponentDropRow = $opponentDropQuery->rowCount();
    
        //S'il existe un ou plusieurs objet pour ce monstre
        if ($opponentDropRow > 0) 
        {
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($opponentDrop = $opponentDropQuery->fetch())
            {
                //On récupère les informations de l'objet
                $opponentDropItemId = $opponentDrop['itemId'];
                $opponentDropItemName = $opponentDrop['itemName'];
                $opponentDropRate = $opponentDrop['monsterDropRate'];
                
                //On calcul le bonus de drop en fonction de la prospection du joueur
                $prospectingDropRateBonus = round($characterProspectingTotal * $characterProspectingTotal / 100);
                //On ajoute le bonus de drop du jeu
                $opponentDropRate = $opponentDropRate + $prospectingDropRateBonus;
                //On calcul le bonus de drop
                $GameDropRateBonus = round($opponentDropRate * $gameDropBonus / 100);
                //On ajoute le bonus de drop du jeu
                $opponentDropRate = $opponentDropRate + $GameDropRateBonus;
    
                //On génère un nombre entre 0 et 1001 (Pour que 1000 puisse aussi être choisi)
                $numberRandom = mt_rand(0, 1001);
                
                //Si le nombre obtenu est inférieur ou égal à l'objet le joueur le gagne
                if ($numberRandom <= $opponentDropRate)
                {
                    //On vérifie si le joueur possède déjà cet objet ou équipement
                    $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                    WHERE itemId = inventoryItemId
                    AND inventoryCharacterId = ?
                    AND itemId = ?");
                    $itemQuery->execute([$characterId, $opponentDropItemId]);
                    $itemRow = $itemQuery->rowCount();
    
                    //Si le joueur possède déjà cet objet ou équipement on modifie les quantités de celui-ci
                    if ($itemRow > 0)
                    {
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($item = $itemQuery->fetch())
                        {
                            //On récupère les informations de l'inventaire
                            $inventoryId = $item['inventoryId'];
                            $itemQuantity = $item['inventoryQuantity'];
                            $inventoryEquipped = $item['inventoryEquipped'];
                        }

                        //On met l'inventaire à jour
                        $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                        inventoryQuantity = inventoryQuantity + 1
                        WHERE inventoryId = :inventoryId");
                        $updateInventory->execute(array(
                        'inventoryId' => $inventoryId));
                        $updateInventory->closeCursor(); 
                    }
                    //Si le joueur ne possède pas cet objet on l'ajoute dans l'inventaire
                    else
                    {
                        //On ajoute l'objet dans la base de donnée
                        $addItem = $bdd->prepare("INSERT INTO car_inventory VALUES(
                        NULL,
                        :characterId,
                        :opponentDropItemId,
                        '1',
                        '0')");
                        $addItem->execute([
                        'characterId' => $characterId,
                        'opponentDropItemId' => $opponentDropItemId]);
                        $addItem->closeCursor(); 
                    }
                    $itemQuery->closeCursor();
                    ?>
                    
                    -1 <?php echo "$opponentDropItemName<br />" ?>
                    
                    <?php
                }
            }
        }
        $opponentDropQuery->closeCursor();

        //S'il s'agit d'un combat d'histoire
        if ($battleType == "Story")
        {
            ?>
            
            <hr>
        
            <?php
            //On récupère la fin du chapitre en cours
            $chapterQuery = $bdd->prepare("SELECT * FROM car_chapters
            WHERE chapterId = ?");
            $chapterQuery->execute([$characterChapter]);
            $chapterRow = $chapterQuery->rowCount();
            
            //Si le chapitre du joueur existe
            if ($chapterRow == 1)
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            	while ($chapter = $chapterQuery->fetch())
            	{
            	    //On récupère les informations du chapitre
            		$chapterEnding = nl2br(htmlspecialchars($chapter['chapterEnding']));
            	}
            	$chapterQuery->closeCursor();
            	
                //On affiche la fin du chapitre
                echo "$chapterEnding";
                
                //On fait passer le joueur au chapitre suivant
                $updateCharacterChapter = $bdd->prepare("UPDATE car_characters 
                SET characterChapter = characterChapter + 1
                WHERE characterId = :characterId");
                $updateCharacterChapter->execute(['characterId' => $characterId]);
                $updateCharacterChapter->closeCursor();  
            }
            //Si le chapitre n'existe pas
            else 
            {
            	echo "Il n'y a actuellement aucun nouveau chapitre";
            }
            $updateCharacterChapter->closeCursor();
        }

        //Si il ne reste qu'un niveau à monte
        if ($characterLevel == $gameMaxLevel -1 )
        {
            //Si le monstre donne plus d'expérience que ce qu'il reste pour monter le dernier niveau
            if ($opponentExperience > $experienceRemaining)
            {
                //On bride les gains d'experience en ajoutant que ce qu'il manque ou en donnant zéro
                $opponentExperience = $experienceRemaining;
            }
        }

        //Si le joueur est au niveau maximum
        if ($characterLevel == $gameMaxLevel)
        {
            //On bride les gains d'experience en ajoutant que ce qu'il manque ou en donnant zéro
            $opponentExperience = 0;
        }

        //On rajoute l'expérience ainsi que l'argent au joueur
        $updateCharacter = $bdd->prepare("UPDATE car_characters
        SET characterExperience = characterExperience + :opponentExperience,
        characterExperienceTotal = characterExperienceTotal + :opponentExperience,
        characterGold = characterGold + :opponentGold
        WHERE characterId = :characterId");
        $updateCharacter->execute([
        'opponentExperience' => $opponentExperience,
        'opponentGold' => $opponentGold,
        'characterId' => $characterId]);
        $updateCharacter->closeCursor();

        //On vérifie si ce monstre est déjà dans le bestiaire du joueur
        $bestiaryQuery = $bdd->prepare("SELECT * FROM car_bestiary
        WHERE bestiaryCharacterId = ?
        AND bestiaryMonsterId = ?");
        $bestiaryQuery->execute([$characterId, $opponentId]);
        $bestiaryRow = $bestiaryQuery->rowCount();
        
        //Si le monstre est déjà dans le bestiaire du joueur
        if ($bestiaryRow > 0)
        {
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
        	while ($bestiary = $bestiaryQuery->fetch())
        	{
        	    //On récupère les informations du bestiaire
        		$bestiaryId = $bestiary['bestiaryId'];
        	}
            //On met à jour le bestiaire du joueur
            $updateBestiary = $bdd->prepare("UPDATE car_bestiary 
            SET bestiaryMonsterQuantity = bestiaryMonsterQuantity + 1
            WHERE bestiaryId = :bestiaryId");
            $updateBestiary->execute(['bestiaryId' => $bestiaryId]);
            $updateBestiary->closeCursor();  
        }
        //Si le monstre n'est pas déjà dans le bestiaire
        else 
        {
            ?>
            
            <hr>
            
            Nouvelle entrée dans le bestiaire pour <?php echo $opponentName ?><br/>
            
            <?php
            //On l'ajoute dans le bestiaire
             $addBestiary = $bdd->prepare("INSERT INTO car_bestiary VALUES(
            NULL,
            :characterId,
            :opponentId,
            '1')");
            $addBestiary->execute([
            'characterId' => $characterId,
            'opponentId' => $opponentId]);
            $addBestiary->closeCursor(); 
        }
        $bestiaryQuery->closeCursor(); 

        //On met à jour les stats du monstre
        $updateMonsterStats = $bdd->prepare("UPDATE car_monsters 
        SET monsterQuantityVictory = monsterQuantityVictory + 1
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
        'VictoryBattle',
        :monsterBattleStatsDateTime)");
        $addBattleStats->execute([
        'monsterBattleStatsMonsterId' => $opponentId,
        'monsterBattleStatsCharacterId' => $characterId,
        'monsterBattleStatsDateTime' => $date]);
        $addBattleStats->closeCursor();
    }
    //S'il s'agit d'un combat contre un autre joueur
    else if ($battleType == "Arena")
    {
        echo "-1 point de victoire<br />";
        
        //On ajoute un point de victoire au joueur
        $updateCharacter = $bdd->prepare("UPDATE car_characters
        SET characterArenaVictory = characterArenaVictory + 1
        WHERE characterId = :characterId");
        $updateCharacter->execute([
        'characterId' => $characterId]);
        $updateCharacter->closeCursor();
    }
    
    //On met fin au combat en cours
    $deleteBattle = $bdd->prepare("DELETE FROM car_battles 
    WHERE battleId = :battleId");
    $deleteBattle->execute(array('battleId' => $battleId));
    $deleteBattle->closeCursor();
    ?>

    <hr>

    <form method="POST" action="../../modules/place/index.php">
        <input type="submit" name="escape" class="btn btn-secondary btn-lg" value="Continuer"><br />
    </form>
    
    <?php
}

//Si le joueur a moins ou a zéro HP et que le monstre à plus de 0 HP
if ($characterHpMin <= 0 && $battleOpponentHpRemaining > 0)
{
    ?>
    
    <p><?php echo $opponentName; ?> remporte le combat !</p>
    
    <?php

    //On soigne le personnage et ont le met à jour dans la base de donnée
    $updateCharacter = $bdd->prepare("UPDATE car_characters
    SET characterHpMin = characterHpTotal,
    characterMpMin = characterMpTotal
    WHERE characterId = :characterId");
    $updateCharacter->execute([
    'characterId' => $characterId]);
    $updateCharacter->closeCursor();

    //Si il s'agit d'un combat d'arène on ajoute 1 point de défaite lié à la défaite
    if ($battleType == "Arena")
    {
        //On ajoute un point de défaite au joueur
        $updateCharacter = $bdd->prepare("UPDATE car_characters
        SET characterArenaDefeate = characterArenaDefeate + 1
        WHERE characterId = :characterId");
        $updateCharacter->execute([
        'characterId' => $characterId]);
        $updateCharacter->closeCursor();
    }
    else
    {
        //On met à jour les stats du monstre
        $updateMonsterStats = $bdd->prepare("UPDATE car_monsters 
        SET monsterQuantityDefeated = monsterQuantityDefeated + 1
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
        'DefeatedBattle',
        :monsterBattleStatsDateTime)");
        $addBattleStats->execute([
        'monsterBattleStatsMonsterId' => $opponentId,
        'monsterBattleStatsCharacterId' => $characterId,
        'monsterBattleStatsDateTime' => $date]);
        $addBattleStats->closeCursor();
    }

    //On met fin au combat en cours
    $deleteBattle = $bdd->prepare("DELETE FROM car_battles 
    WHERE battleId = :battleId");
    $deleteBattle->execute(array('battleId' => $battleId));
    $deleteBattle->closeCursor();    
    ?>

    <hr>

    <form method="POST" action="../../modules/place/index.php">
        <input type="submit" name="escape" class="btn btn-secondary btn-lg" value="Continuer"><br />
    </form>
    
    <?php
}

//Si le monstre et le joueur ont plus de zéro HP le joueur n'a pas a être ici
if ($battleOpponentHpRemaining > 0 && $characterHpMin > 0 )
{
    echo "Erreur : Une erreur est survenue";
}

require_once("../../html/footer.php"); ?>
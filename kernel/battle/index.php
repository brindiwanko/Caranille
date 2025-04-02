<?php 
//On fait une requête pour vérifier S'il y a un combat en cours
$battleQuery = $bdd->prepare("SELECT * FROM car_battles
WHERE battleCharacterId = ?");
$battleQuery->execute([$characterId]);
$battleRow = $battleQuery->rowCount();

//S'il y a un combat de trouvé
if ($battleRow == 1)
{
    //On récupères les informations du combat (Id du combat, Id de l'adversaire, Hp et Mp restant de l'adversaire)
    while ($battle = $battleQuery->fetch())
    {
        //On récupère les informations du combat
        $battleId = stripslashes($battle['battleId']);
        $battleOpponentId = stripslashes($battle['battleOpponentId']);
        $battleType = stripslashes($battle['battleType']);
        $battleOpponentHpRemaining = stripslashes($battle['battleOpponentHpRemaining']);
        $battleOpponentMpRemaining = stripslashes($battle['battleOpponentMpRemaining']);
    }
    $battleQuery->closeCursor();
    
    //S'il s'agit d'un combat de Donjon, de mission ou d'histoire
    if ($battleType == "Dungeon"
    || $battleType == "Mission"
    || $battleType == "Story"
    || $battleType == "battleInvitation")
    {
        //On récupère toutes les informations du monstre que nous sommes en train de combattre
        $opponentQuery = $bdd->prepare("SELECT * FROM car_monsters 
        WHERE monsterId = ?");
        $opponentQuery->execute([$battleOpponentId]);
    
        //On fait une boucle pour récupérer les résultats
        while ($opponent = $opponentQuery->fetch())
        {
            //On récupère les informations de l'opposant
            $opponentId = stripslashes($opponent['monsterId']);
            $opponentPicture = stripslashes($opponent['monsterPicture']);
            $opponentName = stripslashes($opponent['monsterName']);
            $opponentLevel = stripslashes($opponent['monsterLevel']);
            $opponentHp = stripslashes($opponent['monsterHp']);
            $opponentMp = stripslashes($opponent['monsterMp']);
            $opponentStrength = stripslashes($opponent['monsterStrength']);
            $opponentMagic = stripslashes($opponent['monsterMagic']);
            $opponentAgility = stripslashes($opponent['monsterAgility']);
            $opponentDefense = stripslashes($opponent['monsterDefense']);
            $opponentDefenseMagic = stripslashes($opponent['monsterDefenseMagic']);
            $opponentGold = stripslashes($opponent['monsterGold']);
            $opponentExperience = stripslashes($opponent['monsterExperience']);
        }
        $opponentQuery->closeCursor();
    }
    //S'il s'agit d'un combat contre un joueur
    else if ($battleType == "Arena")
    {
        //On récupère toutes les informations du personnage que nous sommes en train de combattre
        $opponentQuery = $bdd->prepare("SELECT * FROM car_characters 
        WHERE characterId = ?");
        $opponentQuery->execute([$battleOpponentId]);
    
        //On fait une boucle pour récupérer les résultats
        while ($opponent = $opponentQuery->fetch())
        {
            //On récupère les informations de l'opposant
            $opponentId = stripslashes($opponent['characterId']);
            $opponentPicture = stripslashes($opponent['characterPicture']);
            $opponentName = stripslashes($opponent['characterName']);
            $opponentLevel = stripslashes($opponent['characterLevel']);
            $opponentHp = stripslashes($opponent['characterHpTotal']);
            $opponentMp = stripslashes($opponent['characterMpTotal']);
            $opponentStrength = stripslashes($opponent['characterStrengthTotal']);
            $opponentMagic = stripslashes($opponent['characterMagicTotal']);
            $opponentAgility = stripslashes($opponent['characterAgilityTotal']);
            $opponentDefense = stripslashes($opponent['characterDefenseTotal']);
            $opponentDefenseMagic = stripslashes($opponent['characterDefenseMagicTotal']);
        }
        $opponentQuery->closeCursor();
    }
}
?>
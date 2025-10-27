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
        $battleId = $battle['battleId'];
        $battleOpponentId = $battle['battleOpponentId'];
        $battleType = $battle['battleType'];
        $battleOpponentHpRemaining = $battle['battleOpponentHpRemaining'];
        $battleOpponentMpRemaining = $battle['battleOpponentMpRemaining'];
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
            $opponentId = $opponent['monsterId'];
            $opponentPicture = $opponent['monsterPicture'];
            $opponentName = $opponent['monsterName'];
            $opponentLevel = $opponent['monsterLevel'];
            $opponentHp = $opponent['monsterHp'];
            $opponentMp = $opponent['monsterMp'];
            $opponentStrength = $opponent['monsterStrength'];
            $opponentMagic = $opponent['monsterMagic'];
            $opponentAgility = $opponent['monsterAgility'];
            $opponentDefense = $opponent['monsterDefense'];
            $opponentDefenseMagic = $opponent['monsterDefenseMagic'];
            $opponentGold = $opponent['monsterGold'];
            $opponentExperience = $opponent['monsterExperience'];
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
            $opponentId = $opponent['characterId'];
            $opponentPicture = $opponent['characterPicture'];
            $opponentName = $opponent['characterName'];
            $opponentLevel = $opponent['characterLevel'];
            $opponentHp = $opponent['characterHpTotal'];
            $opponentMp = $opponent['characterMpTotal'];
            $opponentStrength = $opponent['characterStrengthTotal'];
            $opponentMagic = $opponent['characterMagicTotal'];
            $opponentAgility = $opponent['characterAgilityTotal'];
            $opponentDefense = $opponent['characterDefenseTotal'];
            $opponentDefenseMagic = $opponent['characterDefenseMagicTotal'];
        }
        $opponentQuery->closeCursor();
    }
}
?>
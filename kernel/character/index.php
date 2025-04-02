<?php
require_once("../../kernel/config.php");

//On fait une recherche dans la base de donnée pour récupérer le personnage du joueur
$characterQuery = $bdd->prepare("SELECT * FROM car_characters 
WHERE characterAccountId = ?");
$characterQuery->execute([$accountId]);

//On fait une boucle sur les résultats
while ($character = $characterQuery->fetch())
{
    //On récupère les informations du personnage
    $characterId = stripslashes($character['characterId']);
    $characterAccountId = stripslashes($character['characterAccountId']);
    $characterGuildId = stripslashes($character['characterGuildId']);
    $characterRaceId = stripslashes($character['characterRaceId']);
    $characterPlaceId = stripslashes($character['characterPlaceId']);
    $characterPicture = stripslashes($character['characterPicture']);
    $characterName = stripslashes($character['characterName']);
    $characterLevel = stripslashes($character['characterLevel']);
    $characterSex = stripslashes($character['characterSex']);
    $characterHpMin = stripslashes($character['characterHpMin']);
    $characterHpMax = stripslashes($character['characterHpMax']);
    $characterHpSkillPoints = stripslashes($character['characterHpSkillPoints']);
    $characterHpBonus = stripslashes($character['characterHpBonus']);
    $characterHpEquipments = stripslashes($character['characterHpEquipments']);
    $characterHpGuild = stripslashes($character['characterHpGuild']);
    $characterHpTotal = stripslashes($character['characterHpTotal']);
    $characterMpMin = stripslashes($character['characterMpMin']);
    $characterMpMax = stripslashes($character['characterMpMax']);
    $characterMpSkillPoints = stripslashes($character['characterMpSkillPoints']);
    $characterMpBonus = stripslashes($character['characterMpBonus']);
    $characterMpEquipments = stripslashes($character['characterMpEquipments']);
    $characterMpGuild = stripslashes($character['characterMpGuild']);
    $characterMpTotal = stripslashes($character['characterMpTotal']);
    $characterStrength = stripslashes($character['characterStrength']);
    $characterStrengthSkillPoints = stripslashes($character['characterStrengthSkillPoints']);
    $characterStrengthBonus = stripslashes($character['characterStrengthBonus']);
    $characterStrengthEquipments = stripslashes($character['characterStrengthEquipments']);
    $characterStrengthGuild = stripslashes($character['characterStrengthGuild']);
    $characterStrengthTotal = stripslashes($character['characterStrengthTotal']);
    $characterMagic = stripslashes($character['characterMagic']);
    $characterMagicSkillPoints = stripslashes($character['characterMagicSkillPoints']);
    $characterMagicBonus = stripslashes($character['characterMagicBonus']);
    $characterMagicEquipments = stripslashes($character['characterMagicEquipments']);
    $characterMagicGuild = stripslashes($character['characterMagicGuild']);
    $characterMagicTotal = stripslashes($character['characterMagicTotal']);
    $characterAgility = stripslashes($character['characterAgility']);
    $characterAgilitySkillPoints = stripslashes($character['characterAgilitySkillPoints']);
    $characterAgilityBonus = stripslashes($character['characterAgilityBonus']);
    $characterAgilityEquipments = stripslashes($character['characterAgilityEquipments']);
    $characterAgilityGuild = stripslashes($character['characterAgilityGuild']);
    $characterAgilityTotal = stripslashes($character['characterAgilityTotal']);
    $characterDefense = stripslashes($character['characterDefense']);
    $characterDefenseSkillPoints = stripslashes($character['characterDefenseSkillPoints']);
    $characterDefenseBonus = stripslashes($character['characterDefenseBonus']);
    $characterDefenseEquipment = stripslashes($character['characterDefenseEquipments']);
    $characterDefenseGuild = stripslashes($character['characterDefenseGuild']);
    $characterDefenseTotal = stripslashes($character['characterDefenseTotal']);
    $characterDefenseMagic = stripslashes($character['characterDefenseMagic']);
    $characterDefenseMagicSkillPoints = stripslashes($character['characterDefenseMagicSkillPoints']);
    $characterDefenseMagicBonus = stripslashes($character['characterDefenseMagicBonus']);
    $characterDefenseMagicEquipments = stripslashes($character['characterDefenseMagicEquipments']);
    $characterDefenseMagicGuild = stripslashes($character['characterDefenseMagicGuild']);
    $characterDefenseMagicTotal = stripslashes($character['characterDefenseMagicTotal']);
    $characterWisdom = stripslashes($character['characterWisdom']);
    $characterWisdomSkillPoints = stripslashes($character['characterWisdomSkillPoints']);
    $characterWisdomBonus = stripslashes($character['characterWisdomBonus']);
    $characterWisdomEquipments = stripslashes($character['characterWisdomEquipments']);
    $characterWisdomGuild = stripslashes($character['characterWisdomGuild']);
    $characterWisdomTotal = stripslashes($character['characterWisdomTotal']);
    $characterProspecting = stripslashes($character['characterProspecting']);
    $characterProspectingSkillPoints = stripslashes($character['characterProspectingSkillPoints']);
    $characterProspectingBonus = stripslashes($character['characterProspectingBonus']);
    $characterProspectingEquipments = stripslashes($character['characterProspectingEquipments']);
    $characterProspectingGuild = stripslashes($character['characterProspectingGuild']);
    $characterProspectingTotal = stripslashes($character['characterProspectingTotal']);
    $characterArenaDefeate = stripslashes($character['characterArenaDefeate']);
    $characterArenaVictory = stripslashes($character['characterArenaVictory']);
    $characterExperience = stripslashes($character['characterExperience']);
    $characterExperienceTotal = stripslashes($character['characterExperienceTotal']);
    $characterSkillPoints = stripslashes($character['characterSkillPoints']);
    $characterGold = stripslashes($character['characterGold']);
    $characterChapter = stripslashes($character['characterChapter']);
    $characterOnBattle = stripslashes($character['characterOnBattle']);
    $characterEnable = stripslashes($character['characterEnable']);
}
$characterQuery->closeCursor();

//On fait une recherche dans la base de donnée pour récupérer la race du personnage
$raceQuery = $bdd->prepare("SELECT * FROM car_races 
WHERE raceId = ?");
$raceQuery->execute([$characterRaceId]);

//On récupère les augmentations de statistique lié à la classe
while ($race = $raceQuery->fetch())
{
    //On récupère les informations de la classe
    $characterRaceName = stripslashes($race['raceName']);
    $raceHpBonus = stripslashes($race['raceHpBonus']);
    $raceMpBonus = stripslashes($race['raceMpBonus']);
    $raceStrengthBonus = stripslashes($race['raceStrengthBonus']);
    $raceMagicBonus = stripslashes($race['raceMagicBonus']);
    $raceAgilityBonus = stripslashes($race['raceAgilityBonus']);
    $raceDefenseBonus = stripslashes($race['raceDefenseBonus']);
    $raceDefenseMagicBonus = stripslashes($race['raceDefenseMagicBonus']);
    $raceWisdomBonus = stripslashes($race['raceWisdomBonus']);
    $raceProspectingBonus = stripslashes($race['raceProspectingBonus']);
}
$raceQuery->closeCursor();

//Valeurs des statistiques qui seront ajouté à la monté d'un niveau
$hPByLevel = $raceHpBonus;
$mPByLevel = $raceMpBonus;
$strengthByLevel = $raceStrengthBonus;
$magicByLevel = $raceMagicBonus;
$agilityByLevel = $raceAgilityBonus;
$defenseByLevel = $raceDefenseBonus;
$defenseMagicByLevel = $raceDefenseMagicBonus;
$wisdomByLevel = $raceWisdomBonus;
$prospectingByLevel = $raceProspectingBonus;

//Valeur des points de compétences obtenu à la monté d'un niveau ($gameSkillPoint = kernel/configuration/index.php)
$skillPointsByLevel = $gameSkillPoint;

//On créer une variable qui va s'incrémenter à chaque niveau obtenu pour savoir combien de niveau le joueur gagne en une fois
$levelUpNumber = 0;

//Si le joueur est à un niveau inférieur au maximum on vérifit si il peut monter de niveau
if ($characterLevel < $gameMaxLevel)
{
    //On calcul l'expérience nécessaire pour monter de niveau
    $experienceLevel = $characterLevel * $gameExperience;
    //On calcul combien d'experience il reste au joueur pour monter de niveau
    $experienceRemaining = $characterLevel * $gameExperience - $characterExperience;

    //Tant que le personnage à suffisament d'experience pour la monté d'un niveau
    while ($characterExperience >= $experienceLevel)
    {
        $characterHpMin = $characterHpMin + $hPByLevel;
        $characterHpMax = $characterHpMax + $hPByLevel;
        $characterHpTotal = $characterHpTotal + $hPByLevel;
        $characterMpMin = $characterMpMin + $mPByLevel;
        $characterMpMax = $characterMpMax + $mPByLevel;
        $characterMpTotal = $characterMpTotal + $mPByLevel;
        $characterStrength = $characterStrength + $strengthByLevel;
        $characterStrengthTotal = $characterStrengthTotal + $strengthByLevel;
        $characterMagic = $characterMagic + $magicByLevel;
        $characterMagicTotal = $characterMagicTotal + $magicByLevel;
        $characterAgility = $characterAgility + $agilityByLevel;
        $characterAgilityTotal = $characterAgilityTotal + $agilityByLevel;
        $characterDefense = $characterDefense + $defenseByLevel;
        $characterDefenseTotal = $characterDefenseTotal + $defenseByLevel;
        $characterDefenseMagic = $characterDefenseMagic + $defenseMagicByLevel;
        $characterDefenseMagicTotal = $characterDefenseMagicTotal + $defenseMagicByLevel;
        $characterWisdom = $characterWisdom + $wisdomByLevel;
        $characterWisdomTotal = $characterWisdomTotal + $wisdomByLevel;
        $characterProspecting = $characterProspecting + $prospectingByLevel;
        $characterProspectingTotal = $characterProspectingTotal + $prospectingByLevel;
        $characterExperience = $characterExperience - $experienceLevel;
        $characterSkillPoints = $characterSkillPoints + $skillPointsByLevel;
        $characterLevel = $characterLevel + 1;
        
        //On recalcul l'expérience nécessaire pour monter de niveau
        $experienceLevel = $characterLevel * $gameExperience;
        //On calcul combien de niveau ont été gagné
        $levelUpNumber++;

        //On met le personnage à jour
        $updateCharacter = $bdd->prepare("UPDATE car_characters SET
        characterLevel = :characterLevel,
        characterHpMin = :characterHpMin, 
        characterHpMax = :characterHpMax, 
        characterHpTotal = :characterHpTotal, 
        characterMpMin = :characterMpMin, 
        characterMpMax = :characterMpMax, 
        characterMpTotal = :characterMpTotal, 
        characterStrength = :characterStrength, 
        characterStrengthTotal = :characterStrengthTotal, 
        characterMagic = :characterMagic, 
        characterMagicTotal = :characterMagicTotal, 
        characterAgility = :characterAgility, 
        characterAgilityTotal = :characterAgilityTotal, 
        characterDefense = :characterDefense, 
        characterDefenseTotal = :characterDefenseTotal, 
        characterDefenseMagic = :characterDefenseMagic, 
        characterDefenseMagicTotal = :characterDefenseMagicTotal, 
        characterWisdom = :characterWisdom, 
        characterWisdomTotal = :characterWisdomTotal,
        characterProspecting = :characterProspecting, 
        characterProspectingTotal = :characterProspectingTotal, 
        characterExperience = :characterExperience, 
        characterExperienceTotal = :characterExperienceTotal,
        characterSkillPoints = :characterSkillPoints
        WHERE characterId = :characterId");
        $updateCharacter->execute(array(
        'characterLevel' => $characterLevel,  
        'characterHpMin' => $characterHpMin, 
        'characterHpMax' => $characterHpMax, 
        'characterHpTotal' => $characterHpTotal, 
        'characterMpMin' => $characterMpMin, 
        'characterMpMax' => $characterMpMax, 
        'characterMpTotal' => $characterMpTotal, 
        'characterStrength' => $characterStrength, 
        'characterStrengthTotal' => $characterStrengthTotal, 
        'characterMagic' => $characterMagic, 
        'characterMagicTotal' => $characterMagicTotal, 
        'characterAgility' => $characterAgility, 
        'characterAgilityTotal' => $characterAgilityTotal, 
        'characterDefense' => $characterDefense, 
        'characterDefenseTotal' => $characterDefenseTotal, 
        'characterDefenseMagic' => $characterDefenseMagic, 
        'characterDefenseMagicTotal' => $characterDefenseMagicTotal, 
        'characterWisdom' => $characterWisdom, 
        'characterWisdomTotal' => $characterWisdomTotal,
        'characterProspecting' => $characterProspecting, 
        'characterProspectingTotal' => $characterProspectingTotal,  
        'characterExperience' => $characterExperience, 
        'characterExperienceTotal' => $characterExperienceTotal, 
        'characterSkillPoints' => $characterSkillPoints, 
        'characterId' => $characterId));
        $updateCharacter->closeCursor();
    }
}
else
{
    $experienceLevel = 0;
    $experienceRemaining = 0;
}

//Si le personnage a gagné un niveau
if ($levelUpNumber > 0)
{
    ?>
    <script>alert("Votre personnage vient de gagner <?php echo $levelUpNumber ?> niveau(x) !")</script>
    <?php
}
?>
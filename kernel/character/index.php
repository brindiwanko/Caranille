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
    $characterId = $character['characterId'];
    $characterAccountId = $character['characterAccountId'];
    $characterGuildId = $character['characterGuildId'];
    $characterclasseId = $character['characterclasseId'];
    $characterPlaceId = $character['characterPlaceId'];
    $characterPicture = $character['characterPicture'];
    $characterName = $character['characterName'];
    $characterLevel = $character['characterLevel'];
    $characterSex = $character['characterSex'];
    $characterHpMin = $character['characterHpMin'];
    $characterHpMax = $character['characterHpMax'];
    $characterHpSkillPoints = $character['characterHpSkillPoints'];
    $characterHpBonus = $character['characterHpBonus'];
    $characterHpEquipments = $character['characterHpEquipments'];
    $characterHpGuild = $character['characterHpGuild'];
    $characterHpTotal = $character['characterHpTotal'];
    $characterMpMin = $character['characterMpMin'];
    $characterMpMax = $character['characterMpMax'];
    $characterMpSkillPoints = $character['characterMpSkillPoints'];
    $characterMpBonus = $character['characterMpBonus'];
    $characterMpEquipments = $character['characterMpEquipments'];
    $characterMpGuild = $character['characterMpGuild'];
    $characterMpTotal = $character['characterMpTotal'];
    $characterStrength = $character['characterStrength'];
    $characterStrengthSkillPoints = $character['characterStrengthSkillPoints'];
    $characterStrengthBonus = $character['characterStrengthBonus'];
    $characterStrengthEquipments = $character['characterStrengthEquipments'];
    $characterStrengthGuild = $character['characterStrengthGuild'];
    $characterStrengthTotal = $character['characterStrengthTotal'];
    $characterMagic = $character['characterMagic'];
    $characterMagicSkillPoints = $character['characterMagicSkillPoints'];
    $characterMagicBonus = $character['characterMagicBonus'];
    $characterMagicEquipments = $character['characterMagicEquipments'];
    $characterMagicGuild = $character['characterMagicGuild'];
    $characterMagicTotal = $character['characterMagicTotal'];
    $characterAgility = $character['characterAgility'];
    $characterAgilitySkillPoints = $character['characterAgilitySkillPoints'];
    $characterAgilityBonus = $character['characterAgilityBonus'];
    $characterAgilityEquipments = $character['characterAgilityEquipments'];
    $characterAgilityGuild = $character['characterAgilityGuild'];
    $characterAgilityTotal = $character['characterAgilityTotal'];
    $characterDefense = $character['characterDefense'];
    $characterDefenseSkillPoints = $character['characterDefenseSkillPoints'];
    $characterDefenseBonus = $character['characterDefenseBonus'];
    $characterDefenseEquipment = $character['characterDefenseEquipments'];
    $characterDefenseGuild = $character['characterDefenseGuild'];
    $characterDefenseTotal = $character['characterDefenseTotal'];
    $characterDefenseMagic = $character['characterDefenseMagic'];
    $characterDefenseMagicSkillPoints = $character['characterDefenseMagicSkillPoints'];
    $characterDefenseMagicBonus = $character['characterDefenseMagicBonus'];
    $characterDefenseMagicEquipments = $character['characterDefenseMagicEquipments'];
    $characterDefenseMagicGuild = $character['characterDefenseMagicGuild'];
    $characterDefenseMagicTotal = $character['characterDefenseMagicTotal'];
    $characterWisdom = $character['characterWisdom'];
    $characterWisdomSkillPoints = $character['characterWisdomSkillPoints'];
    $characterWisdomBonus = $character['characterWisdomBonus'];
    $characterWisdomEquipments = $character['characterWisdomEquipments'];
    $characterWisdomGuild = $character['characterWisdomGuild'];
    $characterWisdomTotal = $character['characterWisdomTotal'];
    $characterProspecting = $character['characterProspecting'];
    $characterProspectingSkillPoints = $character['characterProspectingSkillPoints'];
    $characterProspectingBonus = $character['characterProspectingBonus'];
    $characterProspectingEquipments = $character['characterProspectingEquipments'];
    $characterProspectingGuild = $character['characterProspectingGuild'];
    $characterProspectingTotal = $character['characterProspectingTotal'];
    $characterArenaDefeate = $character['characterArenaDefeate'];
    $characterArenaVictory = $character['characterArenaVictory'];
    $characterExperience = $character['characterExperience'];
    $characterExperienceTotal = $character['characterExperienceTotal'];
    $characterSkillPoints = $character['characterSkillPoints'];
    $characterGold = $character['characterGold'];
    $characterChapter = $character['characterChapter'];
    $characterOnBattle = $character['characterOnBattle'];
    $characterEnable = $character['characterEnable'];
}
$characterQuery->closeCursor();

//On fait une recherche dans la base de donnée pour récupérer la classe du personnage
$classeQuery = $bdd->prepare("SELECT * FROM car_classes 
WHERE classeId = ?");
$classeQuery->execute([$characterclasseId]);

//On récupère les augmentations de statistique lié à la classe
while ($classe = $classeQuery->fetch())
{
    //On récupère les informations de la classe
    $characterclasseName = $classe['classeName'];
    $classeHpBonus = $classe['classeHpBonus'];
    $classeMpBonus = $classe['classeMpBonus'];
    $classestrengthBonus = $classe['classestrengthBonus'];
    $classeMagicBonus = $classe['classeMagicBonus'];
    $classeAgilityBonus = $classe['classeAgilityBonus'];
    $classeDefenseBonus = $classe['classeDefenseBonus'];
    $classeDefenseMagicBonus = $classe['classeDefenseMagicBonus'];
    $classeWisdomBonus = $classe['classeWisdomBonus'];
    $classeProspectingBonus = $classe['classeProspectingBonus'];
}
$classeQuery->closeCursor();

//Valeurs des statistiques qui seront ajouté à la monté d'un niveau
$hPByLevel = $classeHpBonus;
$mPByLevel = $classeMpBonus;
$strengthByLevel = $classestrengthBonus;
$magicByLevel = $classeMagicBonus;
$agilityByLevel = $classeAgilityBonus;
$defenseByLevel = $classeDefenseBonus;
$defenseMagicByLevel = $classeDefenseMagicBonus;
$wisdomByLevel = $classeWisdomBonus;
$prospectingByLevel = $classeProspectingBonus;

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
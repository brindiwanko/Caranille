<?php
//On fait une recherche dans la base de donnée pour récupérer la configuration du jeu
$configurationQuery = $bdd->query("SELECT * FROM car_configuration");

//On fait une boucle pour récupérer toutes les information
while ($configuration = $configurationQuery->fetch())
{
    //On récupère les informations du jeu
    $gameId = $configuration['configurationId'];
    $gameName = $configuration['configurationGameName'];
    $gamePresentation = nl2br(htmlspecialchars($configuration['configurationPresentation']));
    $gameMaxLevel = $configuration['configurationMaxLevel']; 
    $gameExperience = $configuration['configurationExperience'];
    $gameSkillPoint = $configuration['configurationSkillPoint'];
    $gameExperienceBonus = $configuration['configurationExperienceBonus'];
    $gameGoldBonus = $configuration['configurationGoldBonus'];
    $gameDropBonus = $configuration['configurationDropBonus'];
    $gameAccess = $configuration['configurationAccess'];
}
$configurationQuery->closeCursor();
?>
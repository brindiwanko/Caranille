<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminRaceId'])
&& isset($_POST['adminRacePicture'])
&& isset($_POST['adminRaceName']) 
&& isset($_POST['adminRaceDescription'])
&& isset($_POST['adminRaceHpBonus'])
&& isset($_POST['adminRaceMpBonus'])
&& isset($_POST['adminRaceStrengthBonus'])
&& isset($_POST['adminRaceMagicBonus'])
&& isset($_POST['adminRaceAgilityBonus'])
&& isset($_POST['adminRaceDefenseBonus'])
&& isset($_POST['adminRaceDefenseMagicBonus'])
&& isset($_POST['adminRaceWisdomBonus'])
&& isset($_POST['adminRaceProspectingBonus'])
&& isset($_POST['token'])
&& isset($_POST['finalEdit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si l'id de la race récupéré dans le formulaire est en entier positif
        if (ctype_digit($_POST['adminRaceId'])
        && ctype_digit($_POST['adminRaceHpBonus'])
        && ctype_digit($_POST['adminRaceMpBonus'])
        && ctype_digit($_POST['adminRaceStrengthBonus'])
        && ctype_digit($_POST['adminRaceMagicBonus'])
        && ctype_digit($_POST['adminRaceAgilityBonus'])
        && ctype_digit($_POST['adminRaceDefenseBonus'])
        && ctype_digit($_POST['adminRaceDefenseMagicBonus'])
        && ctype_digit($_POST['adminRaceWisdomBonus'])
        && ctype_digit($_POST['adminRaceProspectingBonus'])
        && $_POST['adminRaceId'] >= 1
        && $_POST['adminRaceHpBonus'] >= 0
        && $_POST['adminRaceMpBonus'] >= 0
        && $_POST['adminRaceStrengthBonus'] >= 0
        && $_POST['adminRaceMagicBonus'] >= 0
        && $_POST['adminRaceAgilityBonus'] >= 0
        && $_POST['adminRaceDefenseBonus'] >= 0
        && $_POST['adminRaceDefenseMagicBonus'] >= 0
        && $_POST['adminRaceWisdomBonus'] >= 0
        && $_POST['adminRaceProspectingBonus'] >= 0)
        {
            //On récupère les informations du formulaire
            $adminRaceId = htmlspecialchars(addslashes($_POST['adminRaceId']));
            $adminRacePicture = htmlspecialchars(addslashes($_POST['adminRacePicture']));
            $adminRaceName = htmlspecialchars(addslashes($_POST['adminRaceName']));
            $adminRaceDescription = htmlspecialchars(addslashes($_POST['adminRaceDescription']));
            $adminRaceHpBonus = htmlspecialchars(addslashes($_POST['adminRaceHpBonus']));
            $adminRaceMpBonus = htmlspecialchars(addslashes($_POST['adminRaceMpBonus']));
            $adminRaceStrengthBonus = htmlspecialchars(addslashes($_POST['adminRaceStrengthBonus']));
            $adminRaceMagicBonus = htmlspecialchars(addslashes($_POST['adminRaceMagicBonus']));
            $adminRaceAgilityBonus = htmlspecialchars(addslashes($_POST['adminRaceAgilityBonus']));
            $adminRaceDefenseBonus = htmlspecialchars(addslashes($_POST['adminRaceDefenseBonus']));
            $adminRaceDefenseMagicBonus = htmlspecialchars(addslashes($_POST['adminRaceDefenseMagicBonus']));
            $adminRaceWisdomBonus = htmlspecialchars(addslashes($_POST['adminRaceWisdomBonus']));
            $adminRaceProspectingBonus = htmlspecialchars(addslashes($_POST['adminRaceProspectingBonus']));
            
            //On fait une requête pour vérifier si la race choisie existe
            $raceQuery = $bdd->prepare("SELECT * FROM car_races 
            WHERE raceId = ?");
            $raceQuery->execute([$adminRaceId]);
            $raceRow = $raceQuery->rowCount();

            //Si la race existe
            if ($raceRow == 1) 
            {
                //On met à jour la race dans la base de donnée
                $updateRace = $bdd->prepare("UPDATE car_races 
                SET racePicture = :adminRacePicture,
                raceName = :adminRaceName, 
                raceDescription = :adminRaceDescription,
                raceHpBonus = :adminRaceHpBonus,
                raceMpBonus = :adminRaceMpBonus,
                raceStrengthBonus = :adminRaceStrengthBonus,
                raceMagicBonus = :adminRaceMagicBonus,
                raceAgilityBonus = :adminRaceAgilityBonus,
                raceDefenseBonus = :adminRaceDefenseBonus,
                raceDefenseMagicBonus = :adminRaceDefenseMagicBonus,
                raceWisdomBonus = :adminRaceWisdomBonus,
                raceProspectingBonus = :adminRaceProspectingBonus
                WHERE raceId = :adminRaceId");
                $updateRace->execute([
                'adminRacePicture' => $adminRacePicture,
                'adminRaceName' => $adminRaceName,
                'adminRaceDescription' => $adminRaceDescription,
                'adminRaceHpBonus' => $adminRaceHpBonus,
                'adminRaceMpBonus' => $adminRaceMpBonus,
                'adminRaceStrengthBonus' => $adminRaceStrengthBonus,
                'adminRaceMagicBonus' => $adminRaceMagicBonus,
                'adminRaceAgilityBonus' => $adminRaceAgilityBonus,
                'adminRaceDefenseBonus' => $adminRaceDefenseBonus,
                'adminRaceDefenseMagicBonus' => $adminRaceDefenseMagicBonus,
                'adminRaceWisdomBonus' => $adminRaceWisdomBonus,
                'adminRaceProspectingBonus' => $adminRaceProspectingBonus,
                'adminRaceId' => $adminRaceId]);
                $updateRace->closeCursor();
                
                //On cherche les joueurs qui utilise cette classe
                $characterRaceQuery = $bdd->prepare("SELECT * FROM car_characters 
                WHERE characterRaceId = ?");
                $characterRaceQuery->execute([$adminRaceId]);
        
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($characterRace = $characterRaceQuery->fetch())
                {
                    //On définit les statistiques d'un personnage de niveau 1
                    $initialHp = 10;
                    $initialMp = 1;
                    $initialStrength = 1;
                    $initialMagic = 1;
                    $initialAgility = 1;
                    $initialDefense = 1;
                    $initialDefenseMagic = 1;
                    $initialWisdom = 0;
                    $initialProspecting = 0;
                    
                    //On récupère le niveau du joueur et son Id
                    $adminCharacterId = $characterRace['characterId'];
                    $adminCharacterLevel = $characterRace['characterLevel'] - 1;
                    
                    $adminCharacterHP = $initialHp + $adminRaceHpBonus * $adminCharacterLevel;
                    $adminCharacterMP = $initialMp + $adminRaceMpBonus * $adminCharacterLevel;
                    $adminCharacterStrength = $initialStrength + $adminRaceStrengthBonus * $adminCharacterLevel;
                    $adminCharacterMagic = $initialMagic + $adminRaceMagicBonus * $adminCharacterLevel;
                    $adminCharacterAgility = $initialAgility + $adminRaceAgilityBonus * $adminCharacterLevel;
                    $adminCharacterDefense = $initialDefense + $adminRaceDefenseBonus * $adminCharacterLevel;
                    $adminCharacterDefenseMagic = $initialDefenseMagic + $adminRaceDefenseMagicBonus * $adminCharacterLevel;
                    $adminCharacterWisdom = $initialWisdom + $adminRaceWisdomBonus * $adminCharacterLevel;
                    $adminCharacterProspecting = $initialProspecting + $adminRaceProspectingBonus * $adminCharacterLevel;
                    
                    //On met le personnage à jour
                    $updateCharacter = $bdd->prepare("UPDATE car_characters SET
                    characterHpMax = :adminCharacterHP, 
                    characterMpMax = :adminCharacterMP, 
                    characterStrength = :adminCharacterStrength, 
                    characterMagic = :adminCharacterMagic, 
                    characterAgility = :adminCharacterAgility, 
                    characterDefense = :adminCharacterDefense, 
                    characterDefenseMagic = :adminCharacterDefenseMagic, 
                    characterWisdom = :adminCharacterWisdom,
                    characterProspecting = :adminCharacterProspecting
                    WHERE characterId = :adminCharacterId");
                    $updateCharacter->execute(array(
                    'adminCharacterHP' => $adminCharacterHP,  
                    'adminCharacterMP' => $adminCharacterMP, 
                    'adminCharacterStrength' => $adminCharacterStrength, 
                    'adminCharacterMagic' => $adminCharacterMagic, 
                    'adminCharacterAgility' => $adminCharacterAgility, 
                    'adminCharacterDefense' => $adminCharacterDefense, 
                    'adminCharacterDefenseMagic' => $adminCharacterDefenseMagic, 
                    'adminCharacterWisdom' => $adminCharacterWisdom, 
                    'adminCharacterProspecting' => $adminCharacterProspecting, 
                    'adminCharacterId' => $adminCharacterId));
                    $updateCharacter->closeCursor();
                    
                    //On recalcule toutes les statisiques max du personnage
                    $updateCharacter = $bdd->prepare("UPDATE car_characters
                    SET characterHpTotal = characterHpMax + characterHpSkillPoints + characterHpBonus + characterHpEquipments,
                    characterMpTotal = characterMpMax + characterMpSkillPoints + characterMpBonus + characterMpEquipments,
                    characterStrengthTotal = characterStrength + characterStrengthSkillPoints + characterStrengthBonus + characterStrengthEquipments,
                    characterMagicTotal = characterMagic + characterMagicSkillPoints + characterMagicBonus + characterMagicEquipments,
                    characterAgilityTotal = characterAgility + characterAgilitySkillPoints + characterAgilityBonus + characterAgilityEquipments,
                    characterDefenseTotal = characterDefense + characterDefenseSkillPoints + characterDefenseBonus + characterDefenseEquipments,
                    characterDefenseMagicTotal = characterDefenseMagic + characterDefenseMagicSkillPoints + characterDefenseMagicBonus + characterDefenseMagicEquipments,
                    characterWisdomTotal = characterWisdom + characterWisdomSkillPoints + characterWisdomBonus + characterWisdomEquipments,
                    characterProspectingTotal = characterProspecting + characterProspectingSkillPoints + characterProspectingBonus + characterProspectingEquipments
                    WHERE characterId = :adminCharacterId");
                    $updateCharacter->execute(['adminCharacterId' => $adminCharacterId]);
                    $updateCharacter->closeCursor();
                }
                ?>

                La classe a bien été mise à jour

                <hr>
                    
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si la race n'existe pas
            else
            {
                echo "Erreur : Cette classe n'existe pas";
            }
            $raceQuery->closeCursor();
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
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
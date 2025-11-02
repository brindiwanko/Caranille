<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminclasseId'])
&& isset($_POST['adminclassePicture'])
&& isset($_POST['adminclasseName']) 
&& isset($_POST['adminclasseDescription'])
&& isset($_POST['adminclasseHpBonus'])
&& isset($_POST['adminclasseMpBonus'])
&& isset($_POST['adminclassestrengthBonus'])
&& isset($_POST['adminclasseMagicBonus'])
&& isset($_POST['adminclasseAgilityBonus'])
&& isset($_POST['adminclasseDefenseBonus'])
&& isset($_POST['adminclasseDefenseMagicBonus'])
&& isset($_POST['adminclasseWisdomBonus'])
&& isset($_POST['adminclasseProspectingBonus'])
&& isset($_POST['token'])
&& isset($_POST['finalEdit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si l'id de la classe récupéré dans le formulaire est en entier positif
        if (ctype_digit($_POST['adminclasseId'])
        && ctype_digit($_POST['adminclasseHpBonus'])
        && ctype_digit($_POST['adminclasseMpBonus'])
        && ctype_digit($_POST['adminclassestrengthBonus'])
        && ctype_digit($_POST['adminclasseMagicBonus'])
        && ctype_digit($_POST['adminclasseAgilityBonus'])
        && ctype_digit($_POST['adminclasseDefenseBonus'])
        && ctype_digit($_POST['adminclasseDefenseMagicBonus'])
        && ctype_digit($_POST['adminclasseWisdomBonus'])
        && ctype_digit($_POST['adminclasseProspectingBonus'])
        && $_POST['adminclasseId'] >= 1
        && $_POST['adminclasseHpBonus'] >= 0
        && $_POST['adminclasseMpBonus'] >= 0
        && $_POST['adminclassestrengthBonus'] >= 0
        && $_POST['adminclasseMagicBonus'] >= 0
        && $_POST['adminclasseAgilityBonus'] >= 0
        && $_POST['adminclasseDefenseBonus'] >= 0
        && $_POST['adminclasseDefenseMagicBonus'] >= 0
        && $_POST['adminclasseWisdomBonus'] >= 0
        && $_POST['adminclasseProspectingBonus'] >= 0)
        {
            //On récupère les informations du formulaire
            $adminclasseId = htmlspecialchars($_POST['adminclasseId']);
            $adminclassePicture = htmlspecialchars($_POST['adminclassePicture']);
            $adminclasseName = htmlspecialchars($_POST['adminclasseName']);
            $adminclasseDescription = htmlspecialchars($_POST['adminclasseDescription']);
            $adminclasseHpBonus = htmlspecialchars($_POST['adminclasseHpBonus']);
            $adminclasseMpBonus = htmlspecialchars($_POST['adminclasseMpBonus']);
            $adminclassestrengthBonus = htmlspecialchars($_POST['adminclassestrengthBonus']);
            $adminclasseMagicBonus = htmlspecialchars($_POST['adminclasseMagicBonus']);
            $adminclasseAgilityBonus = htmlspecialchars($_POST['adminclasseAgilityBonus']);
            $adminclasseDefenseBonus = htmlspecialchars($_POST['adminclasseDefenseBonus']);
            $adminclasseDefenseMagicBonus = htmlspecialchars($_POST['adminclasseDefenseMagicBonus']);
            $adminclasseWisdomBonus = htmlspecialchars($_POST['adminclasseWisdomBonus']);
            $adminclasseProspectingBonus = htmlspecialchars($_POST['adminclasseProspectingBonus']);
            
            //On fait une requête pour vérifier si la classe choisie existe
            $classeQuery = $bdd->prepare("SELECT * FROM car_classes 
            WHERE classeId = ?");
            $classeQuery->execute([$adminclasseId]);
            $classeRow = $classeQuery->rowCount();

            //Si la classe existe
            if ($classeRow == 1) 
            {
                //On met à jour la classe dans la base de donnée
                $updateclasse = $bdd->prepare("UPDATE car_classes 
                SET classePicture = :adminclassePicture,
                classeName = :adminclasseName, 
                classeDescription = :adminclasseDescription,
                classeHpBonus = :adminclasseHpBonus,
                classeMpBonus = :adminclasseMpBonus,
                classestrengthBonus = :adminclassestrengthBonus,
                classeMagicBonus = :adminclasseMagicBonus,
                classeAgilityBonus = :adminclasseAgilityBonus,
                classeDefenseBonus = :adminclasseDefenseBonus,
                classeDefenseMagicBonus = :adminclasseDefenseMagicBonus,
                classeWisdomBonus = :adminclasseWisdomBonus,
                classeProspectingBonus = :adminclasseProspectingBonus
                WHERE classeId = :adminclasseId");
                $updateclasse->execute([
                'adminclassePicture' => $adminclassePicture,
                'adminclasseName' => $adminclasseName,
                'adminclasseDescription' => $adminclasseDescription,
                'adminclasseHpBonus' => $adminclasseHpBonus,
                'adminclasseMpBonus' => $adminclasseMpBonus,
                'adminclassestrengthBonus' => $adminclassestrengthBonus,
                'adminclasseMagicBonus' => $adminclasseMagicBonus,
                'adminclasseAgilityBonus' => $adminclasseAgilityBonus,
                'adminclasseDefenseBonus' => $adminclasseDefenseBonus,
                'adminclasseDefenseMagicBonus' => $adminclasseDefenseMagicBonus,
                'adminclasseWisdomBonus' => $adminclasseWisdomBonus,
                'adminclasseProspectingBonus' => $adminclasseProspectingBonus,
                'adminclasseId' => $adminclasseId]);
                $updateclasse->closeCursor();
                
                //On cherche les joueurs qui utilise cette classe
                $characterclasseQuery = $bdd->prepare("SELECT * FROM car_characters 
                WHERE characterclasseId = ?");
                $characterclasseQuery->execute([$adminclasseId]);
        
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($characterclasse = $characterclasseQuery->fetch())
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
                    $adminCharacterId = $characterclasse['characterId'];
                    $adminCharacterLevel = $characterclasse['characterLevel'] - 1;
                    
                    $adminCharacterHP = $initialHp + $adminclasseHpBonus * $adminCharacterLevel;
                    $adminCharacterMP = $initialMp + $adminclasseMpBonus * $adminCharacterLevel;
                    $adminCharacterStrength = $initialStrength + $adminclassestrengthBonus * $adminCharacterLevel;
                    $adminCharacterMagic = $initialMagic + $adminclasseMagicBonus * $adminCharacterLevel;
                    $adminCharacterAgility = $initialAgility + $adminclasseAgilityBonus * $adminCharacterLevel;
                    $adminCharacterDefense = $initialDefense + $adminclasseDefenseBonus * $adminCharacterLevel;
                    $adminCharacterDefenseMagic = $initialDefenseMagic + $adminclasseDefenseMagicBonus * $adminCharacterLevel;
                    $adminCharacterWisdom = $initialWisdom + $adminclasseWisdomBonus * $adminCharacterLevel;
                    $adminCharacterProspecting = $initialProspecting + $adminclasseProspectingBonus * $adminCharacterLevel;
                    
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
            //Si la classe n'existe pas
            else
            {
                echo "Erreur : Cette classe n'existe pas";
            }
            $classeQuery->closeCursor();
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
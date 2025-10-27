<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminCharacterLevel'])
&& isset($_POST['token'])
&& isset($_POST['finalEdit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminCharacterLevel']) 
        && $_POST['adminCharacterLevel'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminCharacterLevel = htmlspecialchars($_POST['adminCharacterLevel']);
            
            //On va calculer les statistiques du joueur pour son nouveau niveau
            $hpLevel = 100 + $hPByLevel * $adminCharacterLevel;
            $mpLevel = 10 + $mPByLevel * $adminCharacterLevel;
            $strengthLevel = 1 + $strengthByLevel * $adminCharacterLevel;
            $magicLevel = 1 + $magicByLevel * $adminCharacterLevel;
            $agilityLevel = 1 + $agilityByLevel * $adminCharacterLevel;
            $defenseLevel = 1 + $defenseByLevel * $adminCharacterLevel;
            $defenseMagicLevel = 1 + $defenseMagicByLevel * $adminCharacterLevel;
            $wisdomLevel = 1 + $wisdomByLevel * $adminCharacterLevel;
            $prospectingLevel = 1 + $prospectingByLevel * $adminCharacterLevel;
            $skillPointLevel = $skillPointsByLevel * $adminCharacterLevel - 4;
            
            //On met le personnage à jour
            $updateCharacter = $bdd->prepare("UPDATE car_characters SET
            characterLevel = :adminCharacterLevel,
            characterHpMin = :hpLevel, 
            characterHpMax = :hpLevel, 
            characterHpEquipments = 0,
            characterHpTotal = :hpLevel, 
            characterMpMin = :mpLevel, 
            characterMpMax = :mpLevel, 
            characterMpEquipments = 0,
            characterMpTotal = :mpLevel, 
            characterStrength = :strengthLevel, 
            characterStrengthEquipments = 0,
            characterStrengthTotal = :strengthLevel, 
            characterMagic = :magicLevel, 
            characterMagicEquipments = 0,
            characterMagicTotal = :magicLevel, 
            characterAgility = :agilityLevel, 
            characterAgilityEquipments = 0,
            characterAgilityTotal = :agilityLevel, 
            characterDefense = :defenseLevel, 
            characterDefenseEquipments = 0,
            characterDefenseTotal = :defenseLevel, 
            characterDefenseMagic = :defenseMagicLevel, 
            characterDefenseMagicEquipments = 0,
            characterDefenseMagicTotal = :defenseMagicLevel, 
            characterWisdom = :wisdomLevel, 
            characterWisdomEquipments = 0,
            characterWisdomTotal = :wisdomLevel,
            characterProspecting = :prospectingLevel, 
            characterProspectingEquipments = 0,
            characterProspectingTotal = :prospectingLevel,
            characterSkillPoints = :skillPointsLevel
            WHERE characterId = :characterId");
            $updateCharacter->execute(array(
            'adminCharacterLevel' => $adminCharacterLevel,  
            'hpLevel' => $hpLevel, 
            'mpLevel' => $mpLevel, 
            'mpLevel' => $mpLevel, 
            'strengthLevel' => $strengthLevel, 
            'magicLevel' => $magicLevel, 
            'agilityLevel' => $agilityLevel, 
            'defenseLevel' => $defenseLevel, 
            'defenseMagicLevel' => $defenseMagicLevel,
            'wisdomLevel' => $wisdomLevel,
            'prospectingLevel' => $prospectingLevel,
            'skillPointsLevel' => $skillPointLevel, 
            'characterId' => $characterId));
            $updateCharacter->closeCursor();
            
            //On fait une recherche de tous les équipements qui sont équipé pour les déséquipper
            $inventoryQuery = $bdd->prepare("SELECT * FROM car_inventory 
            WHERE inventoryEquipped = '1'
            AND inventoryCharacterId = ?");
            $inventoryQuery->execute([$characterId]);

            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($inventory = $inventoryQuery->fetch())
            {
                //On récupère les informations de l'inventaire
                $adminInventoryId = $inventory['inventoryId'];

                //On va rendre l'équipement non équipé
                $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                inventoryEquipped = 0
                WHERE inventoryId = :adminInventoryId");
                $updateInventory->execute(array(
                'adminInventoryId' => $adminInventoryId));
                $updateInventory->closeCursor();
            }
        ?>
        
        Le niveau a bien été mit à jour
        
        <hr>
            
        <form method="POST" action="index.php">
            <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
        </form>
        
        <?php
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
require_once("../html/footer.php");
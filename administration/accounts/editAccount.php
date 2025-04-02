<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminAccountId'])
&& isset($_POST['token'])
&& isset($_POST['edit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = uniqid();

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminAccountId'])
        && $_POST['adminAccountId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminAccountId = htmlspecialchars(addslashes($_POST['adminAccountId']));

            //On fait une requête pour vérifier si le compte choisit existe
            $accountQuery = $bdd->prepare("SELECT * FROM car_accounts 
            WHERE accountId = ?");
            $accountQuery->execute([$adminAccountId]);
            $accountRow = $accountQuery->rowCount();

            //Si le compte existe
            if ($accountRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($account = $accountQuery->fetch())
                {
                    //On récupère les informations du compte
                    $adminAccountId = stripslashes($account['accountId']);
                    $adminAccountPseudo = stripslashes($account['accountPseudo']);
                    $adminAccountEmail = stripslashes($account['accountEmail']);
                    $adminAccountAccess = stripslashes($account['accountAccess']);
                }

                //On récupère le personnage pour l'afficher dans le menu d'information du personnage
                $characterQuery = $bdd->prepare("SELECT * FROM car_characters
                WHERE characterAccountId = ?");
                $characterQuery->execute([$adminAccountId]);
                
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($character = $characterQuery->fetch())
                {
                    //On récupère toutes les informations du personnage
                    $adminCharacterId = stripslashes($character['characterId']);
                    $adminCharacterAccountId = stripslashes($character['characterAccountId']);
                    $adminCharacterGuildId = stripslashes($character['characterGuildId']);
                    $adminCharacterRaceId = stripslashes($character['characterRaceId']);
                    $adminCharacterPlaceId = stripslashes($character['characterPlaceId']);
                    $adminCharacterPicture = stripslashes($character['characterPicture']);
                    $adminCharacterName = stripslashes($character['characterName']);
                    $adminCharacterLevel = stripslashes($character['characterLevel']);
                    $adminCharacterSex = stripslashes($character['characterSex']);
                    $adminCharacterHpMin = stripslashes($character['characterHpMin']);
                    $adminCharacterHpMax = stripslashes($character['characterHpMax']);
                    $adminCharacterHpSkillPoints = stripslashes($character['characterHpSkillPoints']);
                    $adminCharacterHpBonus = stripslashes($character['characterHpBonus']);
                    $adminCharacterHpEquipments = stripslashes($character['characterHpEquipments']);
                    $adminCharacterHpGuild = stripslashes($character['characterHpGuild']);
                    $adminCharacterHpTotal = stripslashes($character['characterHpTotal']);
                    $adminCharacterMpMin = stripslashes($character['characterMpMin']);
                    $adminCharacterMpMax = stripslashes($character['characterMpMax']);
                    $adminCharacterMpSkillPoints = stripslashes($character['characterMpSkillPoints']);
                    $adminCharacterMpBonus = stripslashes($character['characterMpBonus']);
                    $adminCharacterMpEquipments = stripslashes($character['characterMpEquipments']);
                    $adminCharacterMpGuild = stripslashes($character['characterMpGuild']);
                    $adminCharacterMpTotal = stripslashes($character['characterMpTotal']);
                    $adminCharacterStrength = stripslashes($character['characterStrength']);
                    $adminCharacterStrengthSkillPoints = stripslashes($character['characterStrengthSkillPoints']);
                    $adminCharacterStrengthBonus = stripslashes($character['characterStrengthBonus']);
                    $adminCharacterStrengthEquipments = stripslashes($character['characterStrengthEquipments']);
                    $adminCharacterStrengthGuild = stripslashes($character['characterStrengthGuild']);
                    $adminCharacterStrengthTotal = stripslashes($character['characterStrengthTotal']);
                    $adminCharacterMagic = stripslashes($character['characterMagic']);
                    $adminCharacterMagicSkillPoints = stripslashes($character['characterMagicSkillPoints']);
                    $adminCharacterMagicBonus = stripslashes($character['characterMagicBonus']);
                    $adminCharacterMagicEquipments = stripslashes($character['characterMagicEquipments']);
                    $adminCharacterMagicGuild = stripslashes($character['characterMagicGuild']);
                    $adminCharacterMagicTotal = stripslashes($character['characterMagicTotal']);
                    $adminCharacterAgility = stripslashes($character['characterAgility']);
                    $adminCharacterAgilitySkillPoints = stripslashes($character['characterAgilitySkillPoints']);
                    $adminCharacterAgilityBonus = stripslashes($character['characterAgilityBonus']);
                    $adminCharacterAgilityEquipments = stripslashes($character['characterAgilityEquipments']);
                    $adminCharacterAgilityGuild = stripslashes($character['characterAgilityGuild']);
                    $adminCharacterAgilityTotal = stripslashes($character['characterAgilityTotal']);
                    $adminCharacterDefense = stripslashes($character['characterDefense']);
                    $adminCharacterDefenseSkillPoints = stripslashes($character['characterDefenseSkillPoints']);
                    $adminCharacterDefenseBonus = stripslashes($character['characterDefenseBonus']);
                    $adminCharacterDefenseEquipment = stripslashes($character['characterDefenseEquipments']);
                    $adminCharacterDefenseGuild = stripslashes($character['characterDefenseGuild']);
                    $adminCharacterDefenseTotal = stripslashes($character['characterDefenseTotal']);
                    $adminCharacterDefenseMagic = stripslashes($character['characterDefenseMagic']);
                    $adminCharacterDefenseMagicSkillPoints = stripslashes($character['characterDefenseMagicSkillPoints']);
                    $adminCharacterDefenseMagicBonus = stripslashes($character['characterDefenseMagicBonus']);
                    $adminCharacterDefenseMagicEquipments = stripslashes($character['characterDefenseMagicEquipments']);
                    $adminCharacterDefenseMagicGuild = stripslashes($character['characterDefenseMagicGuild']);
                    $adminCharacterDefenseMagicTotal = stripslashes($character['characterDefenseMagicTotal']);
                    $adminCharacterWisdom = stripslashes($character['characterWisdom']);
                    $adminCharacterWisdomSkillPoints = stripslashes($character['characterWisdomSkillPoints']);
                    $adminCharacterWisdomBonus = stripslashes($character['characterWisdomBonus']);
                    $adminCharacterWisdomEquipments = stripslashes($character['characterWisdomEquipments']);
                    $adminCharacterProspecting = stripslashes($character['characterProspecting']);
                    $adminCharacterProspectingSkillPoints = stripslashes($character['characterProspectingSkillPoints']);
                    $adminCharacterProspectingBonus = stripslashes($character['characterProspectingBonus']);
                    $adminCharacterProspectingEquipments = stripslashes($character['characterProspectingEquipments']);
                    $adminCharacterProspectingGuild = stripslashes($character['characterProspectingGuild']);
                    $adminCharacterProspectingTotal = stripslashes($character['characterProspectingTotal']);
                    $adminCharacterWisdomGuild = stripslashes($character['characterWisdomGuild']);
                    $adminCharacterWisdomTotal = stripslashes($character['characterWisdomTotal']);
                    $adminCharacterDefeate = stripslashes($character['characterArenaDefeate']);
                    $adminCharacterVictory = stripslashes($character['characterArenaVictory']);
                    $adminCharacterExperience = stripslashes($character['characterExperience']);
                    $adminCharacterExperienceTotal = stripslashes($character['characterExperienceTotal']);
                    $adminCharacterSkillPoints = stripslashes($character['characterSkillPoints']);
                    $adminCharacterGold = stripslashes($character['characterGold']);
                    $adminCharacterChapter = stripslashes($character['characterChapter']);
                    $adminCharacterOnBattle = stripslashes($character['characterOnBattle']);
                    $adminCharacterEnable = stripslashes($character['characterEnable']);
                }
                $characterQuery->closeCursor();

                //On récupère la classe du personnage pour l'afficher dans le menu d'information du personnage
                $raceQuery = $bdd->prepare("SELECT * FROM car_races
                WHERE raceId = ?");
                $raceQuery->execute([$adminCharacterRaceId]);

                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($race = $raceQuery->fetch())
                {
                    //On récupère le nom de la classe du personnage
                    $adminRaceName = stripslashes($race['raceName']);
                }
                $raceQuery->closeCursor();

                //Si adminCharacterPlaceId à un Id supérieur à zéro c'est que le joueur est dans un lieu
                if ($adminCharacterPlaceId > 0)
                {
                    //On récupère le lieu du personnage pour l'afficher dans le menu d'information du personnage
                    $placeQuery = $bdd->prepare("SELECT * FROM car_places
                    WHERE placeId = ?");
                    $placeQuery->execute([$adminCharacterPlaceId]);

                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($place = $placeQuery->fetch())
                    {
                        //On récupère le nom du lieu où se situe le personnage
                        $adminplaceName = stripslashes($place['placeName']);
                    }
                    $placeQuery->closeCursor();
                }
                //Si adminCharacterPlaceId à un Id à zéro c'est que le joueur est sur la carte du monde
                else
                {
                    //On met Carte du monde comme nom de lieu au personnage
                    $adminplaceName = "Carte du monde";
                }

                //On récupère les équipements équipé du personnage pour les afficher dans le menu d'information du personnage
                $equipmentEquipedQuery = $bdd->prepare("SELECT * FROM car_items, car_items_types, car_inventory 
                WHERE itemItemTypeId = itemTypeId
                AND itemId = inventoryItemId
                AND inventoryEquipped = 1
                AND inventoryCharacterId = ?");
                $equipmentEquipedQuery->execute([$adminCharacterId]);

                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations et on vérifit le type d'équipement
                while ($equipment = $equipmentEquipedQuery->fetch())
                {
                    switch ($equipment['itemTypeName'])
                    {
                        //S'il s'agit d'une armure
                        case "Armor":
                            $adminEquipmentArmorId = stripslashes($equipment['itemId']);
                            $adminEquipmentArmorName = stripslashes($equipment['itemName']);
                            $adminEquipmentArmorDescription = stripslashes($equipment['itemDescription']);
                        break;

                        //S'il s'agit de bottes
                        case "Boots":
                            $adminEquipmentBootsId = stripslashes($equipment['itemId']);
                            $adminEquipmentBootsName = stripslashes($equipment['itemName']);
                            $adminEquipmentBootsDescription = stripslashes($equipment['itemDescription']);
                        break;

                        //S'il s'agit de gants
                        case "Gloves":
                            $adminEquipmentGlovesId = stripslashes($equipment['itemId']);
                            $adminEquipmentGlovesName = stripslashes($equipment['itemName']);
                            $adminEquipmentGlovesDescription = stripslashes($equipment['itemDescription']);
                        break;

                        //S'il s'agit d'un casque
                        case "Helmet":
                            $adminEquipmentHelmetId = stripslashes($equipment['itemId']);
                            $adminEquipmentHelmetName = stripslashes($equipment['itemName']);
                            $adminEquipmentHelmetDescription = stripslashes($equipment['itemDescription']);
                        break;

                        //S'il s'agit d'une arme
                        case "Weapon":
                            $adminEquipmentWeaponId = stripslashes($equipment['itemId']);
                            $adminEquipmentWeaponName = stripslashes($equipment['itemName']);
                            $adminEquipmentWeaponDescription = stripslashes($equipment['itemDescription']);
                        break;
                    }
                }

                //Si la variable $equipmentArmorId existe pas c'est que le personnage n'en est pas équipé
                if (!isset($adminEquipmentArmorId))
                {
                    $adminEquipmentArmorId = 0;
                    $adminEquipmentArmorName = "Vide";
                    $adminEquipmentArmorDescription = "";
                }

                //Si la variable $equipmentBootsId existe pas c'est que le personnage n'en est pas équipé
                if (!isset($adminEquipmentBootsId))
                {
                    $adminEquipmentBootsId = 0;
                    $adminEquipmentBootsName = "Vide";
                    $adminEquipmentBootsDescription = "";
                }

                //Si la variable $equipmentGlovesId existe pas c'est que le personnage n'en est pas équipé
                if (!isset($adminEquipmentGlovesId))
                {
                    $adminEquipmentGlovesId = 0;
                    $adminEquipmentGlovesName = "Vide";
                    $adminEquipmentGlovesDescription = "";
                }

                //Si la variable $equipmentHelmetId existe pas c'est que le personnage n'en est pas équipé
                if (!isset($adminEquipmentHelmetId))
                {
                    $adminEquipmentHelmetId = 0;
                    $adminEquipmentHelmetName = "Vide";
                    $adminEquipmentHelmetDescription = "";
                }

                //Si la variable $equipmentWeaponId existe pas c'est que le personnage n'en est pas équipé
                if (!isset($adminEquipmentWeaponId))
                {
                    $adminEquipmentWeaponId = 0;
                    $adminEquipmentWeaponName = "Vide";
                    $adminEquipmentWeaponDescription = "";
                }

                //On va déterminer le sexe du personnage
                switch ($adminCharacterSex)
                {
                    //Si le sexe du personnage est 0 c'est sexe féminin
                    case 0:
                        $adminCharacterSexName = "Féminin";
                    break;

                    //Si le sexe du personnage est 1 c'est sexe masculin
                    case 1:
                        $adminCharacterSexName = "Masculin";
                    break;
                }

                //On va déterminer si le personnage est en combat ou pas
                switch ($adminCharacterOnBattle)
                {
                    //Si OnBattle du personnage est 0 il est hors combat
                    case 0:
                        $adminCharacterOnBattleName = "Non";
                    break;

                    //Si OnBattle du personnage est 1 il est en combat
                    case 1:
                        $adminCharacterOnBattleName = "Oui";
                    break;
                }
                ?>

                <p>Informations du compte</p>
                
                <form method="POST" action="editAccountEnd.php">
                    Pseudo : <?php echo $adminAccountPseudo ?> <br />
                    Email : <?php echo $adminAccountEmail ?> <br />
                    Accès : <select name="adminAccountAccess" class="form-control">

                    <?php
                    switch ($adminAccountAccess)
                    {
                        case 0:
                            ?>
                            
                            <option selected="selected" value="0">Joueur</option>
                            <option value="1">Modérateur</option>
                            <option value="2">Administrateur</option>
                            
                            <?php
                        break;

                        case 1:
                            ?>
                            
                            <option value="0">Joueur</option>
                            <option selected="selected" value="1">Modérateur</option>
                            <option value="2">Administrateur</option>
                            
                            <?php
                        break;

                        case 2:
                            ?>
                            
                            <option value="0">Joueur</option>
                            <option value="1">Modérateur</option>";
                            <option selected="selected" value="2">Administrateur</option>
                            
                            <?php
                        break;
                    }
                    ?>
                    
                    </select>
                    <input type="hidden" name="adminAccountId" value="<?php echo $adminAccountId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input name="finalEdit" class="btn btn-secondary btn-lg" type="submit" value="Modifier">
                </form>

                <hr>

                <p><img src="<?php echo $adminCharacterPicture ?>" height="100" width="100"></p>

                <p>Informations du personnage</p>
                
                Classe : <?php echo $adminRaceName ?><br />
                Nom du personnage : <?php echo $adminCharacterName ?><br />
                Niveau du personnage : <?php echo $adminCharacterLevel ?><br />
                <?php echo $itemArmorNameShow ?> : <?php echo $adminEquipmentArmorName ?><br />
                <?php echo $itemBootsNameShow ?> : <?php echo $adminEquipmentBootsName ?><br />
                <?php echo $itemGlovesNameShow ?> : <?php echo $adminEquipmentGlovesName ?><br />
                <?php echo $itemHelmetNameShow ?> : <?php echo $adminEquipmentHelmetName ?><br />
                <?php echo $itemWeaponNameShow ?> : <?php echo $adminEquipmentWeaponName ?><br />
                Sexe du personnage : <?php echo $adminCharacterSexName ?><br />
                HP actuel : <?php echo $adminCharacterHpMin ?><br />
                HP maximum : <?php echo $adminCharacterHpMax ?><br />
                HP points de compétences : <?php echo $adminCharacterHpSkillPoints ?><br />
                HP bonus : <?php echo $adminCharacterHpBonus ?><br />
                HP équipement : <?php echo $adminCharacterHpEquipments ?><br />
                HP bonus guilde : <?php echo $adminCharacterHpGuild ?><br />
                HP total : <?php echo $adminCharacterHpTotal ?><br />
                MP actuel : <?php echo $adminCharacterMpMin ?><br />
                MP maximum : <?php echo $adminCharacterMpMax ?><br />
                MP points de compétences : <?php echo $adminCharacterMpSkillPoints ?><br />
                MP bonus : <?php echo $adminCharacterMpBonus ?><br />
                MP équipements : <?php echo $adminCharacterMpEquipments ?><br />
                MP bonus guilde : <?php echo $adminCharacterMpGuild ?><br />
                MP total : <?php echo $adminCharacterMpTotal ?><br />
                Force actuelle : <?php echo $adminCharacterStrength ?><br />
                Force points de compétences : <?php echo $adminCharacterStrengthSkillPoints ?><br />
                Force bonus : <?php echo $adminCharacterStrengthBonus ?><br />
                Force équipement : <?php echo $adminCharacterStrengthEquipments ?><br />
                Force bonus guilde : <?php echo $adminCharacterStrengthGuild ?><br />
                Force Total : <?php echo $adminCharacterStrengthTotal ?><br />
                Magie actuelle : <?php echo $adminCharacterMagic ?><br />
                Magie points de compétences : <?php echo $adminCharacterMagicSkillPoints ?><br />
                Magie bonus : <?php echo $adminCharacterMagicBonus ?><br />
                Magie équipement : <?php echo $adminCharacterMagicEquipments ?><br />
                Magie bonus guilde : <?php echo $adminCharacterMagicGuild ?><br />
                Magie Total : <?php echo $adminCharacterMagicTotal ?><br />
                Agilité actuelle : <?php echo $adminCharacterAgility ?><br />
                Agilité points de compétences : <?php echo $adminCharacterAgilitySkillPoints ?><br />
                Agilité bonus : <?php echo $adminCharacterAgilityBonus ?><br />
                Agilité équipement : <?php echo $adminCharacterAgilityEquipments ?><br />
                Agilité bonus guilde : <?php echo $adminCharacterAgilityGuild ?><br />
                Agilité Total : <?php echo $adminCharacterAgilityTotal ?><br />
                Défense actuelle : <?php echo $adminCharacterDefense ?><br />
                Défense points de compétences : <?php echo $adminCharacterDefenseSkillPoints ?><br />
                Défense bonus : <?php echo $adminCharacterDefenseBonus ?><br />
                Défense équipment : <?php echo $adminCharacterDefenseEquipment ?><br />
                Défense bonus guilde : <?php echo $adminCharacterDefenseGuild ?><br />
                Défense Total : <?php echo $adminCharacterDefenseTotal ?><br />
                Défense magique actuelle : <?php echo $adminCharacterDefenseMagic ?><br />
                Défense magique points de compétences : <?php echo $adminCharacterDefenseMagicSkillPoints ?><br />
                Défense magique bonus : <?php echo $adminCharacterDefenseMagicBonus ?><br />
                Défense magique équipement : <?php echo $adminCharacterDefenseMagicEquipments ?><br />
                Défense magique bonus guilde : <?php echo $adminCharacterDefenseMagicGuild ?><br />
                Défense magique Total : <?php echo $adminCharacterDefenseMagicTotal ?><br />
                Sagesse actuelle : <?php echo $adminCharacterWisdom ?><br />
                Sagesse points de compétences : <?php echo $adminCharacterWisdomSkillPoints ?><br />
                Sagesse bonus : <?php echo $adminCharacterWisdomBonus ?><br />
                Sagesse équipement : <?php echo $adminCharacterWisdomEquipments ?><br />
                Sagesse bonus guilde : <?php echo $adminCharacterWisdomGuild ?><br />
                Sagesse Total : <?php echo $adminCharacterWisdomTotal ?><br />
                Prospection actuelle : <?php echo $adminCharacterProspecting ?><br />
                Prospection points de compétences : <?php echo $adminCharacterProspectingSkillPoints ?><br />
                Prospection bonus : <?php echo $adminCharacterProspectingBonus ?><br />
                Prospection équipement : <?php echo $adminCharacterProspectingEquipments ?><br />
                Prospection bonus guilde : <?php echo $adminCharacterProspectingGuild ?><br />
                Prospection Total : <?php echo $adminCharacterProspectingTotal ?><br />
                Défaite(s) : <?php echo $adminCharacterDefeate ?><br />
                Victoire(s) : <?php echo $adminCharacterVictory ?><br />
                Experience : <?php echo $adminCharacterExperience ?><br />
                Experience total : <?php echo $adminCharacterExperienceTotal ?><br />
                Points de compétence : <?php echo $adminCharacterSkillPoints ?><br />
                Argent : <?php echo $adminCharacterGold ?><br />
                lieu : <?php echo $adminplaceName ?><br />
                Chapitre : <?php echo $adminCharacterChapter ?><br />
                En combat : <?php echo $adminCharacterOnBattleName ?><br />
                Activé : <?php echo $adminCharacterEnable ?><br />

                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si le compte n'existe pas
            else
            {
                echo "Erreur : Ce compte n'existe pas";
            }
            $accountQuery->closeCursor();
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
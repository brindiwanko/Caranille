<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['itemId'])
&& isset($_POST['token'])
&& isset($_POST['saleQuantity'])
&& isset($_POST['finalSale']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['itemId'])
        && ctype_digit($_POST['saleQuantity'])
        && $_POST['itemId'] >= 1
        && $_POST['saleQuantity'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $itemId = htmlspecialchars($_POST['itemId']);
            $saleQuantity = htmlspecialchars($_POST['saleQuantity']);
    
            //On cherche à savoir si l'équipement/objet qui va se vendre appartient bien au joueur
            $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
            WHERE itemId = inventoryItemId
            AND inventoryCharacterId = ?
            AND itemId = ?");
            $itemQuery->execute([$characterId, $itemId]);
            $itemRow = $itemQuery->rowCount();
    
            //Si le personne possède cet équipement/objet
            if ($itemRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($item = $itemQuery->fetch())
                {
                    //On récupère les informations de l'équipement/objet
                    $inventoryId = $item['inventoryId'];
                    $itemQuantity = $item['inventoryQuantity'];
                    $itemName = $item['itemName'];
                    $itemSalePrice = $item['itemSalePrice'];
                    $inventoryEquipped = $item['inventoryEquipped'];
                }
                $itemQuery->closeCursor();

                //On vérifie si le joueur a suffisament de quantité que ce qu'il souhaite vendre
                if ($itemQuantity >= $saleQuantity)
                {
                    //On calcul le prix de vente total en prenant en compte la quantité
                    $itemSalePriceTotal = $itemSalePrice * $saleQuantity;
        
                    //Si le joueur possède plus de quantité de l'équipement/objet que ce qu'il veut vendre
                    if ($itemQuantity > $saleQuantity)
                    {
                        //On met l'inventaire à jour
                        $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                        inventoryQuantity = inventoryQuantity - :saleQuantity
                        WHERE inventoryId = :inventoryId");
                        $updateInventory->execute(array(
                        'saleQuantity' => $saleQuantity,
                        'inventoryId' => $inventoryId));
                        $updateInventory->closeCursor();
                    }
                    //Si le joueur ne possède cet équipement/objet en autant d'exemplaire que ce qu'il souhaite vendre
                    else
                    {
                        //Si cet équipement est équipé
                        if ($inventoryEquipped == 1)
                        {
                            //On va donc rendre l'équipement non équipé afin de le mettre en vente
                            $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                            inventoryEquipped = 0
                            WHERE inventoryId = :inventoryId");
                            $updateInventory->execute(array(
                            'inventoryId' => $inventoryId));
                            $updateInventory->closeCursor();
        
                            //On remet les stats du joueurs à zéro pour recalculer ensuite le bonus de tous les équipements équipé
                            $updateCharacter = $bdd->prepare("UPDATE car_characters SET
                            characterHpEquipments = 0,
                            characterMpEquipments = 0, 
                            characterStrengthEquipments = 0, 
                            characterMagicEquipments = 0, 
                            characterAgilityEquipments = 0, 
                            characterDefenseEquipments = 0, 
                            characterDefenseMagicEquipments = 0, 
                            characterWisdomEquipments = 0,
                            characterProspectingEquipments = 0
                            WHERE characterId = :characterId");
                            $updateCharacter->execute(array(
                            'characterId' => $characterId));
                            $updateCharacter->closeCursor();
        
                            //Initialisation des variables qui vont contenir les bonus de tous les équipements équipé
                            $hpBonus = 0;
                            $mpBonus = 0;
                            $strengthBonus = 0;
                            $magicBonus = 0;
                            $agilityBonus = 0;
                            $defenseBonus = 0;
                            $defenseMagicBonus = 0;
                            $wisdomBonus = 0;
                            $prospectingBonus = 0;
        
                            //On va maintenant faire une requête sur tous les équipements que possède le joueurs pour rajouter les bonus
                            $equipmentEquipedQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                            WHERE itemId = inventoryItemId
                            AND inventoryEquipped = 1
                            AND inventoryCharacterId = ?");
                            $equipmentEquipedQuery->execute([$characterId]);
        
                            //On fait une boucle sur les résultats et on additionne les bonus de tous les équipements équipé
                            while ($equipment = $equipmentEquipedQuery->fetch())
                            {
                                //On récupère les informations de l'équippement
                                $hpBonus = $hpBonus + $equipment['itemHpEffect'];
                                $mpBonus = $mpBonus + $equipment['itemMpEffect'];
                                $strengthBonus = $strengthBonus + $equipment['itemStrengthEffect'];
                                $magicBonus = $magicBonus + $equipment['itemMagicEffect'];
                                $agilityBonus = $agilityBonus + $equipment['itemAgilityEffect'];
                                $defenseBonus = $defenseBonus + $equipment['itemDefenseEffect'];
                                $defenseMagicBonus = $defenseMagicBonus + $equipment['itemDefenseMagicEffect'];
                                $wisdomBonus = $wisdomBonus + $equipment['itemWisdomEffect'];
                                $prospectingBonus = $prospectingBonus + $equipment['itemProspectingEffect'];
                            }
                            $equipmentEquipedQuery->closeCursor();
        
                            //On ajoute les bonus des stats au joueurs
                            $updateCharacter = $bdd->prepare("UPDATE car_characters SET
                            characterHpEquipments = :hpBonus,
                            characterMpEquipments = :mpBonus, 
                            characterStrengthEquipments = :strengthBonus, 
                            characterMagicEquipments = :magicBonus, 
                            characterAgilityEquipments = :agilityBonus, 
                            characterDefenseEquipments = :defenseBonus, 
                            characterDefenseMagicEquipments = :defenseMagicBonus, 
                            characterWisdomEquipments = :wisdomBonus,
                            characterProspectingEquipments = :prospectingBonus
                            WHERE characterId = :characterId");
                            $updateCharacter->execute(array(
                            'hpBonus' => $hpBonus,
                            'mpBonus' => $mpBonus,
                            'strengthBonus' => $strengthBonus,
                            'magicBonus' => $magicBonus,
                            'agilityBonus' => $agilityBonus,
                            'defenseBonus' => $defenseBonus,
                            'defenseMagicBonus' => $defenseMagicBonus,
                            'wisdomBonus' => $wisdomBonus,
                            'prospectingBonus' => $prospectingBonus,
                            'characterId' => $characterId));
                            $updateCharacter->closeCursor();
        
                            //On va maintenant finir par actualiser tous le personnage
                            $updateCharacter = $bdd->prepare("UPDATE car_characters
                            SET characterHpTotal = characterHpMax + characterHpSkillPoints + characterHpBonus + characterHpEquipments + characterHpGuild,
                            characterMpTotal = characterMpMax + characterMpSkillPoints + characterMpBonus + characterMpEquipments + characterMpGuild,
                            characterStrengthTotal = characterStrength + characterStrengthSkillPoints + characterStrengthBonus + characterStrengthEquipments + characterStrengthGuild,
                            characterMagicTotal = characterMagic + characterMagicSkillPoints + characterMagicBonus + characterMagicEquipments + characterMagicGuild,
                            characterAgilityTotal = characterAgility + characterAgilitySkillPoints + characterAgilityBonus + characterAgilityEquipments + characterAgilityGuild,
                            characterDefenseTotal = characterDefense + characterDefenseSkillPoints + characterDefenseBonus + characterDefenseEquipments + characterDefenseGuild,
                            characterDefenseMagicTotal = characterDefenseMagic + characterDefenseMagicSkillPoints + characterDefenseMagicBonus + characterDefenseMagicEquipments + characterDefenseMagicGuild,
                            characterWisdomTotal = characterWisdom + characterWisdomSkillPoints + characterWisdomBonus + characterWisdomEquipments + characterWisdomGuild,
                            characterProspectingTotal = characterProspecting + characterProspectingSkillPoints + characterProspectingBonus + characterProspectingEquipments + characterProspectingGuild
                            WHERE characterId = :characterId");
                            $updateCharacter->execute(['characterId' => $characterId]);
                            $updateCharacter->closeCursor();                   
                        }
                        //On supprime l'équipement/objet de l'inventaire
                        $updateInventory = $bdd->prepare("DELETE FROM car_inventory
                        WHERE inventoryId = :inventoryId");
                        $updateInventory->execute(array(
                        'inventoryId' => $inventoryId));
                        $updateInventory->closeCursor();
                    }
        
                    //On donne l'argent de la vente au personnage
                    $updatecharacter = $bdd->prepare("UPDATE car_characters SET
                    characterGold = characterGold + :itemSalePriceTotal
                    WHERE characterId = :characterId");
                    $updatecharacter->execute(array(
                    'itemSalePriceTotal' => $itemSalePriceTotal,  
                    'characterId' => $characterId));
                    $updatecharacter->closeCursor();
                    ?>
                    
                    Vous venez de vendre l'objet <?php echo $itemName ?> en <em><?php echo $itemQuantity ?> quantité pour <?php echo $itemSalePriceTotal ?> Pièce(s) d'or.
        
                    <hr>
        
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
                    </form>
                    
                    <?php
                }
                else
                {
                    echo "Erreur : Vous ne possedez pas autant de quantité que ce que vous avez saisit";
                }
            }
            else
            {
                echo "Erreur : Impossible de vendre un équipement/objet que vous ne possédez pas.";
            }
            $itemQuery->closeCursor();
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
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>
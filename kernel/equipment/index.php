<?php
require_once("../../kernel/config.php");

//On fait une requête pour savoir quel équipement le personnage à d'équipé
$equipmentEquipedQuery = $bdd->prepare("SELECT * FROM car_items, car_items_types, car_inventory 
WHERE itemItemTypeId = itemTypeId
AND itemId = inventoryItemId
AND inventoryEquipped = 1
AND inventoryCharacterId = ?");
$equipmentEquipedQuery->execute([$characterId]);

//On fait une boucle sur les résultats et on vérifie à chaque fois de quel type d'équipement il s'agit
while ($equipment = $equipmentEquipedQuery->fetch())
{
    switch ($equipment['itemTypeName'])
    {
        //S'il s'agit d'une armure
        case "Armor":
            $equipmentArmorId = $equipment['itemId'];
            $equipmentArmorName = $equipment['itemName'];
            $equipmentArmorDescription = $equipment['itemDescription'];
        break;

        //S'il s'agit de bottes
        case "Boots":
            $equipmentBootsId = $equipment['itemId'];
            $equipmentBootsName = $equipment['itemName'];
            $equipmentBootsDescription = $equipment['itemDescription'];
        break;

        //S'il s'agit de gants
        case "Gloves":
            $equipmentGlovesId = $equipment['itemId'];
            $equipmentGlovesName = $equipment['itemName'];
            $equipmentGlovesDescription = $equipment['itemDescription'];
        break;

        //S'il s'agit d'un casque
        case "Helmet":
            $equipmentHelmetId = $equipment['itemId'];
            $equipmentHelmetName = $equipment['itemName'];
            $equipmentHelmetDescription = $equipment['itemDescription'];
        break;

        //S'il s'agit d'une arme
        case "Weapon":
            $equipmentWeaponId = $equipment['itemId'];
            $equipmentWeaponName = $equipment['itemName'];
            $equipmentWeaponDescription = $equipment['itemDescription'];
        break;
    }
}
$equipmentEquipedQuery->closeCursor();

//On cherche maintenant à voir quel équipement le personnage n'a pas d'équipé pour ne pas faire appel à une variable qui n'existerait pas

//Si la variable $equipmentArmorId existe pas c'est que le personnage n'en est pas équipé
if (!isset($equipmentArmorId))
{
    $equipmentArmorId = 0;
    $equipmentArmorName = "Vide";
    $equipmentArmorDescription = "";
}

//Si la variable $equipmentBootsId existe pas c'est que le personnage n'en est pas équipé
if (!isset($equipmentBootsId))
{
    $equipmentBootsId = 0;
    $equipmentBootsName = "Vide";
    $equipmentBootsDescription = "";
}

//Si la variable $equipmentGlovesId existe pas c'est que le personnage n'en est pas équipé
if (!isset($equipmentGlovesId))
{
    $equipmentGlovesId = 0;
    $equipmentGlovesName = "Vide";
    $equipmentGlovesDescription = "";
}

//Si la variable $equipmentHelmetId existe pas c'est que le personnage n'en est pas équipé
if (!isset($equipmentHelmetId))
{
    $equipmentHelmetId = 0;
    $equipmentHelmetName = "Vide";
    $equipmentHelmetDescription = "";
}

//Si la variable $equipmentWeaponId existe pas c'est que le personnage n'en est pas équipé
if (!isset($equipmentWeaponId))
{
    $equipmentWeaponId = 0;
    $equipmentWeaponName = "Vide";
    $equipmentWeaponDescription = "";
}
?>
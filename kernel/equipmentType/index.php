<?php
require_once("../../kernel/config.php");

//On fait une requête pour savoir quel équipement le personnage à d'équipé
$itemTypeNameQuery = $bdd->query("SELECT * FROM car_items_types");

//On fait une boucle sur les résultats et on vérifie à chaque fois de quel type d'équipement il s'agit
while ($item = $itemTypeNameQuery->fetch())
{
    switch ($item['itemTypeName'])
    {
        //S'il s'agit d'une armure
        case "Armor":
            $itemArmorNameShow = $item['itemTypeNameShow'];
        break;
    
        //S'il s'agit de bottes
        case "Boots":
            $itemBootsNameShow = $item['itemTypeNameShow'];
        break;
    
        //S'il s'agit de gants
        case "Gloves":
            $itemGlovesNameShow = $item['itemTypeNameShow'];
        break;
    
        //S'il s'agit d'un casque
        case "Helmet":
            $itemHelmetNameShow = $item['itemTypeNameShow'];
        break;
    
        //S'il s'agit d'une arme
        case "Weapon":
            $itemWeaponNameShow = $item['itemTypeNameShow'];
        break;
        
        //S'il s'agit d'un objet
        case "Item":
            $itemItemNameShow = $item['itemTypeNameShow'];
        break;
        
        //S'il s'agit d'un parchemin
        case "Parchment":
            $itemParchmentNameShow = $item['itemTypeNameShow'];
        break;
    }
}
$itemTypeNameQuery->closeCursor();
?>
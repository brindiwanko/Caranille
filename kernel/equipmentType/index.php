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
            $itemArmorNameShow = stripslashes($item['itemTypeNameShow']);
        break;
    
        //S'il s'agit de bottes
        case "Boots":
            $itemBootsNameShow = stripslashes($item['itemTypeNameShow']);
        break;
    
        //S'il s'agit de gants
        case "Gloves":
            $itemGlovesNameShow = stripslashes($item['itemTypeNameShow']);
        break;
    
        //S'il s'agit d'un casque
        case "Helmet":
            $itemHelmetNameShow = stripslashes($item['itemTypeNameShow']);
        break;
    
        //S'il s'agit d'une arme
        case "Weapon":
            $itemWeaponNameShow = stripslashes($item['itemTypeNameShow']);
        break;
        
        //S'il s'agit d'un objet
        case "Item":
            $itemItemNameShow = stripslashes($item['itemTypeNameShow']);
        break;
        
        //S'il s'agit d'un parchemin
        case "Parchment":
            $itemParchmentNameShow = stripslashes($item['itemTypeNameShow']);
        break;
    }
}
$itemTypeNameQuery->closeCursor();
?>
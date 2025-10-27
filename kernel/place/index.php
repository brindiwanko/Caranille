<?php
require_once("../../kernel/config.php");

//Si le personnage est dans un lieu
if ($characterPlaceId >= 1)
{
    //On fait une recherche dans la base de donnée pour récupérer le lieu du personnage
    $placeQuery = $bdd->prepare("SELECT * FROM car_places 
    WHERE placeId = ?");
    $placeQuery->execute([$characterPlaceId]);

    //On fait une boucle sur les résultats
    while ($place = $placeQuery->fetch())
    {
        //On récupère les informations du lieu
        $placeId = $place['placeId'];
        $placePicture = $place['placePicture'];
        $placeName = $place['placeName'];
        $placeDescription = nl2br(htmlspecialchars($place['placeDescription']));
        $placePriceInn = $place['placePriceInn'];
        $placeChapter = $place['placeChapter'];
        $placeAccess = $place['placeAccess'];
    }
    $placeQuery->closeCursor();

    //On fait une recherche du nombre de magasin dans cette ville
    $shopPlaceQuery = $bdd->prepare("SELECT * FROM car_places_shops
    WHERE placeShopPlaceId = ?");
    $shopPlaceQuery->execute([$placeId]);
    $shopPlaceRow = $shopPlaceQuery->rowCount();
}
?>
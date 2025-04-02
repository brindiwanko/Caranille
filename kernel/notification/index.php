<?php
require_once("../../kernel/config.php");

//On fait une requête pour vérifier le nombre de notifications non lue
$notificationNumberQuery = $bdd->prepare("SELECT * FROM car_notifications
WHERE notificationCharacterId = ?
AND notificationRead = 'No'");
$notificationNumberQuery->execute([$characterId]);
$notificationNumberRow = $notificationNumberQuery->rowCount();

$totalNotification = $notificationNumberRow;
?>

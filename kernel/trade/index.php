<?php
require_once("../../kernel/config.php");

//On fait une requête pour vérifier toutes les demandes d'échange en cours avec notre joueur
$tradeQuery = $bdd->prepare("SELECT * FROM car_trades
WHERE (tradeCharacterOneId = ?
OR tradeCharacterTwoId = ?)");
$tradeQuery->execute([$characterId, $characterId]);
$tradeRow = $tradeQuery->rowCount();
?>
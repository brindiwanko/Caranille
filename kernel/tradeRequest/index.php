<?php
require_once("../../kernel/config.php");

//On fait une requête pour vérifier toutes les demandes d'échange reçus à notre joueur
$tradeRequestQuery = $bdd->prepare("SELECT * FROM car_trades_requests
WHERE tradeRequestCharacterTwoId = ?");
$tradeRequestQuery->execute([$characterId]);
$tradeRequestRow = $tradeRequestQuery->rowCount();
?>
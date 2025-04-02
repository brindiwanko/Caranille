<?php
require_once("../../kernel/config.php");

//On fait une requête pour vérifier le nombre de message non lu
$privateConversationNumberQuery = $bdd->prepare("SELECT * FROM car_private_conversation, car_private_conversation_message
WHERE privateConversationMessagePrivateConversationId = privateConversationId
AND (privateConversationCharacterOneId = ?
OR privateConversationCharacterTwoId = ?)
AND privateConversationMessageCharacterId != ?
AND privateConversationMessageRead = 'No'");
$privateConversationNumberQuery->execute([$characterId, $characterId, $characterId]);
$privateConversationNumberRow = $privateConversationNumberQuery->rowCount();
?>

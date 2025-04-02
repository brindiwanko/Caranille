<?php
require_once("../../kernel/config.php");

//On fait une requête pour vérifier le nombre d'invitation de combat reçu
$battleInvitationQuery = $bdd->prepare("SELECT * FROM car_battles_invitations_characters
WHERE battleInvitationCharacterCharacterId = ?");
$battleInvitationQuery->execute([$characterId]);
$battleInvitationRow = $battleInvitationQuery->rowCount();
?>

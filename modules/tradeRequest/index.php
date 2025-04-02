<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");
?>

Bienvenue sur la place des échanges<br />
Ici vous allez pouvoir proposer ou reçevoir des demandes d'échanges des joueurs du jeu<br /><br />

<hr>

<form method="POST" action="tradeRequestSent.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="viewTrade" value="Demandes d'échange envoyée">
</form>

<form method="POST" action="tradeRequestReceived.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="viewTrade" value="Demandes d'échange reçue">
</form>

<form method="POST" action="tradeRequest.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="newTrade" value="Nouvelle demande d'échange">
</form>

<hr>

<form method="POST" action="../../modules/trade/index.php">
    <input type="submit" class="btn btn-secondary btn-lg" name="manage" value="Echanges en cours">
</form>

<?php require_once("../../html/footer.php"); ?>
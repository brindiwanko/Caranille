<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");

$accountQuery = $bdd->query("SELECT * FROM car_accounts 
WHERE accountId = 1");
$accountQuery->execute();

//On fait une boucle pour récupérer les résultats
while ($account = $accountQuery->fetch())
{
    $accountEmail = $account['accountEmail'];
}
?>

<h1>Nous contacter</h1>
Pour toutes questions/suggestion et soumissions de bogues merci de me contacter à cette adresse Email : <?php echo $accountEmail ?><br /><br />

Attention : Pour toute demande de changement de nom d'utilisateur ou d'adresse Email vous devez être en mesure de pouvoir répondre à votre question secrète qui vous serà demandée par Email.<br /><br />

Tout compte utilisateurs n'ayant pas crée de question secrète (Via mon compte -> Sécurité) ne pourra obtenir de réponse favorable du support.<br /><br />

Bien cordialement,

<?php require_once("../../html/footer.php"); ?>
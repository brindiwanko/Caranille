<?php
//On déclare les variables nécessaire pour la requête SQL
$accountId = $_SESSION['account']['id'];

$accountQuery = $bdd->prepare("SELECT * FROM car_accounts 
WHERE accountId = ?");
$accountQuery->execute([$accountId]);

//On fait une boucle pour récupérer les résultats
while ($account = $accountQuery->fetch())
{
    //On récupère les informations du compte
    $accountId = $account['accountId'];
    $accountPseudo = $account['accountPseudo'];
    $accountEmail = $account['accountEmail'];
    $accountSecretQuestion = $account['accountSecretQuestion'];
    $accountSecretAnswer = $account['accountSecretAnswer'];
    $accountAccess = $account['accountAccess'];
    $accountStatus = $account['accountStatus'];
    $accountReason = $account['accountReason'];
    $accountLastAction = $account['accountLastAction'];  
    $accountLastConnection = $account['accountLastConnection'];        
    $accountLastIp = $account['accountLastIp'];
}
$accountQuery->closeCursor();

$accountLastAction = $date = date('Y-m-d H:i:s');

//On met à jour la dernière action du compte dans la base de donnée
$updateAccount = $bdd->prepare("UPDATE car_accounts SET 
accountLastAction = :accountLastAction
WHERE accountId = :accountId");
$updateAccount->execute(array(
'accountLastAction' => $accountLastAction,   
'accountId' => $accountId));

//Si le joueur est banni
if ($accountStatus == 1)
{
    //On redirige à la page d'accueil
    header("Location: ../logout/index.php");
}
?>

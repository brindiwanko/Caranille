<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

$notificationQuery = $bdd->prepare("SELECT * FROM car_notifications
WHERE notificationCharacterId = ?
ORDER BY notificationId DESC");
$notificationQuery->execute([$characterId]);
$notificationRow = $notificationQuery->rowCount();

//Si il y a des notifications on les affiches
if ($notificationRow > 0)
{
    ?>
    
    <p>Affichage de toutes les notifications</p>
    
    <table class="table">
        
        <tr>
            <td>
                Date/Heure
            </td>
        
            <td>
                Message
            </td>
            
        </tr>
        
        <?php
        //On fait une boucle pour récupérer toutes les information
        while ($notification = $notificationQuery->fetch())
        {
            //On récupère les informations de la notification
            $notificationId = $notification['notificationId'];
            $notificationDateTime = $notification['notificationDateTime'];
            $notificationMessage = $notification['notificationMessage'];
            $notificationRead = $notification['notificationRead'];
            
            //Si la notification n'est pas lue
            if ($notificationRead == "No")
            {
                //On peut enfin le mettre lu car on vient de le lire
                $updateNotification = $bdd->prepare("UPDATE car_notifications
                SET notificationRead = 'Yes'
                WHERE notificationId = :notificationId");
                $updateNotification->execute([
                'notificationId' => $notificationId]);
                $updateNotification->closeCursor();
            }
            ?>
            
            <tr>
                <td>
                    <?php echo (new DateTime($notificationDateTime))->format('d-m-Y H:i') ?> 
                </td>
                
                <td>
                    <?php echo $notificationMessage ?> 
                </td>

            </tr>
            
        <?php
        }
        ?>
        
    </table>
    
    <?php
}
else
{
    echo "Vous n'avez aucune notification";
}
$notificationQuery->closeCursor();
?>
     
<hr>

<form method="POST" action="index.php">
    <input type="submit" class="btn btn-secondary btn-lg" name="showAllMessages" value="Retour">
</form>

<?php require_once("../../html/footer.php"); ?>
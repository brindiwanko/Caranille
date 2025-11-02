<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");

//On recherche la liste des classes dans la base de donnée
$classeQuery = $bdd->query("SELECT * FROM car_classes");
$classeRow = $classeQuery->rowCount();

//On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
while ($classe = $classeQuery->fetch()) 
{
    ?>
    
    <p><img src="<?php echo $classe['classePicture'] ?>" height="100" width="100"></p>
    
     <table class="table">
            <tr>
                <td>
                    Nom
                </td>
            
                <td>
                    <?php echo $classe['classeName']; ?>
                </td>
            </tr>

            <tr>
                <td>
                    Description
                </td>
                
                <td>
                    <?php echo nl2br(htmlspecialchars($classe['classeDescription'])); ?>
                </td>
            </tr>
                
            <tr>
                <td>
                    Amélioration par niveau
                </td>
                
                <td>
                    <?php echo '+' .$classe['classeHpBonus']. ' HP' ?><br />
                    <?php echo '+' .$classe['classeMpBonus']. ' MP' ?><br />
                    <?php echo '+' .$classe['classestrengthBonus']. ' Force' ?><br />
                    <?php echo '+' .$classe['classeMagicBonus']. ' Magie' ?><br />
                    <?php echo '+' .$classe['classeAgilityBonus']. ' Agilité' ?><br />
                    <?php echo '+' .$classe['classeDefenseBonus']. ' Défense' ?><br />
                    <?php echo '+' .$classe['classeDefenseMagicBonus']. ' Défense magique' ?><br />
                    <?php echo '+' .$classe['classeWisdomBonus']. ' Sagesse' ?><br />
                    <?php echo '+' .$classe['classeProspectingBonus']. ' Prospection' ?>
                </td>
            </tr>
        </table>
            
    <?php
}
$classeQuery->closeCursor();

require_once("../../html/footer.php"); ?>
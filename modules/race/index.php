<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");

//On recherche la liste des races dans la base de donnée
$raceQuery = $bdd->query("SELECT * FROM car_races");
$raceRow = $raceQuery->rowCount();

//On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
while ($race = $raceQuery->fetch()) 
{
    ?>
    
    <p><img src="<?php echo $race['racePicture'] ?>" height="100" width="100"></p>
    
     <table class="table">
            <tr>
                <td>
                    Nom
                </td>
            
                <td>
                    <?php echo $race['raceName']; ?>
                </td>
            </tr>

            <tr>
                <td>
                    Description
                </td>
                
                <td>
                    <?php echo nl2br(htmlspecialchars($race['raceDescription'])); ?>
                </td>
            </tr>
                
            <tr>
                <td>
                    Amélioration par niveau
                </td>
                
                <td>
                    <?php echo '+' .$race['raceHpBonus']. ' HP' ?><br />
                    <?php echo '+' .$race['raceMpBonus']. ' MP' ?><br />
                    <?php echo '+' .$race['raceStrengthBonus']. ' Force' ?><br />
                    <?php echo '+' .$race['raceMagicBonus']. ' Magie' ?><br />
                    <?php echo '+' .$race['raceAgilityBonus']. ' Agilité' ?><br />
                    <?php echo '+' .$race['raceDefenseBonus']. ' Défense' ?><br />
                    <?php echo '+' .$race['raceDefenseMagicBonus']. ' Défense magique' ?><br />
                    <?php echo '+' .$race['raceWisdomBonus']. ' Sagesse' ?><br />
                    <?php echo '+' .$race['raceProspectingBonus']. ' Prospection' ?>
                </td>
            </tr>
        </table>
            
    <?php
}
$raceQuery->closeCursor();

require_once("../../html/footer.php"); ?>
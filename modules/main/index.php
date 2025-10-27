<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");

//On recherche la liste des news dans la base de donnée
$newsQuery = $bdd->query("SELECT * FROM car_news ORDER BY newsId desc LIMIT 0,4");
$newsRow = $newsQuery->rowCount();

//S'il existe des news on les affiche
if ($newsRow > 0)
{
    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
    while ($news = $newsQuery->fetch()) 
    {
        ?>
        
        <p><img src="<?php echo $news['newsPicture'] ?>" height="100" width="100"></p>
        
        <h4><?php echo $news['newsTitle']; ?> (le <?php echo (new DateTime($news['newsDate']))->format('d-m-Y') ?> par <?php echo $news['newsAccountPseudo']; ?>)</h4>
        <?php echo nl2br(htmlspecialchars($news['newsMessage'])) ?><br /><br />
        
        <hr>
        
        <?php
    }
}
//S'il n'y a aucune news on prévient le joueur
else
{
    echo "Il n'y a actuellement aucune news ";
}
$newsQuery->closeCursor();

require_once("../../html/footer.php"); ?>

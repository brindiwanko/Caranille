<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['add']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie S'il existe au moins un monstre pour créer le monstre du chapitre (le boss)
        $monsterQuery = $bdd->query("SELECT * FROM car_monsters");
        $monsterRow = $monsterQuery->rowCount();
        
        //S'il y a au moins un monstre de disponible on peut créer un chapitre
        if ($monsterRow >= 1)
        {
            ?>
            
            <p>Informations du chapitre</p>
            
            <form method="POST" action="addChapterEnd.php">
                Monstre du chapitre <select name="adminChapterMonsterId" class="form-control">
                    
                    <?php
                    //On fait une boucle sur tous les résultats
                    while ($monster = $monsterQuery->fetch())
                    {
                        //on récupère les valeurs de chaque monstres qu'on va ensuite mettre dans le menu déroulant
                        $adminMonsterId = $monster['monsterId']; 
                        $adminMonsterName = $monster['monsterName'];
                        ?>
                        <option value="<?php echo $adminMonsterId ?>"><?php echo "N°$adminMonsterId - $adminMonsterName" ?></option>
                        <?php
                    }
                    $monsterQuery->closeCursor();
                    ?>
                    
                </select>
                Titre : <input type="text" name="adminChapterTitle" class="form-control" placeholder="Titre" required>
                Introduction :  <br> <textarea class="form-control" name="adminChapterOpening" id="adminChapterOpening" rows="3" required></textarea>
                Conclusion :  <br> <textarea class="form-control" name="adminChapterEnding" id="adminChapterEnding" rows="3" required></textarea>
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input name="finalAdd" class="btn btn-secondary btn-lg" type="submit" value="Ajouter">
            </form>
            
            <hr>

            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
            </form>
            
            <?php
        }
        //S'il n'y a aucun monstre dans le jeu
        else
        {
            echo "Erreur : Impossible de créer un chapitre si votre jeu ne possède aucun monstre";
        }
    }
    //Si le token de sécurité n'est pas correct
    else
    {
        echo "Erreur : La session a expirée, veuillez réessayer";
    }
}
//Si l'utilisateur n'a pas cliqué sur le bouton edit
else
{
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
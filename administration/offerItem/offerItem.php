<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminCharacterId'])
&& isset($_POST['adminItemId'])
&& isset($_POST['adminItemQuantity'])
&& isset($_POST['token'])
&& isset($_POST['offerItem']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminCharacterId'])
        && ctype_digit($_POST['adminItemId'])
        && ctype_digit($_POST['adminItemQuantity'])
        && $_POST['adminCharacterId'] >= 0
        && $_POST['adminItemId'] >= 0
        && $_POST['adminItemQuantity'] >= 0)
        {
            //On récupère les informations du formulaire précédent
            $adminCharacterId = htmlspecialchars($_POST['adminCharacterId']);
            $adminItemId = htmlspecialchars($_POST['adminItemId']);
            $adminItemQuantity = htmlspecialchars($_POST['adminItemQuantity']);
            
            //Si l'objet à offrir est pour tous les joueurs
            if ($adminCharacterId == 0)
            {
                //On fait une requête pour vérifier si l'objet choisit existe
                $itemQuery = $bdd->prepare("SELECT * FROM car_items 
                WHERE itemId = ?");
                $itemQuery->execute([$adminItemId]);
                $itemRow = $itemQuery->rowCount();

                //Si l'objet existe
                if ($itemRow == 1) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($item = $itemQuery->fetch())
                    {
                        //On récupère les informations de l'objet
                        $adminItemId = $item['itemId'];
                        $adminItemName = $item['itemName'];
                    }
                    ?>
                    
                    <p>ATTENTION</p> 
                    Vous êtes sur le point d'offrir l'objet <em><?php echo $adminItemName ?></em> en <?php echo $adminItemQuantity ?> quantité(s) à <em>tous les joueurs</em>.<br />
                    Confirmez-vous  ?

                    <hr>
                        
                    <form method="POST" action="offerItemEnd.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="adminCharacterId" value="<?php echo $adminCharacterId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="adminItemId" value="<?php echo $adminItemId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="adminItemQuantity" value="<?php echo $adminItemQuantity ?>">
                        <input type="submit" class="btn btn-secondary btn-lg" name="finalAdd" value="Je confirme">
                    </form>
                    
                    <hr>

                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php

                }
                //Si l'objet n'exite pas
                else
                {
                    echo "Erreur : Cet objet n'existe pas";
                }
                $itemQuery->closeCursor();
            }
            //Si l'objet à offrir est pour un seul joueur
            else 
            {
                //On fait une requête pour vérifier si le personnage existe
                $characterQuery = $bdd->prepare("SELECT * FROM car_characters 
                WHERE characterId = ?");
                $characterQuery->execute([$adminCharacterId]);
                $characterRow = $characterQuery->rowCount();
        
                //Si le personnage existe
                if ($characterRow == 1)
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($character = $characterQuery->fetch())
                    {
                        $adminCharacterName = $character['characterName'];
                    }

                    //On fait une requête pour vérifier si l'objet choisit existe
                    $itemQuery = $bdd->prepare("SELECT * FROM car_items 
                    WHERE itemId = ?");
                    $itemQuery->execute([$adminItemId]);
                    $itemRow = $itemQuery->rowCount();

                    //Si l'objet existe
                    if ($itemRow == 1) 
                    {
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($item = $itemQuery->fetch())
                        {
                            //On récupère les informations de l'objet
                            $adminItemId = $item['itemId'];
                            $adminItemName = $item['itemName'];
                        }
                        ?>
                        
                        <p>ATTENTION</p>

                        Vous êtes sur le point d'offrir l'objet <em><?php echo $adminItemName ?></em> en <?php echo $adminItemQuantity ?> quantité(s) à <em><?php echo $adminCharacterName ?></em>.<br />
                        Confirmez-vous ?
            
                        <hr>
                            
                        <form method="POST" action="offerItemEnd.php">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="adminCharacterId" value="<?php echo $adminCharacterId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="adminItemId" value="<?php echo $adminItemId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="adminItemQuantity" value="<?php echo $adminItemQuantity ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" class="btn btn-secondary btn-lg" name="finalAdd" value="Je confirme">
                        </form>
                        
                        <hr>
            
                        <form method="POST" action="index.php">
                            <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                        </form>
                
                        <?php

                    }
                    //Si l'objet n'exite pas
                    else
                    {
                        echo "Erreur : Cet objet n'existe pas";
                    }
                    $itemQuery->closeCursor();
                }
                //Si le compte n'existe pas
                else
                {
                    echo "Erreur : Ce compte n'existe pas";
                }
                $accountQuery->closeCursor();
            }
        }
        //Si tous les champs numérique ne contiennent pas un nombre
        else
        {
            echo "Erreur : Les champs de type numérique ne peuvent contenir qu'un nombre entier";
        }
    }
    //Si le token de sécurité n'est pas correct
    else
    {
        echo "Erreur : La session a expirée, veuillez réessayer";
    }
}
//Si toutes les variables $_POST n'existent pas
else
{
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
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
&& isset($_POST['finalAdd']))
{
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

                //On fait une requêtes pour récupérer chaque personnage
                $characterQuery = $bdd->query("SELECT * FROM car_characters
                ORDER by characterName");
                
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($character = $characterQuery->fetch())
                {
                    //On récupère l'id et le nom du personnage
                    $adminCharacterId = $character['characterId'];
                    $adminCharacterName =  $character['characterName'];

                    //On vérifie si le joueur possède déjà cet objet ou équipement
                    $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                    WHERE itemId = inventoryItemId
                    AND inventoryCharacterId = ?
                    AND itemId = ?");
                    $itemQuery->execute([$adminCharacterId, $adminItemId]);
                    $itemRow = $itemQuery->rowCount();
    
                    //Si le joueur possède déjà cet objet ou équipement on modifie les quantités de celui-ci
                    if ($itemRow > 0)
                    {
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($item = $itemQuery->fetch())
                        {
                            //On récupère les informations de l'inventaire
                            $inventoryId = $item['inventoryId'];
                            $itemQuantity = $item['inventoryQuantity'];
                            $inventoryEquipped = $item['inventoryEquipped'];
                        }

                        //On met l'inventaire à jour
                        $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                        inventoryQuantity = inventoryQuantity + :itemQuantity
                        WHERE inventoryId = :inventoryId");
                        $updateInventory->execute(array(
                        'itemQuantity' => $adminItemQuantity,
                        'inventoryId' => $inventoryId));
                        $updateInventory->closeCursor(); 
                    }
                    //Si le joueur ne possède pas cet objet on l'ajoute dans l'inventaire
                    else
                    {
                        $addItem = $bdd->prepare("INSERT INTO car_inventory VALUES(
                        NULL,
                        :adminCharacterId,
                        :adminItemId,
                        :adminItemQuantity,
                        '0')");
                        $addItem->execute([
                        'adminCharacterId' => $adminCharacterId,
                        'adminItemId' => $adminItemId,
                        'adminItemQuantity' => $adminItemQuantity]);
                        $addItem->closeCursor();  
                    }
                    $itemQuery->closeCursor();
                }
                ?>
                
                Vous venez d'offrir l'objet <em><?php echo $adminItemName ?></em> en <?php echo $adminItemQuantity ?> quantité(s) à <em>tous les joueurs</em>.<br />
                
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

                    //On vérifie si le joueur possède déjà cet objet ou équipement
                    $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_inventory 
                    WHERE itemId = inventoryItemId
                    AND inventoryCharacterId = ?
                    AND itemId = ?");
                    $itemQuery->execute([$adminCharacterId, $adminItemId]);
                    $itemRow = $itemQuery->rowCount();
    
                    //Si le joueur possède déjà cet objet ou équipement on modifie les quantités de celui-ci
                    if ($itemRow > 0)
                    {
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($item = $itemQuery->fetch())
                        {
                            //On récupère les informations de l'inventaire
                            $inventoryId = $item['inventoryId'];
                            $itemQuantity = $item['inventoryQuantity'];
                            $inventoryEquipped = $item['inventoryEquipped'];
                        }

                        //On met l'inventaire à jour
                        $updateInventory = $bdd->prepare("UPDATE car_inventory SET
                        inventoryQuantity = inventoryQuantity + :itemQuantity
                        WHERE inventoryId = :inventoryId");
                        $updateInventory->execute(array(
                        'itemQuantity' => $adminItemQuantity,
                        'inventoryId' => $inventoryId));
                        $updateInventory->closeCursor(); 
                    }
                    //Si le joueur ne possède pas cet objet on l'ajoute dans l'inventaire
                    else
                    {
                        $addItem = $bdd->prepare("INSERT INTO car_inventory VALUES(
                        NULL,
                        :adminCharacterId,
                        :adminItemId,
                        :adminItemQuantity,
                        '0')");
                        $addItem->execute([
                        'adminCharacterId' => $adminCharacterId,
                        'adminItemId' => $adminItemId,
                        'adminItemQuantity' => $adminItemQuantity]);
                        $addItem->closeCursor();  
                    }
                    $itemQuery->closeCursor();
                    ?>

                    Vous venez d'offrir l'objet <em><?php echo $adminItemName ?></em> en <?php echo $adminItemQuantity ?> quantité(s) à <em><?php echo $adminCharacterName ?></em>.<br />

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
//Si toutes les variables $_POST n'existent pas
else
{
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");
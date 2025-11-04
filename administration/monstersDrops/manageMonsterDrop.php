<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminMonsterDropMonsterId'])
&& isset($_POST['token'])
&& isset($_POST['manage']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminMonsterDropMonsterId'])
        && $_POST['adminMonsterDropMonsterId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminMonsterDropMonsterId = htmlspecialchars($_POST['adminMonsterDropMonsterId']);

            //On fait une requête pour vérifier si le monstre choisit existe
            $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters 
            WHERE monsterId = ?");
            $monsterQuery->execute([$adminMonsterDropMonsterId]);
            $monsterRow = $monsterQuery->rowCount();

            //Si le monstre existe
            if ($monsterRow == 1) 
            {
                $monsterDropQuery = $bdd->prepare("SELECT * FROM car_monsters, car_items, car_items_types, car_monsters_drops
                WHERE itemItemTypeId = itemTypeId
                AND monsterDropMonsterID = monsterId
                AND monsterDropItemId = itemId
                AND monsterDropMonsterId = ?
                ORDER BY itemItemTypeId, itemName");
                $monsterDropQuery->execute([$adminMonsterDropMonsterId]);
                $monsterDropRow = $monsterDropQuery->rowCount();

                //S'il existe un ou plusieurs objet pour ce monstre on affiche le menu déroulant
                if ($monsterDropRow > 0) 
                {
                    ?>
                    
                    <form method="POST" action="editDeleteMonsterDrop.php">
                        Objet(s) (Obtention /1000) : <select name="adminMonsterDropItemId" class="form-control">
                            
                        <?php
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($monsterDrop = $monsterDropQuery->fetch())
                        {
                            //On récupère les informations des objets du monstre
                            $adminMonsterDropItemId = $monsterDrop['itemId'];
                            $adminMonsterDropItemName = $monsterDrop['itemName'];
                            $adminMonsterDropRate = $monsterDrop['monsterDropRate'];
                            $adminItemTypeName = $monsterDrop['itemTypeName'];
                            $adminItemTypeNameShow = $monsterDrop['itemTypeNameShow'];
                            ?>
                            <option value="<?php echo $adminMonsterDropItemId ?>"><?php echo "[$adminItemTypeNameShow] - $adminMonsterDropItemName ($adminMonsterDropRate/1000)"; ?></option>
                            <?php
                        }
                        $monsterDropQuery->closeCursor();
                        ?>
                            
                        </select>
                        <input type="hidden" name="adminMonsterDropMonsterId" value="<?php echo $adminMonsterDropMonsterId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="edit" class="btn btn-secondary btn-lg" value="Modifier">
                        <input type="submit" name="delete" class="btn btn-secondary btn-lg" value="Retirer">
                    </form>

                    <hr>

                    <?php
                }
                $monsterQuery->closeCursor();

                //On recherche la liste des objets et équipements du jeu qui ne sont pas attribué à ce monstre
                $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_items_types
                WHERE itemItemTypeId = itemTypeId
                AND (SELECT COUNT(*) FROM car_monsters_drops
                WHERE monsterDropMonsterId = ?
                AND monsterDropItemId = itemId) = 0
                ORDER BY itemItemTypeId, itemName");
                $itemQuery->execute([$adminMonsterDropMonsterId]);
                $itemRow = $itemQuery->rowCount();
                //S'il existe un ou plusieurs objets on affiche le menu déroulant pour proposer au joueur d'en ajouter
                if ($itemRow > 0) 
                {
                    ?>
                    
                    <form method="POST" action="addMonsterDrop.php">
                        Objet(s) existant : <select name="adminMonsterDropItemId" class="form-control">
                                
                            <?php
                            while ($item = $itemQuery->fetch())
                            {
                                //On récupère les informations des objets
                                $adminMonsterDropItemId = $item['itemId'];
                                $adminMonsterDropItemName = $item['itemName'];
                                $adminItemTypeName = $item['itemTypeName'];
                                $adminItemTypeNameShow = $item['itemTypeNameShow'];
                                ?>
                                <option value="<?php echo $adminMonsterDropItemId ?>"><?php echo "[$adminItemTypeNameShow] - $adminMonsterDropItemName"; ?></option>
                                <?php
                            }
                            $itemQuery->closeCursor();
                            ?>
                                
                        </select>
                        Visible dans le bestiaire ? : <select name="adminMonsterDropItemVisible" class="form-control">                        
                            <option value="Yes">Oui</option>
                            <option value="No">Non</option>
                        </select>
                        Taux d'obtention sur 1000 <br />
                        (1 = 0,10%, 10 = 1% etc...) : <input type="number" name="adminMonsterDropRate" class="form-control" placeholder="Taux d'obtention (sur 1000)" value="0" required>
                        Taux visible dans le bestiaire ? : <select name="adminMonsterDropRateVisible" class="form-control">
                            <option value="Yes">Oui</option>
                            <option value="No">Non</option>
                        </select>
                        <input type="hidden" name="adminMonsterDropMonsterId" value="<?php echo $adminMonsterDropMonsterId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="add" class="btn btn-secondary btn-lg" value="Ajouter cet équipement/objet">
                    </form>
                    
                    <?php
                }
                else
                {
                echo "Il n'y a actuellement aucun objets";
                }
                ?>

                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si le monstre n'exite pas
            else
            {
            echo "Erreur : Ce monstre n'existe pas";
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
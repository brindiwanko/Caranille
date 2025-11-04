<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminItemId'])
&& isset($_POST['token'])
&& isset($_POST['edit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminItemId'])
        && $_POST['adminItemId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminItemId = htmlspecialchars($_POST['adminItemId']);

            //On fait une requête pour vérifier si l'équipement choisit existe
            $itemQuery = $bdd->prepare("SELECT * FROM car_items, car_items_types
            WHERE itemItemTypeId = itemTypeId
            AND itemId = ?");
            $itemQuery->execute([$adminItemId]);
            $itemRow = $itemQuery->rowCount();

            //Si l'équipement existe
            if ($itemRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($item = $itemQuery->fetch())
                {
                    //On récupère les informations de l'équipement
                    $adminItemId = $item['itemId'];
                    $adminItemclasseId = $item['itemclasseId'];
                    $adminItemPicture = $item['itemPicture'];
                    $adminItemItemTypeId = $item['itemItemTypeId'];
                    $adminItemItemTypeName = $item['itemTypeName'];
                    $adminItemItemTypeNameShow = $item['itemTypeNameShow'];
                    $adminItemName = $item['itemName'];
                    $adminItemDescription = $item['itemDescription'];
                    $adminItemLevel = $item['itemLevel'];
                    $adminItemLevelRequired = $item['itemLevelRequired'];
                    $adminItemHpEffects = $item['itemHpEffect'];
                    $adminItemMpEffect = $item['itemMpEffect'];
                    $adminItemStrengthEffect = $item['itemStrengthEffect'];
                    $adminItemMagicEffect = $item['itemMagicEffect'];
                    $adminItemAgilityEffect = $item['itemAgilityEffect'];
                    $adminItemDefenseEffect = $item['itemDefenseEffect'];
                    $adminItemDefenseMagicEffect = $item['itemDefenseMagicEffect'];
                    $adminItemWisdomEffect = $item['itemWisdomEffect'];
                    $adminItemProspectingEffect = $item['itemProspectingEffect'];
                    $adminItemPurchasePrice = $item['itemPurchasePrice'];
                    $adminItemSalePrice = $item['itemSalePrice'];
                }

                //On récupère la classe de l'équipement pour l'afficher dans le menu d'information de l'équipement
                $classeQuery = $bdd->prepare("SELECT * FROM car_classes
                WHERE classeId = ?");
                $classeQuery->execute([$adminItemclasseId]);
                while ($classe = $classeQuery->fetch())
                {
                    //On récupère les informations de la classe
                    $adminclasseId = $classe['classeId'];
                    $adminclasseName = $classe['classeName'];
                }
                $classeQuery->closeCursor();
                ?>

                <p>Informations de l'équipement</p>

                <p><img src="<?php echo $adminItemPicture ?>" height="100" width="100"></p>

                <form method="POST" action="editEquipmentEnd.php">
                    Classe <select name="adminItemclasseId" class="form-control">
                        
                        <?php
                        //Si l'équipement a une classe attribuée on l'affiche par défaut
                        if (isset($adminclasseId))
                        {
                            ?>
                            <option selected="selected" value="<?php echo $adminclasseId ?>"><?php echo $adminclasseName ?></option>
                            <?php
                        }
                        //Si l'équipement n'a pas de classe attribuée c'est qu'il est disponible pour toutes les classes
                        else
                        {
                            ?>
                            <option selected="selected" value="0">Toutes les classes</option>
                            <?php
                        }

                        //On rempli le menu déroulant avec la liste des classes disponible sans afficher la classe actuelle qui est affiché juste au dessus
                        $classeListQuery = $bdd->prepare("SELECT * FROM car_classes
                        WHERE classeId != ?");
                        $classeListQuery->execute([$adminItemclasseId]);
                        $classeList = $classeListQuery->rowCount();
                        
                        //S'il y a au moins une classe de disponible on les affiches dans le menu déroulant
                        if ($classeList >= 1)
                        {
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($classeList = $classeListQuery->fetch())
                            {
                                //On récupère les informations de la classe
                                $classeId = $classeList['classeId']; 
                                $classeName = $classeList['classeName'];
                                ?>
                                <option value="<?php echo $classeId ?>"><?php echo $classeName ?></option>
                                <?php
                            }
                        }
                        $classeListQuery->closeCursor();
                        
                        //Si l'équipement a une classe attribuée on donne la possibilité au joueur de le rendre disponible à toutes les classes
                        if (isset($adminclasseId))
                        {
                            ?>
                            <option value="0">Toutes les classes</option>
                            <?php
                        }
                        ?>
                        
                    </select>
                    Image : <input type="mail" name="adminItemPicture" class="form-control" placeholder="Image" value="<?php echo $adminItemPicture ?>" required>
                    Type <select name="adminItemItemTypeId" class="form-control">
                        
                        <?php
                        //On rempli le menu déroulant avec la liste des classes disponible
                        $itemTypeQuery = $bdd->query("SELECT * FROM car_items_types
                        WHERE itemTypeName != 'Item'
                        AND itemTypeName != 'Parchment'");
                        
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($itemType = $itemTypeQuery->fetch())
                        {
                            //On récupère les informations de la classe
                            $adminItemTypeIdSql = $itemType['itemTypeId'];
                            $adminItemTypeName = $itemType['itemTypeName'];
                            $adminItemTypeNameShow = $itemType['itemTypeNameShow'];
                            
                            if ($adminItemTypeIdSql == $adminItemItemTypeId)
                            {
                                ?>
                                <option selected="selected" value="<?php echo $adminItemTypeIdSql ?>"><?php echo $adminItemItemTypeNameShow ?></option>
                                <?php
                            }
                            else
                            {
                                ?>
                                <option value="<?php echo $adminItemTypeIdSql ?>"><?php echo $adminItemTypeNameShow ?></option>
                                <?php
                            }
                            
                        }
                        $itemTypeQuery->closeCursor();
                        ?>
                        
                    </select>
                    Nom : <input type="text" name="adminItemName" class="form-control" placeholder="Nom" value="<?php echo $adminItemName ?>" required>
                    Description : <br> <textarea class="form-control"name="adminItemDescription" id="adminItemDescription" rows="3" required><?php echo $adminItemDescription; ?></textarea>
                    Niveau : <input type="text" name="adminItemLevel" class="form-control" placeholder="Email" value="<?php echo $adminItemLevel ?>" required>
                    Niveau requis : <input type="text" name="adminItemLevelRequired" class="form-control" placeholder="Niveau requis" value="<?php echo $adminItemLevelRequired ?>" required>
                    HP Bonus : <input type="number" name="adminItemHpEffects" class="form-control" placeholder="HP Bonus" value="<?php echo $adminItemHpEffects ?>" required>
                    MP Bonus : <input type="number" name="adminItemMpEffect" class="form-control" placeholder="MP Bonus" value="<?php echo $adminItemMpEffect ?>" required>
                    Force Bonus : <input type="number" name="adminItemStrengthEffect" class="form-control" placeholder="Force Bonus" value="<?php echo $adminItemStrengthEffect ?>" required>
                    Magie Bonus : <input type="number" name="adminItemMagicEffect" class="form-control" placeholder="Magie Bonus" value="<?php echo $adminItemMagicEffect ?>" required>
                    Agilité Bonus : <input type="number" name="adminItemAgilityEffect" class="form-control" placeholder="Agilité Bonus" value="<?php echo $adminItemAgilityEffect ?>" required>
                    Défense Bonus : <input type="number" name="adminItemDefenseEffect" class="form-control" placeholder="Défense Bonus" value="<?php echo $adminItemDefenseEffect ?>" required>
                    Défense Magique Bonus : <input type="number" name="adminItemDefenseMagicEffect" class="form-control" placeholder="Défense Magique Bonus" value="<?php echo $adminItemDefenseMagicEffect ?>" required>
                    Sagesse Bonus : <input type="number" name="adminItemWisdomEffect" class="form-control" placeholder="Sagesse Bonus" value="<?php echo $adminItemWisdomEffect ?>" required>
                    Prospection Bonus : <input type="number" name="adminItemProspectingEffect" class="form-control" placeholder="Prospection Bonus" value="<?php echo $adminItemProspectingEffect ?>" required>
                    Prix d'achat : <input type="number" name="adminItemPurchasePrice" class="form-control" placeholder="Prix d'achat" value="<?php echo $adminItemPurchasePrice ?>" required>
                    Prix de vente : <input type="number" name="adminItemSalePrice" class="form-control" placeholder="Prix de vente" value="<?php echo $adminItemSalePrice ?>" required>
                    <input type="hidden" name="adminItemId" value="<?php echo $adminItemId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input name="finalEdit" class="btn btn-secondary btn-lg" type="submit" value="Modifier">
                </form>

                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                <?php
            }
            //Si l'équipement n'exite pas
            else
            {
                echo "Erreur : Cet équipement n'existe pas";
            }
            $itemQuery->closeCursor();
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
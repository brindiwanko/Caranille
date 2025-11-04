<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['itemId'])
&& isset($_POST['token'])
&& isset($_POST['viewEquipment']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if(ctype_digit($_POST['itemId'])
        && $_POST['itemId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $itemId = htmlspecialchars($_POST['itemId']);
            
            //On fait une requête pour vérifier si l'équipement choisit existe
            $equipmentQuery = $bdd->prepare("SELECT * FROM car_items 
            WHERE itemId = ?");
            $equipmentQuery->execute([$itemId]);
            $equipmentRow = $equipmentQuery->rowCount();
    
            //Si l'équipement existe
            if ($equipmentRow == 1) 
            {
                //On fait une requête pour vérifier si le joueur possède bien cet équipement
                $equipmentInventoryQuery = $bdd->prepare("SELECT * FROM  car_items, car_items_types, car_inventory 
                WHERE itemItemTypeId = itemTypeId
                AND itemId = inventoryItemId
                AND (itemTypeName = 'Armor' 
                OR itemTypeName = 'Boots' 
                OR itemTypeName = 'Gloves' 
                OR itemTypeName = 'Helmet' 
                OR itemTypeName = 'Weapon')
                AND inventoryCharacterId = ?
                AND inventoryItemId = ?");
                $equipmentInventoryQuery->execute([$characterId, $itemId]);
                $equipmentInventoryRow = $equipmentInventoryQuery->rowCount();
                
                //Si le personnage possède cet objet
                if ($equipmentInventoryRow == 1) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($equipmentInventory = $equipmentInventoryQuery->fetch())
                    {
                        //On récupère les informations de l'équippement
                        $equipmentId = $equipmentInventory['itemId'];
                        $equipmentTypeName = $equipmentInventory['itemTypeName'];
                        $equipmentTypeNameShow = $equipmentInventory['itemTypeNameShow'];
                        $equipmentclasseId = $equipmentInventory['itemclasseId'];
                        $equipmentPicture = $equipmentInventory['itemPicture'];
                        $equipmentName = $equipmentInventory['itemName'];
                        $equipmentDescription = $equipmentInventory['itemDescription'];
                        $equipmentLevel = $equipmentInventory['itemLevel'];
                        $equipmentLevelRequired = $equipmentInventory['itemLevelRequired'];
                        $equipmentQuantity = $equipmentInventory['inventoryQuantity'];
                        $equipmentHpEffect = $equipmentInventory['itemHpEffect'];
                        $equipmentMpEffect = $equipmentInventory['itemMpEffect'];
                        $equipmentStrengthEffect = $equipmentInventory['itemStrengthEffect'];
                        $equipmentMagicEffect = $equipmentInventory['itemMagicEffect'];
                        $equipmentAgilityEffect = $equipmentInventory['itemAgilityEffect'];
                        $equipmentDefenseEffect = $equipmentInventory['itemDefenseEffect'];
                        $equipmentDefenseMagicEffect = $equipmentInventory['itemDefenseMagicEffect'];
                        $equipmentWisdomEffect = $equipmentInventory['itemWisdomEffect'];
                        $equipmentProspectingEffect = $equipmentInventory['itemProspectingEffect'];
                        $equipmentSalePrice = $equipmentInventory['itemSalePrice'];
                        $equipmentEquipped = $equipmentInventory['inventoryEquipped'];
            
                        //Si la classe de l'équipement est supérieur à 1 c'est qu'il est attitré à une classe
                        if ($equipmentclasseId >= 1)
                        {
                            //On récupère la classe de l'équipement
                            $classeQuery = $bdd->prepare("SELECT * FROM car_classes
                            WHERE classeId = ?");
                            $classeQuery->execute([$equipmentclasseId]);
    
                            while ($classe = $classeQuery->fetch())
                            {
                                //On récupère les informations de la classe
                                $equipmentclasseName = $classe['classeName'];
                            }
                            $classeQuery->closeCursor();
                        }
                        //Si la classe de l'équipement est égal à 0 c'est qu'il est disponible pour toutes les classes
                        else
                        {
                            $equipmentclasseName = "Toutes les classes";
                        }
                        ?>
                        
                        <p><img src="<?php echo $equipmentPicture ?>" height="100" width="100"></p>
                        
                        <table class="table">
                            
                            <tr>
                                <td>
                                    Type
                                </td>
                                
                                <td>
                                    <?php echo $equipmentTypeNameShow; ?>
                            </td>
    
                            <tr>
                                <td>
                                    Nom
                                </td>
                                
                                <td>
                                    <?php echo $equipmentName; ?>
                                </td>
                            </tr>
                                
                            <tr>
                                <td>
                                    Description
                                </td>
                                
                                <td>
                                    <?php echo nl2br($equipmentDescription); ?>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    Classe requise
                                </td>
                            
                                <td>
                                    <?php echo $equipmentclasseName; ?>
                                </td>
                            </tr>
                            
                             <tr>
                                <td>
                                    Niveau de l'objet
                                </td>
                                
                                <td>
                                    <?php echo $equipmentLevel; ?>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    Niveau requis
                                </td>
                                
                                <td>
                                    <?php echo $equipmentLevelRequired; ?>
                                </td>
                            </tr>
                                
                            <tr>
                                <td>
                                    Quantité
                                </td>
                                
                                <td>
                                    <?php echo $equipmentQuantity; ?>
                                </td>
                            </tr>
                                
                            <tr>
                                <td>
                                    Effet(s)
                                </td>
                                
                                <td>
                                    <?php
                                    //Si l'équipement augmente les HP on l'affiche
                                    if ($equipmentHpEffect > 0)
                                    {
                                        echo "+ $equipmentHpEffect HP<br />";
                                    }
                                    
                                    //Si l'équipement augmente les MP on l'affiche
                                    if ($equipmentMpEffect > 0)
                                    {
                                        echo "+ $equipmentMpEffect MP<br />";
                                    }
                                    
                                    //Si l'équipement augmente la force on l'affiche
                                    if ($equipmentStrengthEffect > 0)
                                    {
                                        echo "+ $equipmentStrengthEffect Force<br />";
                                    }
                                    
                                    //Si l'équipement augmente la magie on l'affiche
                                    if ($equipmentMagicEffect > 0)
                                    {
                                        echo "+ $equipmentMagicEffect Magie<br />";
                                    }
                                    
                                    //Si l'équipement augmente l'agilité on l'affiche
                                    if ($equipmentAgilityEffect > 0)
                                    {
                                        echo "+ $equipmentAgilityEffect Agilité<br />";
                                    }
                                    
                                    //Si l'équipement augmente la défense on l'affiche
                                    if ($equipmentDefenseEffect > 0)
                                    {
                                        echo "+ $equipmentDefenseEffect Défense<br />";
                                    }
                                    
                                    //Si l'équipement augmente la défense magique on l'affiche
                                    if ($equipmentDefenseMagicEffect > 0)
                                    {
                                        echo "+ $equipmentDefenseMagicEffect Défense Magique<br />";
                                    }
                                    
                                    //Si l'équipement augmente la sagesse on l'affiche
                                    if ($equipmentWisdomEffect > 0)
                                    {
                                        echo "+ $equipmentWisdomEffect Sagesse<br />";
                                    }
                                    
                                    //Si l'équipement augmente la prospection on l'affiche
                                    if ($equipmentProspectingEffect > 0)
                                    {
                                        echo "+ $equipmentProspectingEffect Prospection<br />";
                                    }
                                    ?>
                                </td>
                            </tr>
                                
                            <tr>
                                <td>
                                    Prix de vente
                                </td>
                                
                                <td>
                                    <?php echo $equipmentSalePrice; ?>
                                </td>
                            </tr>
                                
                            <tr>
                                <td>
                                    Actions
                                </td>
                                
                                <td>
                                    
                                    <?php
                                    //Si l'équipement n'est pas équipé ont propose au joueur de l'équiper
                                    if ($equipmentEquipped == 0)
                                    {
                                        //On vérifie si la classe du joueur lui permet de s'équiper de cet équipement, ou si celui-ci est pour toutes les classes
                                        if ($characterclasseId == $equipmentclasseId || $equipmentclasseId == 0)
                                        {
                                            //Si le niveau du joueur est supérieur ou égal à celui du niveau requis
                                            if ($characterLevel >= $equipmentLevelRequired)
                                            {
                                                ?>
                                                
                                                <form method="POST" action="equip.php">
                                                    <input type="hidden" name="itemId" value="<?php echo $itemId ?>">
                                                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                                    <input type="submit" class="btn btn-secondary btn-lg" name="equip" value="Equiper">
                                                </form> 
                                                    
                                                <?php
                                            }
                                            //Si le niveau du joueur n'est pas supérieur ou égal à celui du niveau requis
                                            else
                                            {
                                                echo "Votre niveau est trop faible pour vous équiper de cet objet";
                                            }
                                        }
                                        //Si la classe de l'objet est incompatible avec celle du joueur
                                        else
                                        {
                                             echo "Votre classe ne vous permet pas de vous équiper de cet équipement";
                                        }
                                    }
                                    //Si l'équipement est équipé on prévient le joueur
                                    else
                                    {
                                        ?>
                                        
                                        Cet équipement est actuellement équipé
                                        
                                        <form method="POST" action="unEquip.php">
                                            <input type="hidden" name="itemId" value="<?php echo $itemId ?>">
                                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                            <input type="submit" class="btn btn-secondary btn-lg" name="unEquip" value="Déséquiper">
                                        </form>
                                            
                                        <?php
                                    }
                                    ?>
                                    
                                    <form method="POST" action="../../modules/inventory/sale.php">
                                        Quantité à vendre : <input type="number" name="saleQuantity" value="1" class="form-control" required>
                                        <input type="hidden" name="itemId" value="<?php echo $itemId ?>">
                                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                        <input type="submit" class="btn btn-secondary btn-lg" name="sale" value="Vendre">
                                    </form>
                                </td>
                            </tr>
                        </table>
                        
                        <hr>
            
                        <form method="POST" action="index.php">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
                        </form>
                        
                        <?php
                    }
                }
                //Si le joueur ne possède pas cet équipement
                else
                {
                    echo "Erreur : Impossible de visualiser un équipement que vous ne possédez pas.";
                }
                $equipmentInventoryQuery->closeCursor();
            }
            //Si l'équipement n'existe pas
            else
            {
                echo "Erreur : Cet équippement n'existe pas";
            }
            $equipmentQuery->closeCursor();
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
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>
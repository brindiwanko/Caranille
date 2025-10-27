<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['monsterId'])
&& isset($_POST['viewMonster']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if(ctype_digit($_POST['monsterId'])
        && $_POST['monsterId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $monsterId = htmlspecialchars($_POST['monsterId']);
            
            //On fait une requête pour vérifier si le monstre entré est bien dans le bestiaire du joueur
            $monsterBestiaryQuery = $bdd->prepare("SELECT * FROM car_monsters, car_bestiary
            WHERE monsterId = bestiaryMonsterId
            AND bestiaryMonsterId = ?
            AND bestiaryCharacterId = ?");
            $monsterBestiaryQuery->execute([$monsterId, $characterId]);
            $monsterBestiaryRow = $monsterBestiaryQuery->rowCount();
            
            //Si un ou plusieurs équipements ont été trouvé
            if ($monsterBestiaryRow == 1)
            {
                //On récupère le nombre de fois que ce monstre a été vaincu
                while ($monsterBestiary = $monsterBestiaryQuery->fetch())
                {
                    //On récupère les informations du monstre
                    $monsterDefeateQuantity = $monsterBestiary['bestiaryMonsterQuantity'];
                }
                
                //On fait une requête pour vérifier si le monstre entré existe
                $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters, car_monsters_categories
                WHERE monsterCategory = monsterCategoryId 
                AND monsterId = ?");
                $monsterQuery->execute([$monsterId]);
                $monsterRow = $monsterQuery->rowCount();
                
                //Si le monstre existe
                if ($monsterRow == 1)
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($monster = $monsterQuery->fetch())
                    {
                        //On récupère les informations du monstre
                        $monsterId = $monster['monsterId'];
                        $monsterCategoryId = $monster['monsterCategoryId'];
				        $monsterCategoryName = $monster['monsterCategoryName'];
				        $monsterCategoryNameShow = $monster['monsterCategoryNameShow'];
                        $monsterPicture = $monster['monsterPicture'];
                        $monsterName = $monster['monsterName'];
                        $monsterDescription = $monster['monsterDescription'];
                        $monsterLevel = $monster['monsterLevel'];
                        $monsterHp = $monster['monsterHp'];
                        $monsterMp = $monster['monsterMp'];
                        $monsterStrength = $monster['monsterStrength'];
                        $monsterMagic = $monster['monsterMagic'];
                        $monsterAgility = $monster['monsterAgility'];
                        $monsterDefense = $monster['monsterDefense'];
                        $monsterDefenseMagic = $monster['monsterDefenseMagic'];
                        $monsterGold = $monster['monsterGold'];
                        $monsterExperience = $monster['monsterExperience'];
                    }
                    ?>
                    
                    <p><img src="<?php echo $monsterPicture ?>" height="100" width="100"></p>
                    
                    <table class="table">
                        <tr>
                            <td>
                                Numéro
                            </td>
                            
                            <td>
                                <?php echo $monsterId; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Catégorie
                            </td>
                            
                            <td>
                                <?php echo $monsterCategoryNameShow; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Nom
                            </td>
                            
                            <td>
                                <?php echo $monsterName; ?>
                            </td>
                        </tr>
                            
                        <tr>
                            <td>
                                Description
                            </td>
                            
                            <td>
                                <?php echo nl2br($monsterDescription); ?>
                            </td>
                        </tr>
                            
                        <tr>
                            <td>
                                Niveau
                            </td>
                            
                            <td>
                                <?php echo $monsterLevel; ?>
                            </td>
                        </tr>
                            
                        <tr>
                            <td>
                                HP
                            </td>
                            
                            <td>
                                <?php echo $monsterHp; ?>
                            </td>
                        </tr>
                            
                        <tr>
                            <td>
                                MP
                            </td>
                            
                            <td>
                                <?php echo $monsterMp; ?>
                            </td>
                        </tr>
                            
                        <tr>
                            <td>
                                Force
                            </td>
                            
                            <td>
                                <?php echo $monsterStrength; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Magie
                            </td>
                            
                            <td>
                                <?php echo $monsterMagic; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Agilité
                            </td>
                            
                            <td>
                                <?php echo $monsterAgility; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Défense
                            </td>
                            
                            <td>
                                <?php echo $monsterDefense; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Défense Magique
                            </td>
                            
                            <td>
                                <?php echo $monsterDefenseMagic; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Expérience
                            </td>
                            
                            <td>
                                <?php echo $monsterExperience; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Argent
                            </td>
                            
                            <td>
                                <?php echo $monsterGold; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Localisation
                            </td>
                            
                            <td>
                                
                                <?php
                                //On recherche dans la base de donnée les lieu dans lequel ce monstre se trouve
                                $monsterPlaceQuery = $bdd->prepare("SELECT * FROM car_places, car_places_monsters
                                WHERE placeMonsterPlaceId = placeId
                                AND placeMonsterMonsterId = ?");
                                $monsterPlaceQuery->execute([$monsterId]);
                                $monsterPlaceRow = $monsterPlaceQuery->rowCount();
                            
                                //S'il existe une ou plusieurs lieu pour ce monstre
                                if ($monsterPlaceRow > 0) 
                                {
                                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                    while ($monsterPlace = $monsterPlaceQuery->fetch())
                                    {
                                        $monsterplaceName = $monsterPlace['placeName'];
                                        $monsterplaceChapter = $monsterPlace['placeChapter'];
                                        
                                        //Si le joueur à accès à cette lieu on l'affiche
                                        if ($monsterplaceChapter <= $characterChapter)
                                        {
                                        echo "$monsterplaceName<br />"; 
                                        }
                                        //Si le joueur n'a pas accès à cette lieu on cache le nom
                                        else 
                                        {
                                            echo "???<br />";
                                        }
                                    }
                                }
                                //Si ce monstre se trouve dans aucun lieu
                                else
                                {
                                    echo "Lieu inconnu";
                                }
                                $monsterPlaceQuery->closeCursor();
                                ?>
                                
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                Objets (%)
                            </td>
                            
                            <td>
                                
                                <?php
                                //On recherche dans la base de donnée les objets et équipements que ce monstre peut faire gagner
                                $monsterDropQuery = $bdd->prepare("SELECT * FROM car_items, car_items_types, car_monsters_drops
                                WHERE itemItemTypeId = itemTypeId
                                AND monsterDropItemId = itemId
                                AND monsterDropMonsterId = ?
                                ORDER BY itemItemTypeId, itemName");
                                $monsterDropQuery->execute([$monsterId]);
                                $monsterDropRow = $monsterDropQuery->rowCount();
                            
                                //S'il existe un ou plusieurs objet pour ce monstre
                                if ($monsterDropRow > 0) 
                                {
                                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                    while ($monsterDrop = $monsterDropQuery->fetch())
                                    {
                                        $monsterDropItemId = $monsterDrop['itemId'];
                                        $monsterDropItemTypeName = $monsterDrop['itemTypeName'];
                                        $monsterDropItemTypeNameShow = $monsterDrop['itemTypeNameShow'];
                                        
                                        $monsterDropItemVisible = $monsterDrop['monsterDropItemVisible'];
                                        $monsterDropRateVisible = $monsterDrop['monsterDropRateVisible'];
                                        
                                        //Si l'objet est caché
                                        if ($monsterDropItemVisible == "Yes")
                                        {
                                            $monsterDropItemName = $monsterDrop['itemName'];
                                        }
                                        else
                                        {
                                            $monsterDropItemName = "???";
                                            $monsterDropItemTypeNameShow = "???";
                                        }
                                        
                                        //Si le taux d'obtention est caché
                                        if ($monsterDropRateVisible == "Yes")
                                        {
                                            $monsterDropRate = $monsterDrop['monsterDropRate'];
                                        }
                                        else
                                        {
                                            $monsterDropRate = "???";
                                        }
                                        
                                        echo "[$monsterDropItemTypeNameShow] - $monsterDropItemName ($monsterDropRate/1000)<br />";
                                    }
                                }
                                //Si aucun objet ne peut être obtenu
                                else
                                {
                                    echo "Aucun objet";
                                }
                                $monsterDropQuery->closeCursor();
                                ?>
                                
                            </td>
                        </tr>
                    </table>
                                
                    <hr>
        
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
                    </form>
                    
                    <?php
                }
                //Si le monstre n'existe pas
                else 
                {
                    echo "Ce monstre n'existe pas";
                }
                $monsterQuery->closeCursor();
            }
            //Si le monstre n'est pas dans le bestiaire du joueur
            else
            {
                ?>
                
                Ce monstre ne fait pas parti de votre bestiaire

                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
                </form>

                <?php
            }
            $monsterBestiaryQuery->closeCursor(); 
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
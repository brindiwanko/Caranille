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
&& isset($_POST['viewAllOffers']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['itemId'])
        && $_POST['itemId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $itemId = htmlspecialchars($_POST['itemId']);

            //On fait une jointure entre les 3 tables car_market, car_characters, car_items pour récupérer les offres du marché
            $marketQuery = $bdd->prepare("SELECT * FROM car_market, car_characters, car_items
            WHERE marketCharacterId = characterId
            AND marketItemId = itemId
            AND marketItemId = ?");
            $marketQuery->execute([$itemId]);
            $marketRow = $marketQuery->rowCount();

            //Si plusieurs offres ont été trouvée
            if ($marketRow > 0)
            {
                ?>

                <form method="POST" action="viewOffer.php">
                    Liste des offres : <select name="marketId" class="form-control">

                        <?php
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($market = $marketQuery->fetch())
                        {
                            //on récupère les valeurs de chaque magasins qu'on va ensuite mettre dans le menu déroulant
                            $marketId = $market['marketId'];
                            $marketCharacterName = $market['characterName'];
                            $marketItemName = $market['itemName'];
                            $marketSalePrice = $market['marketSalePrice'];
                            ?>
                            <option value="<?php echo $marketId ?>"><?php echo "$marketItemName (Prix $marketSalePrice - Vendeur $marketCharacterName)" ?></option>
                            <?php
                        }
                        ?>

                    </select>
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" name="viewOffer" class="btn btn-secondary btn-lg" value="Afficher l'offre">
                </form>
        
            <?php
            }
            //Si l'offre n'exite pas
            else
            {
                echo "Erreur : Cette offre n'existe pas";
            }
            $marketQuery->closeCursor();
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
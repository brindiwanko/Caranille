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
        ?>
    
        <p>Informations de l'équipement</p>

        <form method="POST" action="addParchmentEnd.php">
            Image : <input type="text" name="adminItemPicture" class="form-control" placeholder="Image" value="../../img/empty.png" required>
            Nom : <input type="text" name="adminItemName" class="form-control" placeholder="Nom" required>
            Description : <br> <textarea class="form-control" name="adminItemDescription" id="adminItemDescription" rows="3" required></textarea>
            HP Bonus : <input type="number" name="adminItemHpEffects" class="form-control" placeholder="HP Bonus" value="0" required>
            MP Bonus : <input type="number" name="adminItemMpEffect" class="form-control" placeholder="MP Bonus" value="0" required>
            Force Bonus : <input type="number" name="adminItemStrengthEffect" class="form-control" placeholder="Force Bonus" value="0" required>
            Magie Bonus : <input type="number" name="adminItemMagicEffect" class="form-control" placeholder="Magie Bonus" value="0" required>
            Agilité Bonus : <input type="number" name="adminItemAgilityEffect" class="form-control" placeholder="Agilité Bonus" value="0" required>
            Défense Bonus : <input type="number" name="adminItemDefenseEffect" class="form-control" placeholder="Défense Bonus" value="0" required>
            Défense Magique Bonus : <input type="number" name="adminItemDefenseMagicEffect" class="form-control" placeholder="Défense Magique Bonus" value="0" required>
            Sagesse Bonus : <input type="number" name="adminItemWisdomEffect" class="form-control" placeholder="Sagesse Bonus" value="0" required>
            Prospection Bonus : <input type="number" name="adminItemProspectingEffect" class="form-control" placeholder="Prospection Bonus" value="0" required>
            Prix d'achat : <input type="number" name="adminItemPurchasePrice" class="form-control" placeholder="Prix d'achat" value="0" required>
            Prix de vente : <input type="number" name="adminItemSalePrice" class="form-control" placeholder="Prix de vente" value="0" required>
            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
            <input name="finalAdd" class="btn btn-secondary btn-lg" type="submit" value="Ajouter">
        </form>
        
        <hr>

        <form method="POST" action="index.php">
            <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
        </form>
        
        <?php
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
<?php
//On démarre le module des sessions de PHP
session_start();
//On récupère le temps Unix actuel une première fois
$timeStart = microtime(true);
//On inclue le fichier de configuration qui contient les paramètre de connexion SQL ainsi que la création d'un objet $bdd pour les requêtes SQL
require_once("../../kernel/config.php");
//On récupère les informations de configuration du jeu
require_once("../../kernel/configuration/index.php");
//Si la session $_SESSION['token'] est vide c'est que le joueur à validé un formulaire
if (empty($_SESSION['token']))
{
	//On génère un token cryptographiquement sécurisé qu'on stock dans une session pour sécuriser les formulaires contre les attaques CSRF
	$_SESSION['token'] = bin2hex(random_bytes(32));
}
//Si le joueur est connecté on va récupérer toutes les informations du joueur (Compte, Personnage, Combat en cours...)
if (isset($_SESSION['account']['id']))
{
    //On récupère toutes les informations du compte
    require_once("../../kernel/account/index.php");
    //On récupère toutes les informations du personnage grâce au compte
    require_once("../../kernel/character/index.php");
    //On vérifie si le personnage est actuellement dans un combat de monstre. Si c'est le cas on récupère toutes les informations du monstre
    require_once("../../kernel/battle/index.php");
    //On vérifie le nombre d'invitation de combat du joueur
    require_once("../../kernel/battleInvitation/index.php");
    //On récupère toutes les informations des équipements équipé au personnage
    require_once("../../kernel/equipment/index.php");
    //On récupère toutes les informations des type d'équipement
    require_once("../../kernel/equipmentType/index.php");
    //On vérifie le nombre d'offre dans le marché
    require_once("../../kernel/market/index.php");
    //On vérifie le nombre de message de notifications non lue
    require_once("../../kernel/notification/index.php");
    //On vérifie le nombre de message de conversation privée non lu
    require_once("../../kernel/privateConversation/index.php");
    //On vérifie si le personnage est actuellement dans un lieu. Si c'est le cas on récupère toutes les informations du lieu
    require_once("../../kernel/place/index.php");
    //On vérifie le nombre de d'échange en cours
    require_once("../../kernel/trade/index.php");
    //On vérifie le nombre de demande d'échange en cours
    require_once("../../kernel/tradeRequest/index.php");
}
?>

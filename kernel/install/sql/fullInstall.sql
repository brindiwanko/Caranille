--
-- Déchargement des données de la table `car_classes`
--

INSERT INTO `car_classes` (`classeId`, `classePicture`, `classeName`, `classeDescription`, `classeHpBonus`, `classeMpBonus`, `classestrengthBonus`, `classeMagicBonus`, `classeAgilityBonus`, `classeDefenseBonus`, `classeDefenseMagicBonus`, `classeWisdomBonus`, `classeProspectingBonus`) VALUES
(1, '../../img/empty.png', 'Chevalier', 'Classe de personnage axé sur la force.', 10, 1, 2, 1, 1, 1, 1, 0, 0),
(2, '../../img/empty.png', 'Mage', 'Classe de personnage axé sur la magie.', 10, 1, 1, 2, 1, 1, 1, 0, 0);

--
-- Déchargement des données de la table `car_configuration`
--

INSERT INTO `car_configuration` (`configurationId`, `configurationGameName`, `configurationPresentation`, `configurationMaxLevel`, `configurationExperience`, `configurationSkillPoint`, `configurationExperienceBonus`, `configurationGoldBonus`, `configurationDropBonus`, `configurationAccess`) VALUES
(1, 'Nom de votre jeu', 'Description de votre jeu', 40, 500, 4, 0, 0, 0, 'Closed');

--
-- Déchargement des données de la table `car_items`
--

INSERT INTO `car_items` (`itemId`, `itemItemTypeId`, `itemclasseId`, `itemPicture`, `itemName`, `itemDescription`, `itemLevel`, `itemLevelRequired`, `itemHpEffect`, `itemMpEffect`, `itemStrengthEffect`, `itemMagicEffect`, `itemAgilityEffect`, `itemDefenseEffect`, `itemDefenseMagicEffect`, `itemWisdomEffect`, `itemProspectingEffect`, `itemPurchasePrice`, `itemSalePrice`) VALUES
(1, 1, 0, '../../img/empty.png', 'Armure de cuivre', 'Cette armure vous donnera un peu de défense', 1, 1, 0, 0, 0, 0, 0, 2, 0, 0, 0, 100, 50),
(2, 1, 0, '../../img/empty.png', 'Cape de lin', 'Cette cape vous donnera un peu de défense magique', 1, 1, 0, 0, 0, 0, 0, 0, 2, 0, 0, 100, 50),
(3, 2, 0, '../../img/empty.png', 'Bottes de cuivre', 'Ses bottes vous donneront un peu de défense', 1, 1, 0, 0, 0, 0, 0, 2, 0, 0, 0, 100, 50),
(4, 2, 0, '../../img/empty.png', 'Botte de lin', 'Ses bottes vous donnerons un peu de défense magique', 1, 1, 0, 0, 0, 0, 0, 0, 2, 0, 0, 100, 50),
(5, 3, 0, '../../img/empty.png', 'Gants de cuivre', 'Ses gants de cuivre vous donnerons un peu de défense', 1, 1, 0, 0, 0, 0, 0, 2, 0, 0, 0, 100, 50),
(6, 3, 0, '../../img/empty.png', 'Gants de lin', 'Ses gants vous donneront un peu de défense magique', 1, 1, 0, 0, 0, 0, 0, 0, 2, 0, 0, 100, 50),
(7, 4, 0, '../../img/empty.png', 'Casque de cuivre', 'Ce casque de cuivre vous donnera un peu de défense', 1, 1, 0, 0, 0, 0, 0, 2, 0, 0, 0, 100, 50),
(8, 4, 0, '../../img/empty.png', 'Chapeau de lin', 'Ce chapeau de lin vous donnera un peu de défense magique', 1, 1, 0, 0, 0, 0, 0, 0, 2, 0, 0, 100, 50),
(9, 5, 0, '../../img/empty.png', 'Epée de cuivre', 'Cette épée de cuivre vous donnera un peu de force', 1, 1, 0, 0, 2, 0, 0, 0, 0, 0, 0, 100, 50),
(10, 5, 0, '../../img/empty.png', 'Baton de bois', 'Ce bâton de bois vous donnera un peu de magie', 1, 1, 0, 0, 0, 2, 0, 0, 0, 0, 0, 100, 50),
(11, 6, 0, '../../img/empty.png', 'Potion (1*)', 'Cette potion vous rendra des HP', 1, 1, 100, 0, 0, 0, 0, 0, 0, 0, 0, 100, 50),
(12, 6, 0, '../../img/empty.png', 'Ether (1*)', 'Cet éther vous rendra des MP', 1, 1, 0, 5, 0, 0, 0, 0, 0, 0, 0, 100, 50),
(13, 7, 0, '../../img/empty.png', 'Parchemin de vitalité', 'Octroi +10 HP', 1, 1, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5000),
(14, 7, 0, '../../img/empty.png', 'Parchemin de MP', 'Octroi +1 MP', 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 5000),
(15, 7, 0, '../../img/empty.png', 'Parchemin de force', 'Octroi +1 en force', 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 5000),
(16, 7, 0, '../../img/empty.png', 'Parchemin de magie', 'Octroi +1 en magie', 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 5000),
(17, 7, 0, '../../img/empty.png', 'Parchemin d\'agilité', 'Octroi +1 en agilité', 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 5000),
(18, 7, 0, '../../img/empty.png', 'Parchemin de défense', 'Octroi +1 en défense', 1, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 5000),
(19, 7, 0, '../../img/empty.png', 'Parchemin de défense magique', 'Octroi +1 en défense magique', 1, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 5000),
(20, 7, 0, '../../img/empty.png', 'Parchemin de sagesse', 'Octroi +1 en sagesse', 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 5000),
(21, 7, 0, '../../img/empty.png', 'Parchemin de prospection', 'Octroi +1 en prospection', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 5000);

--
-- Déchargement des données de la table `car_items_types`
--

INSERT INTO `car_items_types` (`itemTypeId`, `itemTypeName`, `itemTypeNameShow`) VALUES
(1, 'Armor', 'Armure'),
(2, 'Boots', 'Bottes'),
(3, 'Gloves', 'Gants'),
(4, 'Helmet', 'Casque'),
(5, 'Weapon', 'Arme'),
(6, 'Item', 'Objet'),
(7, 'Parchment', 'Parchemin');

--
-- Déchargement des données de la table `car_monsters`
--

INSERT INTO `car_monsters` (`monsterId`, `monsterCategory`, `monsterPicture`, `monsterName`, `monsterDescription`, `monsterLevel`, `monsterHp`, `monsterMp`, `monsterStrength`, `monsterMagic`, `monsterAgility`, `monsterDefense`, `monsterDefenseMagic`, `monsterExperience`, `monsterGold`, `monsterLimited`, `monsterQuantity`, `monsterQuantityBattle`, `monsterQuantityEscaped`, `monsterQuantityVictory`, `monsterQuantityDefeated`, `monsterQuantityDraw`) VALUES
(1, '1', '../../img/empty.png', 'Plop vert', 'Taille : 0.50m\r\nPoids : 2KG\r\nLes Plops sont des petits monstres gélatineux qui adorent manger les chaussures des aventuriers qui ont le malheur de les oublier.', 1, 10, 0, 1, 0, 0, 0, 0, 10, 10, 'No', 0, 0, 0, 0, 0, 0),
(2, '2', '../../img/empty.png', 'Dragon Vert', 'Taille : 5m\r\nPoids : 250KG\r\nLorsque les dragons verts quittent le nid c\\&#039;est qu\\&#039;ils sont devenus adultes, ils adorent manger le blé et le maïs et aussi les aventuriers qui se seraient endormis.', 5, 200, 0, 20, 0, 0, 0, 0, 200, 200, 'No', 0, 0, 0, 0, 0, 0);

--
-- Déchargement des données de la table `car_monsters_categories`
--

INSERT INTO `car_monsters_categories` (`monsterCategoryId`, `monsterCategoryName`, `monsterCategoryNameShow`) VALUES
(1, 'Common', 'Commun'),
(2, 'Unusual', 'Peu commun'),
(3, 'Legendary', 'Légendaire'),
(4, 'Mythical', 'Mythique');

--
-- Déchargement des données de la table `car_monsters_drops`
--

INSERT INTO `car_monsters_drops` (`monsterDropID`, `monsterDropMonsterId`, `monsterDropItemId`, `monsterDropItemVisible`, `monsterDropRate`, `monsterDropRateVisible`) VALUES
(1, 1, 1, 'Yes', 100, 'Yes');

--
-- Déchargement des données de la table `car_places`
--

INSERT INTO `car_places` (`placeId`, `placePicture`, `placeName`, `placeDescription`, `placePriceInn`, `placeChapter`, `placeAccess`) VALUES
(1, '../../img/empty.png', 'Indicia', 'Petit village situé à proximité d\'une grande forêt', 10, 1, 'Yes');

--
-- Déchargement des données de la table `car_places_monsters`
--

INSERT INTO `car_places_monsters` (`placeMonsterId`, `placeMonsterPlaceId`, `placeMonsterMonsterId`) VALUES
(1, 1, 1);

--
-- Déchargement des données de la table `car_places_shops`
--

INSERT INTO `car_places_shops` (`placeShopId`, `placeShopPlaceId`, `placeShopShopId`) VALUES
(1, 1, 1);

--
-- Déchargement des données de la table `car_shops`
--

INSERT INTO `car_shops` (`shopId`, `shopPicture`, `shopName`, `shopDescription`) VALUES
(1, '../../img/empty.png', 'La grande aventure', 'Seul magasin dans lequel vous allez pouvoir trouver des articles rares et temporaire.\r\nRevenez chaque jour pour voir si il y a de nouvelles offres');

--
-- Déchargement des données de la table `car_shops_items`
--

INSERT INTO `car_shops_items` (`shopItemId`, `shopItemShopId`, `shopItemItemId`, `shopItemDiscount`) VALUES
(1, '1', '1', 0);
COMMIT;
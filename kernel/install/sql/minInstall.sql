
--
-- Contenu de la table `car_configuration`
--

INSERT INTO `car_configuration` (`configurationId`, `configurationGameName`, `configurationPresentation`, `configurationMaxLevel`, `configurationExperience`, `configurationSkillPoint`, `configurationExperienceBonus`, `configurationGoldBonus`, `configurationDropBonus`, `configurationAccess`) VALUES
(1, 'Nom de votre jeu', 'Description de votre jeu', 40, 500, 4, 0, 0, 0, 'Closed');

--
-- Contenu de la table `car_items_types`
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
-- Contenu de la table `car_monsters_categories`
--

INSERT INTO `car_monsters_categories` (`monsterCategoryId`, `monsterCategoryName`, `monsterCategoryNameShow`) VALUES
(1, 'Common', 'Commun'),
(2, 'Unusual', 'Peu commun'),
(3, 'Legendary', 'Légendaire'),
(4, 'Mythical', 'Mythique');

--
-- Contenu de la table `car_races`
--

INSERT INTO `car_races` (`raceId`, `racePicture`, `raceName`, `raceDescription`, `raceHpBonus`, `raceMpBonus`, `raceStrengthBonus`, `raceMagicBonus`, `raceAgilityBonus`, `raceDefenseBonus`, `raceDefenseMagicBonus`, `raceWisdomBonus`, `raceProspectingBonus`) VALUES
(1, '../../img/empty.png', 'Humain', 'Classe par défaut', 10, 1, 1, 1, 1, 1, 1, 1, 0);
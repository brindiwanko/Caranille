CREATE TABLE IF NOT EXISTS car_accounts (
  accountId INTEGER PRIMARY KEY AUTOINCREMENT,
  accountPseudo TEXT NOT NULL,
  accountPassword TEXT NOT NULL,
  accountEmail TEXT NOT NULL,
  accountSecretQuestion TEXT NOT NULL,
  accountSecretAnswer TEXT NOT NULL,
  accountAccess INTEGER NOT NULL,
  accountStatus INTEGER NOT NULL,
  accountReason TEXT NOT NULL,
  accountLastAction TEXT NOT NULL,
  accountLastConnection TEXT NOT NULL,
  accountLastIp TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_accounts_verifications
(
  accountVerificationId INTEGER PRIMARY KEY AUTOINCREMENT,
  accountVerificationAccountId INTEGER NOT NULL,
  accountVerificationEmailAdresse TEXT NOT NULL,
  accountVerificationEmailCode INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_battles (
  battleId INTEGER PRIMARY KEY AUTOINCREMENT,
  battleCharacterId INTEGER NOT NULL,
  battleOpponentId INTEGER NOT NULL,
  battleType TEXT NOT NULL,
  battleOpponentHpRemaining INTEGER NOT NULL,
  battleOpponentMpRemaining INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_battles_invitations (
  battleInvitationId INTEGER PRIMARY KEY AUTOINCREMENT,
  battleInvitationMonsterId INTEGER NOT NULL,
  battleInvitationPicture TEXT NOT NULL,
  battleInvitationName TEXT NOT NULL,
  battleInvitationDescription TEXT NOT NULL,
  battleInvitationDateBegin TEXT NOT NULL,
  battleInvitationDateEnd TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_battles_invitations_characters (
  battleInvitationCharacterId INTEGER PRIMARY KEY AUTOINCREMENT,
  battleInvitationCharacterBattleInvitationId INTEGER NOT NULL,
  battleInvitationCharacterCharacterId INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_bestiary (
  bestiaryId INTEGER PRIMARY KEY AUTOINCREMENT,
  bestiaryCharacterId INTEGER NOT NULL,
  bestiaryMonsterId INTEGER NOT NULL,
  bestiaryMonsterQuantity INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_chapters
(
  chapterId INTEGER PRIMARY KEY AUTOINCREMENT,
  chapterMonsterId INTEGER NOT NULL,
  chapterTitle TEXT NOT NULL,
  chapterOpening TEXT NOT NULL,
  chapterEnding TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_characters (
  characterId INTEGER PRIMARY KEY AUTOINCREMENT,
  characterAccountId INTEGER NOT NULL,
  characterGuildId INTEGER NOT NULL,
  characterRaceId INTEGER NOT NULL,
  characterPlaceId INTEGER NOT NULL,
  characterPicture TEXT NOT NULL,
  characterName TEXT NOT NULL,
  characterLevel INTEGER NOT NULL,
  characterSex INTEGER NOT NULL,
  characterHpMin INTEGER NOT NULL,
  characterHpMax INTEGER NOT NULL,
  characterHpSkillPoints INTEGER NOT NULL,
  characterHpBonus INTEGER NOT NULL,
  characterHpEquipments INTEGER NOT NULL,
  characterHpGuild INTEGER NOT NULL,
  characterHpTotal INTEGER NOT NULL,
  characterMpMin INTEGER NOT NULL,
  characterMpMax INTEGER NOT NULL,
  characterMpSkillPoints INTEGER NOT NULL,
  characterMpBonus INTEGER NOT NULL,
  characterMpEquipments INTEGER NOT NULL,
  characterMpGuild INTEGER NOT NULL,
  characterMpTotal INTEGER NOT NULL,
  characterStrength INTEGER NOT NULL,
  characterStrengthSkillPoints INTEGER NOT NULL,
  characterStrengthBonus INTEGER NOT NULL,
  characterStrengthEquipments INTEGER NOT NULL,
  characterStrengthGuild INTEGER NOT NULL,
  characterStrengthTotal INTEGER NOT NULL,
  characterMagic INTEGER NOT NULL,
  characterMagicSkillPoints INTEGER NOT NULL,
  characterMagicBonus INTEGER NOT NULL,
  characterMagicEquipments INTEGER NOT NULL,
  characterMagicGuild INTEGER NOT NULL,
  characterMagicTotal INTEGER NOT NULL,
  characterAgility INTEGER NOT NULL,
  characterAgilitySkillPoints INTEGER NOT NULL,
  characterAgilityBonus INTEGER NOT NULL,
  characterAgilityEquipments INTEGER NOT NULL,
  characterAgilityGuild INTEGER NOT NULL,
  characterAgilityTotal INTEGER NOT NULL,
  characterDefense INTEGER NOT NULL,
  characterDefenseSkillPoints INTEGER NOT NULL,
  characterDefenseBonus INTEGER NOT NULL,
  characterDefenseEquipments INTEGER NOT NULL,
  characterDefenseGuild INTEGER NOT NULL,
  characterDefenseTotal INTEGER NOT NULL,
  characterDefenseMagic INTEGER NOT NULL,
  characterDefenseMagicSkillPoints INTEGER NOT NULL,
  characterDefenseMagicBonus INTEGER NOT NULL,
  characterDefenseMagicEquipments INTEGER NOT NULL,
  characterDefenseMagicGuild INTEGER NOT NULL,
  characterDefenseMagicTotal INTEGER NOT NULL,
  characterWisdom INTEGER NOT NULL,
  characterWisdomSkillPoints INTEGER NOT NULL,
  characterWisdomBonus INTEGER NOT NULL,
  characterWisdomEquipments INTEGER NOT NULL,
  characterWisdomGuild INTEGER NOT NULL,
  characterWisdomTotal INTEGER NOT NULL,
  characterProspecting INTEGER NOT NULL,
  characterProspectingSkillPoints INTEGER NOT NULL,
  characterProspectingBonus INTEGER NOT NULL,
  characterProspectingEquipments INTEGER NOT NULL,
  characterProspectingGuild INTEGER NOT NULL,
  characterProspectingTotal INTEGER NOT NULL,
  characterArenaDefeate INTEGER NOT NULL,
  characterArenaVictory INTEGER NOT NULL,
  characterExperience INTEGER NOT NULL,
  characterExperienceTotal INTEGER NOT NULL,
  characterSkillPoints INTEGER NOT NULL,
  characterGold INTEGER NOT NULL,
  characterChapter INTEGER NOT NULL,
  characterOnBattle INTEGER NOT NULL,
  characterEnable INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_chat
(
  chatMessageId INTEGER PRIMARY KEY AUTOINCREMENT,
  chatCharacterId INTEGER NOT NULL,
  chatDateTime TEXT NOT NULL,
  chatMessage TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_codes (
  codeId INTEGER PRIMARY KEY AUTOINCREMENT,
  codeName TEXT NOT NULL,
  codeBegins TEXT NOT NULL,
  codeFinish TEXT NOT NULL,
  codeAmount INTEGER NOT NULL,
  codeAmountRemaining INTEGER NOT NULL,
  codeType INTEGER NOT NULL
  );

CREATE TABLE IF NOT EXISTS car_codes_gift (
  codeGiftId INTEGER PRIMARY KEY AUTOINCREMENT,
  codeGiftCodeId INTEGER NOT NULL,
  codeGiftCharacterLevel INTEGER NOT NULL,
  codeGiftCharacterHp INTEGER NOT NULL,
  codeGiftCharacterMp INTEGER NOT NULL,
  codeGiftCharacterStrength INTEGER NOT NULL,
  codeGiftCharacterMagic INTEGER NOT NULL,
  codeGiftCharacterAgility INTEGER NOT NULL,
  codeGiftCharacterDefense INTEGER NOT NULL,
  codeGiftCharacterDefenseMagic INTEGER NOT NULL,
  codeGiftcharacterWisdom INTEGER NOT NULL,
  codeGiftCharacterProspecting INTEGER NOT NULL,
  codeGiftExperience INTEGER NOT NULL,
  codeGiftGold INTEGER NOT NULL,
  codeGiftitemId INTEGER NOT NULL,
  codeGiftMonsterId INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_codes_used (
  codeUsedId INTEGER PRIMARY KEY AUTOINCREMENT,
  codeUsedCodeId INTEGER NOT NULL,
  codeUsedaccountId INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_configuration
(
  configurationId INTEGER PRIMARY KEY AUTOINCREMENT,
  configurationGameName TEXT NOT NULL,
  configurationPresentation TEXT NOT NULL,
  configurationMaxLevel INTEGER NOT NULL,
  configurationExperience INTEGER NOT NULL,
  configurationSkillPoint INTEGER NOT NULL,
  configurationExperienceBonus INTEGER NOT NULL,
  configurationGoldBonus INTEGER NOT NULL,
  configurationDropBonus INTEGER NOT NULL,
  configurationAccess TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_forgets_passwords
(
  accountForgetPasswordId INTEGER PRIMARY KEY AUTOINCREMENT,
  accountForgetPasswordAccountId INTEGER NOT NULL,
  accountForgetPasswordEmailAdress TEXT NOT NULL,
  accountForgetPasswordEmailCode TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_inventory
(
  inventoryId INTEGER PRIMARY KEY AUTOINCREMENT,
  inventoryCharacterId INTEGER NOT NULL,
  inventoryItemId INTEGER NOT NULL,
  inventoryQuantity INTEGER NOT NULL,
  inventoryEquipped INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_items (
  itemId INTEGER PRIMARY KEY AUTOINCREMENT,
  itemItemTypeId INTEGER NOT NULL,
  itemRaceId INTEGER NOT NULL,
  itemPicture TEXT NOT NULL,
  itemName TEXT NOT NULL,
  itemDescription TEXT NOT NULL,
  itemLevel INTEGER NOT NULL,
  itemLevelRequired INTEGER NOT NULL,
  itemHpEffect INTEGER NOT NULL,
  itemMpEffect INTEGER NOT NULL,
  itemStrengthEffect INTEGER NOT NULL,
  itemMagicEffect INTEGER NOT NULL,
  itemAgilityEffect INTEGER NOT NULL,
  itemDefenseEffect INTEGER NOT NULL,
  itemDefenseMagicEffect INTEGER NOT NULL,
  itemWisdomEffect INTEGER NOT NULL,
  itemProspectingEffect INTEGER NOT NULL,
  itemPurchasePrice INTEGER NOT NULL,
  itemSalePrice INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_items_types (
  itemTypeId INTEGER PRIMARY KEY AUTOINCREMENT,
  itemTypeName TEXT NOT NULL,
  itemTypeNameShow TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_market (
  marketId INTEGER PRIMARY KEY AUTOINCREMENT,
  marketCharacterId INTEGER NOT NULL,
  marketItemId INTEGER NOT NULL,
  marketSalePrice INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_monsters (
  monsterId INTEGER PRIMARY KEY AUTOINCREMENT,
  monsterCategory TEXT NOT NULL,
  monsterPicture TEXT NOT NULL,
  monsterName TEXT NOT NULL,
  monsterDescription TEXT NOT NULL,
  monsterLevel INTEGER NOT NULL,
  monsterHp INTEGER NOT NULL,
  monsterMp INTEGER NOT NULL,
  monsterStrength INTEGER NOT NULL,
  monsterMagic INTEGER NOT NULL,
  monsterAgility INTEGER NOT NULL,
  monsterDefense INTEGER NOT NULL,
  monsterDefenseMagic INTEGER NOT NULL,
  monsterExperience INTEGER NOT NULL,
  monsterGold INTEGER NOT NULL,
  monsterLimited TEXT NOT NULL,
  monsterQuantity INTEGER NOT NULL,
  monsterQuantityBattle INTEGER NOT NULL,
  monsterQuantityEscaped INTEGER NOT NULL,
  monsterQuantityVictory INTEGER NOT NULL,
  monsterQuantityDefeated INTEGER NOT NULL,
  monsterQuantityDraw INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_monsters_battles_stats (
  monsterBattleStatsId INTEGER PRIMARY KEY AUTOINCREMENT,
  monsterBattleStatsMonsterId INTEGER NOT NULL,
  monsterBattleStatsCharacterId INTEGER NOT NULL,
  monsterBattleStatsType TEXT NOT NULL,
  monsterBattleStatsDateTime TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_monsters_categories (
  monsterCategoryId INTEGER PRIMARY KEY AUTOINCREMENT,
  monsterCategoryName TEXT NOT NULL,
  monsterCategoryNameShow TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_monsters_drops
(
  monsterDropID INTEGER PRIMARY KEY AUTOINCREMENT,
  monsterDropMonsterId INTEGER NOT NULL,
  monsterDropItemId INTEGER NOT NULL,
  monsterDropItemVisible TEXT,
  monsterDropRate INTEGER NOT NULL,
  monsterDropRateVisible TEXT
);

CREATE TABLE IF NOT EXISTS car_news
(
  newsId INTEGER PRIMARY KEY AUTOINCREMENT,
  newsPicture TEXT NOT NULL,
  newsTitle TEXT NOT NULL,
  newsMessage TEXT NOT NULL,
  newsAccountPseudo TEXT NOT NULL,
  newsDate TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_notifications
(
  notificationId INTEGER PRIMARY KEY AUTOINCREMENT,
  notificationCharacterId INTEGER NOT NULL,
  notificationDateTime TEXT NOT NULL,
  notificationMessage TEXT NOT NULL,
  notificationRead TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_places
(
  placeId INTEGER PRIMARY KEY AUTOINCREMENT,
  placePicture TEXT NOT NULL,
  placeName TEXT NOT NULL,
  placeDescription TEXT NOT NULL,
  placePriceInn INTEGER NOT NULL,
  placeChapter INTEGER NOT NULL,
  placeAccess TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_places_monsters
(
  placeMonsterId INTEGER PRIMARY KEY AUTOINCREMENT,
  placeMonsterPlaceId INTEGER NOT NULL,
  placeMonsterMonsterId INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_places_shops
(
  placeShopId INTEGER PRIMARY KEY AUTOINCREMENT,
  placeShopPlaceId INTEGER NOT NULL,
  placeShopShopId INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_private_conversation
(
  privateConversationId INTEGER PRIMARY KEY AUTOINCREMENT,
  privateConversationCharacterOneId INTEGER NOT NULL,
  privateConversationCharacterTwoId INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_private_conversation_message
(
  privateConversationMessageId INTEGER PRIMARY KEY AUTOINCREMENT,
  privateConversationMessagePrivateConversationId INTEGER NOT NULL,
  privateConversationMessageCharacterId INTEGER NOT NULL,
  privateConversationMessageDateTime TEXT NOT NULL,
  privateConversationMessage TEXT NOT NULL,
  privateConversationMessageRead TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_races
(
  raceId INTEGER PRIMARY KEY AUTOINCREMENT,
  racePicture TEXT NOT NULL,
  raceName TEXT NOT NULL,
  raceDescription TEXT NOT NULL,
  raceHpBonus INTEGER NOT NULL,
  raceMpBonus INTEGER NOT NULL,
  raceStrengthBonus INTEGER NOT NULL,
  raceMagicBonus INTEGER NOT NULL,
  raceAgilityBonus INTEGER NOT NULL,
  raceDefenseBonus INTEGER NOT NULL,
  raceDefenseMagicBonus INTEGER NOT NULL,
  raceWisdomBonus INTEGER NOT NULL,
  raceProspectingBonus INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_shops
(
  shopId INTEGER PRIMARY KEY AUTOINCREMENT,
  shopPicture TEXT NOT NULL,
  shopName TEXT NOT NULL,
  shopDescription TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_shops_items
(
  shopItemId INTEGER PRIMARY KEY AUTOINCREMENT,
  shopItemShopId TEXT NOT NULL,
  shopItemItemId TEXT NOT NULL,
  shopItemDiscount INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_trades
(
  tradeId INTEGER PRIMARY KEY AUTOINCREMENT,
  tradeCharacterOneId INTEGER NOT NULL,
  tradeCharacterTwoId INTEGER NOT NULL,
  tradeMessage TEXT NOT NULL,
  tradeLastUpdate TEXT NOT NULL,
  tradeCharacterOneTradeAccepted TEXT NOT NULL,
  tradeCharacterTwoTradeAccepted TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS car_trades_items
(
  tradeItemId INTEGER PRIMARY KEY AUTOINCREMENT,
  tradeItemTradeId INTEGER NOT NULL,
  tradeItemItemId INTEGER NOT NULL,
  tradeItemCharacterId INTEGER NOT NULL,
  tradeItemItemQuantity INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_trades_golds
(
  tradeGoldId INTEGER PRIMARY KEY AUTOINCREMENT,
  tradeGoldTradeId INTEGER NOT NULL,
  tradeGoldCharacterId INTEGER NOT NULL,
  tradeGoldQuantity INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS car_trades_requests
(
  tradeRequestId INTEGER PRIMARY KEY AUTOINCREMENT,
  tradeRequestCharacterOneId INTEGER NOT NULL,
  tradeRequestCharacterTwoId INTEGER NOT NULL,
  tradeRequestMessage TEXT NOT NULL
);

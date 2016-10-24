-- Apprecie Database 16/05/2015

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema appreciedb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema appreciedb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `appreciedb` DEFAULT CHARACTER SET utf8 ;
USE `appreciedb` ;


CREATE TABLE `sessiondata` (
  `sessionId` varchar(35) NOT NULL,
  `data` text NOT NULL,
  `createdAt` int(15) unsigned NOT NULL,
  `modifiedAt` int(15) unsigned DEFAULT NULL,
  PRIMARY KEY (`sessionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SELECT * FROM appreciedb.sessiondata;

CREATE TABLE IF NOT EXISTS `accountlocks` (
  `loginId` int(11) NOT NULL,
  `portalId` int(11) NOT NULL,
  `sessionId` varchar(35) NOT NULL,
  `lastActive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`loginId`,`portalId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `terms` (
  `termsId` bigint(20) NOT NULL AUTO_INCREMENT,
  `version` varchar(45) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `defaultName` varchar(100) NOT NULL,
  `defaultContent` text,
  `state` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`termsId`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `termssettings` (
  `termsSettingsId` bigint(20) NOT NULL AUTO_INCREMENT,
  `termsId` bigint(20) NOT NULL,
  `roleId` int(11) DEFAULT NULL,
  `portalId` int(11) DEFAULT NULL,
  `isRsvp` bit(1) NOT NULL DEFAULT b'0',
  `isPublic` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`termsSettingsId`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `userterms` (
  `userId` bigint(20) NOT NULL,
  `termsId` bigint(20) NOT NULL,
  `acceptedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userId`,`termsId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `itemnotifications` (
  `itemNotificationId` bigint(20) NOT NULL AUTO_INCREMENT,
  `itemId` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `isSent` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`itemNotificationId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `appreciedb`.`organisations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`organisations` (
  `organisationId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `isAffiliateSupplierOf` BIGINT(20) NULL DEFAULT NULL,
  `organisationName` VARCHAR(100) NOT NULL,
  `organisationDescription` VARCHAR(50) NULL DEFAULT NULL,
  `isPortalOwner` BIT(1) NOT NULL DEFAULT b'0',
  `portalId` INT(11) NOT NULL,
  `subDomain` VARCHAR(45) NULL DEFAULT NULL,
  `suspended` BIT(1) NOT NULL DEFAULT b'0',
  `vatNumber` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`organisationId`),
  INDEX `homePortal_idx` (`portalId` ASC),
  INDEX `suppliesOrg_idx` (`isAffiliateSupplierOf` ASC),
  CONSTRAINT `homePortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `suppliesOrg`
  FOREIGN KEY (`isAffiliateSupplierOf`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 885
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`users` (
  `userId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `portalId` INT(11) NOT NULL,
  `organisationId` BIGINT(20) NULL DEFAULT NULL,
  `creatingUser` BIGINT(20) NULL DEFAULT NULL,
  `portalUserId` INT(11) NULL DEFAULT NULL,
  `userGUID` CHAR(82) NOT NULL,
  `creationDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending','active','deactivated') NULL DEFAULT NULL,
  `isDeleted` BIT(1) NULL DEFAULT NULL,
  `tier` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`userId`),
  INDEX `usersHomePortal_idx` (`portalId` ASC),
  INDEX `creatingUserLink_idx` (`creatingUser` ASC),
  INDEX `creatingUserL_idx` (`creatingUser` ASC),
  INDEX `memberOfOrganisation_idx` (`organisationId` ASC),
  CONSTRAINT `creatingUserL`
  FOREIGN KEY (`creatingUser`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `memberOfOrganisation`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `usersHomePortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 17434
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`portals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`portals` (
  `portalId` INT(11) NOT NULL AUTO_INCREMENT,
  `accountManager` BIGINT(20) NULL DEFAULT NULL,
  `portalName` VARCHAR(45) NOT NULL,
  `portalSubdomain` VARCHAR(45) NOT NULL,
  `suspended` BIT(1) NOT NULL DEFAULT b'0',
  `internalAlias` VARCHAR(45) NOT NULL,
  `portalGUID` CHAR(100) NULL DEFAULT NULL,
  `paymentDisabled` BIT(1) NULL DEFAULT b'0',
  `description` VARCHAR(45) NULL DEFAULT NULL,
  `edition` ENUM('FreemiumPro','Professional','Enterprise','VIP','Supplier','System') NULL DEFAULT NULL,
  `createdDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`portalId`),
  UNIQUE INDEX `portalName_UNIQUE` (`portalName` ASC),
  UNIQUE INDEX `portalSubdomain_UNIQUE` (`portalSubdomain` ASC),
  UNIQUE INDEX `internalAlias_UNIQUE` (`internalAlias` ASC),
  UNIQUE INDEX `portalGUID_UNIQUE` (`portalGUID` ASC),
  INDEX `portalAccountManager_idx` (`accountManager` ASC),
  CONSTRAINT `portalAccountManager`
  FOREIGN KEY (`accountManager`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 959
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`activitylog`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`activitylog` (
  `activityId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `ident` VARCHAR(300) NOT NULL,
  `activity` VARCHAR(100) NULL DEFAULT NULL,
  `activityDetails` VARCHAR(8000) NOT NULL,
  `sessionId` VARCHAR(45) NOT NULL,
  `datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ipAddress` VARCHAR(45) NULL DEFAULT NULL,
  `role` VARCHAR(45) NULL DEFAULT NULL,
  `portalId` INT(11) NULL DEFAULT NULL,
  `userId` BIGINT(20) NULL DEFAULT NULL,
  PRIMARY KEY (`activityId`),
  INDEX `relatedActivityPortal` (`portalId` ASC),
  INDEX `relatedActivityUser_idx` (`userId` ASC),
  CONSTRAINT `relatedActivityPortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `relatedActivityUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 150105
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`address` (
  `addressId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `id` VARCHAR(150) NULL DEFAULT NULL,
  `domesticId` VARCHAR(150) NULL DEFAULT NULL,
  `language` VARCHAR(150) NULL DEFAULT NULL,
  `languageAlternatives` VARCHAR(150) NULL DEFAULT NULL,
  `department` VARCHAR(150) NULL DEFAULT NULL,
  `company` VARCHAR(150) NULL DEFAULT NULL,
  `subBuilding` VARCHAR(150) NULL DEFAULT NULL,
  `buildingNumber` VARCHAR(45) NULL DEFAULT NULL,
  `buildingName` VARCHAR(150) NULL DEFAULT NULL,
  `secondaryStreet` VARCHAR(150) NULL DEFAULT NULL,
  `street` VARCHAR(150) NULL DEFAULT NULL,
  `block` VARCHAR(150) NULL DEFAULT NULL,
  `neighbourhood` VARCHAR(150) NULL DEFAULT NULL,
  `district` VARCHAR(150) NULL DEFAULT NULL,
  `city` VARCHAR(150) NULL DEFAULT NULL,
  `line1` VARCHAR(150) NULL DEFAULT NULL,
  `line2` VARCHAR(150) NULL DEFAULT NULL,
  `line3` VARCHAR(150) NULL DEFAULT NULL,
  `line4` VARCHAR(150) NULL DEFAULT NULL,
  `line5` VARCHAR(150) NULL DEFAULT NULL,
  `adminAreaName` VARCHAR(150) NULL DEFAULT NULL,
  `adminAreaCode` VARCHAR(150) NULL DEFAULT NULL,
  `province` VARCHAR(150) NULL DEFAULT NULL,
  `provinceName` VARCHAR(150) NULL DEFAULT NULL,
  `provinceCode` VARCHAR(150) NULL DEFAULT NULL,
  `postalCode` VARCHAR(150) NULL DEFAULT NULL,
  `countryName` VARCHAR(150) NULL DEFAULT NULL,
  `countryIso2` VARCHAR(150) NULL DEFAULT NULL,
  `countryIso3` VARCHAR(150) NULL DEFAULT NULL,
  `countryIsoNumber` VARCHAR(150) NULL DEFAULT NULL,
  `sortingNumber1` VARCHAR(150) NULL DEFAULT NULL,
  `sortingNumber2` VARCHAR(150) NULL DEFAULT NULL,
  `barcode` VARCHAR(150) NULL DEFAULT NULL,
  `poBoxNumber` VARCHAR(150) NULL DEFAULT NULL,
  `label` VARCHAR(500) NULL DEFAULT NULL,
  `type` VARCHAR(45) NULL DEFAULT NULL,
  `dataLevel` VARCHAR(45) NULL DEFAULT NULL,
  `userProvided` BIT(1) NULL DEFAULT b'0',
  `latitude` VARCHAR(45) NULL DEFAULT NULL,
  `longitude` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`addressId`))
  ENGINE = InnoDB
  AUTO_INCREMENT = 1995
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`assetscanresults`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`assetscanresults` (
  `localId` INT(11) NOT NULL AUTO_INCREMENT,
  `englishText` VARCHAR(10000) NOT NULL,
  `sourceFile` VARCHAR(300) NOT NULL,
  `lineNumber` VARCHAR(45) NOT NULL,
  `function` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`localId`))
  ENGINE = InnoDB
  AUTO_INCREMENT = 3404
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`contact`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`contact` (
  `contactId` INT(11) NOT NULL AUTO_INCREMENT,
  `portalId` INT(11) NOT NULL,
  `organisationId` BIGINT(20) NULL DEFAULT NULL,
  `isPrimary` BIT(1) NOT NULL DEFAULT b'0',
  `recordName` VARCHAR(45) NULL DEFAULT NULL,
  `addressId` BIGINT(20) NULL DEFAULT '0',
  `contactNameAndTitle` VARCHAR(100) NULL DEFAULT NULL,
  `contactPosition` VARCHAR(45) NULL DEFAULT NULL,
  `telephone` VARCHAR(15) NULL DEFAULT NULL,
  `mobile` VARCHAR(15) NULL DEFAULT NULL,
  `email` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`contactId`, `portalId`),
  INDEX `relatedPortal_idx` (`portalId` ASC),
  INDEX `contactAddress_idx` (`addressId` ASC),
  INDEX `relatedOrganisation_idx` (`organisationId` ASC),
  CONSTRAINT `contactAddress`
  FOREIGN KEY (`addressId`)
  REFERENCES `appreciedb`.`address` (`addressId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `relatedOrganisation`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `relatedPortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 794
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'contact details for a given portal';


-- -----------------------------------------------------
-- Table `appreciedb`.`content`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`content` (
  `contentId` CHAR(36) NOT NULL,
  `languageId` INT(11) NOT NULL,
  `sourcePortalId` INT(11) NOT NULL,
  `content` TEXT NULL DEFAULT NULL,
  `description` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`contentId`, `languageId`),
  INDEX `sourcePortal_idx` (`sourcePortalId` ASC),
  INDEX `sourcePortalLink_idx` (`sourcePortalId` ASC),
  CONSTRAINT `sourcePortalLink`
  FOREIGN KEY (`sourcePortalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`currencies`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`currencies` (
  `currencyId` INT(11) NOT NULL AUTO_INCREMENT,
  `alphabeticCode` VARCHAR(45) NOT NULL,
  `currency` VARCHAR(45) NOT NULL,
  `enabled` BIT(1) NOT NULL DEFAULT b'0',
  `symbol` VARCHAR(1) NULL DEFAULT NULL,
  PRIMARY KEY (`currencyId`, `alphabeticCode`))
  ENGINE = InnoDB
  AUTO_INCREMENT = 4
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`items`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`items` (
  `itemId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `sourceOrganisationId` BIGINT(20) NOT NULL,
  `type` ENUM('event','offer') NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `state` ENUM('draft','approving','approved','denied','arranging','held','closed') NOT NULL DEFAULT 'draft',
  `summary` VARCHAR(45) NOT NULL,
  `creatorId` BIGINT(20) NULL DEFAULT NULL,
  `sourcePortalId` INT(11) NULL DEFAULT NULL,
  `sourceByArrangement` BIGINT(20) NULL DEFAULT NULL COMMENT 'if this item was created based on a by arrangement the source arrnagement is here',
  `destination` ENUM('private','curated','parent') NULL DEFAULT NULL,
  `unitPrice` INT(11) NULL DEFAULT NULL COMMENT 'price is in pence or smallest whole unit of currency.',
  `taxablePercent` FLOAT NULL DEFAULT '20',
  `tier` SMALLINT(6) NULL DEFAULT NULL,
  `purchaseTerms` VARCHAR(45) NULL DEFAULT NULL,
  `maxUnits` INT(11) NULL DEFAULT NULL,
  `rejectionReason` VARCHAR(1000) NULL DEFAULT NULL,
  `reservationFee` FLOAT NULL DEFAULT NULL,
  `reservationEndDate` TIMESTAMP NULL DEFAULT NULL,
  `reservationLength` INT(11) NULL DEFAULT NULL COMMENT 'expected days,  -1 to indicate no limit  (i.e reservation exists until event runs)',
  `packageSize` INT(11) NULL DEFAULT NULL,
  `commissionAmount` FLOAT NULL DEFAULT NULL,
  `adminFee` FLOAT NULL DEFAULT NULL,
  `currencyId` INT(11) NULL DEFAULT NULL,
  `dateCreated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `isByArrangement` BIT(1) NULL DEFAULT b'0',
  `isArranged` BIT(1) NULL DEFAULT b'0',
  `isArrangedFor` BIGINT(20) NULL DEFAULT NULL,
  `arrangementMessageThread` BIGINT(20) NULL DEFAULT NULL,
  PRIMARY KEY (`itemId`),
  INDEX `sourcePortalId_idx` (`sourcePortalId` ASC),
  INDEX `creator_idx` (`creatorId` ASC),
  INDEX `sourceByArrangement_idx` (`sourceByArrangement` ASC),
  INDEX `inCurrency_idx` (`currencyId` ASC),
  INDEX `sourceOrganisation_idx` (`sourceOrganisationId` ASC),
  INDEX `sourceByArrangement_idx1` (`itemId` ASC, `sourceByArrangement` ASC),
  CONSTRAINT `creator`
  FOREIGN KEY (`creatorId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `inCurrency`
  FOREIGN KEY (`currencyId`)
  REFERENCES `appreciedb`.`currencies` (`currencyId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `sourceByArrangement`
  FOREIGN KEY (`sourceByArrangement`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `sourceOrganisation`
  FOREIGN KEY (`sourceOrganisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON UPDATE NO ACTION,
  CONSTRAINT `sourcePortal`
  FOREIGN KEY (`sourcePortalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 2699
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`curateditems`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`curateditems` (
  `itemId` BIGINT(20) NOT NULL,
  `portalId` INT(11) NOT NULL,
  PRIMARY KEY (`itemId`, `portalId`),
  INDEX `itemForPortal_idx` (`portalId` ASC),
  INDEX `portalHasItem_idx` (`itemId` ASC),
  CONSTRAINT `itemForPortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `portalHasItem`
  FOREIGN KEY (`itemId`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'Items that have been specifically curated to a portal to be made available to MM / RM';


-- -----------------------------------------------------
-- Table `appreciedb`.`dietaryrequirements`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`dietaryrequirements` (
  `requirementId` INT(11) NOT NULL AUTO_INCREMENT,
  `requirement` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`requirementId`))
  ENGINE = InnoDB
  AUTO_INCREMENT = 19
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`events`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`events` (
  `eventId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `itemId` BIGINT(20) NOT NULL,
  `description` VARCHAR(100) NULL DEFAULT NULL,
  `status` ENUM('tbc','open','cancelled','fully-booked','published','locked','closed','expired','rejected') NULL DEFAULT NULL,
  `attendanceTerms` TEXT NULL DEFAULT NULL,
  `targetAge18to34` BIT(1) NULL DEFAULT b'0',
  `targetAge34to65` BIT(1) NULL DEFAULT b'0',
  `targetAge65Plus` BIT(1) NULL DEFAULT b'0',
  `gender` ENUM('male','female','mixed') NULL DEFAULT 'mixed',
  `marketValue` INT(11) NULL DEFAULT NULL,
  `costToDeliver` INT(11) NULL DEFAULT NULL,
  `pricePerAttendee` INT(11) NULL DEFAULT NULL,
  `minUnits` INT(11) NULL DEFAULT NULL,
  `endDateTime` DATETIME NULL DEFAULT NULL,
  `startDateTime` DATETIME NULL DEFAULT NULL,
  `bookingEndDate` TIMESTAMP NULL DEFAULT NULL,
  `bookingStartDate` TIMESTAMP NULL DEFAULT NULL,
  `addressId` BIGINT(20) NULL DEFAULT NULL,
  `afternoonTea` BIT(1) NULL DEFAULT NULL,
  `lunch` BIT(1) NULL DEFAULT NULL,
  `dinner` BIT(1) NULL DEFAULT NULL,
  `breakfast` BIT(1) NULL DEFAULT NULL,
  `lightRefreshment` BIT(1) NULL DEFAULT NULL,
  `bookingEndNoticeSent` BIT(1) NULL DEFAULT b'0',
  PRIMARY KEY (`eventId`),
  INDEX `sourceItem_idx` (`itemId` ASC),
  CONSTRAINT `sourceItem`
  FOREIGN KEY (`itemId`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1335
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`goals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`goals` (
  `goalId` INT(11) NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`goalId`))
  ENGINE = InnoDB
  AUTO_INCREMENT = 9
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`eventgoals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`eventgoals` (
  `goalId` INT(11) NOT NULL,
  `eventId` BIGINT(20) NOT NULL,
  PRIMARY KEY (`goalId`, `eventId`),
  INDEX `eventOfGoal_idx` (`eventId` ASC),
  CONSTRAINT `eventOfGoal`
  FOREIGN KEY (`eventId`)
  REFERENCES `appreciedb`.`events` (`eventId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `goalOfEvent`
  FOREIGN KEY (`goalId`)
  REFERENCES `appreciedb`.`goals` (`goalId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`genworthleaderboard`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`genworthleaderboard` (
  `emailAddress` VARCHAR(145) NOT NULL,
  `kpi` INT(11) NOT NULL,
  `sales` INT(11) NOT NULL,
  `weekNumber` INT(11) NOT NULL,
  `organisationId` INT(11) NOT NULL,
  `userId` INT(11) NOT NULL,
  PRIMARY KEY (`weekNumber`, `userId`))
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`genworthleaderboardx`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`genworthleaderboardx` (
  `emailAddress` VARCHAR(145) NOT NULL,
  `kpi` INT(11) NOT NULL,
  `sales` INT(11) NOT NULL,
  `importDate` DATE NOT NULL,
  `organisationId` INT(11) NOT NULL,
  `userId` INT(11) NOT NULL,
  `weekNumber` INT(11) NOT NULL,
  PRIMARY KEY (`weekNumber`, `userId`))
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`guestlist`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`guestlist` (
  `itemId` BIGINT(20) NOT NULL,
  `userId` BIGINT(20) NOT NULL,
  `owningUserId` BIGINT(20) NOT NULL,
  `suggestedBy` BIGINT(20) NULL DEFAULT NULL,
  `attending` BIT(1) NOT NULL DEFAULT b'0',
  `invitationSent` DATETIME NULL DEFAULT NULL,
  `status` ENUM('confirmed','declined','cancelled','revoked','invited') NULL DEFAULT NULL,
  `confirmingUserId` BIGINT(20) NULL DEFAULT NULL,
  `paid` BIT(1) NULL DEFAULT b'0',
  `confirmationSent` TIMESTAMP NULL DEFAULT NULL,
  `invitationHash` VARCHAR(100) NULL DEFAULT NULL,
  `fiveDayAttendingNoticeSent` BIT(1) NULL DEFAULT NULL,
  `fiveDayNoResponseNoticeSent` BIT(1) NULL DEFAULT NULL,
  `spaces` int(11) DEFAULT '1',
  PRIMARY KEY (`itemId`, `userId`, `owningUserId`),
  INDEX `targetEvent_idx` (`itemId` ASC),
  INDEX `targetUser_idx` (`userId` ASC),
  INDEX `suggestingUser_idx` (`suggestedBy` ASC),
  CONSTRAINT `confirmingUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `purchasingByUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `suggestingUser`
  FOREIGN KEY (`suggestedBy`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `targetEvent`
  FOREIGN KEY (`itemId`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `targetUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`helpcontent`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`helpcontent` (
  `helpId` INT(11) NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(45) NOT NULL,
  `content` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`helpId`))
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`interests`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`interests` (
  `interestId` INT(11) NOT NULL AUTO_INCREMENT,
  `interest` VARCHAR(100) NOT NULL,
  `isTop` BIT(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`interestId`),
  UNIQUE INDEX `interest_UNIQUE` (`interest` ASC))
  ENGINE = InnoDB
  AUTO_INCREMENT = 234
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'stores a record of a users interests, which can be cross referenced against those registrerd for an item';


-- -----------------------------------------------------
-- Table `appreciedb`.`intereststree`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`intereststree` (
  `interestId` INT(11) NOT NULL,
  `parentInterestId` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`interestId`, `parentInterestId`),
  CONSTRAINT `category`
  FOREIGN KEY (`interestId`)
  REFERENCES `appreciedb`.`interests` (`interestId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `subcategory`
  FOREIGN KEY (`interestId`)
  REFERENCES `appreciedb`.`interests` (`interestId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`itemapproval`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`itemapproval` (
  `itemId` BIGINT(20) NOT NULL,
  `creatingOrganisationId` BIGINT(20) NOT NULL,
  `verifyingOrganisationId` BIGINT(20) NOT NULL,
  `verifiedByUserId` BIGINT(20) NULL DEFAULT NULL,
  `status` ENUM('approved','denied','pending','unpublished') NULL DEFAULT 'pending',
  `deniedReason` VARCHAR(1000) NULL DEFAULT NULL,
  `lastProcessed` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`itemId`),
  INDEX `owningOrganisation_idx` (`creatingOrganisationId` ASC),
  INDEX `ownOrganisation_idx` (`creatingOrganisationId` ASC),
  INDEX `verifyingOrg_idx` (`verifyingOrganisationId` ASC),
  INDEX `verfiyingUsr_idx` (`verifiedByUserId` ASC),
  CONSTRAINT `ownOrganisation`
  FOREIGN KEY (`creatingOrganisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `verfiyingUsr`
  FOREIGN KEY (`verifiedByUserId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `verifyingOrg`
  FOREIGN KEY (`verifyingOrganisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'handles approval workflow -  only once approved should an item be sent for actual visibility or curation';


-- -----------------------------------------------------
-- Table `appreciedb`.`iteminterests`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`iteminterests` (
  `itemId` BIGINT(20) NOT NULL,
  `interestId` INT(11) NOT NULL,
  PRIMARY KEY (`itemId`, `interestId`),
  INDEX `eventInterest_idx` (`itemId` ASC),
  INDEX `itemInterest_idx` (`interestId` ASC),
  CONSTRAINT `interestInterest`
  FOREIGN KEY (`interestId`)
  REFERENCES `appreciedb`.`interests` (`interestId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `interestItem`
  FOREIGN KEY (`itemId`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'items can define a set of interests which can be used to better filter and target to specific members.  interests are assumed to be provided by the supplier';


-- -----------------------------------------------------
-- Table `appreciedb`.`itemmedia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`itemmedia` (
  `mediaId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `itemId` BIGINT(20) NOT NULL,
  `order` INT(11) NOT NULL,
  `src` VARCHAR(300) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `alt` VARCHAR(45) NULL DEFAULT NULL,
  `thumbnail` VARCHAR(300) NULL DEFAULT NULL,
  PRIMARY KEY (`mediaId`, `itemId`),
  INDEX `mediaToItem_idx` (`itemId` ASC),
  CONSTRAINT `mediaToItem`
  FOREIGN KEY (`itemId`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 815
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`itemnotes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`itemnotes` (
  `noteid` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `aboutItemId` BIGINT(20) NOT NULL,
  `byUserId` BIGINT(20) NOT NULL,
  `portalId` INT(11) NOT NULL,
  `body` VARCHAR(300) NOT NULL,
  PRIMARY KEY (`noteid`),
  INDEX `sourcePortal_idx` (`portalId` ASC),
  INDEX `byUser_idx` (`byUserId` ASC),
  INDEX `aboutItem_idx` (`aboutItemId` ASC),
  INDEX `sourceNotePortalId_idx` (`portalId` ASC),
  INDEX `noteOwnerId_idx` (`byUserId` ASC),
  INDEX `noteAboutitemId_idx` (`aboutItemId` ASC),
  CONSTRAINT `noteAboutitemId`
  FOREIGN KEY (`aboutItemId`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `noteOwnerId`
  FOREIGN KEY (`byUserId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `sourceNotePortalId`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`portalmembergroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`portalmembergroups` (
  `groupId` INT(11) NOT NULL AUTO_INCREMENT,
  `portalId` INT(11) NOT NULL,
  `ownerId` BIGINT(20) NOT NULL COMMENT 'most likely manager',
  `groupname` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`groupId`, `portalId`, `ownerId`),
  INDEX `groupHomePortal_idx` (`portalId` ASC),
  INDEX `groupOwner_idx` (`ownerId` ASC),
  CONSTRAINT `groupHomePortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `groupOwner`
  FOREIGN KEY (`ownerId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'holds groups defined by managers within a portal.';


-- -----------------------------------------------------
-- Table `appreciedb`.`itemvault`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`itemvault` (
  `vaultItemId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `itemId` BIGINT(20) NOT NULL,
  `ownerId` BIGINT(20) NULL DEFAULT '0',
  `portalId` INT(11) NOT NULL,
  `groupId` INT(11) NULL DEFAULT '0',
  `organisationId` BIGINT(20) NOT NULL DEFAULT '0',
  `clientsCanSee` BIT(1) NULL DEFAULT b'0',
  `internalCanSee` BIT(1) NULL DEFAULT b'0',
  `suggestedBy` BIGINT(20) NULL DEFAULT NULL,
  PRIMARY KEY (`vaultItemId`),
  INDEX `portalVaultItem_idx` (`portalId` ASC),
  INDEX `portalVaultItem_idx1` (`itemId` ASC),
  INDEX `vaultItemOwner_idx` (`ownerId` ASC),
  INDEX `vaultItemGroup_idx` (`groupId` ASC),
  INDEX `valutItemOrg_idx` (`organisationId` ASC),
  CONSTRAINT `VaultItemPortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `portalVaultItem`
  FOREIGN KEY (`itemId`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `valutItemOrg`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `vaultItemGroup`
  FOREIGN KEY (`groupId`)
  REFERENCES `appreciedb`.`portalmembergroups` (`groupId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `vaultItemOwner`
  FOREIGN KEY (`ownerId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1162
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'the portal item vault contains items / packages that have been consumed by a user (expecting MM / RM), thus making the item available to their members.';


-- -----------------------------------------------------
-- Table `appreciedb`.`languages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`languages` (
  `languageId` INT(11) NOT NULL,
  `rtl` BIT(1) NOT NULL DEFAULT b'0',
  `name` VARCHAR(45) NOT NULL,
  `nativeName` VARCHAR(45) NULL DEFAULT NULL,
  `locale` VARCHAR(45) NULL DEFAULT NULL,
  `enabled` BIT(1) NULL DEFAULT b'0',
  PRIMARY KEY (`languageId`))
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`mailsettings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`mailsettings` (
  `organisationId` BIGINT(20) NOT NULL,
  `smtpAddress` VARCHAR(100) NOT NULL,
  `smtpUser` VARCHAR(100) NOT NULL,
  `smtpPassword` VARBINARY(300) NOT NULL,
  `smtpPort` INT(11) NOT NULL,
  PRIMARY KEY (`organisationId`))
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`messages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`messages` (
  `messageId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `targetUser` BIGINT(20) NOT NULL,
  `sourceUser` BIGINT(20) NOT NULL,
  `sourcePortal` INT(11) NOT NULL,
  `sourceOrganisation` BIGINT(20) NULL DEFAULT NULL,
  `referenceItem` BIGINT(20) NULL DEFAULT NULL,
  `sourceDescription` VARCHAR(45) NOT NULL COMMENT 'could be system or Firstname lastname etc - useful for preventing cross portal queries',
  `title` VARCHAR(100) NOT NULL,
  `body` TEXT NOT NULL,
  `sent` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read` TIMESTAMP NULL DEFAULT NULL,
  `responseToMessage` BIGINT(20) NULL DEFAULT NULL,
  `deleted` BIT(1) NULL DEFAULT b'0',
  PRIMARY KEY (`messageId`),
  INDEX `messageSrcUser_idx` (`sourceUser` ASC),
  INDEX `messageDstuser_idx` (`targetUser` ASC),
  INDEX `messageSrcPortal_idx` (`sourcePortal` ASC),
  INDEX `messageRefItem_idx` (`referenceItem` ASC),
  INDEX `messageSrcOrg_idx` (`sourceOrganisation` ASC),
  CONSTRAINT `messageDstuser`
  FOREIGN KEY (`targetUser`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `messageRefItem`
  FOREIGN KEY (`referenceItem`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `messageSrcOrg`
  FOREIGN KEY (`sourceOrganisation`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `messageSrcPortal`
  FOREIGN KEY (`sourcePortal`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `messageSrcUser`
  FOREIGN KEY (`sourceUser`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 468
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`messagethreads`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`messagethreads` (
  `threadId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `startDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `startedByUser` BIGINT(20) NOT NULL,
  `firstRecipientUser` BIGINT(20) NOT NULL,
  `archived` BIT(1) NULL DEFAULT b'0',
  `byArrangementId` BIGINT(20) NULL DEFAULT NULL,
  `seen` bit(1) DEFAULT b'0',
  `type` enum('arrangement','host','generic','invitation','suggestion') DEFAULT NULL,
  PRIMARY KEY (`threadId`))
  ENGINE = InnoDB
  AUTO_INCREMENT = 405
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`messagesinthread`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`messagesinthread` (
  `threadId` BIGINT(20) NOT NULL,
  `messageId` BIGINT(20) NOT NULL,
  PRIMARY KEY (`threadId`, `messageId`),
  INDEX `relatedMsg_idx` (`messageId` ASC),
  CONSTRAINT `relatedMsg`
  FOREIGN KEY (`messageId`)
  REFERENCES `appreciedb`.`messages` (`messageId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `relatedThread`
  FOREIGN KEY (`threadId`)
  REFERENCES `appreciedb`.`messagethreads` (`threadId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`orders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`orders` (
  `orderId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `createdDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `customerId` BIGINT(20) NULL DEFAULT NULL,
  `supplierId` BIGINT(20) NULL DEFAULT NULL,
  `fulfilled` TIMESTAMP NULL DEFAULT NULL,
  `status` ENUM('pending','processing','complete','cancelled','held','error') NULL DEFAULT NULL,
  `statusReason` VARCHAR(500) NULL DEFAULT NULL,
  `currencyId` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`orderId`))
  ENGINE = InnoDB
  AUTO_INCREMENT = 355
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`orderitems`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`orderitems` (
  `orderItemId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `orderId` BIGINT(20) NOT NULL,
  `itemId` BIGINT(20) NOT NULL,
  `userId` BIGINT(20) NOT NULL,
  `portalId` INT(11) NOT NULL,
  `organisationId` BIGINT(20) NOT NULL,
  `isPaidFull` BIT(1) NOT NULL DEFAULT b'0' COMMENT 'mark as paid when parent order is paid',
  `purchaseDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('full','reservation') NULL DEFAULT NULL,
  `reservationExpire` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `packageQuantity` INT(11) NOT NULL,
  `value` INT(11) NULL DEFAULT NULL,
  `description` VARCHAR(150) NULL DEFAULT NULL,
  `tax` FLOAT NULL DEFAULT NULL,
  `adminFee` INT(11) NULL DEFAULT NULL,
  `commissionAmount` INT(11) NULL DEFAULT NULL,
  `isReserved` BIT(1) NULL DEFAULT b'0',
  `reservationAmount` INT(11) NULL DEFAULT NULL,
  `packageSize` INT(11) NULL DEFAULT NULL,
  `cancelled` BIT(1) NULL DEFAULT b'0',
  `cancelledReason` VARCHAR(300) NULL DEFAULT NULL,
  `fullConversionOfOrderItemId` BIGINT(20) NULL DEFAULT NULL COMMENT 'related order item',
  PRIMARY KEY (`orderItemId`),
  INDEX `purchaseitem_idx` (`itemId` ASC),
  INDEX `sourcePortal_idx` (`portalId` ASC),
  INDEX `purchasingUser_idx` (`userId` ASC),
  INDEX `relatedOrder_idx` (`orderId` ASC),
  INDEX `providingOrganisation_idx` (`organisationId` ASC),
  CONSTRAINT `providingOrganisation`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON UPDATE NO ACTION,
  CONSTRAINT `purchaseitem`
  FOREIGN KEY (`itemId`)
  REFERENCES `appreciedb`.`items` (`itemId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `purchasingPortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `purchasingUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `relatedOrder`
  FOREIGN KEY (`orderId`)
  REFERENCES `appreciedb`.`orders` (`orderId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 342
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`organisationmanagementpermissions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`organisationmanagementpermissions` (
  `userId` BIGINT(20) NOT NULL,
  `organisationId` BIGINT(20) NOT NULL,
  PRIMARY KEY (`userId`, `organisationId`),
  INDEX `managedOrg_idx` (`organisationId` ASC),
  CONSTRAINT `managedOrg`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `managinUsr`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`organisationparents`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`organisationparents` (
  `organisationId` BIGINT(20) NOT NULL,
  `parentId` BIGINT(20) NOT NULL,
  PRIMARY KEY (`organisationId`, `parentId`),
  INDEX `parentOrg_idx` (`parentId` ASC),
  INDEX `linkOrgParent_idx` (`parentId` ASC),
  CONSTRAINT `linkOrgParent`
  FOREIGN KEY (`parentId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `linlOrgChild`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'an organisation can have a single parent, but multiple children.';


-- -----------------------------------------------------
-- Table `appreciedb`.`organisationstyles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`organisationstyles` (
  `organisationId` BIGINT(20) NOT NULL,
  `navigationPrimary` VARCHAR(45) NULL DEFAULT NULL,
  `navigationSecondary` VARCHAR(45) NULL DEFAULT NULL,
  `navigationPrimaryA` VARCHAR(45) NULL DEFAULT NULL,
  `navigationSecondaryA` VARCHAR(45) NULL DEFAULT NULL,
  `a` VARCHAR(45) NULL DEFAULT NULL,
  `aHover` VARCHAR(45) NULL DEFAULT NULL,
  `progressBar` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimary` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimaryBorder` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimaryColor` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimaryHover` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimaryHoverBorder` VARCHAR(45) NULL DEFAULT NULL,
  `disabledControl` VARCHAR(45) NULL DEFAULT NULL,
  `font` VARCHAR(150) NULL DEFAULT NULL,
  `fontColor` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`organisationId`),
  CONSTRAINT `organisationHasStyles`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`paymentsettings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`paymentsettings` (
  `organisationId` BIGINT(20) NOT NULL,
  `accessToken` VARCHAR(100) NULL DEFAULT NULL,
  `refreshToken` VARCHAR(100) NULL DEFAULT NULL,
  `publishableKey` VARCHAR(100) NULL DEFAULT NULL,
  `stripeUserId` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`organisationId`),
  CONSTRAINT `owningOrg`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`portalblockedcategories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`portalblockedcategories` (
  `interestId` INT(11) NOT NULL,
  `portalId` INT(11) NOT NULL,
  PRIMARY KEY (`interestId`, `portalId`),
  INDEX `blockedInPortal_idx` (`portalId` ASC),
  CONSTRAINT `blockedInPortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `blockedInterest`
  FOREIGN KEY (`interestId`)
  REFERENCES `appreciedb`.`interests` (`interestId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`portalmembersingroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`portalmembersingroups` (
  `userId` BIGINT(20) NOT NULL,
  `groupId` INT(11) NOT NULL,
  PRIMARY KEY (`userId`, `groupId`),
  INDEX `userInGroup_idx` (`userId` ASC),
  INDEX `groupForUser_idx` (`groupId` ASC),
  CONSTRAINT `groupForUser`
  FOREIGN KEY (`groupId`)
  REFERENCES `appreciedb`.`portalmembergroups` (`groupId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `userInGroup`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'stores a record of which members are in whcih group';


-- -----------------------------------------------------
-- Table `appreciedb`.`portalstyles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`portalstyles` (
  `portalId` INT(11) NOT NULL,
  `navigationPrimary` VARCHAR(45) NULL DEFAULT NULL,
  `navigationSecondary` VARCHAR(45) NULL DEFAULT NULL,
  `navigationPrimaryA` VARCHAR(45) NULL DEFAULT NULL,
  `navigationSecondaryA` VARCHAR(45) NULL DEFAULT NULL,
  `a` VARCHAR(45) NULL DEFAULT NULL,
  `aHover` VARCHAR(45) NULL DEFAULT NULL,
  `progressBar` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimary` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimaryBorder` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimaryColor` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimaryHover` VARCHAR(45) NULL DEFAULT NULL,
  `buttonPrimaryHoverBorder` VARCHAR(45) NULL DEFAULT NULL,
  `disabledControl` VARCHAR(45) NULL DEFAULT NULL,
  `font` VARCHAR(150) NULL DEFAULT NULL,
  `fontColor` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`portalId`),
  CONSTRAINT `portalHasStyles`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`userlogins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`userlogins` (
  `loginId` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(100) NULL DEFAULT NULL,
  `suspended` BIT(1) NULL DEFAULT b'0',
  PRIMARY KEY (`loginId`))
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`userprofiles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`userprofiles` (
  `profileId` INT(11) NOT NULL AUTO_INCREMENT,
  `homeAddressId` BIGINT(20) NULL DEFAULT NULL,
  `workAddressId` BIGINT(20) NULL DEFAULT NULL,
  `deliveryAddressId` BIGINT(20) NULL DEFAULT NULL,
  `firstname` VARBINARY(400) NULL DEFAULT NULL,
  `lastname` VARBINARY(400) NULL DEFAULT NULL,
  `title` ENUM('Mr','Ms','Miss','Mrs','Mstr','Dr','Prof','Sir','Lord','Lady','Dame','Duke','Earl') NULL DEFAULT NULL,
  `email` VARCHAR(100) NULL DEFAULT NULL,
  `phone` VARBINARY(100) NULL DEFAULT NULL,
  `mobile` VARBINARY(100) NULL DEFAULT NULL,
  `birthday` DATE NULL DEFAULT NULL,
  `gender` ENUM('male','female') NULL DEFAULT NULL,
  `occupationId` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`profileId`),
  INDEX `userHomeAddress_idx` (`homeAddressId` ASC),
  INDEX `userWorkAddress_idx` (`workAddressId` ASC),
  INDEX `userDeliveryAddress_idx` (`deliveryAddressId` ASC),
  CONSTRAINT `userDeliveryAddress`
  FOREIGN KEY (`deliveryAddressId`)
  REFERENCES `appreciedb`.`address` (`addressId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `userHomeAddress`
  FOREIGN KEY (`homeAddressId`)
  REFERENCES `appreciedb`.`address` (`addressId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `userWorkAddress`
  FOREIGN KEY (`workAddressId`)
  REFERENCES `appreciedb`.`address` (`addressId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`portalusers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`portalusers` (
  `portalUserId` INT(11) NOT NULL AUTO_INCREMENT,
  `profileId` INT(11) NULL DEFAULT NULL,
  `loginId` INT(11) NULL DEFAULT NULL,
  `reference` VARCHAR(100) NULL DEFAULT NULL,
  `signupSent` TIMESTAMP NULL DEFAULT NULL,
  `welcomeSent` TIMESTAMP NULL DEFAULT NULL,
  `isAnonymous` BIT(1) NULL DEFAULT NULL,
  `familyQuotaAvailable` INT(11) NOT NULL DEFAULT '0',
  `registrationHash` VARCHAR(100) NULL DEFAULT NULL,
  `passwordRecoverySent` TIMESTAMP NULL DEFAULT NULL,
  `passwordRecoveryHash` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`portalUserId`),
  INDEX `portalUserProfile_idx` (`profileId` ASC),
  INDEX `portalLogin_idx` (`loginId` ASC),
  CONSTRAINT `portalLogin`
  FOREIGN KEY (`loginId`)
  REFERENCES `appreciedb`.`userlogins` (`loginId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `portalUserProfile`
  FOREIGN KEY (`profileId`)
  REFERENCES `appreciedb`.`userprofiles` (`profileId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`quotas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`quotas` (
  `organisationId` BIGINT(20) NOT NULL,
  `portalId` INT(11) NULL DEFAULT NULL,
  `portalAdministratorTotal` INT(11) NOT NULL DEFAULT '1',
  `managerTotal` INT(11) NOT NULL DEFAULT '1',
  `internalMemberTotal` INT(11) NOT NULL DEFAULT '1',
  `apprecieSupplierTotal` INT(11) NOT NULL DEFAULT '0',
  `affiliateSupplierTotal` INT(11) NULL DEFAULT '0',
  `memberTotal` INT(11) NOT NULL DEFAULT '20',
  `memberFamilyTotal` INT(11) NOT NULL DEFAULT '3',
  `commissionPercent` DECIMAL(5,2) NOT NULL DEFAULT '10.00',
  `lastTenancyPaidAmount` INT(11) NULL DEFAULT NULL,
  `tenancyEnd` TIMESTAMP NULL DEFAULT NULL,
  `portalAdministratorUsed` INT(11) NOT NULL DEFAULT '0',
  `managerUsed` INT(11) NOT NULL DEFAULT '0',
  `internalMemberUsed` INT(11) NOT NULL DEFAULT '0',
  `apprecieSupplierUsed` INT(11) NOT NULL DEFAULT '0',
  `affiliateSupplierUsed` INT(11) NOT NULL DEFAULT '0',
  `memberUsed` INT(11) NOT NULL DEFAULT '0',
  `familyMemberUsed` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`organisationId`),
  INDEX `owningPortal_idx` (`portalId` ASC),
  CONSTRAINT `owningOrganisation`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `owningPortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'defines member limits for each portal';


-- -----------------------------------------------------
-- Table `appreciedb`.`roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`roles` (
  `roleId` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(150) NULL DEFAULT NULL,
  `defaultController` varchar(150) DEFAULT NULL,
  `defaultAction` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`roleId`))
  ENGINE = InnoDB
  AUTO_INCREMENT = 62
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`stripelog`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`stripelog` (
  `stripeEventId` VARCHAR(45) NOT NULL,
  `organisationId` BIGINT(20) NULL DEFAULT NULL,
  `liveMode` BIT(1) NOT NULL,
  `object` VARCHAR(45) NULL DEFAULT NULL,
  `type` VARCHAR(100) NULL DEFAULT NULL,
  `stripeUserId` VARCHAR(45) NULL DEFAULT NULL,
  `pendingWebhooks` INT(11) NULL DEFAULT NULL,
  `stripeCreatedDate` TIMESTAMP NULL DEFAULT NULL,
  `recordedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`stripeEventId`))
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`transactionitem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`transactionitem` (
  `transactionItemId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `transactionId` BIGINT(20) NOT NULL,
  `description` VARCHAR(100) NOT NULL,
  `amount` INT(11) NOT NULL,
  `taxRate` FLOAT NOT NULL,
  `total` INT(11) NOT NULL,
  PRIMARY KEY (`transactionItemId`),
  INDEX `parentTransaction_idx` (`transactionId` ASC))
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`transactions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`transactions` (
  `transactionId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `userId` BIGINT(20) NOT NULL,
  `organisationId` BIGINT(20) NOT NULL,
  `orderId` BIGINT(20) NULL DEFAULT NULL,
  `currencyId` INT(11) NULL DEFAULT NULL COMMENT 'needs to link to a currency table',
  `amount` INT(11) NOT NULL,
  `total` INT(11) NOT NULL,
  `status` ENUM('pending','processing','declined','approved','error') NOT NULL DEFAULT 'pending',
  `tax` FLOAT NULL DEFAULT NULL,
  `transactionDate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `statusReason` VARCHAR(100) NULL DEFAULT NULL,
  `gatewayData` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`transactionId`),
  INDEX `transactionInPortal_idx` (`organisationId` ASC),
  INDEX `transactionUser_idx` (`userId` ASC),
  INDEX `relatedTransOrder_idx` (`orderId` ASC),
  INDEX `transactionCurrency_idx` (`currencyId` ASC),
  CONSTRAINT `relatedTransOrder`
  FOREIGN KEY (`orderId`)
  REFERENCES `appreciedb`.`orders` (`orderId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transactionCurrency`
  FOREIGN KEY (`currencyId`)
  REFERENCES `appreciedb`.`currencies` (`currencyId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transactionInPortal`
  FOREIGN KEY (`organisationId`)
  REFERENCES `appreciedb`.`organisations` (`organisationId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transactionUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 182
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`translations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`translations` (
  `assetId` INT(11) NOT NULL AUTO_INCREMENT,
  `languageId` INT(11) NOT NULL,
  `context` VARCHAR(48) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL,
  `translatedText` VARCHAR(10000) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `englishText` VARCHAR(10000) NOT NULL,
  `decommissioned` BIT(1) NULL DEFAULT b'0',
  PRIMARY KEY (`assetId`))
  ENGINE = InnoDB
  AUTO_INCREMENT = 3731
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`usercontactpreferences`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`usercontactpreferences` (
  `userId` BIGINT(20) NOT NULL,
  `alertsAndNotifications` BIT(1) NOT NULL DEFAULT b'0',
  `invitations` BIT(1) NOT NULL DEFAULT b'0',
  `suggestions` BIT(1) NOT NULL DEFAULT b'0',
  `partnerCommunications` BIT(1) NOT NULL DEFAULT b'0',
  `updatesAndNewsletters` BIT(1) NOT NULL DEFAULT b'0',
  `intervalInDays` int(11) NOT NULL DEFAULT '7',
  `lastRun` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`userId`),
  CONSTRAINT `permissionsOfUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`userdietaryrequirements`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`userdietaryrequirements` (
  `userId` BIGINT(20) NOT NULL,
  `requirementId` INT(11) NOT NULL,
  PRIMARY KEY (`userId`, `requirementId`),
  INDEX `requirementWithUser_idx` (`requirementId` ASC),
  CONSTRAINT `requirementWithUser`
  FOREIGN KEY (`requirementId`)
  REFERENCES `appreciedb`.`dietaryrequirements` (`requirementId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `userWithRequirement`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`userfamily`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`userfamily` (
  `userId` BIGINT(20) NOT NULL,
  `relatedUserId` INT(11) NOT NULL,
  PRIMARY KEY (`userId`, `relatedUserId`),
  CONSTRAINT `foundingUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `relatedUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `appreciedb`.`userinterests`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`userinterests` (
  `interestId` INT(11) NOT NULL,
  `userId` BIGINT(20) NOT NULL,
  `userIndicated` BIT(1) NULL DEFAULT NULL,
  `managerIndicated` BIT(1) NULL DEFAULT NULL,
  `systemIndicated` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`interestId`, `userId`),
  INDEX `usersInterest_idx` (`interestId` ASC),
  INDEX `interestUser_idx` (`userId` ASC),
  CONSTRAINT `interestUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `interestUserInterest`
  FOREIGN KEY (`interestId`)
  REFERENCES `appreciedb`.`interests` (`interestId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'links a user to a set of interests and records the source of the relation - as future machine learning thought.';


-- -----------------------------------------------------
-- Table `appreciedb`.`useritems`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`useritems` (
  `orderItemId` BIGINT(20) NOT NULL,
  `itemId` BIGINT(20) NOT NULL,
  `userId` BIGINT(20) NOT NULL,
  `organisationId` BIGINT(20) NULL DEFAULT NULL,
  `unitsAvailable` INT(11) NULL DEFAULT '0',
  `state` ENUM('owned','reserved','held') NULL DEFAULT NULL,
  `reservationEnd` TIMESTAMP NULL DEFAULT NULL,
  `holdEnd` TIMESTAMP NULL DEFAULT NULL,
  `originalUnits` INT(11) NULL DEFAULT NULL,
  `reservation3dayNoticeSent` BIT(1) NULL DEFAULT NULL,
  `reservation1dayNoticeSent` BIT(1) NULL DEFAULT NULL,
  PRIMARY KEY (`orderItemId`),
  INDEX `useritemuser_idx` (`userId` ASC))
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'records the available units that a purchasing or gifted to user has available.';


-- -----------------------------------------------------
-- Table `appreciedb`.`usernotes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`usernotes` (
  `noteId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `portalId` INT(11) NOT NULL,
  `noteCreatorUserId` BIGINT(20) NOT NULL,
  `noteAboutUserId` BIGINT(20) NOT NULL,
  `body` VARBINARY(1200) NOT NULL,
  PRIMARY KEY (`noteId`),
  INDEX `noteinPortal_idx` (`portalId` ASC),
  INDEX `noteAboutUser_idx` (`noteAboutUserId` ASC),
  INDEX `noteByUser_idx` (`noteCreatorUserId` ASC),
  CONSTRAINT `noteAboutUser`
  FOREIGN KEY (`noteAboutUserId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `noteByUser`
  FOREIGN KEY (`noteCreatorUserId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `noteinPortal`
  FOREIGN KEY (`portalId`)
  REFERENCES `appreciedb`.`portals` (`portalId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'stores notes against a person';


-- -----------------------------------------------------
-- Table `appreciedb`.`usernotifications`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`usernotifications` (
  `noticeId` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `userId` BIGINT(20) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `body` TEXT NULL DEFAULT NULL COMMENT 'could be null in the case of a simple flash message',
  `url` VARCHAR(300) NULL DEFAULT NULL COMMENT 'expected to perform some action',
  `urlClicked` TIMESTAMP NULL DEFAULT NULL,
  `dismissed` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`noticeId`),
  INDEX `noticeForUser_idx` (`userId` ASC),
  CONSTRAINT `noticeForUser`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  AUTO_INCREMENT = 3969
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'these are notifcations (flashes) that are sent to specific users  - possible broadcast to role or portal requirement?';


-- -----------------------------------------------------
-- Table `appreciedb`.`userparents`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`userparents` (
  `parentId` BIGINT(20) NOT NULL,
  `childId` BIGINT(20) NOT NULL,
  `ignoredForVisibility` BIT(1) NULL DEFAULT NULL,
  PRIMARY KEY (`parentId`, `childId`),
  INDEX `parentUser_idx` (`parentId` ASC),
  INDEX `childUser_idx` (`childId` ASC),
  CONSTRAINT `childUser`
  FOREIGN KEY (`childId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `parentUser`
  FOREIGN KEY (`parentId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COMMENT = 'defines a users parents within the system.  Used for deriving visibility of vault contents';


-- -----------------------------------------------------
-- Table `appreciedb`.`userroles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `appreciedb`.`userroles` (
  `userId` BIGINT(20) NOT NULL,
  `roleId` INT(11) NOT NULL,
  `disabled` BIT(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`userId`, `roleId`),
  INDEX `userInRole_idx` (`userId` ASC),
  INDEX `roleHasUser_idx` (`roleId` ASC),
  CONSTRAINT `roleHasUser`
  FOREIGN KEY (`roleId`)
  REFERENCES `appreciedb`.`roles` (`roleId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `userInRole`
  FOREIGN KEY (`userId`)
  REFERENCES `appreciedb`.`users` (`userId`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8;

CREATE TABLE `securitylog` (
  `activityId` bigint(20) NOT NULL AUTO_INCREMENT,
  `ident` varchar(300) NOT NULL,
  `activity` varchar(100) DEFAULT NULL,
  `activityDetails` varchar(8000) NOT NULL,
  `sessionId` varchar(45) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ipAddress` varchar(45) DEFAULT NULL,
  `role` varchar(45) DEFAULT NULL,
  `portalId` int(11) DEFAULT NULL,
  `userId` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`activityId`),
  KEY `relatedActivityPortalb` (`portalId`),
  KEY `relatedActivityUserb_idx` (`userId`),
  CONSTRAINT `relatedActivityPortalb` FOREIGN KEY (`portalId`) REFERENCES `portals` (`portalId`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `relatedActivityUserb` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1768 DEFAULT CHARSET=utf8;
SELECT * FROM appreciedb.securitylog;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


/*
-- Query: SELECT * FROM appreciedb.languages
LIMIT 0, 1000

-- Date: 2015-01-15 14:24
*/
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (3,0,'English - UK','English - UK','en',1);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (15,0,'German','Deutsch','de',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (17,0,'Japanese','日本語','ja',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (18,0,'Russian','ру́сский','ru',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (19,0,'Greek','Greek','el',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (20,0,'Dutch','Nederlands','nl',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (21,0,'Slovenian','slovenščina','sl',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (22,0,'English - US','English - US','en-us',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (23,0,'English - Canada','English - Canada','en-ca',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (24,0,'Abron','Abron','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (25,0,'Acehnese','Acehnese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (26,0,'Afar','Afar','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (27,0,'Afrikaans','Afrikaans','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (28,0,'Akan','Akan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (29,0,'Albanian','Albanian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (30,0,'Alur','Alur','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (31,0,'Amharic','Amharic','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (32,0,'Ancash Quechua','Ancash Quechua','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (33,0,'Anyi','Anyi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (34,0,'Arabic','Arabic','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (35,0,'Arakanese','Arakanese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (36,0,'Armenian','Armenian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (37,0,'Assamese','Assamese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (38,0,'Azande','Azande','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (39,0,'Azerbaijani','Azerbaijani','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (40,0,'Bai','Bai','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (41,0,'Balinese','Balinese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (42,0,'Balochi','Balochi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (43,0,'Bambara','Bambara','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (44,0,'Baoulé','Baoulé','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (45,0,'Bashkir','Bashkir','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (46,0,'Basque','Basque','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (47,0,'Batak','Batak','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (48,0,'Beja','Beja','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (49,0,'Belarusian','Belarusian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (50,0,'Bemba','Bemba','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (51,0,'Bengali','Bengali','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (52,0,'Betawi creole','Betawi creole','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (53,0,'Beti-Pahuin','Beti-Pahuin','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (54,0,'Bhili','Bhili','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (55,0,'Bhojpuri','Bhojpuri','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (56,0,'Bikol','Bikol','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (57,0,'Bini','Bini','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (58,0,'Brahui','Brahui','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (59,0,'Buginese','Buginese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (60,0,'Bulgarian','Bulgarian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (61,0,'Burmese','Burmese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (62,0,'Buyei','Buyei','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (63,0,'Catalan','Catalan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (64,0,'Cebuano','Cebuano','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (65,0,'Central Aymara','Central Aymara','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (66,0,'Chechen','Chechen','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (67,0,'Chichewa (Nyanja)','Chichewa (Nyanja)','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (68,0,'Chiga','Chiga','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (69,0,'Chin','Chin','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (70,0,'Chinese - Cantonese','Chinese - Cantonese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (71,0,'Chinese - Gan','Chinese - Gan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (72,0,'Chinese - Hakka','Chinese - Hakka','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (73,0,'Chinese - Mainland China','简体中文','mdr-cn',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (74,0,'Chinese - Min','Chinese - Min','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (75,0,'Chinese - Wu','Chinese - Wu','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (76,0,'Chokwe','Chokwe','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (77,0,'Chuvash','Chuvash','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (78,0,'Czech','čeština','cs',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (79,0,'Dagaare','Dagaare','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (80,0,'Dagbani','Dagbani','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (81,0,'dansk','dansk','da',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (82,0,'Dinka','Dinka','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (83,0,'Dogri','Dogri','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (84,0,'Dong','Dong','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (85,0,'Ebira','Ebira','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (86,0,'English - South Africa','English - South Africa','en-sa',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (87,0,'Estonian','Estonian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (88,0,'Ewe','Ewe','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (89,0,'Filipino','Filipino','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (90,0,'Finnish','Finnish','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (91,0,'Fon','Fon','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (92,0,'French - France','français - France','fr',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (93,0,'French - Canada','français - Canada','fr-ca',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (94,0,'Fula','Fula','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (95,0,'Galician','Galician','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (96,0,'Garhwali','Garhwali','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (97,0,'Gbaya','Gbaya','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (98,0,'Georgian','Georgian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (99,0,'German (Austrian)','German (Austrian)','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (100,0,'German (Swiss)','German (Swiss)','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (101,0,'Gikuyu','Gikuyu','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (102,0,'Gilaki','Gilaki','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (103,0,'Gogo','Gogo','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (104,0,'Gondi','Gondi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (105,0,'Guarani','Guarani','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (106,0,'Gujarati','Gujarati','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (107,0,'Gujari','Gujari','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (108,0,'Gusii','Gusii','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (109,0,'Gwari','Gwari','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (110,0,'Haitian Creole','Haitian Creole','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (111,0,'Hausa','Hausa','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (112,0,'Haya','Haya','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (113,0,'Hebrew','עִבְרִית','he',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (114,0,'Hiligaynon','Hiligaynon','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (115,0,'Hindko','Hindko','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (116,0,'Hindustani','Hindustani','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (117,0,'Hmong','Hmong','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (118,0,'Ho','Ho','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (119,0,'Hungarian','Hungarian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (120,0,'Ibibio-Efik','Ibibio-Efik','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (121,0,'Igbo','Igbo','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (122,0,'Ijaw (Izon)','Ijaw (Izon)','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (123,0,'Ilokano','Ilokano','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (124,0,'Indian Sign Language','Indian Sign Language','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (125,0,'Bahasa Indonesia','Bahasa Indonesia','id',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (126,0,'Irish','Irish','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (127,0,'italiano','italiano','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (128,0,'Iu Mien','Iu Mien','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (129,0,'Jamaican Creole','Jamaican Creole','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (130,0,'Javanese','Javanese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (131,0,'Jula','Jula','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (132,0,'Kabardian','Kabardian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (133,0,'Kalenjin','Kalenjin','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (134,0,'Kamba','Kamba','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (135,0,'Kannada','Kannada','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (136,0,'Kanuri','Kanuri','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (137,0,'Kapampangan','Kapampangan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (138,0,'Karen','Karen','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (139,0,'Kashmiri','Kashmiri','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (140,0,'Kazakh','Kazakh','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (141,0,'Khandesi','Khandesi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (142,0,'Khmer','Khmer','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (143,0,'K\'iche\'','K\'iche\'','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (144,0,'Kimbundu','Kimbundu','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (145,0,'Kinaray-a','Kinaray-a','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (146,0,'Kinyarwanda','Kinyarwanda','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (147,0,'Kirundi','Kirundi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (148,0,'Koli','Koli','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (149,0,'Kongo','Kongo','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (150,0,'Konkani','Konkani','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (151,0,'Korean','Korean','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (152,0,'Kumauni','Kumauni','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (153,0,'Kurdish','Kurdish','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (154,0,'Kurux','Kurux','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (155,0,'Kyrgyz','Kyrgyz','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (156,0,'Lampung','Lampung','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (157,0,'Lao','Lao','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (158,0,'Latvian','Latvian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (159,0,'Ligurian','Ligurian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (160,0,'Lingala','Lingala','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (161,0,'Lithuanian','Lithuanian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (162,0,'Lombard','Lombard','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (163,0,'Luganda','Luganda','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (164,0,'Lugbara','Lugbara','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (165,0,'Luo (Dholuo)','Luo (Dholuo)','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (166,0,'Luri','Luri','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (167,0,'Lusoga','Lusoga','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (168,0,'Luyia','Luyia','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (169,0,'Macedonian','Macedonian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (170,0,'Madurese','Madurese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (171,0,'Magindanaw','Magindanaw','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (172,0,'Maithili','Maithili','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (173,0,'Makasar','Makasar','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (174,0,'Makhuwa','Makhuwa','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (175,0,'Makonde','Makonde','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (176,0,'Malagasy','Malagasy','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (177,0,'Malay','Malay','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (178,0,'Malayalam','Malayalam','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (179,0,'Malvi','Malvi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (180,0,'Mandinka','Mandinka','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (181,0,'Maninka','Maninka','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (182,0,'Maranao','Maranao','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (183,0,'Marathi','Marathi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (184,0,'Mazanderani','Mazanderani','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (185,0,'Meithei','Meithei','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (186,0,'Mende','Mende','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (187,0,'Meru','Meru','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (188,0,'Minangkabau','Minangkabau','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (189,0,'Mongolian','Mongolian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (190,0,'More','More','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (191,0,'Mundari','Mundari','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (192,0,'Naga','Naga','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (193,0,'Nahuatl','Nahuatl','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (194,0,'Ndebele','Ndebele','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (195,0,'Neapolitan','Neapolitan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (196,0,'Nepali','Nepali','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (197,0,'Northern Sotho (sePedi)','Northern Sotho (sePedi)','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (198,0,'Norwegian','Norwegian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (199,0,'Nyakyusa','Nyakyusa','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (200,0,'Nyamwezi','Nyamwezi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (201,0,'Nyankore','Nyankore','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (202,0,'Occitan','Occitan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (203,0,'Ometo','Ometo','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (204,0,'Oriya','Oriya','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (205,0,'Oromo','Oromo','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (206,0,'Pahari-Potwari','Pahari-Potwari','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (207,0,'Pangasinan','Pangasinan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (208,0,'Pashto','Pashto','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (209,0,'Persian','Persian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (210,0,'Piemonteis','Piemonteis','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (211,0,'Polish','polski','pl',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (212,0,'Portuguese','português','pt',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (213,0,'Portuguese - Brasil','português - Brasil','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (214,0,'Punjabi','Punjabi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (215,0,'Quechua','Quechua','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (216,0,'Qusqu-Qullaw','Qusqu-Qullaw','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (217,0,'Rajbangsi','Rajbangsi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (218,0,'Rejang','Rejang','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (219,0,'Romani','Romani','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (220,0,'Romanian','română','ro',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (221,0,'Ryu-kyu-','Ryu-kyu-','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (222,0,'Santali','Santali','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (223,0,'Sara','Sara','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (224,0,'Sardinian','Sardinian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (225,0,'Sasak','Sasak','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (226,0,'Scots','Scots','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (227,0,'Sena','Sena','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (228,0,'Senoufo','Senoufo','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (229,0,'Serbo-Croatian','Serbo-Croatian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (230,0,'Serer','Serer','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (231,0,'Sesotho (southern)','Sesotho (southern)','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (232,0,'Shan','Shan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (233,0,'Shona','Shona','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (234,0,'Sidamo','Sidamo','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (235,0,'Sindhi','Sindhi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (236,0,'Sinhalese','Sinhalese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (237,0,'SiSwati','SiSwati','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (238,0,'Slovak','slovenčina','sk',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (239,0,'Slovene','Slovene','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (240,0,'Somali','Somali','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (241,0,'Songe','Songe','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (242,0,'Soninke','Soninke','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (243,0,'South Bolivian Quechua','South Bolivian Quechua','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (244,0,'Southern Quechua','Southern Quechua','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (246,0,'Spanish - Latin America','español - Latinoamérica','es',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (247,0,'Sukuma','Sukuma','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (248,0,'Sundanese','Sundanese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (249,0,'Susu','Susu','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (250,0,'Swahili','Swahili','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (251,0,'Swedish','Swedish','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (252,0,'Tajik','Tajik','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (253,0,'Tamang','Tamang','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (254,0,'Tamazight (Berber)','Tamazight (Berber)','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (255,0,'Tamil','Tamil','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (256,0,'Tatar','Tatar','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (257,0,'Tausug','Tausug','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (258,0,'Tày','Tày','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (259,0,'Telugu','Telugu','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (260,0,'Temne','Temne','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (261,0,'Teso','Teso','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (262,0,'Thai','Thai','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (263,0,'Tharu','Tharu','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (264,0,'Tibetan','Tibetan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (265,0,'Tibetan','Tibetan','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (266,0,'Tigrinya','Tigrinya','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (267,0,'Tiv','Tiv','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (268,0,'Tonga','Tonga','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (269,0,'Tshiluba','Tshiluba','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (270,0,'Tsonga','Tsonga','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (271,0,'Tswana','Tswana','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (272,0,'Tuareg','Tuareg','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (273,0,'Tulu','Tulu','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (274,0,'Tumbuka','Tumbuka','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (275,0,'Turkish','Turkish','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (276,0,'Turkmen','Turkmen','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (277,0,'Ukrainian','Ukrainian','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (278,0,'Umbundu','Umbundu','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (279,0,'Uyghur','Uyghur','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (280,0,'Uzbek','Uzbek','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (281,0,'Venda','Venda','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (282,0,'Vietnamese','Vietnamese','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (283,0,'Vlax Romani','Vlax Romani','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (284,0,'Walloon','Walloon','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (285,0,'Waray-Waray','Waray-Waray','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (286,0,'Wolof','Wolof','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (287,0,'Xhosa','Xhosa','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (288,0,'Yao','Yao','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (289,0,'Yi','Yi','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (290,0,'Yiddish','Yiddish','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (291,0,'Yoruba','Yoruba','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (292,0,'Zarma','Zarma','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (293,0,'Zazaki','Zazaki','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (294,0,'Zhuang','Zhuang','',0);
INSERT INTO `languages` (`languageId`,`rtl`,`name`,`nativeName`,`locale`,`enabled`) VALUES (295,0,'Zulu','Zulu','',0);

/*
-- Query: SELECT * FROM appreciedb.interests
LIMIT 0, 1000

-- Date: 2015-01-15 14:23
*/
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (1,'Apparel and Accessories',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (2,'Art',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (3,'Bespoke Services',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (4,'Boats and Aviation',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (5,'Education',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (6,'Electronics and Gadgetry',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (7,'Entrepreneurship',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (8,'Family',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (9,'Fine Living (Drinking and Dining)',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (10,'Health and Beauty',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (11,'Home',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (12,'Jewellery',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (13,'Motoring and Cycling',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (14,'Music, Culture, and Intellect',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (15,'Philanthropy',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (16,'Professional Services',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (17,'Professional Sports',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (18,'Property and Real Estate',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (19,'Recreational Sports/Adventure',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (20,'Retail',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (21,'Sustainable/Ethical Luxury',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (22,'Travel and Special Hotels',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (23,'Watches',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (24,'Wildlife, Pets, and Animals',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (25,'Other',1);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (34,'Wallets and Leather Goods',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (35,'Accessories',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (36,'Purses and Handbags',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (37,'Shoes',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (38,'Men\'s Fashion',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (39,'Ladies\' Fashion',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (40,'Bespoke Tailoring',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (41,'Paintings and Drawings',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (42,'Sculptures and Carvings',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (43,'Modern',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (44,'Contemporary',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (45,'Museum/Gallery',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (46,'Photography',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (47,'Antiques',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (48,'Collectibles',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (56,'Security',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (57,'Removals and Relocations',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (58,'Lifestyle',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (59,'Concierge and Chauffeuring',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (60,'Personal Shopping',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (61,'Travel',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (63,'Boat Racing',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (64,'Air Racing',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (65,'Boats and Watercraft',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (66,'Aircraft and Aviation',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (67,'Private Charters',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (68,'Educational Consultancy',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (69,'Tutoring',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (70,'Experiences and Courses',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (71,'Books/Literature',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (72,'Computers and Tablets',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (73,'Technology/Gadgetry',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (74,'Photography and Cameras',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (75,'Mobile Communications and Phones',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (76,'Audio/Visual',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (77,'Car Electronics and GPS',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (78,'Video Games & Consoles',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (79,'Business Development',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (80,'Startups',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (81,'Investment and Evangelism',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (89,'Child-Friendly',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (90,'Children/Baby Toys and Goods',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (91,'Family Memories',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (92,'Genealogy',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (93,'Childcare',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (94,'Family Fun',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (95,'Restaurants/Fine Dining',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (96,'Wine',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (97,'Spirits',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (98,'Cigars',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (99,'Private Members\' Clubs',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (100,'Nightclubs and Bars',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (101,'Medical and Health Care',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (102,'Salon and Spa',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (103,'Skin and Body Care',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (104,'Hair and Makeup',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (105,'Fitness and Gym',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (106,'Colognes and Perfumery',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (114,'Decor',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (115,'Furniture',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (116,'Interior Design',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (117,'Garden and Outdoor',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (118,'Kitchen, Cooking, and Dining',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (119,'Vintage/Antique Jewellery',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (120,'Rings, Necklaces, and Bracelets',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (121,'Gems and Jewels',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (122,'Engagement and Wedding',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (123,'Fashion Jewellery',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (124,'Men\'s Jewellery',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (125,'Fine Jewellery',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (126,'Motor Racing',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (127,'Driving Days',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (128,'Cycling',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (129,'Driving Accessories',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (130,'Luxury Cars',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (131,'Sports Cars',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (132,'Motorcycles',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (141,'Music/Concert/Festivals',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (142,'Film/Media',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (143,'Historical/Heritage',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (144,'Theatre/Performing Arts',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (145,'Dance',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (146,'Exhibitions',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (147,'Local Charity',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (148,'International Charity',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (149,'Fundraising and Giving',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (150,'Humanitarian Aid',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (151,'Opportunities',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (157,'Accounting',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (158,'Legal',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (159,'Insurance',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (160,'Tax',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (171,'Football',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (172,'Horse Racing',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (173,'Water Sports',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (174,'Rugby',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (175,'Tennis',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (176,'Golf',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (177,'Winter Sports',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (178,'Extreme Sports',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (179,'Shooting',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (186,'UK and Ireland',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (187,'International',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (188,'Commercial',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (189,'Residential',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (190,'Architecture',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (199,'Adventuring Holidays',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (200,'Fishing',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (201,'Gambling',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (202,'Sportswear and Equipment',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (206,'Auctions',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (207,'Shopping Experience',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (208,'Fashion',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (217,'Special Hotels',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (218,'Luxury Holidays',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (219,'Luggage/Travel Accessories',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (220,'Cruises/Boating',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (221,'Adventure',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (222,'Snow',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (223,'Weddings and Celebrations',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (224,'Men\'s Watches',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (225,'Ladies\' Watches',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (226,'Sports Watches',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (227,'Luxury Watches',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (228,'Pocket Watches',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (229,'Wrist Watches',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (230,'Safari and Wildlife Tours',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (231,'Pet Care and Supplies',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (232,'Exotic Animals',0);
INSERT INTO `interests` (`interestId`,`interest`,`isTop`) VALUES (233,'Wildlife Support',0);

/*
-- Query: SELECT * FROM appreciedb.intereststree
LIMIT 0, 1000

-- Date: 2015-01-15 14:24
*/
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'1');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'10');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'11');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'12');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'13');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'14');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'15');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'16');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'18');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'19');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'2');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'20');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'21');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'22');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'23');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'24');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'3');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'4');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'6');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'7');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'8');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (25,'9');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (34,'1');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (35,'1');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (36,'1');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (37,'1');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (38,'1');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (39,'1');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (40,'1');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (40,'3');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (41,'2');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (42,'2');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (43,'2');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (44,'2');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (45,'14');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (45,'2');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (46,'2');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (46,'8');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (47,'11');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (47,'2');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (48,'2');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (56,'3');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (57,'3');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (58,'3');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (59,'3');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (60,'20');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (60,'3');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (61,'22');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (61,'3');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (63,'4');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (64,'4');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (65,'4');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (66,'4');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (67,'4');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (68,'5');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (69,'5');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (70,'5');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (71,'14');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (71,'25');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (71,'5');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (72,'6');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (73,'11');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (73,'6');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (74,'6');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (75,'6');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (76,'6');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (77,'6');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (78,'6');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (79,'7');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (80,'7');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (81,'7');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (89,'8');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (90,'8');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (91,'8');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (92,'8');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (93,'8');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (94,'8');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (95,'9');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (96,'9');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (97,'9');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (98,'9');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (99,'9');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (100,'9');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (101,'10');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (101,'16');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (102,'10');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (103,'10');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (104,'10');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (105,'10');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (106,'10');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (114,'11');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (115,'11');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (116,'11');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (116,'18');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (117,'11');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (118,'11');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (119,'12');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (120,'12');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (121,'12');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (122,'12');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (123,'12');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (124,'12');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (125,'12');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (126,'13');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (126,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (127,'13');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (127,'19');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (128,'13');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (129,'13');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (130,'13');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (131,'13');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (132,'13');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (141,'14');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (142,'14');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (143,'14');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (144,'14');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (145,'14');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (146,'14');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (147,'15');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (148,'15');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (149,'15');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (150,'15');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (151,'15');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (157,'16');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (158,'16');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (159,'16');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (160,'16');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (171,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (172,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (173,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (173,'19');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (174,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (175,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (176,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (176,'19');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (177,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (177,'19');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (178,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (179,'17');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (186,'18');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (187,'18');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (188,'18');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (189,'18');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (190,'18');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (199,'19');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (200,'19');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (201,'19');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (202,'19');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (206,'20');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (207,'20');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (208,'21');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (217,'22');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (218,'22');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (219,'22');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (220,'22');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (221,'22');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (222,'22');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (223,'22');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (224,'23');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (225,'23');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (226,'23');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (227,'23');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (228,'23');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (229,'23');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (230,'24');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (231,'24');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (232,'24');
INSERT INTO `intereststree` (`interestId`,`parentInterestId`) VALUES (233,'24');

/*
-- Query: SELECT * FROM appreciedb.goals
LIMIT 0, 1000

-- Date: 2015-01-15 14:23
*/
INSERT INTO `goals` (`goalId`,`label`) VALUES (1,'Product Launch');
INSERT INTO `goals` (`goalId`,`label`) VALUES (2,'Charity');
INSERT INTO `goals` (`goalId`,`label`) VALUES (3,'Education');
INSERT INTO `goals` (`goalId`,`label`) VALUES (4,'General Company Promotion');
INSERT INTO `goals` (`goalId`,`label`) VALUES (5,'Networking');
INSERT INTO `goals` (`goalId`,`label`) VALUES (6,'Investment Related');
INSERT INTO `goals` (`goalId`,`label`) VALUES (7,'Passive Event (party/talk/performance)');
INSERT INTO `goals` (`goalId`,`label`) VALUES (8,'Active Event (adventure/activity/doing)');


/*
-- Query: SELECT * FROM appreciedb.dietaryrequirements
LIMIT 0, 1000

-- Date: 2015-01-15 14:23
*/
INSERT INTO `dietaryrequirements` (`requirementId`,`requirement`) VALUES (10,'Halal');
INSERT INTO `dietaryrequirements` (`requirementId`,`requirement`) VALUES (11,'Kosher');
INSERT INTO `dietaryrequirements` (`requirementId`,`requirement`) VALUES (12,'No Alcohol');
INSERT INTO `dietaryrequirements` (`requirementId`,`requirement`) VALUES (13,'Nut Allergies');
INSERT INTO `dietaryrequirements` (`requirementId`,`requirement`) VALUES (14,'No Seafood');
INSERT INTO `dietaryrequirements` (`requirementId`,`requirement`) VALUES (15,'Vegetarian');
INSERT INTO `dietaryrequirements` (`requirementId`,`requirement`) VALUES (16,'Vegan');
INSERT INTO `dietaryrequirements` (`requirementId`,`requirement`) VALUES (17,'No Gluten');
INSERT INTO `dietaryrequirements` (`requirementId`,`requirement`) VALUES (18,'No Dairy or Lactose');

/*
-- Query: SELECT * FROM appreciedb.roles
LIMIT 0, 1000

-- Date: 2015-01-15 14:21
*/
INSERT INTO `roles` (`roleId`,`name`,`description`,`defaultController`,`defaultAction`) VALUES (1,'PortalAdministrator','Portal Administrator','dashboard','PortalAdministrator');
INSERT INTO `roles` (`roleId`,`name`,`description`,`defaultController`,`defaultAction`) VALUES (11,'Manager','Manager','vault','index');
INSERT INTO `roles` (`roleId`,`name`,`description`,`defaultController`,`defaultAction`) VALUES (21,'ApprecieSupplier','Apprecie Supplier','vault','index');
INSERT INTO `roles` (`roleId`,`name`,`description`,`defaultController`,`defaultAction`) VALUES (31,'Internal','Internal Member','vault','index');
INSERT INTO `roles` (`roleId`,`name`,`description`,`defaultController`,`defaultAction`) VALUES (41,'SystemAdministrator','System Administrator','dashboard','SystemAdministrator');
INSERT INTO `roles` (`roleId`,`name`,`description`,`defaultController`,`defaultAction`) VALUES (51,'Client','Client','vault','index');
INSERT INTO `roles` (`roleId`,`name`,`description`,`defaultController`,`defaultAction`) VALUES (61,'AffiliatedSupplier','Affiliated Supplier','vault','index');
INSERT INTO `roles` (`roleId`,`name`,`description`,`defaultController`,`defaultAction`) VALUES (71,'Contact','Contact',NULL,NULL);

/*
-- Query: SELECT * FROM appreciedb.currencies
LIMIT 0, 1000

-- Date: 2015-01-15 14:21
*/
INSERT INTO `currencies` (`currencyId`,`alphabeticCode`,`currency`,`enabled`,`symbol`) VALUES (1,'GBP','Pound Sterling',1,'£');
INSERT INTO `currencies` (`currencyId`,`alphabeticCode`,`currency`,`enabled`,`symbol`) VALUES (2,'USD','US Dollar',1,'$');
INSERT INTO `currencies` (`currencyId`,`alphabeticCode`,`currency`,`enabled`,`symbol`) VALUES (3,'EUR','Euro',1,'€');





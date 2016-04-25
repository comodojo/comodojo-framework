-- -----------------------------------------------------
-- Schema comodojo
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `comodojo` DEFAULT CHARACTER SET latin1 ;
USE `comodojo` ;

-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_packages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_packages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `package` VARCHAR(256) NOT NULL,
  `version` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `package_unique` (`package` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_applications`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_applications` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `package` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `applicationpackage_idx` (`package` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  CONSTRAINT `applicationpackage`
    FOREIGN KEY (`package`)
    REFERENCES `comodojo`.`cmdj_packages` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_roles` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `landingapp` INT(10) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `landingapp_idx` (`landingapp` ASC),
  CONSTRAINT `landingapp`
    FOREIGN KEY (`landingapp`)
    REFERENCES `comodojo`.`cmdj_applications` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_applications_to_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_applications_to_roles` (
  `application` INT(10) UNSIGNED NOT NULL,
  `role` INT(10) UNSIGNED NOT NULL,
  UNIQUE INDEX `approle` (`application` ASC, `role` ASC),
  INDEX `role_idx` (`role` ASC),
  CONSTRAINT `atr_application`
    FOREIGN KEY (`application`)
    REFERENCES `comodojo`.`cmdj_applications` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `atr_role`
    FOREIGN KEY (`role`)
    REFERENCES `comodojo`.`cmdj_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_authentication`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_authentication` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `class` VARCHAR(256) NOT NULL,
  `parameters` TEXT NULL DEFAULT NULL,
  `package` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `authenticationpackage_idx` (`package` ASC),
  CONSTRAINT `authenticationpackage`
    FOREIGN KEY (`package`)
    REFERENCES `comodojo`.`cmdj_packages` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_commands`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_commands` (
  `id` INT(11) NOT NULL,
  `command` VARCHAR(256) NOT NULL,
  `class` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `aliases` TEXT NOT NULL,
  `options` TEXT NOT NULL,
  `arguments` TEXT NOT NULL,
  `package` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `command_UNIQUE` (`command` ASC),
  INDEX `commandpackage_idx` (`package` ASC),
  CONSTRAINT `commandpackage`
    FOREIGN KEY (`package`)
    REFERENCES `comodojo`.`cmdj_packages` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_jobs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_jobs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `task` VARCHAR(128) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `enabled` TINYINT(1) NULL DEFAULT '0',
  `min` VARCHAR(16) NULL DEFAULT NULL,
  `hour` VARCHAR(16) NULL DEFAULT NULL,
  `dayofmonth` VARCHAR(16) NULL DEFAULT NULL,
  `month` VARCHAR(16) NULL DEFAULT NULL,
  `dayofweek` VARCHAR(16) NULL DEFAULT NULL,
  `year` VARCHAR(16) NULL DEFAULT NULL,
  `params` TEXT NULL DEFAULT NULL,
  `lastrun` INT(64) NULL DEFAULT NULL,
  `firstrun` INT(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_plugins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_plugins` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `class` VARCHAR(256) NOT NULL,
  `method` VARCHAR(256) NULL DEFAULT NULL,
  `event` VARCHAR(256) NOT NULL,
  `framework` VARCHAR(16) NOT NULL,
  `package` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `plugin` (`name` ASC, `framework` ASC),
  INDEX `pluginpackage_idx` (`package` ASC),
  CONSTRAINT `pluginpackage`
    FOREIGN KEY (`package`)
    REFERENCES `comodojo`.`cmdj_packages` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_routes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_routes` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `route` VARCHAR(256) NOT NULL,
  `type` VARCHAR(16) NOT NULL DEFAULT 'ROUTE',
  `class` VARCHAR(256) NOT NULL,
  `parameters` TEXT NULL DEFAULT NULL,
  `application` INT(10) UNSIGNED NULL DEFAULT NULL,
  `package` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `route_UNIQUE` (`route` ASC),
  INDEX `application` (`application` ASC),
  INDEX `routepackage_idx` (`package` ASC),
  CONSTRAINT `application`
    FOREIGN KEY (`application`)
    REFERENCES `comodojo`.`cmdj_applications` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `routepackage`
    FOREIGN KEY (`package`)
    REFERENCES `comodojo`.`cmdj_packages` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_rpc`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_rpc` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `callback` VARCHAR(256) NOT NULL,
  `method` VARCHAR(256) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `signatures` TEXT NOT NULL,
  `package` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `rpcpackage_idx` (`package` ASC),
  CONSTRAINT `rpcpackage`
    FOREIGN KEY (`package`)
    REFERENCES `comodojo`.`cmdj_packages` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_settings` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `value` TEXT NULL DEFAULT NULL,
  `constant` TINYINT(1) NULL DEFAULT '0',
  `type` VARCHAR(16) NOT NULL DEFAULT 'STRING',
  `validation` TEXT NULL DEFAULT NULL,
  `package` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `settingpackage_idx` (`package` ASC),
  CONSTRAINT `settingpackage`
    FOREIGN KEY (`package`)
    REFERENCES `comodojo`.`cmdj_packages` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_tasks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_tasks` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `class` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `package` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `taskpackage_idx` (`package` ASC),
  CONSTRAINT `taskpackage`
    FOREIGN KEY (`package`)
    REFERENCES `comodojo`.`cmdj_packages` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_themes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_themes` (
  `id` INT(11) NOT NULL,
  `name` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `package` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `themepackage_idx` (`package` ASC),
  CONSTRAINT `themepackage`
    FOREIGN KEY (`package`)
    REFERENCES `comodojo`.`cmdj_packages` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_users` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(128) NOT NULL,
  `password` VARCHAR(128) NOT NULL,
  `displayname` VARCHAR(256) NOT NULL,
  `mail` VARCHAR(256) NOT NULL,
  `birthdate` DATE NULL DEFAULT NULL,
  `gender` VARCHAR(1) NULL DEFAULT NULL,
  `enabled` TINYINT(1) NULL DEFAULT '0',
  `authentication` INT(10) UNSIGNED NOT NULL,
  `primaryrole` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `mail_UNIQUE` (`mail` ASC),
  INDEX `authentication_idx` (`authentication` ASC),
  INDEX `primaryrole_idx` (`primaryrole` ASC),
  CONSTRAINT `authentication`
    FOREIGN KEY (`authentication`)
    REFERENCES `comodojo`.`cmdj_authentication` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `primaryrole`
    FOREIGN KEY (`primaryrole`)
    REFERENCES `comodojo`.`cmdj_roles` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_users_to_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_users_to_roles` (
  `user` INT(10) UNSIGNED NOT NULL,
  `role` INT(10) UNSIGNED NOT NULL,
  UNIQUE INDEX `userrole` (`user` ASC, `role` ASC),
  INDEX `role_idx` (`role` ASC),
  CONSTRAINT `utr_role`
    FOREIGN KEY (`role`)
    REFERENCES `comodojo`.`cmdj_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `utr_user`
    FOREIGN KEY (`user`)
    REFERENCES `comodojo`.`cmdj_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `comodojo`.`cmdj_worklogs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comodojo`.`cmdj_worklogs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT(10) UNSIGNED NULL DEFAULT NULL,
  `jobid` INT(10) UNSIGNED NULL DEFAULT NULL,
  `name` VARCHAR(128) NOT NULL,
  `task` VARCHAR(128) NOT NULL,
  `status` VARCHAR(12) NOT NULL,
  `success` TINYINT(1) NULL DEFAULT '0',
  `result` TEXT NULL DEFAULT NULL,
  `start` VARCHAR(128) NOT NULL,
  `end` VARCHAR(128) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

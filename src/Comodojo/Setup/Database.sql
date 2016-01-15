-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`comodojo_settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `value` TEXT NULL DEFAULT NULL,
  `package` VARCHAR(256) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_apps`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_apps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `package` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `landingapp` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `landingapp_idx` (`landingapp` ASC),
  CONSTRAINT `landingapp`
    FOREIGN KEY (`landingapp`)
    REFERENCES `mydb`.`comodojo_apps` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_authentication`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_authentication` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `class` VARCHAR(256) NOT NULL,
  `parameters` TEXT NULL DEFAULT NULL,
  `package` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(128) NOT NULL,
  `password` VARCHAR(128) NOT NULL,
  `displayname` VARCHAR(256) NOT NULL,
  `mail` VARCHAR(256) NOT NULL,
  `birthdate` DATE NULL DEFAULT NULL,
  `gender` VARCHAR(1) NULL DEFAULT NULL,
  `enabled` TINYINT(1) NULL DEFAULT 0,
  `authentication` INT UNSIGNED NULL DEFAULT NULL,
  `primaryrole` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `mail_UNIQUE` (`mail` ASC),
  INDEX `authentication_idx` (`authentication` ASC),
  INDEX `primaryrole_idx` (`primaryrole` ASC),
  CONSTRAINT `authentication`
    FOREIGN KEY (`authentication`)
    REFERENCES `mydb`.`comodojo_authentication` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `primaryrole`
    FOREIGN KEY (`primaryrole`)
    REFERENCES `mydb`.`comodojo_roles` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_users_to_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_users_to_roles` (
  `user` INT UNSIGNED NOT NULL,
  `role` INT UNSIGNED NOT NULL,
  UNIQUE INDEX `userrole` (`user` ASC, `role` ASC),
  INDEX `role_idx` (`role` ASC),
  CONSTRAINT `user`
    FOREIGN KEY (`user`)
    REFERENCES `mydb`.`comodojo_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `role`
    FOREIGN KEY (`role`)
    REFERENCES `mydb`.`comodojo_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_apps_to_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_apps_to_roles` (
  `app` INT UNSIGNED NOT NULL,
  `role` INT UNSIGNED NOT NULL,
  UNIQUE INDEX `approle` (`app` ASC, `role` ASC),
  INDEX `role_idx` (`role` ASC),
  CONSTRAINT `app`
    FOREIGN KEY (`app`)
    REFERENCES `mydb`.`comodojo_apps` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `role`
    FOREIGN KEY (`role`)
    REFERENCES `mydb`.`comodojo_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_routes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_routes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `route` VARCHAR(256) NOT NULL,
  `type` VARCHAR(16) NOT NULL DEFAULT 'ROUTE',
  `class` VARCHAR(256) NOT NULL,
  `parameters` TEXT NULL DEFAULT NULL,
  `package` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `route_UNIQUE` (`route` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_plugins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_plugins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `class` VARCHAR(256) NOT NULL,
  `method` VARCHAR(256) NULL DEFAULT NULL,
  `event` VARCHAR(256) NOT NULL,
  `framework` VARCHAR(16) NOT NULL,
  `package` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `plugin` (`name` ASC, `framework` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_themes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_themes` (
  `id` INT NOT NULL,
  `name` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `package` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_commands`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_commands` (
  `id` INT NOT NULL,
  `command` VARCHAR(256) NOT NULL,
  `class` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `aliases` TEXT NOT NULL,
  `options` TEXT NOT NULL,
  `arguments` TEXT NOT NULL,
  `package` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `command_UNIQUE` (`command` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_tasks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_tasks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `class` VARCHAR(256) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `package` VARCHAR(256) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`comodojo_rpc`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`comodojo_rpc` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(256) NOT NULL,
  `callback` VARCHAR(256) NOT NULL,
  `method` VARCHAR(256) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `signatures` TEXT NOT NULL,
  `package` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


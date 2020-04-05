CREATE TABLE `car-seller`.`brands` ( `id` INT NOT NULL AUTO_INCREMENT , `name` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `brands` CHANGE `name` `name` VARCHAR(20) NOT NULL;
CREATE TABLE `car-seller`.`models` ( `id` INT NOT NULL AUTO_INCREMENT , `brand` INT NOT NULL , `model` VARCHAR(30) NOT NULL , `year_from` YEAR NOT NULL , `year_to` YEAR NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `models` ADD CONSTRAINT `models_brands` FOREIGN KEY (`brand`) REFERENCES `brands`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
INSERT INTO `brands` (`id`, `name`) VALUES (NULL, 'Audi'), (NULL, 'BMW');
CREATE TABLE `car-seller`.`types` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(20) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `models` ADD `type` INT NOT NULL AFTER `brand`;
ALTER TABLE `models` ADD CONSTRAINT `models_types` FOREIGN KEY (`type`) REFERENCES `types`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
INSERT INTO `models` (`id`, `brand`, `type`, `model`, `year_from`, `year_to`) VALUES (NULL, '1', '2', 'A4 I (B5)', '1994', '2001')
CREATE TABLE `car-seller`.`cars` ( `id` INT NOT NULL AUTO_INCREMENT , `brand` INT NOT NULL , `model` INT NOT NULL , `engine` VARCHAR(20) NOT NULL , `clutch` VARCHAR(20) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `cars` CHANGE `model` `year` YEAR(11) NOT NULL;
ALTER TABLE `cars` CHANGE `brand` `model` INT(11) NOT NULL;
ALTER TABLE `cars` ADD CONSTRAINT `cars_models` FOREIGN KEY (`model`) REFERENCES `models`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

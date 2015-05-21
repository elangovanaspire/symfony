
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- user
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100),
    `email` VARCHAR(100),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- book
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `book`;

CREATE TABLE `book`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100),
    `isbn` VARCHAR(20),
    `author_id` INTEGER,
    PRIMARY KEY (`id`),
    INDEX `book_FI_1` (`author_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- author
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `author`;

CREATE TABLE `author`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(100),
    `last_name` VARCHAR(100),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- book_club_list
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `book_club_list`;

CREATE TABLE `book_club_list`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for a school reading list.',
    `group_leader` VARCHAR(100) NOT NULL COMMENT 'The name of the teacher in charge of summer reading.',
    `theme` VARCHAR(50) COMMENT 'The theme, if applicable, for the reading list.',
    `created_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM COMMENT='Reading list for a book club.';

-- ---------------------------------------------------------------------
-- book_x_list
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `book_x_list`;

CREATE TABLE `book_x_list`
(
    `book_id` INTEGER NOT NULL COMMENT 'Fkey to book.id',
    `book_club_list_id` INTEGER NOT NULL COMMENT 'Fkey to book_club_list.id',
    PRIMARY KEY (`book_id`,`book_club_list_id`),
    INDEX `book_x_list_FI_2` (`book_club_list_id`)
) ENGINE=MyISAM COMMENT='Cross-reference table for many-to-many relationship between book rows and book_club_list rows.';

-- ---------------------------------------------------------------------
-- task
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `task`;

CREATE TABLE `task`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(100),
    `tags` VARCHAR(20),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- product
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `product`;

CREATE TABLE `product`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100),
    `price` DECIMAL,
    `description` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;

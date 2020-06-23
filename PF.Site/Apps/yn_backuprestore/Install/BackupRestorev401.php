<?php
/**
 * User: huydnt
 * Date: 16/12/2016
 * Time: 10:13
 */

namespace Apps\yn_backuprestore\Install;

use Phpfox;

class BackupRestorev401
{
    public function process()
    {
        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynbackuprestore_backups') . "`(
            `backup_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(64) NOT NULL,
            `type` ENUM('automatic', 'manual') NOT NULL,
            `plugins_included` TEXT DEFAULT NULL,
            `themes_included` TEXT DEFAULT NULL,
            `uploads_included` TEXT DEFAULT NULL,
            `database_included` TEXT DEFAULT NULL,
            `maintenance_mode` TINYINT(1) NOT NULL DEFAULT '1',
            `lock_database` TINYINT(1) NOT NULL DEFAULT '1',
            `archive_format` VARCHAR(10) NOT NULL DEFAULT 'zip',
            `prefix` VARCHAR(64) NOT NULL DEFAULT 'backup',
            `size` DECIMAL(6,2),
            `creation_timestamp` INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`backup_id`),
            KEY `type` (`type`)
        ) ENGINE=InnoDB;");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynbackuprestore_schedules') . "`(
            `schedule_id` INT(11) NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(64) NOT NULL,
            `interval` VARCHAR(20) NOT NULL,
            `start_date` DATETIME NOT NULL,
            `plugins_included` TEXT DEFAULT NULL,
            `themes_included` TEXT DEFAULT NULL,
            `uploads_included` TEXT DEFAULT NULL,
            `database_included` TEXT DEFAULT NULL,
            `prefix` VARCHAR(64) NOT NULL DEFAULT 'backup',
            `archive_format` VARCHAR(10) NOT NULL DEFAULT 'zip',
            `maintenance_mode` TINYINT(1) NOT NULL DEFAULT '1',
            `lock_database` TINYINT(1) NOT NULL DEFAULT '1',
            `creation_timestamp` INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`schedule_id`)
        ) ENGINE=InnoDB;");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynbackuprestore_destination_maps') . "`(
            `map_id` INT(11) NOT NULL AUTO_INCREMENT,
            `parent_id` INT(11) NOT NULL,
            `parent_type` VARCHAR(64) NOT NULL,
            `destination_id` INT(11) NOT NULL,
            PRIMARY KEY (`map_id`)
        ) ENGINE=InnoDB;");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynbackuprestore_destinations') . "`(
            `destination_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(64) NOT NULL,
            `type_id` INT(11) UNSIGNED NOT NULL,
            `params` TEXT,
            PRIMARY KEY (`destination_id`),
            KEY `title` (`title`)
        ) ENGINE=InnoDB;");

        $this->database()->query("CREATE TABLE IF NOT EXISTS `" . Phpfox::getT('ynbackuprestore_destination_types') . "`(
            `type_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(64) NOT NULL,
            PRIMARY KEY (`type_id`)
        ) ENGINE=InnoDB;");

        $this->database()->query("INSERT IGNORE INTO `" . Phpfox::getT('ynbackuprestore_destination_types') . "`
            (`type_id`, `title`) VALUES
            ('2', 'Email'),
            ('3', 'FTP Server'),
            ('4', 'SFTP Server'),
            ('5', 'MySQL Database'),
            ('6', 'Amazon S3'),
            ('7', 'Dropbox'),
            ('8', 'Microsoft OneDrive'),
            ('9', 'Google Drive')"
        );
    }

    private function database()
    {
        return Phpfox::getLib('phpfox.database');
    }
}
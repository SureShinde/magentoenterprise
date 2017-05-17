<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_SeoGoogleSitemap
 */


$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amseogooglesitemap/sitemap')}` CHANGE COLUMN `max_items` `max_items` INT NOT NULL DEFAULT '0';
    ALTER TABLE `{$this->getTable('amseogooglesitemap/sitemap')}` ADD `navigation` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
    ALTER TABLE `{$this->getTable('amseogooglesitemap/sitemap')}` ADD `navigation_priority` VARCHAR (4) NOT NULL DEFAULT '';
    ALTER TABLE `{$this->getTable('amseogooglesitemap/sitemap')}` ADD `navigation_frequency` VARCHAR (16) NOT NULL DEFAULT '';
");

$this->endSetup();

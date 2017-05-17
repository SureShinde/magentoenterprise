<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AlsoViewed
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

/** @var $this Mage_Core_Model_Resource_Setup */
$this->startSetup();
$this->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('aw_alsoviewed/history')} (
        `session_id` CHAR(64) NOT NULL,
        `product_id` INT(20) UNSIGNED NOT NULL,
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`session_id`, `product_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS {$this->getTable('aw_alsoviewed/product')} (
        `product_id` INT(10) UNSIGNED NOT NULL,
        `link_product_id` INT(10) UNSIGNED NOT NULL,
        `views` INT(10) UNSIGNED NOT NULL DEFAULT '1',
        PRIMARY KEY (`product_id`, `link_product_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
");
$this->endSetup();
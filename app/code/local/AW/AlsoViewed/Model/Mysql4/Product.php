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

class AW_AlsoViewed_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_isPkAutoIncrement = false;

    /**
     * Resource initialization
     */
    protected function _construct() {
        $this->_init('aw_alsoviewed/product', 'product_id_link_product_id');
    }

    /**
     * Override parent save method
     * @param Mage_Core_Model_Abstract $object
     * @return AW_AlsoViewed_Model_Mysql4_Product
     */
    public function save(Mage_Core_Model_Abstract $object) {
        $db = $this->_getWriteAdapter();
        $sql = "INSERT INTO " . $db->quoteTableAs($this->getMainTable()) . " (product_id, link_product_id)
            VALUES (?, ?) ON DUPLICATE KEY UPDATE views = views + 1";

        $db->query($sql, array($object->getProductId(), $object->getLinkProductId()));

        return $this;
    }
}

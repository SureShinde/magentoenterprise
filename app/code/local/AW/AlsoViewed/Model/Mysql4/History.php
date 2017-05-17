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

class AW_AlsoViewed_Model_Mysql4_History extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_isPkAutoIncrement = false;

    /**
     * Resource initialization
     */
    protected function _construct() {
        $this->_init('aw_alsoviewed/history', 'session_id_product_id');
    }

    /**
     * Save history
     * @param Mage_Core_Model_Abstract $object
     * @return AW_AlsoViewed_Model_Mysql4_History
     */
    public function save(Mage_Core_Model_Abstract $object) {
        $this->_beforeSave($object);
        try {
            $db = $this->_getWriteAdapter();
            $db->insert($this->getMainTable(), $this->_prepareDataForSave($object));
        } catch (Zend_Db_Statement_Exception $e) {}

        $db->update($this->getMainTable(), array('created_at' => $object->getCreatedAt()), "session_id = '{$object->getSessionId()}'");
        $this->_afterSave($object);

        return $this;
    }

    /**
     * Clear history by session id
     * @param array $sessions
     * @return AW_AlsoViewed_Model_Mysql4_History
     */
    public function clearHistoryBySessions($sessions) {
        $this->_getWriteAdapter()->delete($this->getMainTable(), "session_id IN ('" . implode("','", $sessions). "')");

        return $this;
    }
}

<?php
/**
 * @category   CLS
 * @package    ProductCompare
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_ProductCompare_Model_Resource_Catalog_Product_Compare_Item_Collection extends Mage_Catalog_Model_Resource_Product_Compare_Item_Collection {

    /**
     * Get SQL for get record count
     *
     * @param Varien_Db_Select $select
     * @param bool $resetLeftJoins
     * @return Varien_Db_Select
     */
    protected function _getSelectCountSql($select = null, $resetLeftJoins = true)
    {
        $parentSelect = parent::_getSelectCountSql($select, $resetLeftJoins);
        $parentSelect->reset(Zend_Db_Select::GROUP);
        return $parentSelect;
    }

}
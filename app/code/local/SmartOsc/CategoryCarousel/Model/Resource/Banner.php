<?php

/**
 * Banner resource model
 *
 */
class SmartOsc_CategoryCarousel_Model_Resource_Banner extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        $this->_init('smartosc_categorycarousel/banner', 'entity_id');
    }
}

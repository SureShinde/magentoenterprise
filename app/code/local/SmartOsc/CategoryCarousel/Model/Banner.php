<?php

/**
 * Banner model
 *
 * @category    SmartOsc
 * @package     SmartOsc_CategoryCarousel
 * @author      Ultimate Module Creator
 */
class SmartOsc_CategoryCarousel_Model_Banner extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'smartosc_categorycarousel_banner';
    const CACHE_TAG = 'smartosc_categorycarousel_banner';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'smartosc_categorycarousel_banner';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'banner';

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('smartosc_categorycarousel/banner');
    }

    /**
     * before save banner
     *
     * @access protected
     * @return SmartOsc_CategoryCarousel_Model_Banner
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save banner relation
     *
     * @access public
     * @return SmartOsc_CategoryCarousel_Model_Banner
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}

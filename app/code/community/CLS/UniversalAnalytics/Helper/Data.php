<?php
/**
 * @category   CLS
 * @package    UniversalAnalytics
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_UniversalAnalytics_Helper_Data extends Mage_Core_Helper_Abstract
{
    const GA_TYPE_CLASSIC = 'classic';
    const GA_TYPE_UNIVERSAL = 'universal';

    protected $_currentPageName = null;

    /**
     * Get the name of the current page
     *
     * @return string
     */
    public function getCurrentPageName()
    {
        if (is_null($this->_currentPageName)) {
            $name = Mage::getSingleton('core/url')->escape($_SERVER['REQUEST_URI']);
            $name = rtrim(str_replace('index/','',$name), '/');
            $name = preg_replace('/\/\?.*/', '', $name);
            $this->_currentPageName = $name;
        }
        return $this->_currentPageName;
    }
}

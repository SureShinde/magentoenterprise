<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_Widgets_Helper_Block extends Mage_Core_Helper_Abstract
{
    const TYPE_WIDGET = 'widget';
    const TYPE_SLIDESHOW = 'slideshow';
    const TYPE_PRODUCT_CAROUSEL = 'product_carousel';

    protected $_registeredTypes = array();

    /**
     * Register the existence of a certain type of widget
     *
     * @param string $type
     */
    public function registerWidgetType($type)
    {
        $this->_registeredTypes[$type] = true;
    }

    /**
     * Determine if a type of widget has been registered on the page
     *
     * @param string $type
     * @return bool
     */
    public function widgetTypeIsRegistered($type)
    {
        return (isset($this->_registeredTypes[$type]));
    }
}
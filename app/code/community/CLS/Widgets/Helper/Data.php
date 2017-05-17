<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_Widgets_Helper_Data extends Mage_Core_Helper_Abstract
{
    const WIDGET_OPTION_ENTERPRISE = 'enterprise_only';

    /**
     * Get a unique, sequential index for all CLS widgets on a page
     *
     * @return int
     */
    public function getWidgetIndex()
    {
        $index = Mage::registry('cls_widget_index');
        if (is_null($index)) {
            $index = 0;
        } else {
            $index++;
        }
        Mage::unregister('cls_widget_index');
        Mage::register('cls_widget_index', $index);
        return $index;
    }
}
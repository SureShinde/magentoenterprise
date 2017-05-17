<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_Widgets_Model_Widget_Widget extends Mage_Widget_Model_Widget
{
    /**
     * Return filtered list of widgets as SimpleXml object
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return Varien_Simplexml_Element
     */
    public function getWidgetsXml($filters = array())
    {
        $isEnterprise = (Mage::getEdition() == Mage::EDITION_ENTERPRISE);

        $widgets = parent::getWidgetsXml($filters);
        if (!$isEnterprise) {
            $result = clone $widgets;
            foreach ($widgets as $code => $widget) {
                $value = $widget->getAttribute(CLS_Widgets_Helper_Data::WIDGET_OPTION_ENTERPRISE);
                if ((bool) $value) {
                    unset($result->{$code});
                }
            }
            return $result;
        } else {
            return $widgets;
        }
    }
}
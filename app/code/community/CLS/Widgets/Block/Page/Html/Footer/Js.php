<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */
class CLS_Widgets_Block_Page_Html_Footer_Js extends Mage_Page_Block_Html_Head
{
    /**
     * Ensure we don't try to access items if there are none
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        return (array_key_exists('items', $this->_data)) ? parent::getCssJsHtml() : '';
    }

    protected function _toHtml()
    {
        // If the appropriate type of widget was never registered, don't output anything
        if ($this->hasData('register_check_type') && !Mage::helper('cls_widgets/block')->widgetTypeIsRegistered($this->getRegisterCheckType())) {
            return '';
        }

        // For each item, if it's already in the head block, remove it
        $headBlock = $this->getLayout()->getBlock('head');
        $headBlockItems = $headBlock->getItems();
        $thisItems = $this->getItems();
        if (!is_null($thisItems)) {
            foreach ($this->getItems() as $id=>$item) {
                if (isset($headBlockItems[$id])) {
                    unset($this->_data['items'][$id]);
                }
            }
        }

        return parent::_toHtml();
    }
}
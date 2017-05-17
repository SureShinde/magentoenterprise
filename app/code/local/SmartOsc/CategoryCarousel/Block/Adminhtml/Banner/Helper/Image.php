<?php

/**
 * Banner image field renderer helper
 *
 */
class SmartOsc_CategoryCarousel_Block_Adminhtml_Banner_Helper_Image extends Varien_Data_Form_Element_Image
{
    /**
     * get the url of the image
     *
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::helper('smartosc_categorycarousel/banner_image')->getImageBaseUrl().
                $this->getValue();
        }
        return $url;
    }
}

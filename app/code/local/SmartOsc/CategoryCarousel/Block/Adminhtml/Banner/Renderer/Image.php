<?php

class SmartOsc_CategoryCarousel_Block_Adminhtml_Banner_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
  {
    if ($row->getImage())
      return sprintf('
                <a href="%s">%s</a>',
          Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/banner/image/' . $row->getImage(),
          '<img alt="" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/banner/image/' . $row->getImage() . '" width="150" />'
      );
  }
}
<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Cms' . DS . 'Block' . DS . 'WidgetController.php';

class CLS_Widgets_Adminhtml_Cms_Block_WidgetController extends Mage_Adminhtml_Cms_Block_WidgetController
{
    /**
     * Chooser Source action
     */
    public function multichooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $this->getLayout()->createBlock('cls_widgets/adminhtml_cms_block_widget_multichooser', '', array(
            'id' => $uniqId,
        ));
        $this->getResponse()->setBody($pagesGrid->toHtml());
    }
}

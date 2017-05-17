<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Ced
 * @package     Ced_Jet
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_Jet_Block_Adminhtml_Prod_Renderer_Upload extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row){
    	$ur = Mage::helper('adminhtml')->getUrl('*/');
		$zz = array();
		$zz = explode('index/index',$ur);

        $id=$row->getData('entity_id');
        $content='<a href="javascript: void(0);" id="upload_'.$id.'" onclick="uploadProduct(event,'.$id.','."'".$zz[0]."'".')">Upload</a>';
        $content.='<div class="saveall" id="manage_'.$id.'" ></div>';
        return $content;

    }
}
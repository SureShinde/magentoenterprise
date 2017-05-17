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

class Ced_Jet_Block_Adminhtml_Prod_Edit_Renderer_Price extends  Varien_Data_Form_Element_Abstract
{
    protected $_element;

    public function getElementHtml()
    {   
		$prices = $this->getValue();
		$html = "";
	    $html .= '<div class="grid"><table><thead><tr class="headings"><th>Fulfillment Node</th><th>Price</th></tr></thead>';
		$html .= '<tbody>';
		$i=0;
		foreach($prices as $price){
			
		$class="";;
		if($i++/2==0)
			$class="even";
			$html .= '<tr '.$class.'><td>'.$price->fulfillment_node_id.'</td><td>'.$price->fulfillment_node_price.'</td></tr>';
	   	}
		$html .= '</tbody>';
	    $html .='</table></div>';
		
		return $html;
    }
}
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

class Ced_Jet_Block_Adminhtml_Prod_Edit_Tab_Shippingform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('jet_shipping',array('legend'=>Mage::helper('jet')->__('Shipping Exception')));
        if(Mage::registry('shipping_data'))
        {

        }else{
                 $fieldset->setHeaderBar('<script type="text/javascript">function showshippingdiv(f){
                        container=document.getElementById(f);
                        container.style.display = "block";
                        var tagNames = ["INPUT", "SELECT", "TEXTAREA"];
                              for (var i = 0; i < tagNames.length; i++) {
                                var elems = container.getElementsByTagName(tagNames[i]);
                                for (var j = 0; j < elems.length; j++) {
                                  elems[j].disabled = false;
                                }
                        }
                  }</script><script type="text/javascript">document.addEventListener("DOMContentLoaded", 
                                    function(event) {
                                      container=document.getElementById("jet_shipping");
                                      container.style.display = "none";
                                        var tagNames = ["INPUT", "SELECT", "TEXTAREA"];
                                        for (var i = 0; i < tagNames.length; i++) {
                                          var elems = container.getElementsByTagName(tagNames[i]);
                                          for (var j = 0; j < elems.length; j++) {
                                            elems[j].disabled = true;
                                          }
                                        }
            });</script><button type="button" onclick="showshippingdiv(\'jet_shipping\');">Add Exception</button>');
        }

       

       /* $fieldset->addField('sku', 'text', array(
          'label'     => Mage::helper('jet')->__('Sku'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'sku',
        ));*/

        $fieldset->addField('shipping_carrier', 'select', array(
          'label'     => Mage::helper('jet')->__('Shipping Level'),
          'name'      => 'shipping_carrier',
          'values'    => Mage::helper('jet')->shippingCarrier(),
        ));
		
		/*
        $fieldset->addField('shipping_method', 'select', array(
          'label'     => Mage::helper('jet')->__('Shipping Method'),
          'name'      => 'shipping_method',
          'values'    => Mage::getSingleton('jet/shippingexception')->shippingMethod(),
        ));
		*/
		
		$fieldset->addField('shipping_method', 'text', array(
          'label'     => Mage::helper('jet')->__('Shipping Method'),
          'name'      => 'shipping_method',
		      'note'    => 'A specific shipping method e.g. UPS Ground, UPS Next Day Air, FedEx Home, Freight',			 
        ));

        $fieldset->addField('shipping_override', 'select', array(
          'label'     => Mage::helper('jet')->__('Override Type'),
          'class'     => 'required-entry validate-select',
          'required'  => true,
          'name'      => 'shipping_override',
          'values'    => Mage::helper('jet')->shippingOverride(),
        ));

        $fieldset->addField('shipping_charge', 'text', array(
          'label'     => Mage::helper('jet')->__('Shipping Charge Amount'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'shipping_charge',
        ));

        $fieldset->addField('shipping_excep', 'select', array(
          'label'     => Mage::helper('jet')->__('Shipping Exception'),
          'name'      => 'shipping_excep',
          'class'     => 'required-entry validate-select',
          'required'  => true,
          'values'    => Mage::helper('jet')->shippingExcep(),
        ));
		
        if(Mage::registry('shipping_data'))
        {
          $form->setValues(Mage::registry('shipping_data')->getData());
        }
        return parent::_prepareForm();
    }
}

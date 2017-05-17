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

class Ced_Jet_Block_Adminhtml_Refund_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
       $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('jet_Refund',array('legend'=>Mage::helper('jet')->__('Refund Information')));

        if($this->getRequest()->getParam('id') && Mage::registry('refund_data')){
              $saved_data="";
              $saved_data=Mage::registry('refund_data')->getData('saved_data');
               $saved_data=unserialize( $saved_data);
              $fieldset->addField('refund_id', 'text', array(
                'label'     => Mage::helper('jet')->__('Refund Id'),
                'name'      => 'refund_id',
                'readonly' => true,
                'value'=>Mage::registry('refund_data')->getData('refund_id'),
              ));
              $fieldset->addField('refund_merchantid', 'text', array(
                'label'     => Mage::helper('jet')->__('Refund Merchant Id'),
                'class'     => 'required-entry',
                'required'  => true,
                'readonly' => true,
                'name'      => 'refund_merchantid',
                'note' => 'Please fill Merchant Id.',
                'value'=>Mage::registry('refund_data')->getData('refund_merchantid'),
              ));
              $fieldset->addField('refund_orderid', 'text', array(
                'label'     => Mage::helper('jet')->__('Refund Order Id'),
                'class'     => 'required-entry',
                'required'  => true,
                'readonly' => true,
                'name'      => 'refund_orderid',
                'note' => 'Please fill Merchant Order Id to be refund.',
                'value'=>Mage::registry('refund_data')->getData('refund_orderid'),
              ));
              $i=0;
              foreach( $saved_data['sku_details'] as $details){
                 $fieldset1=$fieldset->addFieldset('sku_return_'.$i,array('legend'=>Mage::helper('jet')->__('sku : '.$details['merchant_sku'])));
                 $fieldset1->addField('order_item_id'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Order Item Id'),
                    'class'     => 'required-entry',
                    'required'  => true,
                    'readonly' => true,
                    'name'      => 'order_item_id',
                    'note' => 'Please fill Order Item Id for current order.',
                    'value'=>$details['order_item_id'],
                  ));

                  $fieldset1->addField('qty_requested'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Qty Requested'),
                    'class'     => 'validate-number',
                    'required'  => true,
                    'readonly' => true,
                    'name'      => 'qty_requested',
                    'note' => 'This is qty requested for current Order Item of current order.',
                    'value'=>$details['qty_requested'],
                  ));

                 $fieldset1->addField('qty_already_refunded'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Qty Already Refunded'),
                    'class'     => 'validate-number',
                    'required'  => true,
                    'readonly' => true,
                    'name'      => 'qty_already_refunded',
                    'note' => 'Qty already refunded for this Item for current order.',
                    'value'=>$details['qty_already_refunded'],
                  ));

                 $fieldset1->addField('available_to_refund_qty'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Qty Available for Refund'),
                    'class'     => 'validate-number',
                    'required'  => true,
                    'readonly' => true,
                    'name'      => 'available_to_refund_qty',
                    'note' => 'Qty available for Refund for this item.',
                    'value'=>$details['available_to_refund_qty'],
                  ));

                  $fieldset1->addField('qty_returned'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Qty Returned'),
                    'class'     => 'required-entry  validate-number',
                    'required'  => true,
                    'readonly' => true,
                    'name'      => 'qty_returned',
                    'note' => 'Please fill Quanitity of the given item that was cancelled.',
                    'value'=>$details['return_quantity'],
                  ));

                  $fieldset1->addField('refund_quantity'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Qty Refunded'),
                    'class'     => 'required-entry  validate-number',
                    'required'  => true,
                    'readonly' => true,
                    'name'      => 'refund_quantity',
                    'note' => 'Please fill Quanitity of the given item that the merchant wants to refund to the customer.',
                    'value'=>$details['refund_quantity'],
                  ));

                  $fieldset1->addField('refund_tax'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Refund Tax'),
                    //'class'     => 'required-entry',
                    //'required'  => true,
                    'name'      => 'refund_tax',
                    'readonly' => true,
                    'note' => 'Please fill the amount to be refunded for the given item in USD associated with tax that was charged for the item.',
                    'value'=>$details['return_tax'],
                  ));

                  $fieldset1->addField('refund_amount'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Refund Amount'),
                    'class'     => 'required-entry',
                    'required'  => true,
                    'readonly' => true,
                    'name'      => 'refund_amount',
                    'note' => 'Please fill the Amount to be refunded for the given item in USD associated with the item itself. This should be the total cost for this item not the unit cost.',
                    'value'=>$details['return_principal'],
                  ));

                  $fieldset1->addField('refund_shippingcost'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Refund Shipping Cost'),
                    'class'     => 'required-entry',
                    'required'  => true,
                    'readonly' => true,
                    'name'      => 'refund_shippingcost',
                    'note' => 'Please fill the amount to be refunded for the given item in USD associated with the shipping cost that was allocated to this item.',
                    'value'=>$details['return_shipping_cost'],
                  ));

                  $fieldset1->addField('refund_shippingtax'.$i, 'text', array(
                    'label'     => Mage::helper('jet')->__('Refund Shipping Tax'),
                    //'class'     => 'required-entry',
                    //'required'  => true,
                    'name'      => 'refund_shippingtax',
                    'readonly' => true,
                    'note' => 'Please fill the amount to be refunded for the given item in USD associated with the tax that was charged on shipping.',
                    'value'=>$details['return_shipping_tax'],
                  ));
                  $fieldset1->addField('refund_feedback'.$i, 'select', array(
                    'label'     => Mage::helper('jet')->__('Refund Feedback'),
                    'class'     => 'required-entry',
                    'required'  => true,
                    'name'      => 'refund_feedback',
                    'readonly' => true,
                    'values'    => Mage::helper('jet')->feedbackOptArray(),
                    'note' => 'Please fill the reason this refund is less than the full amount.',
                    'value'=>$details['return_refundfeedback'],
                  ));

                   $fieldset1->addField('refund_reason'.$i, 'select', array(
                    'label'     => Mage::helper('jet')->__('Refund Reason'),
                    'class'     => 'required-entry',
                    'required'  => true,
                    'name'      => 'refund_reason',
                    'readonly' => true,
                    'values'    => Mage::helper('jet')->refundreasonOptionArr(),
                    'note' => 'Please fill the reason the customer initiated the return.',
                    'value'=>$details['return_refundreason'],
                  ));
                 $i++;    
              }
        }else{
              /*$fieldset->addField('refund_merchantid', 'text', array(
                'label'     => Mage::helper('jet')->__('Refund Merchant Id'),
                'class'     => 'required-entry',
                'required'  => true,
                
                'name'      => 'refund_merchantid',
                'note' => 'Please fill Merchant Id.',
              ));*/
              $html4="";
              $html4='<script type="text/javascript">'.
                        'function checkamount(ele){'.
                          'var id=ele.id;'.
                          'var new_id=id.slice(12);'.
                          'var qty = document.getElementById("qty_refunded"+new_id).value;var avail_qty = document.getElementById("available_to_refund_qty"+new_id).value;'.
                          'if(qty != "n/a"){var amt= (document.getElementById("actual_amount"+new_id).value)*qty;} else{var amt=document.getElementById("actual_amount"+new_id).value;}'.
                          'if (ele.value == 0 || ele.value == "") {'.
                            'document.getElementById("amount"+new_id).value=0;'.
                            'ele.value=0;'.
                            'document.getElementById("return_refundfeedback"+new_id).disabled = true;'.
                            'document.getElementById("return_refundreason"+new_id).disabled = true;'.
                          '}else if(qty > avail_qty){alert("Total available quantity to refund : "+avail_qty);document.getElementById("qty_refunded"+new_id).value = avail_qty;}else{'.
                                'document.getElementById("amount"+new_id).value=amt;'.
                                'document.getElementById("qty_returned"+new_id).value=qty;'.
                            'document.getElementById("return_refundfeedback"+new_id).disabled = false;'.
                            'document.getElementById("return_refundreason"+new_id).disabled = false;'.
                          '}'.
                        '}'.
                      '</script>';
              
              $html3="";
              $html3='<script type="text/javascript">function showreturndiv(f,b,n){
                                document.getElementById(b).style.display = "block";
                                document.getElementById(n).style.display = "none";
                                container=document.getElementById(f);
                               container.style.display = "block";
                                var tagNames = ["INPUT", "SELECT", "TEXTAREA" ,"BUTTON"];
                                      for (var i = 0; i<tagNames.length; i++) {
                                        var elems = container.getElementsByTagName(tagNames[i]);
                                        for (var j = 0; j<elems.length; j++) {
                                          elems[j].disabled = false;
                                        }
                                }
                          }function hidereturndiv(f,b,n){
                                document.getElementById(b).style.display = "block";
                                document.getElementById(n).style.display = "none";
                                container=document.getElementById(f);
                               container.style.display = "none";
                                var tagNames = ["INPUT", "SELECT", "TEXTAREA" ,"BUTTON"];
                                      for (var i = 0; i<tagNames.length; i++) {
                                        var elems = container.getElementsByTagName(tagNames[i]);
                                        for (var j = 0; j<elems.length; j++) {
                                          elems[j].disabled = true;
                                        }
                                }
                          }</script>';
              $div="<div class='catch_child' id='catch_child'></div>";
              $html1="";
              $html1='<script type="text/javascript">'.
               'document.addEventListener("DOMContentLoaded",function() {'.
                      'var h1="'.$div.'";'.
                      'document.getElementById("jet_Refund").innerHTML=document.getElementById("jet_Refund").innerHTML+h1;'

               .'});'
              .'</script>';
              $url="";
              $url=$this->getUrl('*/*/getchildhtml');
              $html="";
              $html='<script type="text/javascript">'.
              'function loadchildren(){var val=document.getElementById("refund_orderid").value;'.
              'if(val.length<=0){'.
                'return;'.
              '}'.
              'var url="'.$url.'mer_id/"+val;'.
              'new Ajax.Request(url, {'.
                      'method: "post",'.
                      'onSuccess: function(transport) {'.
                        'var html = transport.responseText.evalJSON();'.
                        'if(html.success){'.
                          'document.getElementById("catch_child").innerHTML=html.success;'.
                        '}else{'.
                          'document.getElementById("catch_child").innerHTML="";'.
                          'alert(html.error);'.
                        '}'
                     .' },
                      onFailure: function(){'.
                          'alert("Something Went Wrong.Please try again.");'.
                      '},'.
                 '});'.

              '}</script>';
                                   
            $fieldset->addField('refund_orderid', 'text', array(
                'label'     => Mage::helper('jet')->__('Enter Merchant Order Id'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'refund_orderid',
                'note' => 'Please fill Merchant Order Id to be refund.',
            ))->setAfterElementHtml($html.$html1.$html3.$html4);

            $fieldset->addField('get_info', 'button', array(
                'required'  => true,
                'value'  => 'Fetch Order Info',
                'onclick' => "loadchildren()",
                'tabindex' => 1
            ));

            
                    
            
      }
        return parent::_prepareForm();
    }
}
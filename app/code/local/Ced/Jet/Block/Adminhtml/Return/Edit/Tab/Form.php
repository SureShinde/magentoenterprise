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

class Ced_Jet_Block_Adminhtml_Return_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $data=array();
        if(Mage::registry('return_data'))
        {
          $data=Mage::registry('return_data');
          $skus_details=$data['sku_details'];
         //$form->setValues(Mage::registry('return_data'));
        }
        //print_r($data);die();
        if(count($data)>0){
                   $fieldset = $form->addFieldset('jet_return',array('legend'=>Mage::helper('jet')->__('Refurn Information')));
                    $fieldset->addField('id', 'hidden', array(
                      'label'     => Mage::helper('jet')->__('id'),
                      //'class'     => 'required-entry',
                      'readonly' => true,
                      'required'  => true,
                      'name'      => 'id',
                      'value'=>$data['id'],
                    ));
                    $fieldset->addField('returnid', 'text', array(
                      'label'     => Mage::helper('jet')->__('Return Id'),
                      //'class'     => 'required-entry',
                      'readonly' => true,
                      'required'  => true,
                      'name'      => 'returnid',
                      'value'=>$data['returnid'],
                      'note'=>'This is a return id on Jet.com for current order.',
                    ));
                     $fieldset->addField('merchant_order_id', 'text', array(
                                'label'     => Mage::helper('jet')->__('Merchant Order Id'),
                                //'class'     => 'required-entry',
                                'readonly' => true,
                                'required'  => true,
                                'name'      => 'merchant_order_id',
                                'value'=>$data['merchant_order_id'],
                                'note'=>'This is Order Id for current Order at Jet.com.',
                              ));
                     $fieldset->addField('agreeto_return', 'select', array(
                                'label'     => Mage::helper('jet')->__('Agree To Return'),
                                'class'     => 'required-entry validate-select',
                                'required'  => true,
                                'name'=>"agreeto_return",
                                //'name'      => 'agreeto_return[]',
                                'value'=>$data["agreeto_return"],
                                'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
                                'note'=>'If Yes that means the merchant agrees to wholly pay the return charge to Jet.com from the return notification.',
                              ));
                    if(count($skus_details)>0){
                        $i=0;
                        foreach($skus_details as $detail){
                            $flag=false;
                            if(($detail["changes_made"] && $detail["changes_made"] =='1')){
                                  $flag=true;
                            }

                            $html1="";
                            if($flag){
                                        $html1='<script type="text/javascript">'.
                                       'document.addEventListener("DOMContentLoaded",function() {'.
                                              'var f="sku_return_'.$i.'";'.
                                              'container=document.getElementById(f);
                                         var tagNames = ["INPUT", "SELECT", "TEXTAREA" ,"BUTTON"];
                                                for (var i = 0; i<tagNames.length; i++) {
                                                  var elems = container.getElementsByTagName(tagNames[i]);
                                                  for (var j = 0; j<elems.length; j++) {
                                                    elems[j].readOnly = true;
                                                  }
                                          }'

                                       .'});'
                                      .'</script>';
                            }
                            

                           $fieldset1 = $fieldset->addFieldset("sku_return_".$i,array('legend'=>Mage::helper('jet')->__('sku : '.$detail['merchant_sku'])));
                              
                              if($flag){
                                    $fieldset1->addField('want_to_return'.$i, 'hidden', array(
                                      'label'     => Mage::helper('jet')->__('Generate Return for the item'),
                                     'readonly' => true,
                                      'name'      => "sku_details[sku$i][want_to_return]",
                                      'value'=>$detail["want_to_return"],
                                    ))->setAfterElementHtml("<h3>Submitted Item.</h3>");
                              }else{
                                      $html4="";
                                      $html4='<script type="text/javascript">'.
                                        'function feedbackchange'.$i.'(ele){'.
                                          'var v=ele.options[ele.selectedIndex].value;'.
                                          'if(v==1){'.
                                             'document.getElementById("return_refundfeedback'.$i.'").classList.add("required-entry");'.
                                             'document.getElementById("return_refundfeedback'.$i.'").classList.add("validate-select");'.
                                            'var parent_tr = document.getElementById("return_refundfeedback'.$i.'").parentNode.parentNode;'.
                                            'var span=parent_tr.getElementsByTagName("span");'.
                                            'span[0].style.display = "inline-block";'.
                                          '}'.
                                          'if(v==0){'.
                                              'document.getElementById("return_refundfeedback'.$i.'").classList.remove("validate-select");'.
                                               'document.getElementById("return_refundfeedback'.$i.'").classList.remove("required-entry");'.
                                            'var parent_tr = document.getElementById("return_refundfeedback'.$i.'").parentNode.parentNode;'.
                                            'var span=parent_tr.getElementsByTagName("span");'.
                                            'span[0].style.display = "none";'.
                                          '}'.
                                        '}'.
                                      '</script>';

                                      $html3="";
                                      $html3='<script type="text/javascript">'.
                                       'document.addEventListener("DOMContentLoaded",function() {'.
                                        'document.getElementById("return_refundfeedback'.$i.'").classList.remove("required-entry");'.
                                        'document.getElementById("return_refundfeedback'.$i.'").classList.remove("validate-select");'.
                                        'var parent_tr = document.getElementById("return_refundfeedback'.$i.'").parentNode.parentNode;'.
                                        'var span=parent_tr.getElementsByTagName("span");'.
                                        'span[0].style.display = "none";'.
                                        '});'.
                                      '</script>';

                                     $fieldset1->addField('want_to_return'.$i, 'select', array(
                                      'label'     => Mage::helper('jet')->__('Generate Return for the item'),
                                      'class'     => 'required-entry validate-select',
                                      'required'  => true,
                                      'readonly'  => $flag,
                                      'onchange'  => "feedbackchange$i(this)",
                                      'name'      => "sku_details[sku$i][want_to_return]",
                                      'values'    => Mage::helper('jet')->wanttoreturn(),
                                      'value'=>$detail["want_to_return"],
                                      'note'=>'If select Yes than data of current sku will beQty Available for Refund sent to Jet.com for return otherwise not.',
                                    ))->setAfterElementHtml($html3.$html4);
                              }
                             
                              $fieldset1->addField('order_item_id'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Order Item Id'),
                                'readonly' => true,
                                'required'  => true,
                                'name'      => "sku_details[sku$i][order_item_id]",
                                'value'=>$detail["order_item_id"],
                                'note'=>'Jet\'s unique identifier for an item in a merchant order.',
                              ));
                              /*$fieldset1->addField('qty_ordered'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Qty Shipped'),
                                'class'     => 'validate-number',
                                'readonly' => true,
                                'name'      => "sku_details[sku$i][qty_shipped]",
                                'value'=>$detail["qty_ordered"],
                                'note'=>'Quanity Ordered for this item in current order.',
                              ));
                              $fieldset1->addField('qty_already_refunded'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Qty already Refunded'),
                                'class'     => 'validate-number',
                                'readonly' => true,
                                'name'      => "sku_details[sku$i][qty_already_refunded]",
                                'value'=>$detail["qty_already_refunded"],
                                'note'=>'Already refunded qty of this item in current order.',
                              ));*/
                              $fieldset1->addField('available_to_refund_qty'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Qty Available for Refund'),
                                'class'     => ' validate-number',
                                'readonly' => true,
                                'name'      => "sku_details[sku$i][available_to_refund_qty]",
                                'value'=>$detail["available_to_refund_qty"],
                                'note'=>'Qty available to refund for this item in current order.',
                              ));
                               $fieldset1->addField('merchant_sku'.$i, 'hidden', array(
                                'readonly' => true,
                                'name'      => "sku_details[sku$i][merchant_sku]",
                                'value'=>$detail["merchant_sku"],
                              ));
                               $fieldset1->addField('changes_made'.$i, 'hidden', array(
                                'readonly' => true,
                                'name'      => "sku_details[sku$i][changes_made]",
                                'value'=>$detail["changes_made"],
                              ));
                             $fieldset1->addField('qty_returned'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Qty Returned by Customer'),
                                'class'     => 'required-entry  validate-number',
                                //'required'  => true,
                                 'readonly' => true,
                                'name'      => "sku_details[sku$i][return_quantity]",
                                'value'=>$detail["return_quantity"],
                                'note'=>'Quantity of the given item that was returned.',
                              ));
                              $html="";//if -> document.getElementById("return_refundfeedback'.$i.'").disabled = true;else ->document.getElementById("return_refundfeedback'.$i.'").disabled = false;
                              $html='<script type="text/javascript">'.
                              'function checkamount'.$i.'(ele){ 
                                var qty_ret_cst = document.getElementById("qty_returned'.$i.'").value;
                                if(ele.value > qty_ret_cst)
                                {
                                  alert("Refund Quantity can not be greator than return quantity");
                                }

                                var am='.$detail["return_principal"].';var amount=(am !="" ? am : 0);if(ele.value==0){document.getElementById("amount'.$i.'").value=0;}else{document.getElementById("amount'.$i.'").value=(amount/qty_ret_cst)*ele.value;}}</script>';
                              $fieldset1->addField('qty_refunded'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Qty you want to Refund'),
                                'onchange' => "checkamount$i(this)",
                                'class'     => 'required-entry  validate-number',
                                'required'  => true,
                                'name'      => "sku_details[sku$i][refund_quantity]",
                                'value'=>($detail["refund_quantity"] !="" ? $detail["refund_quantity"] : 1),
                                'note'=>'Qty you want to refund for this item in current order.',
                              ))->setAfterElementHtml($html.$html1);

                              if($flag){
                                      $fieldset1->addField('return_refundfeedback'.$i, 'text', array(
                                        'label'     => Mage::helper('jet')->__('Return Feedback'),
                                        'class'     => 'required-entry',
                                        'required'  => true,
                                        'name'=>"sku_details[sku$i][return_refundfeedback]",
                                        'value'=>$detail["return_refundfeedback"],
                                        'note'=>'The reason this refund is less than the full amount.',
                                      ));
                              }else{
                                    $fieldset1->addField('return_refundfeedback'.$i, 'select', array(
                                      'label'     => Mage::helper('jet')->__('Return Feedback'),
                                      'class'     => 'required-entry validate-select',
                                      'required'  => true,
                                      'name'=>"sku_details[sku$i][return_refundfeedback]",
                                     'values'    =>  Mage::helper('jet')->feedbackOptArray(),
                                      'value'=>$detail["return_refundfeedback"],
                                      'note'=>'The reason this refund is less than the full amount.',
                                    ));
                              }
                            $fieldset1->addField('amount'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Amount'),
                                'class'     => 'required-entry  validate-number',
                                //'required'  => true,
                                 
                                'name'=>"sku_details[sku$i][return_principal]",
                                'value'=>($detail["return_principal"] !="" ? $detail["return_principal"] : 0),
                                'note'=>'Amount to be refund for the given item in USD associated with the item itself. This should be the total cost for this item not the unit cost.',
                               ));
                               $fieldset1->addField('actual_amount'.$i, 'hidden', array(
                               'name'=>"sku_details[sku$i][return_actual_principal]",
                                'value'=>($detail["return_principal"] !="" ? $detail["return_principal"] : 0),
                              ));
                               $fieldset1->addField('shipping_cost'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Shipping cost'),
                                'class'     => 'validate-number',
                               'name'=>"sku_details[sku$i][return_shipping_cost]",
                                'value'=>($detail["return_shipping_cost"] !="" ? $detail["return_shipping_cost"] : 0),
                                'note'=>'Amount to be refund for the given item in USD associated with the shipping cost that was allocated to this item.',
                              ));
                              $fieldset1->addField('shipping_tax'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Shipping tax'),
                                'value'=>($detail["return_shipping_tax"] !="" ? $detail["return_shipping_tax"] : 0),
                                 'name'=>"sku_details[sku$i][return_shipping_tax]",
                                 'class'     => 'validate-number',
                                 'note'=>'Amount to be refund for the given item in USD associated with the tax that was charged on shipping.',
                               ));
                               $fieldset1->addField('tax'.$i, 'text', array(
                                'label'     => Mage::helper('jet')->__('Tax'),
                                'value'=>($detail["return_tax"] !="" ? $detail["return_tax"] : 0),
                                 'name'=>"sku_details[sku$i][return_tax]",
                                 'class'     => 'validate-number',
                                  'note'=>'Amount to be refund for the given item in USD associated with tax that was charged for the item.',
                              ));
                               

                              $i++;
                        }
                     }
        }
       
        return parent::_prepareForm();
    }
}

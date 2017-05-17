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

class Ced_Jet_Block_Adminhtml_Rejected_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
       if (Mage::registry('errorfile_collection'))
        {
            $data = Mage::registry('errorfile_collection')->getData();
		}
        else
        {
            $data = array();
        }
 
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('adminhtml/adminhtml_jetproduct/resubmit', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                //'enctype' => 'multipart/form-data',
        ));
 
        $form->setUseContainer(true);
 
        $this->setForm($form);
 
        $fieldset = $form->addFieldset('example_form', array(
             'legend' =>Mage::helper('jet')->__('Error Files Information')
        ));
 
 		$fieldset->addField('id', 'hidden', array(
             'label'     => Mage::helper('jet')->__('error_id'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'id',
			 'readonly' => true,
            // 'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));

 
        $fieldset->addField('jetinfofile_id', 'hidden', array(
             'label'     => Mage::helper('jet')->__('jetinfofile_id'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'jetinfofile_id',
			 'readonly' => true,
            // 'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));
		
		
		$fieldset->addField('jet_file_id', 'text', array(
             'label'     => Mage::helper('jet')->__('Jet File Id'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'jet_file_id',
			 'readonly' => true,
            // 'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));
		$fieldset->addField('file_name', 'text', array(
             'label'     => Mage::helper('jet')->__('File Name'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'file_name',
			 'readonly' => true,
          //   'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));
		$fieldset->addField('file_type', 'text', array(
             'label'     => Mage::helper('jet')->__('File Type'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'file_type',
			 'readonly' => true,
             //'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));
		$fieldset->addField('error', 'textarea', array(
             'label'     => Mage::helper('jet')->__('Error Description'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'error',
			 'readonly' => true,
             //'note'     => Mage::helper('jet')->__('The name of the example.'),
        ));
 		
		
        $form->setValues($data);
		$this->_readFile($data);
		$fdata = Mage::registry('errorfilecontent');
		$errorfilecontent = isset($fdata )? $fdata: 'File not found';
		
		$fieldset->addField('errorfile', 'textarea', array(
			'label' => Mage::helper('jet')->__('File Content'),
			'class' => 'required-entry',
            'required' => true,
			'readonly' => true,
			'name'  => 'errorfile',
			'value' => $errorfilecontent
		));
 
        return parent::_prepareForm();
    }
	
	
	protected function _readFile($data)
	{
		$file_name = explode(' ', $data['file_name']);
		$data['file_name'] = implode('',$file_name);
		$errorfile = Mage::getBaseDir('var').DS.'jetupload'.DS.$data['file_name'];
		$errorfilecontent = file_get_contents($errorfile);
		
		Mage::register('errorfilecontent', $errorfilecontent);
		
		return $errorfile;
	}
}
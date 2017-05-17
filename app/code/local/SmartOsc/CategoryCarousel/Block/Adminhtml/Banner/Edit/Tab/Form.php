<?php

/**
 * Banner edit form tab
 *
 */
class SmartOsc_CategoryCarousel_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('banner_');
        $form->setFieldNameSuffix('banner');
        $this->setForm($form);
        $store = Mage::app()->getWebsite(true)->getDefaultStore();
        //The root category id of default store
        $parent = $store->getRootCategoryId();
        /**
         * Check if parent node of the store still exists
         */
        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */
        if (!$category->checkId($parent)) {
            return array();
        }

        $recursionLevel = max(0, (int)$store->getConfig('catalog/navigation/max_depth'));
        $storeCategories = $category->getCategories($parent, $recursionLevel, false, true, true);
        //categories list
        $catlist = $this->getHelper()->getoutputList($parent, $storeCategories, "name", "entity_id", "parent_id");
        $clist = array();
        foreach ($catlist as $id => $cat) {
            $category = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($id);
            $url = str_replace(Mage::getBaseUrl(), "", $category->getUrl());
            $clist[$url] = $cat;
        }
        $fieldset = $form->addFieldset(
            'banner_form',
            array('legend' => Mage::helper('smartosc_categorycarousel')->__('Item'))
        );
        $fieldset->addType(
            'image',
            Mage::getConfig()->getBlockClassName('smartosc_categorycarousel/adminhtml_banner_helper_image')
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('smartosc_categorycarousel')->__('Title'),
                'name'  => 'title',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'image',
            'image',
            array(
                'label' => Mage::helper('smartosc_categorycarousel')->__('Choose Image'),
                'name'  => 'image',

           )
        );

        $fieldset->addField('category', 'select', array(
            'label' => Mage::helper('smartosc_categorycarousel')->__('Select a category'),
            'required' => false,
            'size' => 10,
            'name' => 'category',
            'values' => $clist,
        ));

        $fieldset->addField(
            'discription',
            'textarea',
            array(
                'label' => Mage::helper('smartosc_categorycarousel')->__('Description'),
                'name'  => 'discription',
                'required'  => false,
           )
        );
    if ($this->getRequest()->getParam('id')) {
      $orders = Mage::helper('smartosc_categorycarousel')->getorders($this->getRequest()->getParam('id'),(Int)$menuGroupId);
    }
    if ($this->getRequest()->getParam('id')) {
        $fieldset->addField(
            'ordering',
            'select',
            array(
                'label' => Mage::helper('smartosc_categorycarousel')->__('Order'),
                          'required' => false,
          'size' => 10,
          'name' => 'ordering',
          'values' => $orders,      

           )
        );
    }
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('smartosc_categorycarousel')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('smartosc_categorycarousel')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('smartosc_categorycarousel')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_banner')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getBannerData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getBannerData());
            Mage::getSingleton('adminhtml/session')->setBannerData(null);
        } elseif (Mage::registry('current_banner')) {
            $formValues = array_merge($formValues, Mage::registry('current_banner')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
    public function getHelper()
    {
        return Mage::helper('smartosc_categorycarousel');
    }
}

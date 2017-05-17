<?php

/**
 * Banner admin grid block
 *
 */
class SmartOsc_CategoryCarousel_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('bannerGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('smartosc_categorycarousel/banner')
            ->getCollection()->setOrder("ordering", "ASC");
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('smartosc_categorycarousel')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'    => Mage::helper('smartosc_categorycarousel')->__('Title'),
                'align'     => 'left',
                'index'     => 'title',
            )
        );
        
        $this->addColumn(
            'category',
            array(
                'header' => Mage::helper('smartosc_categorycarousel')->__('Category Link'),
                'index'  => 'category',
                'type'=> 'text',

            )
        );
        $this->addColumn(
            'image',
            array(
                'header' => Mage::helper('smartosc_categorycarousel')->__('Images'),
                'width' => '150',
                'index' => 'image',
                'renderer' => 'smartosc_categorycarousel/adminhtml_banner_renderer_image'
            ));

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('smartosc_categorycarousel')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('smartosc_categorycarousel')->__('Enabled'),
                    '0' => Mage::helper('smartosc_categorycarousel')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('smartosc_categorycarousel')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('smartosc_categorycarousel')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('smartosc_categorycarousel')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('banner');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('smartosc_categorycarousel')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('smartosc_categorycarousel')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('smartosc_categorycarousel')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('smartosc_categorycarousel')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('smartosc_categorycarousel')->__('Enabled'),
                            '0' => Mage::helper('smartosc_categorycarousel')->__('Disabled'),
                        )
                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}

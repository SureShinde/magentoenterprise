<?php
/**
 * @category   CLS
 * @package    Widgets
 * @copyright  Copyright (c) 2015 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    See LICENSE_CLS.txt for license details
 */

class CLS_Widgets_Block_Adminhtml_Cms_Block_Widget_Multichooser extends Mage_Adminhtml_Block_Cms_Block_Grid
{
    /**
     * Store selected static block Ids
     *
     * @var array
     */
    protected $_selectedBlocks = array();

    /**
     * Store hidden block ids field id
     *
     * @var string
     */
    protected $_elementValueId = '';

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('in_widget'=>1));
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_elementValueId = "{$element->getId()}";
        $this->_selectedBlocks = explode(',', $element->getValue());

        //Create hidden field that stores selected block ids
        $hidden = new Varien_Data_Form_Element_Hidden($element->getData());
        $hidden->setId($this->_elementValueId)->setForm($element->getForm());
        $hiddenHtml = $hidden->getElementHtml();

        $element->setValue('')->setValueClass('value2');
        $element->setData('after_element_html', $hiddenHtml . $this->toHtml());

        return $element;
    }

    /**
     * Grid row init js callback
     *
     * @return string
     */
    public function getRowInitCallback()
    {
        return '
        function(grid, row){
            if(!grid.selBlocksIds){
                grid.selBlocksIds = {};
                if($(\'' . $this->_elementValueId . '\').value != \'\'){
                    var elementValues = $(\'' . $this->_elementValueId . '\').value.split(\',\');
                    for(var i = 0; i < elementValues.length; i++){
                        grid.selBlocksIds[elementValues[i]] = i+1;
                    }
                }
                grid.reloadParams = {};
                grid.reloadParams[\'selected_blocks[]\'] = Object.keys(grid.selBlocksIds);
            }
            var inputs      = Element.select($(row), \'input\');
            var checkbox    = inputs[0];
            var position    = inputs[1];
            var blockId    = checkbox.value;

            inputs[1].checkboxElement = checkbox;

            var indexOf = Object.keys(grid.selBlocksIds).indexOf(blockId);
            if(indexOf >= 0){
                checkbox.checked = true;
                if (!position.value) {
                    position.value = indexOf + 1;
                }
            }

            Event.observe(position,\'change\', function(){
                var checkb = Element.select($(row), \'input\')[0];
                if(checkb.checked){
                    grid.selBlocksIds[checkb.value] = this.value;
                    var idsclone = Object.clone(grid.selBlocksIds);
                    var blockKeys = Object.keys(grid.selBlocksIds);
                    var pos = Object.values(grid.selBlocksIds).sort(sortNumeric);
                    var blocks = [];
                    var k = 0;

                    for(var j = 0; j < pos.length; j++){
                        for(var i = 0; i < blockKeys.length; i++){
                            if(idsclone[blockKeys[i]] == pos[j]){
                                blocks[k] = blockKeys[i];
                                k++;
                                delete(idsclone[blockKeys[i]]);
                                break;
                            }
                        }
                    }
                    $(\'' . $this->_elementValueId . '\').value = blocks.join(\',\');
                }
            });
        }
        ';
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        return '
            function (grid, event) {
                if(!grid.selBlocksIds){
                    grid.selBlocksIds = {};
                }

                var trElement   = Event.findElement(event, "tr");
                var isInput     = Event.element(event).tagName == \'INPUT\';
                var inputs      = Element.select(trElement, \'input\');
                var checkbox    = inputs[0];
                var position    = inputs[1].value || 1;
                var checked     = isInput ? checkbox.checked : !checkbox.checked;
                checkbox.checked = checked;
                var blockId    = checkbox.value;

                if(checked){
                    if(Object.keys(grid.selBlocksIds).indexOf(blockId) < 0){
                        grid.selBlocksIds[blockId] = position;
                    }
                }
                else{
                    delete(grid.selBlocksIds[blockId]);
                }

                var idsclone = Object.clone(grid.selBlocksIds);
                var blockKeys = Object.keys(grid.selBlocksIds);
                var pos = Object.values(grid.selBlocksIds).sort(sortNumeric);
                var blocks = [];
                var k = 0;
                for(var j = 0; j < pos.length; j++){
                    for(var i = 0; i < blockKeys.length; i++){
                        if(idsclone[blockKeys[i]] == pos[j]){
                            blocks[k] = blockKeys[i];
                            k++;
                            delete(idsclone[blockKeys[i]]);
                            break;
                        }
                    }
                }
                $(\'' . $this->_elementValueId . '\').value = blocks.join(\',\');
                grid.reloadParams = {};
                grid.reloadParams[\'selected_blocks[]\'] = blocks;
            }
        ';
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        return 'function (grid, element, checked) {
                    if(!grid.selBlocksIds){
                        grid.selBlocksIds = {};
                    }
                    var checkbox    = element;

                    checkbox.checked = checked;
                    var blockId    = checkbox.value;
                    if(blockId == \'on\'){
                        return;
                    }
                    var trElement   = element.up(\'tr\');
                    var inputs      = Element.select(trElement, \'input\');
                    var position    = inputs[1].value || 1;

                    if(checked){
                        if(Object.keys(grid.selBlocksIds).indexOf(blockId) < 0){
                            grid.selBlocksIds[blockId] = position;
                        }
                    }
                    else{
                        delete(grid.selBlocksIds[blockId]);
                    }

                    var idsclone = Object.clone(grid.selBlocksIds);
                    var blockKeys = Object.keys(grid.selBlocksIds);
                    var pos = Object.values(grid.selBlocksIds).sort(sortNumeric);
                    var blocks = [];
                    var k = 0;
                    for(var j = 0; j < pos.length; j++){
                        for(var i = 0; i < blockKeys.length; i++){
                            if(idsclone[blockKeys[i]] == pos[j]){
                                blocks[k] = blockKeys[i];
                                k++;
                                delete(idsclone[blockKeys[i]]);
                                break;
                            }
                        }
                    }
                    $(\'' . $this->_elementValueId . '\').value = blocks.join(\',\');
                    grid.reloadParams = {};
                    grid.reloadParams[\'selected_blocks[]\'] = blocks;
                }';
    }

    /**
     * Create grid columns
     *
     * @return CLS_Widgets_Block_Adminhtml_Cms_Block_Widget_Chooser
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_widget', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_widget',
            'values'    => $this->getSelectedBlocks(),
            'align'     => 'center',
            'index'     => 'block_id',
        ));

        $this->addColumn('block_title', array(
            'header'    => Mage::helper('cms')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('position', array(
            'header'         => Mage::helper('cls_widgets')->__('Position'),
            'name'           => 'position',
            'type'           => 'number',
            'validate_class' => 'validate-number',
            'index'          => 'position',
            'editable'       => true,
            'filter'         => false,
            'edit_only'      => true,
            'sortable'       => false
        ));
        $this->addColumnsOrder('position', 'is_active');

        parent::_prepareColumns();

        $this->removeColumn('title');
        $this->removeColumn('creation_time');
        $this->removeColumn('update_time');

        return $this;
    }

    /**
     * Set custom filter for in widget flag
     *
     * @param string $column
     * @return CLS_Widgets_Block_Adminhtml_Cms_Block_Widget_Chooser
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_widget') {
            $blockIds = $this->getSelectedBlocks();
            if (empty($blockIds)) {
                $blockIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.block_id', array('in' => $blockIds));
            } else {
                if ($blockIds) {
                    $this->getCollection()->addFieldToFilter('main_table.block_id', array('nin' => $blockIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Adds additional parameter to URL for loading only blocks grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/cms_block_widget/multichooser', array(
            'blocks_grid' => true,
            '_current' => true,
            'uniq_id' => $this->getId(),
        ));
    }

    /**
     * Setter
     *
     * @param array $selectedBlocks
     * @return CLS_Widgets_Block_Adminhtml_Cms_Block_Widget_Chooser
     */
    public function setSelectedBlocks($selectedBlocks)
    {
        if (is_string($selectedBlocks)) {
            $selectedBlocks = explode(',', $selectedBlocks);
        }
        $this->_selectedBlocks = $selectedBlocks;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedBlocks()
    {
        if ($selectedBlocks = $this->getRequest()->getParam('selected_blocks', $this->_selectedBlocks)) {
            $this->setSelectedBlocks($selectedBlocks);
        }
        return $this->_selectedBlocks;
    }

    /**
     * Set blocks' positions of saved blocks
     *
     * @return CLS_Widgets_Block_Adminhtml_Cms_Block_Widget_Chooser
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();

        foreach ($this->getCollection() as $item) {
            foreach ($this->getSelectedBlocks() as $pos => $block) {
                if ($block == $item->getBlockId()) {
                    $item->setPosition($pos + 1);
                }
            }
        }
        return $this;
    }
}
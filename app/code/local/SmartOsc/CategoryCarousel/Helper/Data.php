<?php

/**
 * CategoryCarousel default helper
 */
class SmartOsc_CategoryCarousel_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }
    public function treemenu($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $label, $key, $parent)
    {

        if (@$children[$id] && $level <= $maxlevel) {
            foreach ($children[$id] as $v) {

                $id = $v->$key;
                $pre = '- ';
                $spacer = '---';
                if ($v->$parent == 0) {
                    $txt = $v->$label;
                } else {
                    $txt = $pre . $v->$label;
                }
                $pt = $v->$parent;
                $list[$id] = $v;
                $list[$id]->$label = "$indent$txt";
                $list[$id]->children = count(@$children[$id]);
                $list = $this->treemenu($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $label, $key, $parent);
            }
        }
        return $list;

    }
    public function getoutputList($root = 0, $collections, $labelfield = "title", $keyfield = "id", $parentfield = "parent", $addtop = false)
    {
        @$children = array();
        foreach ($collections as $collection) {
            $pt = $collection->$parentfield;
            $list = isset($children[$pt]) ? $children[$pt] : array();
            array_push($list, $collection);
            $children[$pt] = $list;
        }

        $lists = $this->treemenu($root, '', array(), $children, 9999, 0, $labelfield, $keyfield, $parentfield);
        if ($addtop) {
            $outputs = array('0' => "Top");
        }
        foreach ($lists as $id => $list) {

            $lists[$id]->$labelfield = "--" . $lists[$id]->$labelfield;
            $outputs[$lists[$id]->$keyfield] = $lists[$id]->$labelfield;

        }

        return $outputs;
    }
    /**
     * Prepare List of Ordering field in menu table
     * @param $id
     * @return array
     */
    public function getorders($id)
    {
        $item = Mage::getModel('smartosc_categorycarousel/banner')->load($id);
        $menu=Mage::getModel('smartosc_categorycarousel/banner')->getCollection()->setOrder("ordering", "ASC");;
        $menu->addFieldToSelect(array('ordering','title'));
        $rows=$menu->getData();
        foreach ($rows as $k => $v)
        {
            unset($rows[$k]['entity_id']);
            $rows[$k]['value']=$rows[$k]['ordering'];
            $rows[$k]['label']=$rows[$k]['title'];
            unset($rows[$k]['ordering']);
            unset($rows[$k]['title']);
        }
        $rows[] = array('value' => count($rows) + 1, "label" => "Last");
        array_unshift($rows, array('value' => 0, "label" => "First"));
        foreach ($rows as $k => $v) {
            $rows[$k]['label'] = $rows[$k]['value'] . " " . $rows[$k]['label'];
        }
        return $rows;
    }
        public function reorder($where = '')
    {
        $k = "entity_id";
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $write = $resource->getConnection('core_write');
        $menutable = $resource->getTableName('smartosc_categorycarousel_banner');
        $query = 'SELECT entity_id, ordering'
            . ' FROM ' . $menutable
            . ' WHERE ordering >= 0'
            . ' ORDER BY ordering';
        if (!($orders = $read->fetchAll($query))) {
            return false;
        }
        // compact the ordering numbers
        for ($i = 0, $n = count($orders); $i < $n; $i++) {
            if ($orders[$i]['ordering'] >= 0) {
                if ($orders[$i]['ordering'] != $i + 1) {
                    $orders[$i]['ordering'] = $i + 1;
                    $query = 'UPDATE ' . $menutable
                        . ' SET ordering = ' . (int)$orders[$i]['ordering']
                        . ' WHERE ' . $k . ' = \'' . $orders[$i][$k] . '\'';
                    $write->query($query);
                }
            }
        }
        return true;
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


class Amasty_SeoRichData_Block_Product_Breadcrumbs extends Mage_Catalog_Block_Product_Abstract
{
    protected $_result;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('amasty/amseorichdata/catalog/product/richdata.phtml');
    }

    public function _toHtml()
    {
        if (!Mage::getStoreConfig('amseorichdata/breadcrumbs/enabled')
        ) {
            return '';
        }

        $path  = Mage::helper('catalog')->getBreadcrumbPath();

        $itemListElement = array();
        $position = 1;

        foreach ($path as $category) {
            $itemListElement[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'item' => array(
                    '@id' => isset($category['link']) ? $category['link'] : '',
                    'name' => isset($category['label']) ? $category['label'] : ''
                )
            );
            $position++;
        }

        $data['breadcrumbs'] = array(
            '@context' => 'http://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        );

        foreach ($data as $section) {
            $json = json_encode($section, JSON_UNESCAPED_SLASHES);
            $this->_result .= "<script type=\"application/ld+json\">{$json}</script>";
        }

        return parent::_toHtml();
    }

    public function getResult()
    {
        return $this->_result;
    }
}

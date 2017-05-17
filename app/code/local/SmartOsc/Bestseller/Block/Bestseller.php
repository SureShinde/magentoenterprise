<?php

class SmartOsc_Bestseller_Block_Bestseller extends Mage_Catalog_Block_Product_Abstract // Mage_Core_Block_Template
{

	public function getProductCollection()
	{
		$storeId = (int) Mage::app()->getStore()->getId();
		// Date
		$date = new Zend_Date();
		$toDate = $date->setDay(1)->getDate()->get('Y-MM-dd');
		$fromDate = $date->subMonth(1)->getDate()->get('Y-MM-dd');

		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
			->addStoreFilter()
			->addPriceData()
			->addTaxPercents()
			->addUrlRewrite()
			->setPageSize(6);

		$collection->getSelect()
			->joinLeft(
				array('aggregation' => $collection->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
				"e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId} AND aggregation.period BETWEEN '{$fromDate}' AND '{$toDate}'",
				array('SUM(aggregation.qty_ordered) AS sold_quantity')
			)
			->group('e.entity_id')
			->order(array('sold_quantity DESC', 'e.created_at'));

		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		return $collection;
	}
}



?>
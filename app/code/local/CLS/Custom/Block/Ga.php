<?php
/**
 * GoogleAnalitics Page Block
 *
 * @category   CLS
 * @package    CLS_Custom
 * @author     Klayton Hawkins <klayton.hawkins@classyllama.com>
 */
class CLS_Custom_Block_Ga extends CLS_UniversalAnalyticsEnterprise_Block_Googleanalyticsuniversal_Ga
{

    protected function _getPageTrackingCodeUniversal($accountId){
        return "
        ga('create', '{$this->jsQuoteEscape($accountId)}', 'auto');
        ga('require', 'displayfeatures');
        ga('require', 'linkid', 'linkid.js');
        ga('require', 'ec');
        " . $this->_getAnonymizationCode() . "
        ga('send', 'pageview');
        ";
    }

    protected function _getOrdersTrackingCodeUniversal(){
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds));
        $result = array();
        foreach ($collection as $order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $result[] = sprintf("ga('ec:addProduct', {
					'id': '%s',
					'name': '%s',
					'category': '%s',
					'price': '%s',
					'quantity': '%s'
				});",
                    $this->jsQuoteEscape($item->getSku()),
                    $this->jsQuoteEscape($item->getName()),
                    null, // there is no "category" defined for the order item
                    $item->getBasePrice(),
                    $item->getQtyOrdered()
                );
            }
            $result[] = sprintf("ga('ec:setAction', 'purchase', {
                'id': '%s',
                'affiliation': '%s',
                'revenue': '%s',
                'tax': '%s',
                'shipping': '%s'
            });",
                $order->getIncrementId(),
                $this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName()),
                $order->getBaseGrandTotal(),
                $order->getBaseTaxAmount(),
                $order->getBaseShippingAmount()
            );
            //Send Ecommerce Transaction Data
            $result[] = "ga('send', 'event', 'Enhanced Ecommerce', 'transaction');";
            //Checkout Process
            foreach ($order->getAllVisibleItems() as $item) {
                $result[] = sprintf("ga('ec:addProduct', {
                    'id': '%s',
                    'name': '%s',
                    'category': '%s',
                    'price': '%s',
                    'quantity': '%s'
                });",
                    $this->jsQuoteEscape($item->getSku()),
                    $this->jsQuoteEscape($item->getName()),
                    null, // there is no "category" defined for the order item
                    $item->getBasePrice(),
                    $item->getQtyOrdered()
                );
            }
            $result[] = "ga('ec:setAction','checkout', {'step': 7});";
            //Send Checkout Proccess Data
            $result[] = "ga('send', 'event', 'Enhanced Ecommerce', 'checkout', 'step 7');";

     
        }
        return implode("\n", $result);
    }
}
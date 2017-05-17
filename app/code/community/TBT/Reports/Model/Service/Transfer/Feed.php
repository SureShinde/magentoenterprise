<?php
/**
 * Service Model Class for Transfer Feed
 */
class TBT_Reports_Model_Service_Transfer_Feed
{
    /**
     * Will return an unloaded collection of most recent transfers, in reverse-chronological order.
     *
     * @param int $count (optional, default: 20). Number of transfers to load.
     * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
     */
    public function getRecentTransfers($count = 20)
    {
        $collection = $this->getTransferCollectionModel();

        $collection
            ->setOrder('main_table.rewards_transfer_id', Varien_Data_Collection_Db::SORT_ORDER_DESC)
            ->getSelect()->limit($count);

        return $collection;
    }

    /**
     * Return an unloaded collection of reference objects for the specified transfer
     * @param int|string $transferId
     * @return TBT_Rewards_Model_Mysql4_Transfer_Reference_Collection
     */
    public function getTransferReferences($transferId)
    {
        return $this->getReferencesCollectionModel()
                    ->filterByTransfer($transferId);
    }

    /**
     * Will return an unloaded collection of
     * all subsequent transfers after the specified Id - in chronological order.
     *
     * @param int $afterId.
     * @param int $count (optional, default: 20). If specified, will only fetch this many results
     * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
     */
    public function getNextTransfers($afterId, $count = 20)
    {
        $collection = $this->getTransferCollectionModel();
        $collection
            ->addFieldToFilter('main_table.rewards_transfer_id', array('gt' => $afterId))
            ->setOrder('main_table.rewards_transfer_id', Varien_Data_Collection_Db::SORT_ORDER_ASC);

        if ($count) {
            $collection->getSelect()->limit($count);
        }

        return $collection;
    }

    /**
     * Will return an unloaded collection of
     * all previous transfers before the specified Id - in reverse-chronological order.
     *
     * @param int $beforeId.
     * @param int $count (optional, default: 20). If specified, will only fetch this many results
     * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
     */
    public function getPreviousTransfers($beforeId, $count = 20)
    {
        $collection = $this->getTransferCollectionModel();
        $collection
            ->addFieldToFilter('main_table.rewards_transfer_id', array('lt' => $beforeId))
            ->setOrder('main_table.rewards_transfer_id', 'main_table.rewards_transfer_id', Varien_Data_Collection_Db::SORT_ORDER_DESC);

        if ($count) {
            $collection->getSelect()->limit($count);
        }

        return $collection;
    }

    /**
     * @return TBT_Rewards_Model_Mysql4_Transfer_Collection
     */
    protected function getTransferCollectionModel()
    {
        return Mage::getModel('rewards/transfer')->getCollection();
    }

    /**
     * @return TBT_Rewards_Model_Mysql4_Transfer_Reference_Collection
     */
    protected function getReferencesCollectionModel()
    {
        return Mage::getModel('rewards/transfer_reference')->getCollection();
    }
}
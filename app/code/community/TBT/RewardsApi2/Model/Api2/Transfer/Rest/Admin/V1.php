<?php

/**
 * Class TBT_RewardsApi2_Model_Api2_Transfer_Rest_Admin_V1
 * API implementation for the Sweet Tooth transfer model
 *
 * @extends    Mage_Api2_Model_Resource
 * @category   TBT
 * @package    TBT_RewardsApi2
 * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_RewardsApi2_Model_Api2_Transfer_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{
    /**
     * @var array, key-value pair to indicate real attribute and it's alias mapping.
     */
    protected $_attributeNamingMap = array(
        'status' => 'status_id',
        'creation_ts' => 'created_at',
        'last_update_ts' => 'updated_at',
        'last_update_by' => 'updated_by'
    );

    /**
     * @var array, key-value pair to indicate default values when creating a new transfer
     */
    protected $_defaultValues = array(
        'comments' => 'Points transfer created through API',
        'status_id' => 5,
        'reason_id' => 845
    );

    /**
     * @var array, list of attribute which should be ignored during POST / PUT requests
     */
    protected $_readOnlyAttributes = array(
        'rewards_transfer_id',
        'created_at',
        'updated_at',
        'updated_by',
        'issued_by'
    );

    /**
     * Temporary mapping for casting of attribute data types
     * @var array
     */
    protected $_attributeCasting = array(
        'quantity' => "int",
        'is_dev_mode' => "boolean"
    );


    /**
     * Retrieve information about transfer
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        $id = $this->getRequest()->getParam('id');
        $transfer = Mage::getModel('rewards/transfer')->load($id);
        
        if (!$transfer || !$transfer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        
        $data = $transfer->getData();
        $this->_forceAttributeCasting($data);
        $this->_remapAttributes($data);
        return $data;
    }
    
    /**
     * Create trasnfer
     * @param array $data
     */
    protected function _create(array $data)
    {
        $validator = Mage::getResourceModel('api2/validator_fields', array('resource' => $this));

        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        if (isset($data['id']) || isset($data['rewards_transfer_id'])) {
            $this->_critical("Wrong URI and method for resource update request", Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $this->_removeReadOnlyAttributes($data);
        $this->_setDefaultValues($data);
        $this->_remapAttributes($data, true);
        $defaultCurrencyId = Mage::helper('rewards/currency')->getDefaultCurrencyId();
        
        $transfer = Mage::getModel('rewards/transfer');
        $transfer->setData($data)
            ->setCurrencyId($defaultCurrencyId);

        try {
            $transfer->save();
        } catch (Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        }
        
        return $this->_getLocation($transfer);
    }
    
    /**
     * Update trasnfer
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _update(array $data)
    {
        $id = $this->getRequest()->getParam('id');
        $transfer = Mage::getModel('rewards/transfer')->load($id);
        
        $validator = Mage::getResourceModel('api2/validator_fields', array('resource' => $this));

        if (!$validator->isValidData($data, true)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $this->_removeReadOnlyAttributes($data);
        $this->_remapAttributes($data, true);
        $transfer->addData($data);
        
        try {
            $transfer->save();
        } catch (Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        }
        
        return $this->_getLocation($transfer);
    }
    
    /**
     * Get trasnfers list
     * @return array
     */
    protected function _retrieveCollection()
    {
        $collection = Mage::getResourceModel('rewardsapi2/transfer_collection')
            ->excludeTransferReferences()
            ->setAttributeMapping($this->_attributeNamingMap);

        $this->_applyCollectionModifiers($collection);
        $data = $collection->load()->toArray();

        if (!empty($data['items'])) {
            $data = array_map(array($this, '_forceAttributeCasting'), $data['items']);

        } else {
            $data = array();
        }

        return $data;
    }
    
    /**
     * Remap attributes based on pre-determined configuration
     * Used as a temporary solution until the transfer system is refactored.
     *
     * @param array $entity
     * @param bool $reverse, to map in reverse (default: false)
     * @return array
     */
    protected function _remapAttributes(&$entity, $reverse = false)
    {
        $mapping = !$reverse? $this->_attributeNamingMap : array_flip($this->_attributeNamingMap);

        foreach ($mapping as $attribute => $alias) {
            if (array_key_exists($attribute, $entity)) {
                $entity[$alias] = $entity[$attribute];
                unset($entity[$attribute]);
            }
        }

        return $entity;
    }

    /**
     * Set default values
     * 
     * @param array $entity
     * @return array
     */
    protected function _setDefaultValues(&$entity)
    {
        foreach ($this->_defaultValues as $key => $value) {
            if (!isset($entity[$key])) {
                $entity[$key] = $value;
            }
        }
        
        return $entity;
    }

    /**
     * Remove read-only attributes from provided entity array
     * @param array $entity
     * @return array
     */
    protected function _removeReadOnlyAttributes(&$entity)
    {
        foreach ($this->_readOnlyAttributes as $key => $value) {
            if (isset($entity[$value])) {
                unset($entity[$value]);
            }
        }

        return $entity;
    }

    /**
     * Will force attributes of the supplied entity to be of the
     * data type specified in $this->attributeCasting
     * @param array &$entity
     * @return array
     */
    protected function _forceAttributeCasting(&$entity)
    {
        foreach ($this->_attributeCasting as $key => $type) {
            if (isset($entity[$key])) {
                switch ($type) {
                    case "int":
                        $entity[$key] = (int) $entity[$key];
                        break;
                    case "string":
                        $entity[$key] = (string) $entity[$key];
                        break;
                    case "boolean":
                    case "bool":
                        $entity[$key] = (bool) $entity[$key];
                        break;
                }
            }
        }

        return $entity;
    }
}

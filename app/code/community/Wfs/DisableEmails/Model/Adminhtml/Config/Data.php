<?php
/**
 * MageryThemes
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://magery-themes.com/MAGERY-LICENSE.txt
 *
 *
 * MAGENTO EDITION USAGE NOTICE
 *
 * This package designed for Magento COMMUNITY edition
 * MageryThemes does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * MageryThemes does not provide extension support in case of
 * incorrect edition usage.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category   Wfs
 * @package    Wfs_DisableEmails
 * @copyright  Copyright (c) 2013 MageryThemes (http://magery-themes.com)
 * @license    http://magery-themes.com/MAGERY-LICENSE.txt
 */
class Wfs_DisableEmails_Model_Adminhtml_Config_Data extends Mage_Adminhtml_Model_Config_Data
{
    /**
     * Save config section
     * Require set: section, website, store and groups
     *
     * @return Mage_Adminhtml_Model_Config_Data
     */
    public function save()
    {
        $emailTemplateSettings = array();

        $this->_validate();
        $this->_getScope();

        Mage::dispatchEvent('model_config_data_save_before', array('object' => $this));

        $section = $this->getSection();
        $website = $this->getWebsite();
        $store   = $this->getStore();
        $groups  = $this->getGroups();
        $scope   = $this->getScope();
        $scopeId = $this->getScopeId();

        if (empty($groups)) {
            return $this;
        }

        $sections = Mage::getModel('adminhtml/config')->getSections();
        /* @var $sections Mage_Core_Model_Config_Element */

        $oldConfig = $this->_getConfig(true);

        $deleteTransaction = Mage::getModel('core/resource_transaction');
        /* @var $deleteTransaction Mage_Core_Model_Resource_Transaction */
        $saveTransaction = Mage::getModel('core/resource_transaction');
        /* @var $saveTransaction Mage_Core_Model_Resource_Transaction */

        // Extends for old config data
        $oldConfigAdditionalGroups = array();

        foreach ($groups as $group => $groupData) {
            /**
             * Map field names if they were cloned
             */
            $groupConfig = $sections->descend($section.'/groups/'.$group);

            if ($clonedFields = !empty($groupConfig->clone_fields)) {
                if ($groupConfig->clone_model) {
                    $cloneModel = Mage::getModel((string)$groupConfig->clone_model);
                } else {
                    Mage::throwException('Config form fieldset clone model required to be able to clone fields');
                }
                $mappedFields = array();
                $fieldsConfig = $sections->descend($section.'/groups/'.$group.'/fields');

                if ($fieldsConfig->hasChildren()) {
                    foreach ($fieldsConfig->children() as $field => $node) {
                        foreach ($cloneModel->getPrefixes() as $prefix) {
                            $mappedFields[$prefix['field'].(string)$field] = (string)$field;
                        }
                    }
                }
            }
            // set value for group field entry by fieldname
            // use extra memory
            $fieldsetData = array();
            foreach ($groupData['fields'] as $field => $fieldData) {
                $fieldsetData[$field] = (is_array($fieldData) && isset($fieldData['value']))
                    ? $fieldData['value'] : null;
            }

            foreach ($groupData['fields'] as $field => $fieldData) {
                $fieldConfig = $sections->descend($section . '/groups/' . $group . '/fields/' . $field);
                if (!$fieldConfig && $clonedFields && isset($mappedFields[$field])) {
                    $fieldConfig = $sections->descend($section . '/groups/' . $group . '/fields/'
                        . $mappedFields[$field]);
                }
                if (!$fieldConfig) {
                    $node = $sections->xpath($section .'//' . $group . '[@type="group"]/fields/' . $field);
                    if ($node) {
                        $fieldConfig = $node[0];
                    }
                }

                /**
                 * Get field backend model
                 * Fix core issue here, because not all fields has 'backend_model' property
                 */
                if (!empty($fieldConfig->backend_model)) {
                    $backendClass = $fieldConfig->backend_model;
                } else {
                    $backendClass = 'core/config_data';
                }

                /** @var $dataObject Mage_Core_Model_Config_Data */
                $dataObject = Mage::getModel($backendClass);
                if (!$dataObject instanceof Mage_Core_Model_Config_Data) {
                    Mage::throwException('Invalid config field backend model: '.$backendClass);
                }

                $dataObject
                    ->setField($field)
                    ->setGroups($groups)
                    ->setGroupId($group)
                    ->setStoreCode($store)
                    ->setWebsiteCode($website)
                    ->setScope($scope)
                    ->setScopeId($scopeId)
                    ->setFieldConfig($fieldConfig)
                    ->setFieldsetData($fieldsetData)
                ;

                if (!isset($fieldData['value'])) {
                    $fieldData['value'] = null;
                }

                $path = $section . '/' . $group . '/' . $field;

                /**
                 * Look for custom defined field path
                 */
                if (is_object($fieldConfig)) {
                    $configPath = (string)$fieldConfig->config_path;
                    if (!empty($configPath) && strrpos($configPath, '/') > 0) {
                        // Extend old data with specified section group
                        $groupPath = substr($configPath, 0, strrpos($configPath, '/'));
                        if (!isset($oldConfigAdditionalGroups[$groupPath])) {
                            $oldConfig = $this->extendConfig($groupPath, true, $oldConfig);
                            $oldConfigAdditionalGroups[$groupPath] = true;
                        }
                        $path = $configPath;
                    }
                }

                $inherit = !empty($fieldData['inherit']);

                $dataObject->setPath($path)
                    ->setValue($fieldData['value']);

                if (isset($oldConfig[$path])) {
                    $dataObject->setConfigId($oldConfig[$path]['config_id']);

                    /**
                     * Delete config data if inherit
                     */
                    if (!$inherit) {
                        $saveTransaction->addObject($dataObject);
                    }
                    else {
                        $deleteTransaction->addObject($dataObject);
                    }
                }
                elseif (!$inherit) {
                    $dataObject->unsConfigId();
                    $saveTransaction->addObject($dataObject);
                }

                if ($this->hasEmailTemplateSetting($dataObject)) {
                    $emailTemplateSettings []= $dataObject;
                }
            }
        }

        foreach ($emailTemplateSettings as $emailTemplateConfig) {
            if (strpos($emailTemplateConfig->getPath(), 'wfs') === false) {
                // Not WFS setting, lookup WFS config
                $wfsPath = Wfs_DisableEmails_Model_Email_Template::XML_PATH_PREFIX
                    . str_replace('/', '_', $emailTemplateConfig->getPath());
                $wfsValue = Mage::getStoreConfig($wfsPath);
                $clonedConfig = clone $emailTemplateConfig;
                $clonedConfig->unsConfigId();
                $clonedConfig->setValue($wfsValue);
                $clonedConfig->setPath(
                    Wfs_DisableEmails_Model_Email_Template::XML_PATH_PREFIX . '_' .$emailTemplateConfig->getValue()
                );

                $saveTransaction->addObject($clonedConfig);
            } else {
                // Saving of WFS config
                $searchPath = str_replace(
                    Wfs_DisableEmails_Model_Email_Template::XML_PATH_PREFIX, '', $emailTemplateConfig->getPath()
                );
                /** @var Mage_Core_Model_Resource_Config_Data_Collection $collection */
                $collection = Mage::getResourceModel('core/config_data_collection');
                $collection->addFieldToFilter('scope', $emailTemplateConfig->getScope());
                $collection->addFieldToFilter('scope_id', $emailTemplateConfig->getScopeId());
                $collection->getSelect()
                    ->where("REPLACE(path, '/', '_') = ?", $searchPath)
                    ->limit(1);

                $coreConfigRow = $collection->getFirstItem();
                if ($coreConfigRow->getId() && $coreConfigRow->getValue()) {
                    $templateId = $coreConfigRow->getValue();
                    $clonedConfig = clone $emailTemplateConfig;
                    $clonedConfig->unsConfigId();
                    $clonedConfig->setPath(
                        Wfs_DisableEmails_Model_Email_Template::XML_PATH_PREFIX . '_' .$templateId
                    );
                    $saveTransaction->addObject($clonedConfig);
                }
            }
        }

        $deleteTransaction->delete();
        $saveTransaction->save();

        return $this;
    }

    /**
     * @param Mage_Core_Model_Config_Data $dataObject
     *
     * @return bool
     */
    public function hasEmailTemplateSetting($dataObject)
    {
        $emailSettings = array('checkout_payment_failed_template', 'moneybookers_activateemail');
        $hasEmailPrefix = (
            strpos(str_replace('/', '_', $dataObject->getPath()), '_email') !== false
            || in_array($dataObject->getPath(), $emailSettings)
        );

        return $hasEmailPrefix && is_numeric($dataObject->getValue());
    }
}

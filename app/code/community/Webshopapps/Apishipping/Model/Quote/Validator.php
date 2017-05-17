<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
/** WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Apishipping
 * User         Genevieve Eddison
 * Date         17 June 2013
 * Time         11:45
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 */


class Webshopapps_Apishipping_Model_Quote_Validator extends Mage_Api2_Model_Resource_Validator
{

    protected $_operation;

    public function __construct($options)
    {

        if (!isset($options['operation']) || empty($options['operation'])) {
            throw new Exception("Passed parameter 'operation' is empty.");
        }
        $this->_operation = $options['operation'];
    }


    public function isValidData(array $data)
    {

        try {
            foreach ($data['Products'] as $product) {
                $this->_validateSku($product);
            }
            $this->_validateAddress($data['Address']);
            $isSatisfied = count($this->getErrors()) == 0;
        } catch (Mage_Api2_Exception $e) {
            $this->_addError($e->getMessage());
            $isSatisfied = false;
        }

        return $isSatisfied;
    }

    protected function _validateSku($data)
    {
        if (!isset($data['sku']) || empty($data['sku'])) {
            $this->_critical('Missing sku in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        $productId = Mage::getModel('catalog/product')->getIdBySku($data['sku']);
        if (!$productId) {
            $this->_critical('Invalid SKU: ' . $data['sku'], Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!isset($data['qty']) || empty($data['qty']) || !is_numeric($data['qty'])) {
            $this->_critical('Missing qty in request for sku ' . $data['sku'], Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    protected function _validateAddress($data)
    {
        if (!isset($data['Zip']) || empty($data['Zip'])) {
            $this->_critical('Missing "Zip" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!isset($data['Country']) || empty($data['Country'])) {
            $this->_critical('Missing "Country" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    protected function _critical($message, $code)
    {
        throw new Mage_Api2_Exception($message, $code);
    }

}
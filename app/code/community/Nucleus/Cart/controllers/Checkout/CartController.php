<?php
/**
 * Nucleus
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Nucleus License
 * that is bundled with this package in the file LICENSE_NUCLEUS.txt.
 * It is also available through the World Wide Web at this URL:
 * http://www.nucleuscommerce.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the World Wide Web, please send an email
 * to support@nucleuscommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Nucleus
 * to newer versions in the future.
 *
 * @category   Nucleus
 * @package    Cart
 * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
 * @license    http://www.nucleuscommerce.com/license
 */

require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'CartController.php';

class Nucleus_Cart_Checkout_CartController extends Mage_Checkout_CartController
{
    public function ajaxAddAction()
    {

        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        $result = array();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                Mage::throwException('Product is not found.');
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );


            /**
             * Starting from here, handling of success/errors is different from the standard addAction
             */
            $errors = false;

            $checkoutSession = $this->_getSession();
            $checkoutErrors = $checkoutSession->getMessages(true)->getItems(Mage_Core_Model_Message::ERROR);
            if (count($checkoutErrors) > 0) {
                $errorText = array();
                foreach ($checkoutErrors as $error) {
                    $errorText[] = $error->getText();
                }
                $result['success'] = 0;
                $result['error'] = implode('<br />', $errorText);
                $errors = true;
            } elseif ($cart->getQuote()->getHasError()) {
                $quoteErrors = $cart->getQuote()->getErrors();
                $errorText = array();
                foreach ($quoteErrors as $error) {
                    $errorText[] = $error->getText();
                }
                $result['success'] = 0;
                $result['error'] = implode('<br />', $errorText);
                $errors = true;
            }

            if (!$errors) {
                $this->loadLayout();
                $result['content'] = $this->getLayout()->getBlock('minicart_content')->toHtml();
                $result['qty'] = $this->_getCart()->getSummaryQty();

                $result['message'] = 'Item was added successfully.';
                $result['success'] = 1;
            }
        } catch (Mage_Core_Exception $e) {
            $result['success'] = 0;
            if ($this->_getSession()->getUseNotice(true)) {
                $result['error'] = Mage::helper('core')->escapeHtml($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                $result['error'] = implode('<br />', $messages);
            }
        } catch (Exception $e) {
            $result['success'] = 0;
            $result['error'] = $this->__('Cannot add the item to shopping cart.');
            Mage::logException($e);
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
<?php
/**
 * Customer Notifications Controller
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Customer_NotificationsController extends Mage_Core_Controller_Front_Action 
{
    /*
     * Unsubscribe a customer from the Point Summary monthly notification - must have a valid customer id
     * to be unserialized prior to loading.
     */
    public function unsubscribeAction() 
    {
        $encryptedCustomerId = $this->getRequest()->getParam('customer');
        
        if ($encryptedCustomerId) {
            $customerId = (int) urldecode(base64_decode($encryptedCustomerId));
            $customer = Mage::getModel('rewards/customer')->load($customerId);
            if ($customer->getId()) {
                try {
                    $customer->setRewardsPointsNotification(0)
                        ->save();
                    Mage::getSingleton('core/session')->addSuccess($this->__("You have been successfully unsubscribed."));   
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addException($e, $this->__('There was a problem unsubscribing you from this notification.'));
                }
            } else {
                
                $message = $this->__("The link you clicked on is outdated. Please login to continue with unsubscribing.");
                $message .= '<br />';
                $message .= $this->__("Alternatively, please follow the unsubscribe link on a more recent email.");
                        
                Mage::getSingleton('core/session')->addError($message);
                $this->_redirect('newsletter/manage');
                return $this;
            }
        }
        
		$this->_redirect('/');

        return $this;
	}
	
	/**
     *  Save Customer notification preference for points email
     */
    public function savePrefAction() {
        if ($this->getRequest()->isPost()) {
            $customerSession = Mage::getSingleton('rewards/session')->getSessionCustomer();
            try {
                $data = $this->getRequest()->getPost();
                $sendPointsNotification = isset($data['rewards_points_notification']) ? true : false;
                $customerSession->setRewardsPointsNotification($sendPointsNotification);
                
                $customerSession->save();

                Mage::getSingleton('core/session')->addSuccess($this->__("Your preferences were saved successfully."));
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addException($e, $this->__('There was a problem saving your preferences.'));
                Mage::logException($e);
            }
        }

        $this->_redirect('*/customer/');

        return $this;
    }
}

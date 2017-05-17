<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AlsoViewed
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_AlsoViewed_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case {

    public function setup() {

        AW_AlsoViewed_Test_Model_Mocks_Foreignresetter::dropForeignKeys();
        parent::setup();
    }

     
    /**
     *  @test
     *  @dataProvider provider__onViewProduct
     *  @loadFixture
     * 
     */
    
    public function onViewProduct($productIds) {
     
        foreach($productIds as $id) {            
            
            $product = new Varien_Object();
            $product->setId($id);
            
            $observer = new Varien_Object();
            $observer->setProduct($product);
            
           Mage::getModel('aw_alsoviewed/observer')->onViewProduct($observer);
    
        }
        session_start();
        $this->assertEquals(Mage::getModel('aw_alsoviewed/history')->getCollection()->count(),count($productIds));
       
    }
    
    public function provider__onViewProduct() {
        
        return array(
            
            array(array(1,2,3,4,5,6,7,8,9,10))
            
            
        );
        
        
    }
    
    

}
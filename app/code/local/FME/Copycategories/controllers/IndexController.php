<?php
class FME_Copycategories_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        
        /*
        * Load an object by id 
        * Request looking like:
        * http://site.com/copycategories?id=15 
        *  or
        * http://site.com/copycategories/id/15 	
        */
        /* 
        $copycategories_id = $this->getRequest()->getParam('id');

        if($copycategories_id != null && $copycategories_id != '')	{
        $copycategories = Mage::getModel('copycategories/copycategories')->load($copycategories_id)->getData();
        } else {
        $copycategories = null;
        }	
        */
        
        /*
        * If no param we load a the last created item
        */ 
        /*
        if($copycategories == null) {
        $resource = Mage::getSingleton('core/resource');
        $read= $resource->getConnection('core_read');
        $copycategoriesTable = $resource->getTableName('copycategories');
			
        $select = $read->select()
        ->from($copycategoriesTable,array('copycategories_id','title','content','status'))
        ->where('status',1)
        ->order('created_time DESC') ;
			   
        $copycategories = $read->fetchRow($select);
        }
        Mage::register('copycategories', $copycategories);
        */

            
        $this->loadLayout();     
        $this->renderLayout();
    }
}
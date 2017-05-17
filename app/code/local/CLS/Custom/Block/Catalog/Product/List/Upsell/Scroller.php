<?php

class CLS_Custom_Block_Catalog_Product_List_Upsell_Scroller extends Mage_Catalog_Block_Product_List_Upsell
{
    public function getProductCollection()
    {
        return $this->getItemCollection();
    }
}

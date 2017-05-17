<?php

class CLS_Custom_Block_Alsoviewed_Products_Scroller extends AW_AlsoViewed_Block_Products
{
    public function getProductCollection()
    {
        return $this->getAlsoViewedCollection();
    }
}

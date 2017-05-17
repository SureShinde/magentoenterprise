<?php

class CLS_Custom_Block_Relatedproducts_Relatedproducts_Scroller extends AW_Relatedproducts_Block_Relatedproducts
{
    public function getProductCollection()
    {
        return $this->getRelatedProductsCollection();
    }
}

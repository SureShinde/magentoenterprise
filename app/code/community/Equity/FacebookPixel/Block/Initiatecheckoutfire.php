<?php
class Equity_FacebookPixel_Block_InitiateCheckoutFire extends Equity_FacebookPixel_Block_Abstract
{

    protected function _canShow() {
        return $this->_getConfigHelper()->isInitiateCheckoutEnabled();
    }

}
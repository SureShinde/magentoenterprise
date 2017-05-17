<?php

class SmartOsc_Catalog_Block_Badges extends Nucleus_Catalog_Block_Badges
{
    protected function _construct()
    {
        parent::_construct();
    }

    public function getAllBadges()
    {
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'badges');

        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
            foreach ($options as $option)
            {
                $badges[] = $option['label'];
            }
        }
        return $badges;
    }
}
<?php

class TBT_Milestone_Block_Manage_Grid_Renderer_Milestone_Condition extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $milestoneDetails = $this->_getValue($row);
        
        $milestoneDetailsDecoded = json_decode($milestoneDetails, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            $milestoneDetails = $milestoneDetailsDecoded;
        }
        
        // if $milestoneDetails doesn't exist, it's an older version
        if (is_null($milestoneDetails)) {
            return 'Data Not Available.';
        }

        return $milestoneDetails['condition']['message'];
    }
}

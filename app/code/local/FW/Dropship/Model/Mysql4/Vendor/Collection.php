<?php
class FW_Dropship_Model_Mysql4_Vendor_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        //parent::__construct();
        $this->_init('dropship/vendor');
    }
}
?>

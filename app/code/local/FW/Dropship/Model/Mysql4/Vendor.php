<?php
class FW_Dropship_Model_Mysql4_Vendor extends Mage_Core_Model_Mysql4_Abstract {
	function _construct() {
		$this->_init('dropship/vendor', 'dropship_vendor_id');
	}
}

?>

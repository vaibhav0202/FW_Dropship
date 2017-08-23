<?php

class FW_Dropship_Model_Observer {
	
	protected $customer;
	
	/**
	 * Description - After the order is placed, this function is called in order to send the vendor a work order email if there are products that were ordered 
	 * belonging to that vendor.
	 * 
	 */
	public function emailVendorAfterOrder($observer)
	{
		$vendorEmails			= array();
		
		//GET ORDER IDs
		$orderIds = $observer->getOrderIds();
		
		if (!empty($orderIds) && is_array($orderIds))
		{
			foreach ($orderIds as $oid){
				
				//LOAD ORDER MAGE MODEL
				$order = Mage::getSingleton('sales/order');
				//$order = Mage::getModel('sales/order')->load($oid); 

				if ($order->getId() != $oid) $order->reset()->load($oid);
				
				Mage::getModel('dropship/vendor')->processDropShipVendorEmail($order);
				
			}
		}
	}	//End of function hookToOrderSaveEvent

	public function completeFailedOrderEmailVendorAfterOrder($observer)
	{
		//GET ORDER
		$order = $observer->getOrder();
		Mage::getModel('dropship/vendor')->processDropShipVendorEmail($order);
	}
}
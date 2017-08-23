<?php
class FW_Dropship_Model_Vendor extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_init('dropship/vendor');
    }

    public function processDropShipVendorEmail($order){
    	// Check if customer was logged in during order - grab shipping address
    	if( isset( $order->getShippingAddressId ) ) {
    		$shippingAddressId 	= $order->getShippingAddressId;
    		$shipping_address 	= Mage::getModel('customer/address')->load($shippingAddressId);
    	} else {
    		$shipping_address 	= $order->getShippingAddress();
    	}
    	
    	$order_id 		= $order->getIncrementId();
    	//$curr_store 	= Mage::app()->getStore()->getName();
    	$storeData		= Mage::getModel('core/store')->load($order->getStoreId());
	$curr_store 		= $storeData->getName();
    	$fromEmail           = Mage::getStoreConfig('trans_email/ident_support/email', $order->getStoreId());
    	$customerName        = $order->getCustomerFirstname() . " " . $order->getCustomerLastname();
    	$customer_email 	= $order->getCustomerEmail();
    	
    	try
    	{
    		$items = $order->getAllItems();
    		//Check each product that was ordered to see if and what vendor it belongs to.
    		foreach ($items as $itemId => $item) {
    			$product = Mage::getModel('catalog/product')->load($item->getProductId());
    	
    			$vendorId = $product->getDropshipVendorId();
    	
    			//CHECK IF ITEM IS CONFIGURABLE - IF SO LOAD ASSOCIATED DROPSHIPSKU IF AVAILABLE
    			$children = $item->getChildrenItems();
    	
    			if(count($children) && ($product->getData('has_options') == 1))
    			{
    				foreach($children as $child)
    				{
    					$child_product = Mage::getModel('catalog/product')->load($child->getProductId());
    					$productVendorSku = $child_product->getDropShipSku();
    				}
    					
    			}else{
    				$productVendorSku = $product->getDropShipSku();
    			}
    	
    			$vendor = Mage::getModel('dropship/vendor');
    	
    			if ( $vendorId > 0) {
    				$vendor->load($vendorId);
    				$shippingMethod = $vendor->getShippingCarrier() . " " . $vendor->getShippingMethod();
    					
    				// check if dropshipper ID has already appeared in the order
    				//If Vendor exists in the array, just add the new product line to the email body.
    				if (isset($vendorEmails[$vendorId])) {
    					$vendorEmails[$vendorId]['message'] .=
    					'Qty: ' 				. $item->getQtyOrdered() . "\r\n" .
    					'Partner SKU: ' 		. $productVendorSku . "\r\n" .
    					'Name: ' 				. $item->getName() . "\r\n" .
    					'F+W SKU: ' 			. $item->getSku() . "\r\n" . "\r\n";
    				} else {
    					//If the vendor doesn't exist in the array yet, start creating the email for it.
    					$vendorEmails[$vendorId]['subject'] =
    					'Order #' 				. $order_id . $vendor->getVendorPoCode() . " from " . $curr_store;
    	
    					$vendorEmails[$vendorId]['message'] =
    					'Order ID: ' 			. $order_id . $vendor->getVendorPoCode() . "\r\n" .
    					'Email: ' 				. $customer_email;
    					if($shipping_address) {
    						$phoneNumber =  $shipping_address->getTelephone();
    						if($phoneNumber != "" && $phoneNumber != null)
    							$vendorEmails[$vendorId]['message'] .= "\r\n" . "Phone: " . $phoneNumber;
    					}
    						
    					$vendorEmails[$vendorId]['message'] .= "\r\n" . "\r\n" . 'Customer Name: ' . $customerName . "\r\n" . "\r\n";
    						
    					if($shipping_address) {
    						$vendorEmails[$vendorId]['message'] .= 'Shipping Information: ' . "\r\n" .
    								$this->getShippingInformation($shipping_address) . "\r\n" . "\r\n";
    					}
    						
    					$vendorEmails[$vendorId]['message'] .= 'Qty: ' . $item->getQtyOrdered() . "\r\n" .
    							'Partner SKU: ' 		. $productVendorSku . "\r\n" .
    							'Name: ' 				. $item->getName() . "\r\n" .
    							'F+W SKU: ' 			. $item->getSku() . "\r\n" . "\r\n";
    				}
    			}
    		}

    		if(isset($vendorEmails)){
    			//After generating all of the emails to be sent to the vendors, loop through and email the vendor the work order.
    			foreach ( $vendorEmails as $vendorId=>$emailMessage) {
    				$vendor = Mage::getModel('dropship/vendor');
    				$vendor->load($vendorId);
    				$name	= $vendor->getName();
    				 
    				//Email list to email vendor.
    				$emailList	= $vendor->getVendorEmailList();
    				//Email list of who to email when there is an error.
    				$emailErrorList = $vendor->getErrorEmailList();
    				$this->emailDropshipperById( $name, $emailList, $fromEmail, $emailMessage['subject'], $emailMessage['message'], $emailErrorList, $order_id);
    			}
    		}
    	}
    	catch (Exception $e)
    	{
    		//Log that there was an error emailing all of the vendors.
    		$logMessage = "Order # $order_id did not send notifications to dropship items correctly.";
    		Mage::log($logMessage, null, 'FW_VendorEmail.log');
    		Mage::logException($e);
    			
    		//Email that there was an error sending an email to all the dropshippers.
    		$mail = new Zend_Mail();
    		$msg = "Error sending the following message to all of the dropshippers in order $order_id";
    		 
    		//The to email is the stores customer service email because if exception is caught here, no way to tell which vendor caused the issue.
    		$mail->setBodyText($msg);
    		$mail->setFrom($fromEmail, "Magento Dropshipper Email Error")
    		->setSubject("Magento Dropshipper Email Error")
    		->addTo($fromEmail, $fromEmail);
    	
    		$mail->send();
    		return false;
    	}
    }
    
    protected function emailDropshipperById($sendToName, $sendToEmailList, $fromEmail, $subject, $msg, $emailErrorList, $order_id)
    {
    	$mail = new Zend_Mail();
    	$mail->setBodyText($msg);
    	$mail->setFrom($fromEmail, "Dropshipping Support")
    	->setSubject($subject);
    
    	$emailArray = explode(',', $sendToEmailList);
    
    	foreach($emailArray as $emailAddress) {
    		$mail->addTo(trim($emailAddress), $sendToName);
    	}
    
    	try
    	{
    		$mail->send();
    	}
    	//Handles any exceptions on a per vendor basis.
    	catch (Exception $e)
    	{
    		//If exception log the error, and email the vendor error email list.
    		$logMessage = "Order # $order_id did not send order to $sendToName correctly.";
    		Mage::log($logMessage, null, 'FW_VendorEmail.log');
    		 
    		Mage::logException($e);
    		$mail = new Zend_Mail();
    		$msg = "Error sending the following message to the dropshipper:\r\n\r\n" . $msg;
    		$mail->setBodyText($msg);
    		$mail->setFrom($fromEmail, "Magento Dropshipper Email Error")
    		->setSubject("Magento Dropshipper Email Error");
    		 
    		//I save the email list comma separated, so this creates an array using the "," as a separator.
    		$emailErrorListArray = explode(',', $emailErrorList);
    
    		//Add the emails to the mail client.
    		foreach($emailErrorListArray as $emailAddress) {
    			$mail->addTo(trim($emailAddress), $emailAddress);
    		}
    
    		//Possible we are in this catch block due to an error in the email server, in which this won't work.
    		try {
    			$mail->send();
    		} catch(Exception $e) {
    			$logMessage = "Error message did not send. Problem with email.";
    			Mage::log($logMessage, null, 'FW_VendorEmail.log');
    		}
    	}
    }
    
    protected function getShippingInformation( $shipping_address ) {
    	$the_shipping_address = "";
    	if($shipping_address) {
    		$the_company = $shipping_address->getCompany();
    
    		$the_shipping_address = $shipping_address->getName() . "\r\n";
    		$the_shipping_address .= strlen($the_company) > 1 ? $the_company . "\r\n":'';  # check if company name present
    		$address1 = $shipping_address->getStreet(1);
    		$address2 = $shipping_address->getStreet(2);
    			
    		$the_shipping_address .=
    		$address1 . "\r\n";
    		if($address2) {
    			$the_shipping_address .= $address2 . "\r\n";
    		}
    		$the_shipping_address .= $shipping_address->getCity() . ", " .
    				$shipping_address->getRegionCode() . "  " .
    				$shipping_address->getPostcode() . "\r\n" .
    				$shipping_address->getCountry();
    	}
    	return $the_shipping_address;
    }
}
?>

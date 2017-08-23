<?php
class FW_Dropship_Block_Adminhtml_Vendor_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        //Note that the first paramater is NOT the same FW_Dropship_Block_Vendor_Edit_Form id.  That will cause it to not work.
        $fieldset = $form->addFieldset('edit_vendor_form', array('legend'=>Mage::helper('dropship')->__('Vendor Information')));
       
       //Add all the fields to the form for adding and editing.
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('dropship')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));
        $fieldset->addField('vendor_po_code', 'text', array(
            'label'     => Mage::helper('dropship')->__('Vendor PO Code'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'vendor_po_code',
        ));
        $fieldset->addField('description', 'text', array(
            'label'     => Mage::helper('dropship')->__('Description'),
            'required'  => false,
            'name'      => 'description',
        ));
        $fieldset->addField('address', 'text', array(
            'label'     => Mage::helper('dropship')->__('Address'),
            'required'  => false,
            'name'      => 'address',
        ));
        $fieldset->addField('city', 'text', array(
            'label'     => Mage::helper('dropship')->__('City'),
            'required'  => false,
            'name'      => 'city',
        ));
        
        $fieldset->addField('state_code', 'text', array(
            'label'     => Mage::helper('dropship')->__('State'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'state_code',
        ));
        
        $fieldset->addField('postal_code', 'text', array(
            'label'     => Mage::helper('dropship')->__('Postal Code'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'postal_code',
        ));
        
        
        $fieldset->addField('country_code', 'text', array(
            'label'     => Mage::helper('dropship')->__('Country Code'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'country_code',
        ));
        
        
        
        $carrierEvent = $fieldset->addField('shipping_carrier', 'select', array(
            'label'     => Mage::helper('dropship')->__('Shipping Carrier'),
            'name'      => 'shipping_carrier',
            'values'    => array(
                array(
                    'value'     => 'none',
                    'label'     => Mage::helper('dropship')->__('No Carrier'),
                ),            
                array(
                    'value'     => 'ups',
                    'label'     => Mage::helper('dropship')->__('UPS'),
                ),
 
                array(
                    'value'     => 'fedex',
                    'label'     => Mage::helper('dropship')->__('FedEx'),
                ),
                
                array(
                    'value'     => 'usps',
                    'label'     => Mage::helper('dropship')->__('USPS'),
                ),                
            ),
        ));
       
        
        //Initiate the array that holds the methods for dropdown list and fill it with "No Shipping" in case a vendor ships free
        $noShippingArray = array(array('label'=>'- No Shipping Method -', 'value'=>'0'));
        
        //Grab all of the UPS methods that can be used.
    	$upsMethods = Mage::getModel('fw_shipping/carrier_ups_source_method');
    	$upsArray = $upsMethods->toOptionArray();
		
		//Grab all the FedEx methods that can be used.
    	$fedexMethods = Mage::getModel('fw_shipping/carrier_fedex_source_method');
    	$fedexArray = $fedexMethods->toOptionArray();
    	
    	//Grab all the USPS methods that can be used.
    	$uspsMethods = Mage::getModel('fw_shipping/carrier_usps_source_method');
    	$uspsArray = $uspsMethods->toOptionArray();        
    	        
    	//Merge all the method arrays to make one array for the admin when editing/adding a vendor.
        $methodsArray = array_merge($noShippingArray, $upsArray, $fedexArray, $uspsArray);
       
        
        $fieldset->addField('shipping_method', 'select', array(
            'label'     => Mage::helper('dropship')->__('Shipping Method'),
            'class'     => 'required-entry',
            'id'        => 'shipping_method_id',
            'required'  => true,
            'name'      => 'shipping_method',
            'values'    => $methodsArray,
        ));

        $fieldset->addField('vendor_email_list', 'text', array(
            'label'     => Mage::helper('dropship')->__('Vendor Email List (comma separated)'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'vendor_email_list',
        ));
        $fieldset->addField('error_email_list', 'text', array(
            'label'     => Mage::helper('dropship')->__('Error Log Email List (comma separated)'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'error_email_list',
        ));        
        
        if ( Mage::getSingleton('adminhtml/session')->getVendorData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorData());
            Mage::getSingleton('adminhtml/session')->setVendorData(null);
        } elseif ( Mage::registry('vendor_data') ) {
            $form->setValues(Mage::registry('vendor_data')->getData());
        }
        
        return parent::_prepareForm();
    }
}
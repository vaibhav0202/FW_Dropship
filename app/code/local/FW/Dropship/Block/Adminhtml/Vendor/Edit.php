<?php
class FW_Dropship_Block_Adminhtml_Vendor_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'dropship';
        $this->_controller = 'adminhtml_vendor';
 
        $this->_updateButton('save', 'label', Mage::helper('dropship')->__('Save Vendor'));
        $this->_updateButton('delete', 'label', Mage::helper('dropship')->__('Delete Vendor'));
    }
 
    public function getHeaderText()
    {
        if( Mage::registry('vendor_data') && Mage::registry('vendor_data')->getId() ) {
            return Mage::helper('dropship')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('vendor_data')->getName()));
        } else {
            return Mage::helper('dropship')->__('Add Vendor');
        }
    }
}

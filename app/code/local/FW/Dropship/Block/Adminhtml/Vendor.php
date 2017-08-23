<?php
class FW_Dropship_Block_Adminhtml_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_vendor';
        $this->_blockGroup = 'dropship';
        $this->_headerText = Mage::helper('dropship')->__('Vendor Manager');
        $this->_addButtonLabel = Mage::helper('dropship')->__('Add Vendor');
        parent::__construct();
    }
}

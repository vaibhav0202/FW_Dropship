<?php
 
class FW_Dropship_Block_Adminhtml_Vendor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('vendorGrid');
        
        // This is the primary key of the database and sorting by dropship_vendor_id
        $this->setDefaultSort('dropship_vendor_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('dropship/vendor')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('dropship_vendor_id', array(
            'header'    => Mage::helper('dropship')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'dropship_vendor_id',
        ));
 
        $this->addColumn('name', array(
            'header'    => Mage::helper('dropship')->__('Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));
        
        $this->addColumn('vendor_po_code', array(
        	'header'    => Mage::helper('dropship')->__('Vendor PO Code'),
        	'align'     =>'left',
        	'index'     => 'vendor_po_code',
        ));
 
        $this->addColumn('shipping_carrier', array(
 
            'header'    => Mage::helper('dropship')->__('Shipping Carrier'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'shipping_carrier',
            'type'      => 'options',
            'options'   => array(
                'ups'   => 'UPS',
                'fedex' => 'FedEx',
                'usps'  => 'USPS'
            ),
        ));
        $this->addColumn('shipping_method', array(
 
            'header'    => Mage::helper('dropship')->__('Shipping Method'),
            'align'     => 'left',
            'index'     => 'shipping_method'
        ));
        $this->addColumn('address', array(
            'header'    => Mage::helper('dropship')->__('Address'),
            'align'     => 'left',
            'index'     => 'address',
        ));
 
        $this->addColumn('city', array(
            'header'    => Mage::helper('dropship')->__('City'),
            'align'     => 'left',
            'index'     => 'city',
        ));   
 
 
        $this->addColumn('state_code', array(
 
            'header'    => Mage::helper('dropship')->__('State'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'state_code',
        ));

        $this->addColumn('postal_code', array(
 
            'header'    => Mage::helper('dropship')->__('Postal Code'),
            'align'     => 'left',
            'index'     => 'postal_code',
            'width'     => '80px',
        ));
        
        $this->addColumn('country_code', array(
 
            'header'    => Mage::helper('dropship')->__('Country Code'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'country_code',
        ));
        
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
 }
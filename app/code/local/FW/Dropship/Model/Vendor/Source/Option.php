<?php
/**
 * This class is to grab an array of all the vendor names and id's for the dropdown list when assigning a product to a vendor.
 */
class FW_Dropship_Model_Vendor_Source_Option extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
	
    public function getAllOptions()
    {
        return $this->getOptionFromTable();
    }
 
    private function getOptionFromTable(){
        $return=array();
        $col=Mage::getModel("dropship/vendor")->getCollection();
        //Sort the collection by name so it's easier to find the vendor.
        $col->setOrder('name','ASC');
        $col->load();
        //Push array element for F+W Media Product which is an option for products that aren't dropshipped.
        array_push($return, array('label'=>'F+W Media Product', 'value'=>''));
        
        foreach($col as $row){
            array_push($return,array('label'=>$row->getName(),'value'=>$row->getDropshipVendorId()));
        }
        return $return;
 
    }
 
 	/**
 	 * @param int - the vendor id
 	 * @return String - returns the label of the vendor based on the id. 
 	 */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
       
        foreach ($options as $option) {
            if(is_array($value)){
                if (in_array($option['value'],$value)) {
                    return $option['label'];
                }
            }
            else{
                if ($option['value']==$value) {
                    return $option['label'];
                }
            }
 
        }
        return false;
    }
}
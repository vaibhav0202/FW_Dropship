<?php
class FW_Dropship_Adminhtml_VendorController extends Mage_Adminhtml_Controller_Action
{
   protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('dropship_menu/vendors');
        return $this;
    }  
    
     /** Check ACL permissions
     * @return
     */
    public function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dropship_menu/vendors');
    }
   
    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('dropship/adminhtml_vendor'));
        $this->renderLayout();
    }
    
   
    public function editAction()
    {
        $vendorId     = $this->getRequest()->getParam('id');
        $vendorModel  = Mage::getModel('dropship/vendor')->load($vendorId);
 
        if ($vendorModel->getId() || $vendorId == 0) {
 
            Mage::register('vendor_data', $vendorModel);
 
            $this->loadLayout();
            $this->_setActiveMenu('dropship_menu/vendors');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('dropship/adminhtml_vendor_edit'))
                 ->_addLeft($this->getLayout()->createBlock('dropship/adminhtml_vendor_edit_tabs'));
               
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendor')->__('Vendor does not exist'));
            $this->_redirect('*/*/');
        }
    }
   
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    //Saves either a new vendor or one that is being edited.
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
                $vendorModel = Mage::getModel('dropship/vendor');

                $vendorModel->setDropshipVendorId($this->getRequest()->getParam('id'))
                    ->setName($postData['name'])
                    ->setVendorPoCode($postData['vendor_po_code'])
                    ->setDescription($postData['description'])
                    ->setShippingCarrier($postData['shipping_carrier'])
                    ->setShippingMethod($postData['shipping_method'])
                    ->setAddress($postData['address'])
                    ->setCity($postData['city'])
                    ->setStateCode($postData['state_code'])
                    ->setPostalCode($postData['postal_code'])
                    ->setCountryCode($postData['country_code'])
                    ->setVendorEmailList($postData['vendor_email_list'])
                    ->setErrorEmailList($postData['error_email_list'])
                    ->save();
               
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Vendor was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setVendorData(false);
 
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setVendorData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    //Deletes the vendor that is selected.
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $vendorModel = Mage::getModel('dropship/vendor');
               
                $vendorModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Vendor was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
}

<?php

/**
 * Created by PhpStorm.
 * User: tmills
 * Date: 6/12/2015
 * Time: 3:18 PM
 */
class Aimlessmedia_Groupfeatures_Adminhtml_PriceController extends Mage_Adminhtml_Controller_Action
{

    public function inBulkAction()
    {
        $groupPriceToApply = trim($this->getRequest()->getParam('groupfeatures_priceToApply'));
        $productsToMod_Arr = $this->getRequest()->getParam('product');
        if ($this->getRequest()->getParam('groupfeatures_priceremovegroup')) {
            $groupToMod = trim($this->getRequest()->getParam('groupfeatures_priceremovegroup'));
            Mage::getModel('aimlessmedia_groupfeatures/adminhtml_catalog_product_modifygroups')->bulkRemoveGroupPricing($productsToMod_Arr, $groupToMod, $groupPriceToApply);
        } elseif ($this->getRequest()->getParam('groupfeatures_priceToApply')) {
            $groupToMod = trim($this->getRequest()->getParam('groupfeatures_Selectedgroup'));
            Mage::getModel('aimlessmedia_groupfeatures/adminhtml_catalog_product_modifygroups')->bulkApplyGroupPricing($productsToMod_Arr, $groupToMod, $groupPriceToApply);
        }

        $url = Mage::getModel('adminhtml/url')->getUrl('*/catalog_product', array(
            '_current' => true
        ));
        $this->_redirectUrl($url);
    }
}
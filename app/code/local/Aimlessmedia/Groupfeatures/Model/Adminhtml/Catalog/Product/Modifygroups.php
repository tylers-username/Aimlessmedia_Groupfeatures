<?php

class Aimlessmedia_Groupfeatures_Model_Adminhtml_Catalog_Product_Modifygroups
{
    public function bulkApplyGroupPricing($productsToMod_Arr = array(), $groupToMod = null, $groupPriceToApply = null)
    {
        if (!$this->validateGroupID($groupToMod)) return false;
        if (!is_numeric($groupPriceToApply)) {
            Mage::getSingleton("core/session")->addError("The price you entered appears to be invalid. Please input numbers only.");
            return false;
        }

        $failedIDs = [];
        foreach ($productsToMod_Arr as $prodID) {
            if (!is_numeric($prodID)) {
                $failedIDs[] = $prodID;
                continue;
            }
            $this->applyGroupPrice($prodID, $groupToMod, $groupPriceToApply);
        }

        if (isset($failedIDs[0])) {
            Mage::getSingleton("core/session")->addWarning("Group pricing could not be applied to the following ID(s): " . implode(", ", $failedIDs));
        }
        Mage::getSingleton("core/session")->addNotice("Group price modification process completed.");
        return true;
    }

    public function bulkRemoveGroupPricing($productsToMod_Arr = array(), $groupToMod = null, $groupPriceToApply = null)
    {
        if (!$this->validateGroupID($groupToMod)) return false;

        $failedIDs = [];
        foreach ($productsToMod_Arr as $prodID) {
            if (!is_numeric($prodID)) {
                $failedIDs[] = $prodID;
                continue;
            }
            $this->removeGroupPrice($prodID, $groupToMod, $groupPriceToApply);
        }

        if (isset($failedIDs[0])) {
            Mage::getSingleton("core/session")->addWarning("Group pricing could not be removed from the following ID(s): " . implode(", ", $failedIDs));
        }
        Mage::getSingleton("core/session")->addNotice("Group price removal process completed.");
        return true;
    }

    public function applyGroupPrice($productToMod = null, $groupToMod = null, $groupPriceToApply = null)
    {

        if (!$this->validateGroupID($groupToMod)) return false;
        if (!is_numeric($productToMod)) return false;
        $product = Mage::getModel('catalog/product')->loadByAttribute('entity_id', $productToMod);
        //zend_debug::dump($product);die;
        if ($product == null) {
            Mage::getSingleton("core/session")->addWarning("Group pricing could not be applied to the following ID: $productToMod");
            return false;
        } else {
            $websiteCode = Mage::app()->getRequest()->getParam('website');
            if ($websiteCode) {
                $website = Mage::getModel('core/website')->load($websiteCode);
                $websiteId = $website->getId();
                //do your magic with $websiteId
                Mage::getSingleton("core/session")->addError("Needs modified to ensure proper functionality with multi-website/stores");
                return false;
            } else {
                $group_prices = $product->getData('group_price');
                if (is_null($group_prices)) {
                    $attribute = $product->getResource()->getAttribute('group_price');
                    if ($attribute) {
                        $attribute->getBackend()->afterLoad($product);
                        $group_prices = $product->getData('group_price');
                    }
                }

                if (!is_array($group_prices)) {
                    $group_prices = array();
                }

                $new_price = array(array('website_id' => 0,
                    'cust_group' => $groupToMod,
                    'price' => $groupPriceToApply));

                $group_prices = array_merge($group_prices, $new_price);
                $product->setData('group_price', $group_prices);
                $product->save();
            }
        }

    }

    public function removeGroupPrice($productToMod = null, $groupToMod = null)
    {
        if (!$this->validateGroupID($groupToMod)) return false;
        if (!is_numeric($productToMod)) return false;
        $product = Mage::getModel('catalog/product')->loadByAttribute('entity_id', $productToMod);
        //zend_debug::dump($product);die;
        if ($product == null) {
            Mage::getSingleton("core/session")->addWarning("Group pricing could not be removed from the following ID: $productToMod");
            return false;
        } else {
            $websiteCode = Mage::app()->getRequest()->getParam('website');
            if ($websiteCode) {
                $website = Mage::getModel('core/website')->load($websiteCode);
                $websiteId = $website->getId();
                //do your magic with $websiteId
                Mage::getSingleton("core/session")->addError("Needs modified to ensure proper functionality with multi-website/stores");
                return false;
            } else {
                $group_prices = $product->getData('group_price');
                if (is_null($group_prices)) {
                    $attribute = $product->getResource()->getAttribute('group_price');
                    if ($attribute) {
                        $attribute->getBackend()->afterLoad($product);
                        $group_prices = $product->getData('group_price');
                    }
                }

                if (!is_array($group_prices)) {
                    $group_prices = array();
                }

                foreach ($group_prices as $parentArrayKey => $priceArray) {
                    if ($priceArray['website_id'] == 0 && $priceArray['cust_group'] == $groupToMod) {
                        unset($group_prices[$parentArrayKey]);
                        break;
                    }
                }
                $product->setData('group_price', $group_prices);
                $product->save();
            }
        }
    }

    public function validateGroupID($groupToMod = null)
    {
        if (!is_numeric($groupToMod)) {
            Mage::getSingleton("core/session")->addError("The group field seems to be invalid. Please ensure that a group was selected.");
            return false;
        }
        return true;
    }
}
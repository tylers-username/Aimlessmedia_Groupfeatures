<?php

class Aimlessmedia_Groupfeatures_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();

        $groups = $this->helper('customer')->getGroups()->toOptionArray();
        //zend_debug::dump($groups);
        $this->getMassactionBlock()->addItem('groupfeatures_setGroupPrice', array(
                'label' => Mage::helper('catalog')->__('Apply Group Price'),
                'url' => $this->getUrl('*/price/inbulk', array('_current' => true)),
                //'confirm' => $this->__("It is recommended that product indexing be disabled before continue"),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'groupfeatures_Selectedgroup',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('customer')->__('Group'),
                        'values' => $groups
                    ),
                    'additional'=>array(
                        'name' => 'groupfeatures_priceToApply',
                        'type' => 'text',
                        'class' => 'validate-number required-entry',
                        'required' => true,
                        'label' => Mage::helper('customer')->__('Group Price'),
                        'value' => ""

                    )
                )
            )
        )->addItem('groupfeatures_removeGroupPrice', array(
                'label' => Mage::helper('catalog')->__('Remove Group from Pricing'),
                'url' => $this->getUrl('*/price/inbulk', array('_current' => true)),
                //'confirm' => $this->__("It is recommended that product indexing be disabled before continue"),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'groupfeatures_priceremovegroup',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('customer')->__('Group'),
                        'values' => $groups
                    )
                ),
                'confirm' => "WARNING: This will remove the price from the selected group from all checked products."
            )
        );

    }
}
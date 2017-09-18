<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_DynamicCategory
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * Product Condition Class
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 *
 * @method string getAttribute()
 */
class FireGento_DynamicCategory_Model_Rule_Condition_Child_Product
    extends FireGento_DynamicCategory_Model_Rule_Condition_Product
{
    /**
     * Add some special attributes to the attribute list
     *
     * @param array &$attributes Attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);

        foreach ($attributes as $attrCode => $label) {
            $attributes[$attrCode] = $this->getHelper()->__('Child %s', $label);
        }
    }

    /**
     * Validate product attribute value for condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        if ('configurable' == $object->getTypeId()) {
            return $this->_validateConfigurableChildren($object);
        }

        if ('grouped' == $object->getTypeId()) {
            return $this->_validateGroupedChildren($object);
        }

        return false;
    }

    /**
     * Load and validate product children
     * @param Varien_Object $object
     * @return bool
     */
    protected function _validateGroupedChildren(Varien_Object $object)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('catalog/product_collection');

        $productCollection->getSelect()
            ->joinLeft(
                array('link' => $this->getResource()->getTableName('catalog/product_link')),
                'e.entity_id = link.linked_product_id',
                array()
            )
            ->joinLeft(
                array('type' => $this->getResource()->getTableName('catalog/product_link_type')),
                'link.link_type_id = type.link_type_id',
                array()
            )
            ->where('type.code = ?', 'super')
            ->where('link.product_id = ?', $object->getId());

        $this->collectValidatedAttributes($productCollection);

        foreach ($productCollection as $child) {
            $valid = parent::validate($child);
            if ($valid) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Varien_Object $object
     * @return bool
     */
    public function _validateConfigurableChildren(Varien_Object $object)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('catalog/product_collection');

        $productCollection->getSelect()
            ->joinLeft(
                array('super' => $this->getResource()->getTableName('catalog/product_super_link')),
                'e.entity_id = super.product_id',
                array()
            )
            ->where('super.parent_id = ?', $object->getId());

        $this->collectValidatedAttributes($productCollection);

        foreach ($productCollection as $child) {
            $valid = parent::validate($child);
            if ($valid) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Varien_Db_Adapter_Interface
     */
    public function getAdapter()
    {
        return $this->getResource()->getConnection('read');
    }

    /**
     * @return Mage_Core_Model_Abstract|Mage_Core_Model_Resource
     */
    public function getResource()
    {
        return Mage::getSingleton('core/resource');
    }
}

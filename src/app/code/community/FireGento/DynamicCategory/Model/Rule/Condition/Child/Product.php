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
     * Validate product attrbute value for condition
     *
     * @param  Varien_Object $object Object
     * @return boolean True/False
     */
    public function validate(Varien_Object $object)
    {
        $valid = parent::validate($object);
        if ($valid) {

            // fetch all parent product ids
            $select = $this->getAdapter()->select()
                ->from($this->getResource()->getTableName('catalog/product_link'), array('product_id'))
                ->where('linked_product_id = ?', $object->getId());

            $parentProductIds = (array)$this->getAdapter()->fetchCol($select);
            $object->setDynamicCategoryProductIds($parentProductIds);
        }
        return $valid;
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

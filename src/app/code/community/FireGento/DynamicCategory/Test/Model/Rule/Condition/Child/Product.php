<?php

class FireGento_DynamicCategory_Test_Model_Rule_Condition_Child_Product extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @loadFixture ~FireGento_DynamicCategory/bigdata
     * @dataProvider dataProvider
     */
    public function testValidate($conditions, $expectedProductIds)
    {
        $rule = Mage::getSingleton('dynamiccategory/rule');

        $rule->setData('conditions', $conditions);
        $rule->loadPost(array('conditions' => $conditions));
        $rule->setWebsiteIds('1');
        $ids = $rule->getMatchingProductIds();

        $this->assertEquals($expectedProductIds, $ids);
    }
}
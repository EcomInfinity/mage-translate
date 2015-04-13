<?php
    class Ecominfinity_Translator_Model_Resource_Cmspage extends Mage_Cms_Model_Resource_Page{

    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /*
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */
        foreach (array('custom_theme_from', 'custom_theme_to') as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->formatDate($value));
        }

        // if (!$this->getIsUniquePageToStores($object)) {
        //     Mage::throwException(Mage::helper('cms')->__('A page URL key for specified store already exists.'));
        // }

        if (!$this->isValidPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cms')->__('The page URL key contains capital letters or disallowed symbols.'));
        }

        if ($this->isNumericPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cms')->__('The page URL key cannot consist only of numbers.'));
        }

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return Mage_Core_Model_Resource_Db_Abstract::_beforeSave($object);
    }
}

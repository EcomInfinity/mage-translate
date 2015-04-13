<?php
    class Ecominfinity_Translator_IndexController extends Mage_Core_Controller_Front_Action{
        public function indexAction(){
            $modules = Mage::getConfig()->getNode('modules')->children();
            $result = Mage::helper('translator')->objectToArray($modules);
            var_dump($result);
        }
    }
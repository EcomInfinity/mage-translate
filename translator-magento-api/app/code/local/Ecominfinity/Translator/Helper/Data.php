<?php
class Ecominfinity_Translator_Helper_Data extends Mage_Core_Helper_Data
{
    public function objectToArray($object){
            $object=(array)$object;
            foreach($object as $k=>$value){
                if( gettype($value)=='object' || gettype($value)=='array' ){
                    $object[$k]=(array)($this->objectToArray($value));
                }
            }
            return $object;
        } 
}
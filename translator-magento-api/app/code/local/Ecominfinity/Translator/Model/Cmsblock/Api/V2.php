<?php
    class Ecominfinity_Translator_Model_Cmsblock_Api_V2 extends Ecominfinity_Translator_Model_Cmsblock_Api{
        
         public function info($blockId){
        $block = Mage::getModel('cms/block')->load($blockId)->getData();
        foreach($block['store_id'] as $store_id){
                $store = Mage::getModel('core/store')->load($store_id)->getData();
                $block['store_code'][$store_id]= $store['code'];
            }
        return $block;
        }
        
        public function items(){
            $cms_blocks = Mage::getResourceModel('cms/block_collection');
            $blocks = array();
            foreach($cms_blocks as $cms_block){
                $blockId = $cms_block->getBlockId();
                $blocks[] = Mage::getModel('cms/block')->load($blockId)->getData();
            }
            foreach($blocks as &$block){
                    foreach($block['store_id'] as $store_id){
                        $store = Mage::getModel('core/store')->load($store_id)->getData();
                        $block['store_code'][$store_id]=$store['code'];
                    }
                }
            return $blocks;
        }

        public function create($data){
            $block_data = (array)$data;
            try{
                $block = Mage::getModel('cms/block')->setData($block_data);
                $block->save();
            }catch(Mage_Core_Exception $e){
                $this->_fault('data_invalid');
            }
            return $block->getId();
        }

        public function update($blockId,$data){
            $block_data = (array)$data;
            try{
                $block = Mage::getSingleton('cms/block')->load($blockId);
                $block ->addData($block_data);
                $block ->save();
            }catch(Mage_Core_Exception $e){
                $this->_fault('data_invalid');
            }
            return true;
        }

        public function delete($blockId){
            $block = Mage::getModel('cms/block')->load($blockId);
            try{
                $block->delete();
            }catch(Mage_Core_Exception $e){
                return $this->_fault('not_delete',$e->getMessage());
            }
                
            return true;
        }
    }
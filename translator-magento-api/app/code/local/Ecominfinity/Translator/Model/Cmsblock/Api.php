<?php
    class Ecominfinity_Translator_Model_Cmsblock_Api extends Mage_Api_Model_Resource_Abstract{
    
        public function info($blockId){
            $block = Mage::getModel('cms/block')->load($blockId)->getData();
            foreach($block['store_id'] as $store_id){
                    $store = Mage::getModel('core/store')->load($store_id)->getData();
                    $block['store_code'][$store_id]= $store['code'];
                }
            return json_encode($block);
            }
            
        public function items(){
            $cms_blocks = Mage::getResourceModel('cms/block_collection');
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
            return json_encode($blocks);
        }

        public function create($datas){
            try{
                foreach($datas as $key=>$data){
                    $block = Mage::getModel('cms/block')->setData($data);
                    $block->save();
                    $blockId[] = $block->getId();
                }
            }catch(Mage_Core_Exception $e){
                $this->_fault('data_invalid', $e->getMessage());
            }
            return $blockId;
        }

        public function update($datas){
            foreach($datas as $key=>$data){
                $blockId = $data['block_id'];
                $block = Mage::getModel('cms/block')->load($blockId);
                if(!$block->getId()){
                    $this->_fault('data_not_exists');
                }
                try{
                    $block ->addData($data);
                    $block ->save();
                }catch(Mage_Core_Exception $e){
                    $this->_fault('data_invalid', $e->getMessage());
                }
            }
            return true;
        }

        public function delete($blockId){
            $block = Mage::getModel('cms/block')->load($blockId);
            if(!$block->getId()){
                $this->_fault('data_not_exists');
            }
            try{
                $block->delete();
            }catch(Mage_Core_Exception $e){
                $this->_fault('not_delete',$e->getMessage());
            }
            return true;
        }
            
    }
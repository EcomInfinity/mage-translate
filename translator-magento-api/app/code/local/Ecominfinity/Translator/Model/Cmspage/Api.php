<?php
    class Ecominfinity_Translator_Model_Cmspage_Api extends Mage_Api_Model_Resource_Abstract{
        
        public function info($pageId){
            $page = Mage::getModel('cms/page')->load($pageId)->getData();
            foreach($page['store_id'] as $store_id){
                $store = Mage::getModel('core/store')->load($store_id)->getData();
                $page['store_code'][$store_id]= $store['code'];
            }
            return json_encode($page);
        }

        public function items(){
            $cms_pages = Mage::getResourceModel('cms/page_collection');
            foreach($cms_pages as $cms_page){
                $pageId = $cms_page ->getPageId();
                $pages[] = Mage::getModel('cms/page')->load($pageId)->getData();
            }
            foreach($pages as &$page){
                foreach($page['store_id'] as $store_id){
                    $store = Mage::getModel('core/store')->load($store_id)->getData();
                    $page['store_code'][$store_id]=$store['code'];
                }
            }
            return json_encode($pages);
        }

        public function create($datas){
            try{
                foreach($datas as $key =>$data){
                    $page = Mage::getModel('cms/page')->setData($data);
                    $page->save();
                    $pageId[] = $page->getId();
                }
            }catch(Mage_Core_Exception $e){
                return $this->_fault('data_invalid',$e->getMessage());
            }
            return $pageId;
        }

        public function update($datas){
            foreach($datas as $key =>$data){
                $pageId = $data['page_id'];
                $page = Mage::getModel('cms/page')->load($pageId);
                if(!$page->getId()){
                    $this->_fault('data_not_exists');
                }
                try{
                    $page ->addData($data);
                    $page ->save();
                }catch(Mage_Core_Exception $e){
                    return $this->_fault('data_invalid',$e->getMessage());
                }
            }
            return true;
        }

        public function delete($pageId){
            $page = Mage::getModel('cms/page')->load($pageId);
            if(!$page->getId()){
                $this->_fault('data_not_exists');
            }
            try{
                $page->delete();
            }catch(Mage_Core_Exception $e){
                return $this->_fault('not_delete',$e->getMessage());
            }
            
            return true;
        }
    }
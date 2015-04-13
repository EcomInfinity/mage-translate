<?php
    class Ecominfinity_Translator_Model_Cmspage_Api_V2 extends Ecominfinity_Translator_Model_Cmspage_Api{
        
        public function info($pageId){
            $page = Mage::getModel('cms/page')->load($pageId)->getData();
            foreach($page['store_id'] as $store_id){
                $store = Mage::getModel('core/store')->load($store_id)->getData();
                $page['store_code'][$store_id]= $store['code'];
            }
            return $page;
        }

        public function items(){
            $cms_pages = Mage::getResourceModel('cms/page_collection');
            $pages = array();
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
            return $pages;
        }

        public function create($data){
            $page_data = (array)$data;
            $page = Mage::getModel('cms/page');
            try{
                $page->setData($page_data);
                $page->save();
            }catch(Mage_Core_Exception $e){
                return $this->_fault('data_invalid');
            }
            return $page->getId();
        }
        
        public function update($pageId,$data){
            $page_data = (array)$data;
            try{
                $page = Mage::getSingleton('cms/page')->load($pageId);
                $page ->addData($page_data);
                $page ->save();
            }catch(Mage_Core_Exception $e){
                return $this->_fault('data_invalid');
            }
            return true;
        }

        public function delete($pageId){
            $page = Mage::getModel('cms/page')->load($pageId);
            try{
                $page->delete();
            }catch(Mage_Core_Exception $e){
                return $this->_fault('not_delete',$e->getMessage());
            }
            
            return true;
        }
    }
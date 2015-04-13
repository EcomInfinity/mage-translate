<?php
    class Ecominfinity_Translator_Model_Getwebinfo_Api_V2 extends Ecominfinity_Translator_Model_Getwebinfo_Api{
        
         public function items(){
            $websites = Mage::app()->getWebsites();
                foreach($websites as $key=>$website){
                    $website_name = $website->getName();
                    $website_id = $website->getId();
                    $website_code = $website->getCode();
                    $stores = $website->getGroups();
                    $webinfo[$key] = array('website_id'=>$website_id,'website_name'=>$website_name,'website_code'=>$website_code);
                    foreach($stores as $store_key=>$store){
                        $store_id = $store->getId();
                        $store_name = $store->getName();
                        $store_views = $store->getStores();
                        $webinfo[$key]['stores'][$store_key] = array('store_id'=>$store_id,'store_name'=>$store_name);
                        foreach ($store_views as $store_view_key=> $store_view) {
                           $store_view_id = $store_view->getId();
                           $store_view_name = $store_view->getName();
                           $store_view_code = $store_view->getCode();
                           $webinfo[$key]['stores'][$store_key][$store_view_key] = array('store_view_id'=>$store_view_id,'store_view_name'=>$store_view_name,'store_view_code'=>$store_view_code);
                        }
                    }
                }
            return $webinfo;
        }

        public function info($website_id){
            $website = Mage::app()->getWebsite($website_id);
            $website_name = $website->getName();
            $website_id = $website->getId();
            $website_code = $website->getCode();
            $stores = $website ->getGroups();
            $webinfo = array('website_id'=>$website_id,'website_name'=>$website_name,'website_code'=>$website_code);
                foreach($stores as $store_key=>$store){
                            $store_id = $store->getId();
                            $store_name = $store->getName();
                            $store_views = $store->getStores();
                            $webinfo['website_stores'][$store_key] = array('store_id'=>$store_id,'store_name'=>$store_name);
                            foreach ($store_views as $store_view_key=> $store_view) {
                               $store_view_id = $store_view->getId();
                               $store_view_name = $store_view->getName();
                               $store_view_code = $store_view->getCode();
                               $webinfo['website_stores'][$store_key][$store_view_key] = array('store_view_id'=>$store_view_id,'store_view_name'=>$store_view_name,'store_view_code'=>$store_view_code);
                            }
                }
                return $webinfo;
        }

        public function storeViewList(){
            $stores = Mage::app()->getStores();
            $store_views = array();
            foreach ($stores as $store) {
                $store_views[] = array(
                    'store_id'    => $store->getId(),
                    'code'        => $store->getCode(),
                    'website_id'  => $store->getWebsiteId(),
                    'group_id'    => $store->getGroupId(),
                    'name'        => $store->getName(),
                    'sort_order'  => $store->getSortOrder(),
                    'is_active'   => $store->getIsActive()
                );
            }
            $store = array();
            foreach($store_views as $store_view){
                $store_view_id = $store_view['store_id'];
                $localeCode = Mage::getStoreConfig('general/locale/code',$store_view_id);
                $store_view['store_view_language'] = $localeCode;
                $store[] = $store_view;
            }
            return $store;
        }

        public function storeViewInfo($storeId){
            try {
                $store = Mage::app()->getStore($storeId);
            } catch (Mage_Core_Exception $e) {
                return $this->_fault('data_not_exists');
            }
            if (!$store->getId()) {
                return $this->_fault('data_not_exists');
            }
            $result = array();
            $result['store_id'] = $store->getId();
            $result['code'] = $store->getCode();
            $result['website_id'] = $store->getWebsiteId();
            $result['group_id'] = $store->getGroupId();
            $result['name'] = $store->getName();
            $result['sort_order'] = $store->getSortOrder();
            $result['is_active'] = $store->getIsActive();
            $result['store_view_language'] = Mage::getStoreConfig('general/locale/code',$result['store_id']);
            return $result;
        }

    }
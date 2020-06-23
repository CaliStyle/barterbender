<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 9:50 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Service_Product_Product extends Phpfox_Service
{
    /**
     * @return string
     */
    public function getSTable()
    {
        return $this->_sTable;
    }

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ecommerce_product');
    }

    public function getStoreIdByProductId($iProductId)
    {
        return $this->database()->select('st.store_id')
            ->from(Phpfox::getT('ynstore_store'), 'st')
            ->join($this->_sTable, 'ecp', 'ecp.item_id = st.store_id')
            ->where('ecp.product_id = '.$iProductId)
            ->execute('getSlaveField');
    }

    public function getManageProducts($aConds = array(), $iPage = 0, $iLimit = NULL, $order_by = 'ecp.product_id DESC')
    {
        $sWhere = '';
        $sWhere .= "ecp.product_status <> 'deleted' AND ecp.product_creating_type = 'ynsocialstore_product' AND ecp.module_id = 'ynsocialstore'";
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        $iCount = $this->database()
            ->select("COUNT(DISTINCT ecp.product_id)")
            ->from($this->getSTable(), 'ecp')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = ecp.item_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'eccd', 'ecp.product_id = eccd.product_id AND eccd.product_type = \'ynsocialstore_product\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ecc', 'ecc.category_id = eccd.category_id')
            ->where($sWhere)
            ->execute("getSlaveField");

        $aProducts = array();
        if ($iCount) {
            $aProducts = $this->database()
                ->select(' DISTINCT (ecp.product_id), ecp.product_quantity_main, ecp.product_price, ecp.name, ecp.product_status, ecp.feature_start_time, ecp.feature_end_time, ecp.product_creation_datetime as time_stamp, ecp.product_quantity, st.name as store_name, st.status as store_status, st.store_id, ecc.title as category_name, ecp.user_id,ecp.total_orders,eps.product_type')
                ->from($this->getSTable(), 'ecp')
                ->join(Phpfox::getT('ecommerce_product_ynstore'),'eps','eps.product_id = ecp.product_id')
                ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = ecp.item_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'eccd', 'ecp.product_id = eccd.product_id AND eccd.product_type = \'ynsocialstore_product\' AND eccd.is_main = 1')
                ->join(Phpfox::getT('ecommerce_category'), 'ecc', 'ecc.category_id = eccd.category_id')
                ->where($sWhere)
                ->order($order_by)
                ->limit($iPage, $iLimit, $iCount)
                ->execute("getSlaveRows");

            foreach ($aProducts as $key => $aItem)
            {
                if ($aItem['feature_end_time'] == 1 || ($aItem['feature_start_time'] <= PHPFOX_TIME && $aItem['feature_end_time'] >= PHPFOX_TIME)) {
                    $aProducts[$key]['is_featured'] = 1;
                } else {
                    $aProducts[$key]['is_featured'] = 0;
                }
                $aProducts[$key]['product_status'] = Phpfox::getService('ynsocialstore.helper')->getProductStatus($aItem['product_status']);
                $aProducts[$key]['remaining'] = $aItem['product_quantity'] ;
            }
        }


        return array($iCount, $aProducts);
    }

    public function getFeaturedProductByStoreId($iLimit, $iStoreId = 0)
    {
        if (!$iStoreId) return array();

        $iCount = $this->database()->select('COUNT(ecp.product_id)')
            ->from($this->getSTable(), 'ecp')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = ecp.item_id')
            ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'eps.product_id = ecp.product_id')
            ->where('ecp.product_status = "running" AND st.status = "public" AND ecp.module_id ="ynsocialstore" AND (ecp.feature_start_time <= '.PHPFOX_TIME.' AND ecp.feature_end_time >= '.PHPFOX_TIME . ' OR ecp.feature_end_time = 1) AND ecp.product_creating_type = "ynsocialstore_product" AND st.store_id = '.$iStoreId)
            ->order('RAND()')
            ->group('ecp.product_id')
            ->limit($iLimit)
            ->execute('getSlaveField');

        $aRows = array();

        if ($iCount) {
            $aRows = $this->database()->select('ect.description_parsed as short_description, ecp.product_id, eps.link, ecp.server_id, ecp.logo_path, ecp.total_orders, ecp.product_price, ecp.name as product_name, eps.discount_price, eps.discount_percentage, eps.rating, eu.title as uom_title, (ecp.product_price - eps.discount_price) as discount_display, eps.discount_start_date, eps.discount_end_date, eps.discount_timeless')
                ->from($this->getSTable(),'ecp')
                ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = ecp.item_id')
                ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ecp.uom_id')
                ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'eps.product_id = ecp.product_id')
                ->join(Phpfox::getT('ecommerce_product_text'), 'ect', 'ect.product_id = ecp.product_id')
                ->where('ecp.product_status = "running" AND ecp.module_id ="ynsocialstore" AND (ecp.feature_start_time <= '.PHPFOX_TIME.' AND ecp.feature_end_time >= '.PHPFOX_TIME . ' OR ecp.feature_end_time = 1) AND ecp.product_creating_type = "ynsocialstore_product" AND st.store_id = '.$iStoreId)
                ->order('RAND()')
                ->group('ecp.product_id')
                ->limit($iLimit)
                ->execute('getSlaveRows');

            foreach($aRows as $key => $aRow)
            {
                $aRows[$key]['has_attribute'] = false;
                if(($iPrice = (int)$this->getDisplayPriceByAttribute($aRow['product_id'])) > 0)
                {
                    $aRows[$key]['discount_display'] = $aRows[$key]['product_price'] = $iPrice;
                    if($aRow['discount_percentage'] && ($aRow['discount_timeless'] || ($aRow['discount_start_date'] <= PHPFOX_TIME && $aRow['discount_end_date'] >= PHPFOX_TIME))){
                        $aRows[$key]['product_price'] = round($iPrice*100/(100 - $aRow['discount_percentage']),2);
                    }
                    $aRows[$key]['has_attribute'] = true;
                }
            }
        }

        return $aRows;
    }

    public function getProductForDetailById($iProductId)
    {
        if (!is_numeric($iProductId) && $iProductId <= 0) {
            return null;
        }

        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' = ecp.user_id OR (f.user_id = ecp.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }

        if (Phpfox::isModule('like'))
        {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynsocialstore_product\' AND l.item_id = ecp.product_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('ecp.*, eps.*, (ecp.product_price - eps.discount_price) as discount_display, eps.*,ecpt.description, ecpt.description_parsed, st.store_id, st.logo_path as store_logo, st.server_id AS store_server_id, st.name as store_name,st.status as store_status, st.email, st.ship_payment_info, st.buyer_protection, st.return_policy, st.rating as store_rating, st.total_products, st.privacy as store_privacy, eu.title as uom_title,'.Phpfox::getUserField())
            ->from($this->getSTable(),'ecp')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = ecp.item_id')
            ->join(Phpfox::getT('ecommerce_product_text'),'ecpt','ecp.product_id = ecpt.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ecp.user_id')
            ->where('ecp.product_status <> "deleted" AND ecp.module_id ="ynsocialstore" AND st.status <> "deleted" AND ecp.product_id = '.$iProductId)
            ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ecp.uom_id')
            ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'eps.product_id = ecp.product_id')
            ->execute('getRow');

        if (empty($aRow)) return null;

        $aRow['location'] = $this ->database()->select('*')
                                        ->from(Phpfox::getT('ynstore_store_location'))
                                        ->where("store_id = ". (int)$aRow['store_id'])
                                        ->limit(1)
                                        ->execute('getSlaveRow');

        $aInfos = $this->database() ->select('ynsinfo.info, ynsinfo.type, ynsinfo.title')
                                    ->from(Phpfox::getT('ynstore_store_infomation'), 'ynsinfo')
                                    ->where("ynsinfo.type <> 'addinfo' AND ynsinfo.store_id = ". $aRow['store_id'])
                                    ->group('ynsinfo.type')
                                    ->execute('getSlaveRows');

        $aRow['images'] = $this->getImages($aRow['product_id']);

        $aRow['product_status_display'] = Phpfox::getService('ynsocialstore.helper')->getProductStatus($aRow['product_status']);

        foreach ($aInfos as $info)
        {
            $aRow[$info['type']] = $info['info'];
        }
        if (!isset($aRow['is_friend']))
        {
            $aRow['is_friend'] = 0;
        }
        $aRow['bookmark_url'] =  Phpfox::permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']);

        $this->retrieveMoreInfoForProduct($aRow);
        $this->retrievePermission($aRow);

        return $aRow;
    }

    public function getProductForEdit($iProductId, $bForce = false)
    {
        $aItem = $this->database()->select('ep.*, eps.*,ept.description')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'eps.product_id = ep.product_id')
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
            ->where('ep.product_id = ' . (int) $iProductId.' AND ep.product_creating_type = \'ynsocialstore_product\'')
            ->execute('getSlaveRow');

        if ($aItem)
        {
            if($aItem['discount_start_date'] > 0) {
                $aItem['start_time_month'] = Phpfox::getTime('n', $aItem['discount_start_date'], false);
                $aItem['start_time_day'] = Phpfox::getTime('j', $aItem['discount_start_date'], false);
                $aItem['start_time_year'] = Phpfox::getTime('Y', $aItem['discount_start_date'], false);
            }
            else{
                $aItem['start_time_month'] = Phpfox::getTime('n', PHPFOX_TIME, false);
                $aItem['start_time_day'] = Phpfox::getTime('j', PHPFOX_TIME, false);
                $aItem['start_time_year'] = Phpfox::getTime('Y', PHPFOX_TIME, false);

            }
            if($aItem['discount_start_date'] > 0) {
                $aItem['end_time_month'] = Phpfox::getTime('n', $aItem['discount_end_date'], false);
                $aItem['end_time_day'] = Phpfox::getTime('j', $aItem['discount_end_date'], false);
                $aItem['end_time_year'] = Phpfox::getTime('Y', $aItem['discount_end_date'], false);
            }
            else{
                $aItem['end_time_month'] = Phpfox::getTime('n', PHPFOX_TIME, false);
                $aItem['end_time_day'] = Phpfox::getTime('j', PHPFOX_TIME, false);
                $aItem['end_time_year'] = Phpfox::getTime('Y', PHPFOX_TIME, false);
            }
            $aItem['categories'] = Phpfox::getService('ecommerce.category')->getCategoryIds($aItem['product_id'],'ynsocialstore_product');
            if($aItem['feature_end_time'] > 0)
            {
                $aItem['is_unlimited_feature'] = false;
                $aItem['expire_feature_day'] = '';
                if($aItem['feature_end_time'] >= PHPFOX_TIME)
                {
                    $aItem['expire_feature_day'] = date(Phpfox::getParam('core.global_update_time'),$aItem['feature_end_time']);

                }
                elseif($aItem['feature_end_time'] == 1)
                {
                    $aItem['is_unlimited_feature'] = true;
                }

            }
            $aItem['selling_price'] = $aItem['product_price'] - $aItem['discount_price'];
            $aItem['product_quantity_main'] = $aItem['product_quantity'];
        }

        return $aItem;
    }

    public function getProductById($iProductId){
        $aItem = $this->database()->select('ep.*,eps.*')
            ->from(Phpfox::getT('ecommerce_product_ynstore'), 'eps')
            ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = eps.product_id')
            ->join(Phpfox::getT('ynstore_store'),'st','st.store_id = ep.item_id')
            ->where('eps.product_id = ' . (int) $iProductId)
            ->execute('getSlaveRow');

        return $aItem;
    }

    public function getAdvSearchConditions()
    {
        $aVals = array();

        $aVals['keywords'] = $this->request()->get('keywords');
        $aVals['price_from'] = $this->search()->get('price_from');
        $aVals['price_to'] = $this->search()->get('price_to');
        $aVals['category_id'] = $this->search()->get('category_id');

        return $aVals;
    }

    public function setAdvSearchConditions($aVals)
    {
        // Filter keywords
        if(!empty($aVals['keywords'])) {
            $this->search()->search('like%', 'ecp.name', $aVals['keywords']);
        }

        if(isset($aVals['price_from']) && is_numeric($aVals['price_from'])) {
            $this->search()->setCondition("AND ecp.product_price >= " . $aVals['price_from']);
        }

        if(isset($aVals['price_to']) && is_numeric($aVals['price_to'])) {
            $this->search()->setCondition("AND ecp.product_price <= " . $aVals['price_to']);
        }

        if(!empty($aVals['category_id'])) {
            $this->search()->setCondition("AND ecd.category_id = " . $aVals['category_id']);
        }
    }

    public function retrievePermission(&$aProduct)
    {
        $aProduct['permission'] = true;
        $aProduct['canFeature'] = Phpfox::getService('ynsocialstore.permission')->canFeatureProduct(false, $aProduct['user_id'], $aProduct['product_status']);
        $aProduct['canClose'] = Phpfox::getService('ynsocialstore.permission')->canCloseProduct($aProduct['user_id'], $aProduct['product_status']);
        $aProduct['canReopen'] = Phpfox::getService('ynsocialstore.permission')->canReopenProduct($aProduct['user_id'], $aProduct['product_status']);
        $aProduct['canDelete'] = Phpfox::getService('ynsocialstore.permission')->canDeleteProduct(false, $aProduct['user_id']);
        $aProduct['canApprove'] = Phpfox::getService('ynsocialstore.permission')->canApproveProduct(false, $aProduct['product_status']);
        $aProduct['canDeny'] = Phpfox::getService('ynsocialstore.permission')->canDenyProduct(false, $aProduct['product_status']);
        $aProduct['canPublish'] = Phpfox::getService('ynsocialstore.permission')->canPublishProduct($aProduct['user_id'], $aProduct['product_status']);
        $aProduct['canEdit'] = Phpfox::getService('ynsocialstore.permission')->canEditProduct(false, $aProduct['user_id']);
        $aProduct['canDoAction'] = $aProduct['canFeature'] || $aProduct['canClose'] || $aProduct['canReopen'] || $aProduct['canDelete'] ||
            $aProduct['canApprove'] || $aProduct['canDeny'] || $aProduct['canPublish'] || $aProduct['canEdit'];
    }

    public function retrieveMoreInfoForProduct(&$aProduct)
    {
        if ($aProduct['feature_end_time'] == 1 || ($aProduct['feature_start_time'] <= PHPFOX_TIME && $aProduct['feature_end_time'] >= PHPFOX_TIME)) {
            $aProduct['is_featured'] = 1;
        } else {
            $aProduct['is_featured'] = 0;
        }
        $aProduct['location'] = $this->getLocationByStoreId($aProduct['store_id']);

        if (!empty($aProduct['location']['location'])) {
            $aProduct['location']['address'] = $aProduct['location']['location'];
        }

        $aProduct['is_liked'] = Phpfox::getService('like')->didILike('ynsocialstore_product', $aProduct['product_id']);
        $aProduct['currency_symbol'] = Phpfox::getService('core.currency')->getSymbol($aProduct['creating_item_currency']);

        if (!isset($aProduct['is_friend'])) {
            $aProduct['is_friend'] = 0;
        }
        if ($aProduct['product_status'] == 'running' && Phpfox::getService('privacy')->check('ynsocialstore_product', $aProduct['product_id'], $aProduct['user_id'], $aProduct['privacy'], $aProduct['is_friend'], true) && Phpfox::getService('privacy')->check('ynsocialstore_store', $aProduct['store_id'], $aProduct['user_id'], $aProduct['store_privacy'], $aProduct['is_friend'], true)) {
            $aProduct['canAddToCart'] = true;
        } else {
            $aProduct['canAddToCart'] = false;
        }
        $aProduct['has_attribute'] = false;
        if (($iPrice = (int)$this->getDisplayPriceByAttribute($aProduct['product_id'])) > 0) {
            $aProduct['discount_display'] = $aProduct['product_price'] = $iPrice;
            if ($aProduct['discount_percentage'] && (!empty($aProduct['discount_timeless']) || ($aProduct['discount_start_date'] <= PHPFOX_TIME && $aProduct['discount_end_date'] >= PHPFOX_TIME))) {
                $aProduct['product_price'] = round($iPrice * 100 / (100 - $aProduct['discount_percentage']),2);
            }
            $aProduct['has_attribute'] = true;
        }
    }

    public function getCustomFieldByCategoryId($iCategoryId)
    {
        $aFields = $this->database()
            ->select('ecf.*')
            ->from(Phpfox::getT('ecommerce_category_customgroup_data'), 'eccd')
            ->join(Phpfox::getT('ecommerce_custom_group'), 'ecg', 'ecg.group_id = eccd.group_id AND ecg.is_active = 1')
            ->join(Phpfox::getT('ecommerce_custom_field'), 'ecf', 'ecf.group_id = ecg.group_id')
            ->where('eccd.category_id = ' . (int) $iCategoryId)
            ->order('ecg.group_id ASC, ecf.ordering ASC, ecf.field_id ASC')
            ->execute('getSlaveRows');

        $aHasOption = Phpfox::getService('ecommerce.custom')->getHasOption();

        if (is_array($aFields) && count($aFields))
        {
            foreach ($aFields as $k => $aField)
            {
                if (in_array($aField['var_type'], $aHasOption))
                {
                    $aOptions = $this->database()
                        ->select('*')
                        ->from(Phpfox::getT('ecommerce_custom_option'))
                        ->where('field_id = ' . $aField['field_id'])
                        ->order('option_id ASC')
                        ->execute('getSlaveRows');

                    if (is_array($aOptions) && count($aOptions))
                    {
                        foreach ($aOptions as $k2 => $aOption)
                        {
                            $aFields[$k]['option'][$aOption['option_id']] = $aOption['phrase_var_name'];
                        }
                    }
                }
            }
        }

        return $aFields;
    }

    public function getProductMainCategory($iAuctionId)
    {
        $aAuctionMainCategory = $this->database()
            ->select('ecd.category_id')
            ->from(Phpfox::getT('ecommerce_category_data'), 'ecd')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->where('ecd.product_type = "ynsocialstore_product" AND ecd.product_id = ' . (int) $iAuctionId)
            ->execute('getRow');

        return $aAuctionMainCategory;
    }

    public function getCountProduct($mConditions)
    {
        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' = ep.user_id OR (f.user_id = ep.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }
        return $this->database()->select('COUNT(DISTINCT ep.product_id)')
            ->from($this->_sTable, 'ep')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = ep.item_id')
            ->where($mConditions)
            ->execute('getField');
    }

    public function getImages($iProductId, $iLimit = null)
    {
        $aImages = $this->database()->select('epi.*')
            ->from(Phpfox::getT('ecommerce_product_image'),'epi')
            ->where('epi.product_id = '.$iProductId)
            ->order('epi.ordering ASC')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        return $aImages;
    }

    public function setMainProductPhoto($iProductId, $iPhotoId)
    {
        $aImage = $this->database()->select('epi.*')
            ->from(Phpfox::getT('ecommerce_product_image'),'epi')
            ->where('epi.product_id = '.$iProductId.' AND epi.image_id = '.$iPhotoId)
            ->execute('getRow');

        if (!empty($aImage)) {
            return $this->database()->update($this->_sTable, array('logo_path' => $aImage['image_path'],'server_id' => $aImage['server_id']), 'product_id = '.$iProductId);
        } else {
            return false;
        }
    }

    /*
     * Return array products which have category in $sCategoryIds and product_id not in $sProductIds
     * $sProductIds: products not in this string. EX: (1,2,3,4)
     * $sCategoryIds: products have category in this string. EX: (1,2,3,4)
     */
    public function getRelatedProducts($sProductIds, $sCategoryIds, $iLimit)
    {
        $sSelect = Phpfox::getService('ynsocialstore.helper')->getNormalSelectQuery();
        $aCond[] = "AND ecp.product_status = 'running'";
        $aCond[] = "AND eccd.category_id IN ({$sCategoryIds}) AND ecp.product_id NOT IN ({$sProductIds})";
        $iCount = 0;

        $aProducts = $this->getProducts($sSelect, $iLimit, null, $iCount, $aCond, 'RAND()', true);

        return $aProducts;
    }

    public function getCategoryByProductId($iProductId)
    {
        $aCategoriesId =  $this->database()->select('etd.category_id,et.title')
                        ->from(Phpfox::getT('ecommerce_category_data'),'etd')
                        ->join(Phpfox::getT('ecommerce_category'),'et','et.category_id = etd.category_id ')
                        ->where('etd.product_type = "ynsocialstore_product" AND etd.product_id = '.$iProductId)
                        ->order('etd.category_id DESC')
                        ->limit(1)
                        ->execute('getRow');
        if(count($aCategoriesId))
        {
            return $aCategoriesId;
        }
        return [];
    }

    public function getMyWishListProduct($iPage = 0, $iLimit = null, &$iCount)
    {
        $aWishList = $this->database()->select('product_id')
            ->from(Phpfox::getT('ecommerce_product_ynstore_wishlist'))
            ->where('user_id = '.Phpfox::getUserId())
            ->order('time_stamp')
            ->execute('getRows');
        if (empty($aWishList))
            return [];

        $aWishListId = array();

        foreach ($aWishList as $aItem)
        {
            $aWishListId[] = array_shift($aItem);
        }

        $sWishList = implode(',', $aWishListId);

        $oHelper = Phpfox::getService('ynsocialstore.helper');

        $sStatus = "'" . $oHelper->getProductStatusKey('public') . "','" . $oHelper->getProductStatusKey('closed'). "'";

        $sSelect = Phpfox::getService('ynsocialstore.helper')->getNormalSelectQuery();
        $aCond[] = "AND ecp.product_status IN ({$sStatus}) AND ecp.product_id IN ({$sWishList})";

        $aProducts = Phpfox::getService('ynsocialstore.product')->getProducts($sSelect, $iLimit, $iPage, $iCount, $aCond, 'FIELD(ecp.product_id, ' . $sWishList . ')', true);

        foreach ($aProducts as &$aProduct)
        {
            $this->retrieveMoreInfoForProduct($aProduct);
        }

        return $aProducts;
    }

    public function getCategoriesByList($sList)
    {
        return $this->database()->select('category_id, title')
            ->from(Phpfox::getT('ecommerce_category'))
            ->where("category_id IN ({$sList})")
            ->order('FIELD(category_id, '.$sList.')')
            ->execute('getRows');
    }

    public function addElementAttribute($aVals)
    {
        $oInput = Phpfox_Parse_Input::instance();
        $aVals['name'] = $oInput->clean($aVals['name']);
        $aVals['quantity'] = (int)$aVals['quantity'];
        $aVals['attribute_id'] = (int)$aVals['attribute_id'];

        if ($aVals['quantity']) {
            $aVals['quantity'] = $aVals['remain'] = $aVals['amount'];
        } else {
            $aVals['remain'] = 0;
        }

        $aVals['product_id'] = (int)$aVals['product_id'];
        if(!is_numeric($aVals['price']))
        {
            Phpfox_Error::set(_p('please_enter_a_valid_number'));
            return false;
        }
        if((float)$aVals['price'] > 9999999999.99)
        {
            Phpfox_Error::set(_p('Please enter a value less than or equal to 9999999999.99'));
            return false;
        }

        $aElementAttribute = array();

        if (!empty($aVals['attribute_id']))
        {
            $aElementAttribute = $this->getElementAttribute($aVals['attribute_id']);
        }
        if (!empty($aElementAttribute)) {
            $iAttributeId = Phpfox::getService('ynsocialstore.product.process')->saveElementAttribute($aVals, $aElementAttribute['attribute_id']);
            Phpfox::addMessage(_p("An element has been successfully updated"));
        } else {
            $iAttributeId = Phpfox::getService('ynsocialstore.product.process')->saveElementAttribute($aVals);
            Phpfox::addMessage(_p("An element has been successfully added"));
        }

        if (!$iAttributeId) {
            Phpfox::addMessage(_p("We're sorry. There was an error occur."));
        }

        Phpfox_Url::instance()->send('ynsocialstore.manage-attributes', array('id' => $aVals['product_id']));
    }

    public function getAllElements($iProductId, $bQueryDelete = false)
    {
        $aRows = $this->database()->select('*')
            ->from(Phpfox::getT('ecommerce_product_attribute'))
            ->where('product_id = '.$iProductId.($bQueryDelete ? '' : ' AND is_deleted = 0'))
            ->execute('getRows');

        return $aRows;
    }

    public function getOwnerId($iProductId)
    {
        return $this->database()->select('user_id')->from($this->_sTable)->where('product_id = '.$iProductId)->execute('getField');
    }

    public function getElementAttribute($iElementId)
    {
        return $this->database()->select('*')->from(Phpfox::getT('ecommerce_product_attribute'))->where('attribute_id = '.$iElementId)->execute('getRow');
    }

    public function addAttribute($aVals)
    {
        $oInput = Phpfox_Parse_Input::instance();
        if(iconv_strlen($aVals['title']) > 16)
        {
            return Phpfox_Error::set(_p('Attribute title have maximum 16 characters'));
        }
        $aVals['title'] = $oInput->clean($aVals['title']);
        Phpfox::getService('ynsocialstore.product.process')->saveAttribute($aVals);
        Phpfox::addMessage(_p("Attribute has been successfully updated"));
        Phpfox_Url::instance()->send('ynsocialstore.manage-attributes', array('id' => $aVals['product_id']));
    }

    public function getProductForManageAttr($iProductId)
    {
        $is_admin = Phpfox::isAdmin() ? '' : (' AND ecp.user_id = '.Phpfox::getUserId());

        $aRow = $this->database()->select('ecp.logo_path,eps.product_type,eps.enable_inventory, ecp.product_quantity_main, ecp.product_id, ecp.name, ecp.product_status, st.store_id, st.name as store_name, ecp.user_id, eps.attribute_style, eps.attribute_name')
            ->from($this->_sTable, 'ecp')
            ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps' ,'ecp.product_id = eps.product_id' . $is_admin)
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.status <> "deleted" AND st.store_id = ecp.item_id')
            ->where('ecp.product_creating_type = "ynsocialstore_product" AND ecp.product_id = '.$iProductId)
            ->execute('getRow');

        return $aRow;
    }

    public function countProductOfStore($iStoreId)
    {
        $sWhere = '';
        $sWhere .= ' AND ep.product_creating_type = \'ynsocialstore_product\' AND ep.module_id = \'ynsocialstore\' AND ep.product_status != "deleted" AND ep.item_id = ' . (int)$iStoreId;
        $iCount = $this->database()
            ->select("COUNT(ep.product_id)")
            ->from($this->_sTable, 'ep')
            ->where('1=1' . $sWhere)
            ->innerJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->execute("getSlaveField");

        return $iCount;
    }

    public function getAllCategoryForCompare($sProductId)
    {
        $aCategory = $this->database()->select('DISTINCT ct.category_id,ct.title')
                        ->from(Phpfox::getT('ecommerce_category_data'),'ctd')
                        ->join($this->_sTable,'ep','ep.product_id = ctd.product_id AND ep.product_status != \'deleted\'')
                        ->join(Phpfox::getT('ecommerce_category'),'ct','ct.category_id = ctd.category_id')
                        ->where('ctd.product_type=\'ynsocialstore_product\' AND ctd.is_main = 1 AND ctd.product_id IN ('.$sProductId.')')
                        ->execute('getSlaveRows');
        return $aCategory;
    }

    public function getProductToCompare($iCateId,$sProductId)
    {
        $aProducts = $this->database()->select('ep.name,ep.logo_path,ep.server_id,ep.product_id,st.store_id,st.name as store_name')
            ->from($this->_sTable,'ep')
            ->join(Phpfox::getT('ynstore_store'),'st','st.store_id = ep.item_id')
            ->join(Phpfox::getT('ecommerce_category_data'),'ctd','ep.product_id = ctd.product_id')
            ->where('ep.product_status != \'deleted\' AND ctd.product_type = \'ynsocialstore_product\' AND ctd.is_main = 1 AND ep.product_creating_type = \'ynsocialstore_product\'  AND ep.product_id IN ('.$sProductId.') AND ctd.category_id ='.$iCateId)
            ->group('ep.product_id')
            ->execute('getSlaveRows');
        return $aProducts;
    }

    public function doComparisonField($aFields)
    {
        $aFieldStatus = array();
        foreach ($aFields as $keyaFields => $valueaFields) {
            switch ($valueaFields['field']) {
                case 'name':
                    $aFieldStatus['name'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['name'] = true;
                    }
                    break;
                case 'rating':
                    $aFieldStatus['rating'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['rating'] = true;
                    }
                    break;
                case 'price':
                    $aFieldStatus['price'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['price'] = true;
                    }
                    break;
                case 'total_orders':
                    $aFieldStatus['total_orders'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['total_orders'] = true;
                    }
                    break;
                case 'total_reviews':
                    $aFieldStatus['total_reviews'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['total_reviews'] = true;
                    }
                    break;
                case 'total_views':
                    $aFieldStatus['total_views'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['total_views'] = true;
                    }
                    break;
                case 'seller':
                    $aFieldStatus['seller'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['seller'] = true;
                    }
                    break;
                case 'custom_fields':
                    $aFieldStatus['custom_fields'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['custom_fields'] = true;
                    }
                    break;
                case 'description':
                    $aFieldStatus['description'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['description'] = true;
                    }
                    break;
            }
        }

        return $aFieldStatus;
    }

    public function getProductDetailToCompare($iCateId,$sProductId)
    {
        $aItems = $this->database()->select('ep.product_id,ep.logo_path,ep.server_id,ep.product_price,ep.total_view,ep.total_review,ep.total_orders,ep.name,st.name as store_name,st.store_id,eu.title as uom_title,ept.description_parsed AS description,eps.discount_price,(ep.product_price - eps.discount_price) as discount_display,eps.discount_percentage,eps.discount_timeless,eps.discount_start_date,eps.discount_end_date,ep.creating_item_currency,eps.rating,eps.product_type')
            ->from($this->_sTable,'ep')
            ->join(Phpfox::getT('ynstore_store'),'st','st.store_id = ep.item_id')
            ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ep.uom_id')
            ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'ep.product_id = eps.product_id')
            ->join(Phpfox::getT('ecommerce_product_text'), 'ept', 'ep.product_id = ept.product_id')
            ->join(Phpfox::getT('ecommerce_category_data'),'ctd','ep.product_id = ctd.product_id')
            ->where('ctd.product_type = \'ynsocialstore_product\' AND ctd.is_main = 1 AND ep.product_creating_type = \'ynsocialstore_product\'  AND ep.product_id IN ('.$sProductId.') AND ctd.category_id ='.$iCateId)
            ->group('ep.product_id')
            ->execute('getSlaveRows');
        if(count($aItems))
        {
            $parent_category_id = Phpfox::getService('ynsocialstore.category')->getFirstParentId($iCateId);
            $aCustomFields = Phpfox::getService('ynsocialstore.product')->getCustomFieldByCategoryId($parent_category_id);

            foreach($aItems as $key => $aItem)
            {
                $aCustomData = $aCustomFields;
                $aCustomDataTemp = Phpfox::getService('ecommerce.custom')->getCustomFieldByProductId($aItem['product_id'],'ynsocialstore_product');

                if(count($aCustomFields)){
                    foreach ($aCustomFields as $key2 => $aField) {
                        foreach ($aCustomDataTemp as $aFieldValue) {
                            if($aField['field_id'] == $aFieldValue['field_id']){
                                $aCustomData[$key2]['value'] = $aFieldValue['value'];
                                $aCustomData[$key2]['product_id'] = $aFieldValue['product_id'];
                                $aCustomData[$key2]['group_phrase_var_name'] = $aFieldValue['group_phrase_var_name'];
                            }
                        }
                    }
                }
                $aItems[$key]['custom_field_list'] = $aCustomData;
                $aItems[$key]['currency_symbol'] = Phpfox::getService('core.currency')->getSymbol($aItem['creating_item_currency']);
                $aItems[$key]['has_attribute'] = false;
                if(($iPrice = (int)$this->getDisplayPriceByAttribute($aItem['product_id'])) > 0)
                {
                    $aItems[$key]['discount_display'] = $aItems[$key]['product_price'] = $iPrice;
                    if($aItem['discount_percentage'] && ($aItem['discount_timeless'] || ($aItem['discount_start_date'] <= PHPFOX_TIME && $aItem['discount_end_date'] >= PHPFOX_TIME))){
                        $aItems[$key]['product_price'] = round($iPrice*100/(100 - $aItem['discount_percentage']),2);
                    }
                    $aItems[$key]['has_attribute'] = true;
                }
            }
        }
        return $aItems;
    }

    public function getFriendBoughtThisProduct($iProductId, $iLimit = null, $iPage = null)
    {
        $iCount = $this->database()->select('COUNT(DISTINCT eo.user_id)')
            ->from(Phpfox::getT('ecommerce_order_product'), 'eop')
            ->join(Phpfox::getT('ecommerce_order'), 'eo', 'eo.order_id = eop.orderproduct_order_id AND eo.order_payment_status = "completed"')
            ->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = eo.user_id AND friends.friend_user_id = ' . Phpfox::getUserId())
            ->where('eop.orderproduct_product_id = ' . $iProductId)
            ->execute('getSlaveField');

        $aRows = array();

        if ($iCount > 0)
        {
            $aRows = $this->database()->select(Phpfox::getUserField())
                        ->from(Phpfox::getT('ecommerce_order_product'), 'eop')
                        ->join(Phpfox::getT('ecommerce_order'), 'eo', 'eo.order_id = eop.orderproduct_order_id AND eo.order_payment_status = "completed"')
                        ->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = eo.user_id AND friends.friend_user_id = ' . Phpfox::getUserId())
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = eo.user_id')
                        ->limit($iPage, $iLimit, $iCount)
                        ->group('u.user_id')
                        ->where('eop.orderproduct_product_id = ' . $iProductId)
                        ->execute('getRows');
        }

        return array($iCount, $aRows);
    }

    public function getListProductIdsBoughtByFriends($iPage = null, $iLimit = null)
    {
        list($iCount, $aRows) = $this->getMutualFriendsListIds(Phpfox::getUserId(), null);

        // Return if this user not has friends
        if (!$iCount) return null;

        $aFriendsIds = array();

        foreach ($aRows as $aItem)
        {
            $aFriendsIds[] = array_shift($aItem);
        }

        $sFriendsIds = implode(',', $aFriendsIds);

        // Get list product id bought by friends
        $aProductRows = $this->database()->select('eop.orderproduct_product_id')
            ->from(Phpfox::getT('ecommerce_order_product'), 'eop')
            ->join(Phpfox::getT('ecommerce_order'), 'eo', 'eo.order_id = eop.orderproduct_order_id AND eo.order_payment_status = "completed"')
            ->where('eo.module_id = \'ynsocialstore\' AND eo.user_id IN ('.$sFriendsIds.')')
            ->limit($iPage, $iLimit)
            ->group('eop.orderproduct_product_id')
            ->order('RAND()')
            ->execute('getRows');

        // Return if friends of this user not bought anythings
        if(!count($aProductRows)) return null;

        $aProductIds = array();

        foreach ($aProductRows as $aItem)
        {
            $aProductIds[] = array_shift($aItem);
        }

        $sProductIds = implode(',', $aProductIds);

        return$sProductIds;
    }

    public function getProductsBoughtByFriends($iLimit = 3, &$iCount)
    {
        // Cache because this has so many query
        $iUserId = Phpfox::getUserId();
        $iDefaultTimeCache = 5*60; // Cache 5 hours but if someone buy sth we will remove this cache on callback of module e-commerce

        $sCacheId = $this->cache()->set('ynsocialstore_product_buyingactivity_bought_by_friends_'. $iUserId . '_' . $iLimit);
        if (($aCacheData = $this->cache()->get($sCacheId, $iDefaultTimeCache)) )
        {
            $aProducts = [];
            if (count($aCacheData) == 2) {
                $iCount = $aCacheData[0];
                $aProducts = $aCacheData[1];
            }
            return $aProducts;
        }

        $sProductIds = $this->getListProductIdsBoughtByFriends($iLimit);

        if(empty($sProductIds))
            return null;

        //Prepare params
        $sSelect = Phpfox::getService('ynsocialstore.helper')->getNormalSelectQuery();
        $aCond[] = "AND ecp.product_status = 'running' AND ecp.product_id IN (".$sProductIds.")";
        $iCount = 0;

        $aProducts = Phpfox::getService('ynsocialstore.product')->getProducts($sSelect, $iLimit, null, $iCount, $aCond, 'FIELD(ecp.product_id, '.$sProductIds.')');

        if (!$iCount) return null;

        foreach ($aProducts as $ikey => &$aProduct)
        {
            list($iTmp, $aTmp) = $this->getFriendBoughtThisProduct($aProduct['product_id'], 1);
            $aProduct['friends_list']['iMore'] = $iTmp - 1;
            $aProduct['friends_list']['first_friend'] = $aTmp;
        }

        $this->cache()->save($sCacheId, array($iCount, $aProducts));

        return $aProducts;

    }

    public function getMutualFriendsListIds($iUserId, $iLimit = 7, $bNoCount = false)
    {
        static $aCache = array();

        if (isset($aCache[$iUserId . '_' . $iLimit]))
        {
            return $aCache[$iUserId . '_' . $iLimit];
        }
        $iUserId = (int)$iUserId;

        if (Phpfox::getParam('friend.cache_mutual_friends') > 0)
        {
            $sCacheId = $this->cache()->set(array('mutual_friend_ids', Phpfox::getUserId() . '_' . $iUserId . '_' . $iLimit));
            if (($aMutual = $this->cache()->get($sCacheId, Phpfox::getParam('friend.cache_mutual_friends') )) )
            {
                $aCache[$iUserId] = $aMutual;

                return $aMutual;
            }
        }

        if ($bNoCount == false)
        {
            $iCnt = $this->database()->select('count(f.user_id)')
                ->from(Phpfox::getT('friend'), 'f')
                ->join(Phpfox::getT('friend'), 'sf', 'sf.friend_user_id = f.friend_user_id AND sf.user_id = ' . (int)$iUserId)
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
                ->where('f.is_page = 0 AND f.user_id = ' . Phpfox::getUserId())
                ->group('f.friend_user_id')
                ->execute('getSlaveRows');
            $iCnt = count($iCnt);

        }
        $aRows = $this->database()->select('u.user_id')
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('friend'), 'sf', 'sf.friend_user_id = f.friend_user_id AND sf.user_id = ' . (int)$iUserId)
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
            ->where('f.is_page = 0 AND f.user_id = ' . Phpfox::getUserId())
            ->order('f.time_stamp DESC')
            ->group('f.friend_user_id')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if (Phpfox::getParam('friend.cache_mutual_friends') > 0)
        {
            $sCacheId = $this->cache()->set(array('mutual_friend_ids', Phpfox::getUserId() . '_' . $iUserId . '_' . $iLimit));
            $this->cache()->save($sCacheId, array($iCnt, $aRows));
        }

        $aCache[$iUserId] = array($iCnt, $aRows);

        return array($iCnt, $aRows);
    }

    public function countOnCartProductByUser($iProductId,$iUserId)
    {
        $aRows = $this->database()->select('ecp.cartproduct_attribute_id as attribute_id, SUM(ecp.cartproduct_quantity) as total_in_cart')
                    ->from(Phpfox::getT('ecommerce_cart_product'),'ecp')
                    ->leftjoin(Phpfox::getT('ecommerce_product_attribute'),'eca','eca.attribute_id = ecp.cartproduct_attribute_id AND eca.is_deleted = 0')
                    ->join(Phpfox::getT('ecommerce_cart'),'ec','ecp.cartproduct_cart_id = ec.cart_id AND ecp.cartproduct_module =\'ynsocialstore\'')
                    ->where('ec.cart_user_id ='.$iUserId.' AND ecp.cartproduct_product_id ='.$iProductId.' AND cartproduct_payment_status = \'init\'')
                    ->group('ecp.cartproduct_attribute_id')
                    ->execute('getSlaveRows');
        $aResult = [];
        if(count($aRows)) {
            foreach ($aRows as $aRow) {
                $aResult[$aRow['attribute_id']] = $aRow['total_in_cart'];
            }
        }
        return $aResult;
    }

    public function countOrderOfProduct($iProductId)
    {
        $aRows = $this->database()->select('eop.orderproduct_attribute_id as attribute_id, SUM(eop.orderproduct_product_quantity) as total_in_order')
            ->from(Phpfox::getT('ecommerce_order_product'),'eop')
            ->join(Phpfox::getT('ecommerce_order'),'eo','eop.orderproduct_order_id = eo.order_id AND eop.orderproduct_module =\'ynsocialstore\'')
            ->where('eop.orderproduct_product_id ='.$iProductId.' AND eo.order_payment_status IN(\'completed\',\'pending\')')
            ->group('eop.orderproduct_attribute_id')
            ->execute('getSlaveRows');
        $aResult = [];
        if(count($aRows)) {
            foreach ($aRows as $aRow) {
                $aResult[$aRow['attribute_id']] = $aRow['total_in_order'];
            }
        }
        return $aResult;
    }

    public function countOrderAndCartOfProductByUser($iProductId,$iUserId)
    {
        $iTotalOrder = $this->database()->select('SUM(eop.orderproduct_product_quantity) as total_order')
            ->from(Phpfox::getT('ecommerce_order_product'),'eop')
            ->join(Phpfox::getT('ecommerce_order'),'eo','eop.orderproduct_order_id = eo.order_id AND eop.orderproduct_module =\'ynsocialstore\'')
            ->where('eo.user_id ='.$iUserId.' AND eop.orderproduct_product_id ='.$iProductId.' AND eo.order_payment_status IN(\'completed\',\'pending\')')
            ->execute('getField');

        $iTotalCart = $this->countTotalOnCartByProduct($iProductId,$iUserId);

        return  $iTotalOrder + $iTotalCart;
    }

    public function countTotalOnCartByProduct($iProductId,$iUserId)
    {
        return $this->database()->select('SUM(ecp.cartproduct_quantity) as total_cart')
            ->from(Phpfox::getT('ecommerce_cart_product'),'ecp')
            ->leftjoin(Phpfox::getT('ecommerce_product_attribute'),'eca','eca.attribute_id = ecp.cartproduct_attribute_id AND eca.is_deleted = 0')
            ->join(Phpfox::getT('ecommerce_cart'),'ec','ecp.cartproduct_cart_id = ec.cart_id AND ecp.cartproduct_module =\'ynsocialstore\'')
            ->where('ec.cart_user_id ='.$iUserId.' AND ecp.cartproduct_product_id ='.$iProductId.' AND ecp.cartproduct_payment_status = \'init\'')
            ->execute('getField');
    }

    public function isEnableInventory($iProductId)
    {
        return $this->database()->select('enable_inventory')->from(Phpfox::getT('ecommerce_product_ynstore'))->where('product_id = '.$iProductId)->execute('getSlaveField');
    }

    /*
     * return -1 if not enable or not has any attribute
     * return sum of quantity attribute
     */
    public function getSumOfTotalAmountQuantityAttributes($iProductId, $iElementId = 0)
    {
        if ($this->isEnableInventory($iProductId)) {
            $sTable = Phpfox::getT('ecommerce_product_attribute');
            return $this->database()->select('SUM(CASE WHEN attribute_id <> '.$iElementId.' AND product_id = '.$iProductId.' THEN quantity ELSE 0 END)')->from($sTable)->execute('getSlaveField');
        }
        else {
            return 0;
        }

    }

    /*
     * Return string product ids bought by login user
     */
    public function getRecentlyBoughtProductIds($sCond = '', $iLimit = 10)
    {
        $iLimit = 10;
        $aRows = $this->database()->select('DISTINCT (eop.orderproduct_product_id)')
            ->from(Phpfox::getT('ecommerce_order_product'), 'eop')
            ->join(Phpfox::getT('ecommerce_order'), 'eo', 'eop.orderproduct_module = \'ynsocialstore\' AND eo.order_id = eop.orderproduct_order_id AND eo.user_id = '.Phpfox::getUserId())
            ->limit($iLimit)
            ->where($sCond)
            ->execute('getRows');

        if (!count($aRows))
            return null;

        $aProductIds = array();

        foreach ($aRows as $aItem)
        {
            $aProductIds[] = array_shift($aItem);
        }

        return implode(',', $aProductIds);
    }

    public function getYouMayLikeProducts($iLimit)
    {
        $sProductsIds = $this->getRecentlyBoughtProductIds();

        // If current logged in user not bought any product return null
        if (empty($sProductsIds))
            return null;

        $sCategoriesIds = Phpfox::getService('ynsocialstore.category')->getCategoriesOfListProducts($sProductsIds);

        if (!empty($sProductsIds) && !empty($sCategoriesIds))
            return $this->getRelatedProducts($sProductsIds, $sCategoriesIds, $iLimit);
        else
            return null;
    }


    /**
     * @param $iLimit
     * @return mixed
     */
    public function getHotSellingInThisWeek($iLimit, $iStoreId = 0)
    {
        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' = ecp.user_id OR (f.user_id = ecp.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }

        $iStartOfWeek = Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart());
        $iEndOfWeek = Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd());

        $aRows = $this->database()->select('COUNT(DISTINCT eo.order_id) as total_orders, (ecp.product_price - eps.discount_price) as discount_display, ecp.creating_item_currency, ecp.product_status, ecp.privacy, ecp.feature_start_time,ecp.feature_end_time,ecp.user_id,ecp.product_id, eps.link, ecp.server_id, ecp.logo_path, ecp.product_price, ecp.name as product_name, eps.discount_price, eps.discount_percentage, eps.rating, eu.title as uom_title,st.name as store_name, st.store_id, st.privacy as store_privacy, eps.product_type,ecp.item_id, eps.discount_start_date, eps.discount_end_date, eps.discount_timeless')
                        ->from($this->_sTable,'ecp')
                        ->join(Phpfox::getT('ecommerce_order_product'),'eop','ecp.product_id = eop.orderproduct_product_id AND ecp.product_creating_type like \'ynsocialstore_product\' AND ecp.product_status = \''. Phpfox::getService('ynsocialstore.helper')->getProductStatusKey('public'). '\'')
                        ->join(Phpfox::getT('ecommerce_order'),'eo','eo.order_id = eop.orderproduct_order_id AND eop.orderproduct_module like \'ynsocialstore\' AND eo.order_payment_status = \'completed\'')
                        ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = ecp.item_id AND st.module_id like \'ynsocialstore\' AND st.status = "public"'.($iStoreId ? (' AND st.store_id = '.$iStoreId) : ''))
                        ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ecp.uom_id')
                        ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'eps.product_id = ecp.product_id')
                        ->where('eo.order_creation_datetime >='.$iStartOfWeek.' AND eo.order_creation_datetime <='.$iEndOfWeek)
                        ->group('eop.orderproduct_product_id')
                        ->limit($iLimit)
                        ->order('COUNT(eo.order_id) DESC')
                        ->execute('getSlaveRows');
        if(count($aRows))
        {
            foreach($aRows as $key => &$aProduct)
            {
                $this->retrieveMoreInfoForProduct($aProduct);
                $this->retrievePermission($aProduct);
            }
        }
        return $aRows;
    }

    public function checkProductIsInCart($iProductId,$iUserId,$iAttributeId = 0)
    {
        $aRow =  $this->database()->select('ecp.cartproduct_id,ecp.cartproduct_quantity,ecp.cartproduct_price')
                        ->from(Phpfox::getT('ecommerce_cart_product'),'ecp')
                        ->join(Phpfox::getT('ecommerce_cart'),'ec','ecp.cartproduct_cart_id = ec.cart_id')
                        ->where('ecp.cartproduct_payment_status = \'init\' AND ecp.cartproduct_module like \'ynsocialstore\' AND ec.cart_user_id ='.(int)$iUserId.' AND ecp.cartproduct_product_id ='.(int)$iProductId.' AND ecp.cartproduct_attribute_id ='.(int)$iAttributeId)
                        ->execute('getRow');
        return $aRow;
    }

    public function checkHavingCart($iUserId)
    {
        return (int) $this->database()->select('cart_id')
            ->from(Phpfox::getT('ecommerce_cart'))
            ->where(strtr('cart_user_id=:user',[
                ':user'=> intval($iUserId),
            ]))
            ->execute('getSlaveField');
    }

    public function getProductDiscountPrice($iProductId)
    {
        $aRow = $this->database()->select('ecp.product_price,eps.discount_price,eps.discount_percentage,eps.discount_timeless,eps.discount_start_date,eps.discount_end_date')
                    ->from($this->_sTable,'ecp')
                    ->join(Phpfox::getT('ecommerce_product_ynstore'),'eps','ecp.product_id = eps.product_id AND ecp.product_creating_type like \'ynsocialstore_product\'')
                    ->where('ecp.product_id ='.$iProductId)
                    ->execute('getRow');
        if($aRow)
        {
            if($aRow['discount_percentage'] && ($aRow['discount_timeless'] || ($aRow['discount_start_date'] <= PHPFOX_TIME && $aRow['discount_end_date'] >= PHPFOX_TIME)))
            {
                return $aRow['product_price'] - $aRow['discount_price'];
            }
            else{
                return $aRow['product_price'];
            }
        }
        else{
            return null;
        }

    }

    public function getAttributePrice($iAttributeId)
    {
        return $this->database()->select('price')
                    ->from(Phpfox::getT('ecommerce_product_attribute'),'eca')
                    ->join($this->_sTable,'ecp','ecp.product_id = eca.product_id AND ecp.product_creating_type like \'ynsocialstore_product\'')
                    ->where('eca.attribute_id ='.$iAttributeId)
                    ->execute('getField');
    }

    public function getMyCartData(){

        $aDataMyCart = $this->database()->select("ec.*,ecp.cartproduct_id,ecp.cartproduct_quantity,ecp.cartproduct_type,ecp.cartproduct_price,ecp.cartproduct_currency,ecp.cartproduct_attribute_id,ecp.cartproduct_payment_status,ep.item_id,ep.product_id,ep.name as product_name,ep.logo_path,ep.server_id,ep.product_quantity_main,ep.product_quantity,ep.product_price,ep.creating_item_currency,ep.total_orders,eu.title as uom_title")
            ->from(Phpfox::getT('ecommerce_cart'), 'ec')
            ->join(Phpfox::getT('ecommerce_cart_product'), 'ecp', 'ec.cart_id = ecp.cartproduct_cart_id')
            ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = ecp.cartproduct_product_id')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'ep.item_id = st.store_id')
            ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ep.uom_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->where('ec.cart_user_id = '.(int)Phpfox::getUserId().' AND ecp.cartproduct_payment_status =\'init\' AND ep.product_status = \'running\' AND st.status = \'public\' AND ecp.cartproduct_module like "ynsocialstore"')
            ->execute('getSlaveRows');
        $aData = array();
        $iCount = 0;
        /*maybe dont using this processing*/
        if(count($aDataMyCart)){
            foreach ($aDataMyCart as $key => $aItem) {

                    $aProduct =  $this->database()->select("eps.discount_start_date,eps.discount_end_date,eps.discount_timeless,eps.discount_percentage,eps.min_order,eps.max_order,eps.product_type,eps.enable_inventory,eps.discount_price,eps.attribute_style,eps.attribute_name,epa.attribute_id,epa.title,epa.image_path,epa.quantity,epa.price,epa.server_id,epa.remain")
                        ->from(Phpfox::getT('ecommerce_product_ynstore'), 'eps')
                        ->leftjoin(Phpfox::getT('ecommerce_product_attribute'),'epa','eps.product_id = epa.product_id AND epa.is_deleted = 0')
                        ->where('eps.product_id = '.(int)$aItem['product_id'].(($aItem['cartproduct_attribute_id'] > 0) ? ' AND epa.attribute_id ='.(int)$aItem['cartproduct_attribute_id'] : ''))
                        ->execute('getSlaveRow');
                    if(count($aProduct)) {
                        switch ($aItem['cartproduct_type']) {
                            case 'buy':
                                if ($aItem['cartproduct_attribute_id'] == 0) {
                                    $aItem['cartproduct_price'] = ($aProduct['discount_percentage'] && ($aProduct['discount_timeless'] || ($aProduct['discount_start_date'] <= PHPFOX_TIME && $aProduct['discount_end_date'] >= PHPFOX_TIME))) ? $aItem['product_price'] - $aProduct['discount_price'] : $aItem['product_price'];
                                } else {
                                    $aItem['cartproduct_price'] = $aProduct['price'];
                                }
                                break;
                        }
                        $iCount = $iCount + $aItem['cartproduct_quantity'];
                        $aItem['cartproduct_subtotal'] = number_format($aItem['cartproduct_quantity'] * $aItem['cartproduct_price'], 2);
                        $keySeller = $aItem['item_id'];
                        $aStore = $this->database()->select('store_id,name as store_name,user_id')->from(Phpfox::getT('ynstore_store'))->where('store_id = ' . $aItem['item_id'])->execute('getRow');
                        $quantity_in_cart_order = $this->countOrderAndCartOfProductByUser($aItem['product_id'], (int)Phpfox::getUserId()) - $aItem['cartproduct_quantity'];
                        if($aProduct['enable_inventory'] && $aProduct['product_type'] == 'physical')
                        {
                            if($aItem['product_quantity_main'] > 0)
                            {
                                if($aItem['product_quantity'] > $aProduct['max_order'] && $aProduct['max_order'] > 0)
                                {
                                    if($aProduct['max_order'] > $quantity_in_cart_order)
                                    {
                                        $aProduct['max_quantity_can_add'] = abs($aProduct['max_order'] - $quantity_in_cart_order);
                                    }
                                    else{
                                        $aProduct['max_quantity_can_add'] = 0;
                                    }
                                }
                                else{
                                    $aProduct['max_quantity_can_add'] = $aItem['product_quantity'];
                                }
                            }
                            elseif($aProduct['max_order'] > 0)
                            {
                                if($aProduct['max_order'] > $quantity_in_cart_order)
                                {
                                    $aProduct['max_quantity_can_add'] = abs($aProduct['max_order'] - $quantity_in_cart_order);
                                }
                                else{
                                    $aProduct['max_quantity_can_add'] = 0;
                                }
                            }
                            else{
                                $aProduct['max_quantity_can_add'] = 'unlimited';
                            }
                        }
                        else{
                            $aProduct['max_quantity_can_add'] = 'unlimited';
                        }
                        $aItem['is_wishlist'] = Phpfox::getService('ynsocialstore.product.wishlist')->isWishlist(Phpfox::getUserId(), $aItem['product_id']);
                        if ($aProduct['product_type'] == 'physical') {
                            $aElements = Phpfox::getService('ynsocialstore.product')->getElementAttribute($aItem['cartproduct_attribute_id']);
                            if (count($aElements)) {
                                $aElementsOnOrder = Phpfox::getService('ynsocialstore.product')->countOrderOfProduct($aItem['product_id']);
                                $iInUsed = 0;

                                if (isset($aElementsOnOrder[$aElements['attribute_id']])) {
                                    $iInUsed = $iInUsed + $aElementsOnOrder[$aElements['attribute_id']];
                                }
                                $aElements['quantity_sold'] = $iInUsed;
                                $aElements['remain_of_attribute'] = ($aElements['quantity'] > 0) ? ($aElements['quantity'] - $iInUsed) : 0;
                                if ($aElements['quantity'] == 0 && $aProduct['max_quantity_can_add'] == 'unlimited') {
                                    $aItem['real_quantity_can_add'] = 'unlimited';
                                } elseif ($aProduct['max_quantity_can_add'] !== 'unlimited' && ($aElements['quantity'] == 0 || $aElements['remain_of_attribute'] > $aProduct['max_quantity_can_add'] || $aProduct['max_quantity_can_add'] === 0)) {
                                    $aItem['real_quantity_can_add'] = $aProduct['max_quantity_can_add'];
                                } elseif ($aElements['quantity'] > 0 && ($aElements['remain_of_attribute'] <= $aProduct['max_quantity_can_add'] || $aProduct['max_quantity_can_add'] == 'unlimited')) {
                                    $aItem['real_quantity_can_add'] = $aElements['remain_of_attribute'];
                                }
                            } else {
                                $aItem['real_quantity_can_add'] = $aProduct['max_quantity_can_add'];
                            }
                        } else {
                            $aItem['real_quantity_can_add'] = 1;
                        }
                        $aItem['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aItem['creating_item_currency']);
                        $serverId = $aItem['server_id'];
                        $aItem = array_merge($aItem, $aProduct);
                        $aItem = array_merge($aItem, $aStore);
                        $aItem['server_id'] = !empty($aItem['server_id']) ? $aItem['server_id'] : $serverId;
                        $aData[] = $aItem;
                    }
            }
        }

        return array($iCount, $aData);

    }

    public function countTotalAttributeQuantity($iProductId)
    {
        $iId = $this->database()->select('eca.attribute_id')
                ->from(Phpfox::getT('ecommerce_product_attribute'),'eca')
                ->join($this->_sTable,'ecp','ecp.product_id = eca.product_id')
                ->where('eca.quantity = 0 AND eca.product_id ='.(int)$iProductId)
                ->execute('getField');
        if((int)$iId > 0)
        {
            return 'unlimited';
        }
        return $this->database()->select('SUM(eca.quantity) as total_quantity')
                    ->from(Phpfox::getT('ecommerce_product_attribute'),'eca')
                    ->join($this->_sTable,'ecp','ecp.product_id = eca.product_id')
                    ->where('eca.product_id ='.(int)$iProductId)
                    ->execute('getField');
    }

    public function getProductSomeInfo($iProductId){
        $aItem = $this->database()->select('eps.*')
            ->from(Phpfox::getT('ecommerce_product_ynstore'), 'eps')
            ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = eps.product_id')
            ->where('eps.product_id = ' . (int) $iProductId)
            ->execute('getSlaveRow');

        return $aItem;
    }

    public function checkIsBuyThisProduct($iProductId){
        $aRow =  $this->database()->select('eop.orderproduct_order_id')
            ->from(Phpfox::getT('ecommerce_order_product'),'eop')
            ->join(Phpfox::getT('ecommerce_order'),'eo','eop.orderproduct_order_id = eo.order_id')
            ->where('eo.order_payment_status = \'completed\' AND eop.orderproduct_module like \'ynsocialstore\' AND eop.orderproduct_product_id ='.$iProductId.' AND eo.user_id ='.(int)Phpfox::getUserId())
            ->execute('getRow');
        return $aRow;
    }

    public function checkQuantityWithMinOrder($iProductId,$iQuantity = 0)
    {
        if($iQuantity == 0)
        {
            $iQuantity = $this->getTotalQuantityOnCartByProduct($iProductId,(int)Phpfox::getUserId());
        }

        $aItem = $this->database()->select('eps.min_order,eps.enable_inventory,eps.product_type,ecp.name')
                        ->from($this->_sTable,'ecp')
                        ->join(Phpfox::getT('ecommerce_product_ynstore'),'eps','eps.product_id = ecp.product_id')
                        ->where('ecp.product_id ='.$iProductId)
                        ->execute('getRow');
        if(empty($aItem))
        {
            return array(-1,[]);
        }
        $bPass = 0;

        if($aItem['product_type'] == 'physical' && $aItem['enable_inventory'])
        {
            if($aItem['min_order'] == 0 || $iQuantity >= $aItem['min_order'])
            {
                $bPass = 1;
            }
            elseif($iQuantity < $aItem['min_order'] && $aItem['min_order'] > 0){
                $bPass = 0;
            }
        }
        elseif($aItem['product_type'] == 'digital' || ($aItem['product_type'] == 'physical' && !$aItem['enable_inventory'])){
            $bPass = 1;
        }

        return array($bPass,$aItem);
    }

    public function getTotalQuantityOnCartByProduct($iProductId,$iUserId)
    {
         return $this->database()->select('SUM(eccp.cartproduct_quantity) as total_quantity')
                    ->from(Phpfox::getT('ecommerce_cart'),'ecc')
                    ->join(Phpfox::getT('ecommerce_cart_product'),'eccp','eccp.cartproduct_cart_id = ecc.cart_id')
                    ->where('eccp.cartproduct_payment_status = \'init\' AND ecc.cart_user_id ='.$iUserId.' AND eccp.cartproduct_product_id ='.$iProductId)
                    ->execute('getField');
    }

    public function getAllSubscriberOfProduc($iProducId)
    {
        return $this->database()->select('*')
            ->from(Phpfox::getT('ecommerce_product_ynstore_subscribers'))
            ->where('is_send = 0 AND product_id = '.$iProducId)
            ->execute('getRows');
    }

    public function cronUpdateProduct()
    {
        $aProducts = $this->database()->select('ep.product_status, ep.product_quantity, ep.product_quantity_main, eps.*')
            ->from($this->_sTable, 'ep')
            ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'eps.product_id = ep.product_id')
            ->where('eps.auto_close > 0 OR eps.discount_timeless = 0')
            ->execute('getSlaveRows');
        foreach ($aProducts as $key => $aProduct) {
            if (!empty($aProduct['auto_close']) && (int)$aProduct['product_quantity'] <= 0 && !empty($aProduct['product_quantity_main'])) {
                Phpfox::getService('ynsocialstore.product.process')->closeProduct($aProduct['product_id'], true);
            }

            if (empty($aProduct['discount_timeless']) && $aProduct['discount_end_date'] < PHPFOX_TIME) {
                $this->database()->update(Phpfox::getT('ecommerce_product_ynstore'), array('discount_price' => 0, 'discount_percentage' => 0), 'product_id = '.$aProduct['product_id']);
            }
        }

    }

    public function getDisplayPriceByAttribute($iProductId)
    {
        $aRow = $this->database()->select('attribute_id,price')
            ->from(Phpfox::getT('ecommerce_product_attribute'))
            ->where('product_id ='.(int)$iProductId)
            ->order('price')
            ->execute('getRow');
        if($aRow && count($aRow))
        {
            return $aRow['price'];
        }
        return false;
    }

    public function getAllElementsOrderPrice($iProductId, $bQueryDelete = false)
    {
        $aRows = $this->database()->select('*')
            ->from(Phpfox::getT('ecommerce_product_attribute'))
            ->where('product_id = '.$iProductId.($bQueryDelete ? '' : ' AND is_deleted = 0'))
            ->order('price')
            ->execute('getRows');

        return $aRows;
    }

    public function getLocationByStoreId($iStoreId)
    {
        return $this->database()->select('*')
            ->from(Phpfox::getT('ynstore_store_location'))
            ->where("store_id = " . (int) $iStoreId)
            ->execute('getRow');
    }

    public function getProducts($sSelect = 'ecp.*, st.*, ept.*, eps.*', $iLimit, $iPage, &$iCount, $aCond = [], $sOrder, $bIsRetrievePermission = false, $bIsCache = false)
    {
        if (!empty($aCond)) {
            $sWhere = implode(' ', $aCond);
        } else {
            $sWhere = '';
        }

        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' = ecp.user_id OR (f.user_id = ecp.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }

        $iCount = $this->database()
            ->select("COUNT(ecp.product_id)")
            ->from($this->_sTable, 'ecp')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = ecp.item_id')
            ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'eps.product_id = ecp.product_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'eccd', 'ecp.product_id = eccd.product_id AND eccd.product_type = \'ynsocialstore_product\' AND eccd.is_main = 1')
            ->join(Phpfox::getT('ecommerce_category'), 'ecc', 'ecc.category_id = eccd.category_id')
            ->group('ecp.product_id')
            ->where('1=1 ' . $sWhere)
            ->execute("getSlaveField");
        $aProducts = array();

        if ($iCount) {

            if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
                $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' = ecp.user_id OR (f.user_id = ecp.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
            }

            $aProducts = $this->database()
                ->select($sSelect)
                ->from($this->_sTable, 'ecp')
                ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = ecp.item_id')
                ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ecp.uom_id')
                ->join(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ecp.product_id')
                ->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'eps.product_id = ecp.product_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'eccd', 'ecp.product_id = eccd.product_id AND eccd.product_type = \'ynsocialstore_product\' AND eccd.is_main = 1')
                ->join(Phpfox::getT('ecommerce_category'), 'ecc', 'ecc.category_id = eccd.category_id')
                ->group('ecp.product_id')
                ->order($sOrder)
                ->where('st.status = \'public\' AND ecp.module_id = \'ynsocialstore\' AND ecp.product_creating_type = \'ynsocialstore_product\' ' . $sWhere)
                ->limit($iPage, $iLimit, $iCount)
                ->execute("getSlaveRows");

            foreach ($aProducts as $key => &$aProduct) {
                $this->retrieveMoreInfoForProduct($aProduct);

                if ($bIsRetrievePermission) {
                    $this->retrievePermission($aProduct);
                }
            }
        }

        return $aProducts;
    }
}
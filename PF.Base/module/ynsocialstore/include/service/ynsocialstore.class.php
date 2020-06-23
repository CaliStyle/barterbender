<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 9/30/16
 * Time: 6:42 PM
 */
class Ynsocialstore_Service_Ynsocialstore extends Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynstore_store');
    }

    public function checkUserStores($userId = null)
    {
        if(empty($userId)) {
            $userId = Phpfox::getUserId();
        }

        if(!Phpfox::isModule('ecommerce')) {
            return false;
        }

        db()->select('COUNT(ep.product_id) AS total_product, store.store_id')
            ->from($this->_sTable, 'store')
            ->leftJoin(Phpfox::getT('ecommerce_product'),'ep', 'store.store_id = ep.item_id AND ep.product_creating_type = \'ynsocialstore_product\' AND ep.module_id = \'ynsocialstore\' AND ep.product_status != "deleted" AND ep.user_id = '. Phpfox::getUserId())
            ->where( 'store.module_id = "ynsocialstore" AND store.status NOT IN ("deleted","draft") AND store.user_id = '. $userId)
            ->group('store.store_id')
            ->union()
            ->unionFrom('sub_store');

        $check = db()->select('COUNT(*)')
                    ->join($this->_sTable, 'store', 'store.store_id = sub_store.store_id')
                    ->join(Phpfox::getT("ynstore_store_package"), 'pkg', 'store.package_id = pkg.package_id')
                    ->where('(sub_store.total_product < pkg.max_products AND pkg.max_products > 0) OR (pkg.max_products = 0)')
                    ->execute('getSlaveField');
        return !!$check;
    }

    /**
     * @param $store
     * @return bool
     */
    public function getMoreInfomationForStoreInsight(&$store) {
        if(empty($store)) {
            return false;
        }
        db()->select('SUM(eop.orderproduct_product_quantity) AS total_product_sold , SUM(eop.orderproduct_product_price * eop.orderproduct_product_quantity) AS total_price, SUM((eop.orderproduct_product_price * eop.orderproduct_product_quantity) / eo.order_commission_rate) AS total_commission_price, eop.orderproduct_product_id AS product_id')
            ->from(Phpfox::getT('ecommerce_order_product'),'eop')
            ->join(Phpfox::getT('ecommerce_order'),'eo', 'eo.order_id = eop.orderproduct_order_id AND eo.order_payment_status = "completed"')
            ->where('eop.orderproduct_module = "ynsocialstore"')
            ->group('product_id')
            ->union()
            ->unionFrom('eop');

        $statistic = db()->select('SUM(eop.total_product_sold) AS product_sold, SUM(ep.total_like) AS total_product_like, SUM(eop.total_price) AS total_product_price, SUM(eop.total_commission_price) AS total_commission_price')
                    ->join(Phpfox::getT('ecommerce_product'),'ep', 'ep.product_id = eop.product_id AND ep.item_id = '. (int)$store['store_id'])
                    ->execute('getSlaveRow');
        $statistic['total_product_price'] = number_format(round($statistic['total_product_price'],2),2);
        $statistic['total_commission_price'] = number_format(round($statistic['total_commission_price'],2),2);
        $store = array_merge($store, $statistic);
    }

    
    public function getAllThemes()
    {
        return array('1' => 'theme_1', '2' => 'theme_2');
    }

    public function getFieldsComparison($sType)
    {
        $aRows = $this->database()
            ->select("*")
            ->from(Phpfox::getT('ynstore_comparison'))
            ->where('for_type = \''.$sType.'\'')
            ->execute("getSlaveRows");
        return $aRows;
    }

    public function getManageStores($aConds = array(), $iPage = 0, $iLimit = NULL, $iCount = NULL, $order_by = 'ynst.store_id DESC')
    {
        $sWhere = '';
        $sWhere .= "ynst.status <> 'deleted'";
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        $iCount = $this->database()
            ->select("COUNT(DISTINCT ynst.store_id)")
            ->from(Phpfox::getT("ynstore_store"), 'ynst')
            ->join(Phpfox::getT("user"), 'u', 'ynst.user_id =  u.user_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ynst.store_id = ecd.product_id AND ecd.product_type = \'store\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id')
            ->join(Phpfox::getT('ynstore_store_package'), 'ystpk', 'ystpk.package_id = ynst.package_id')
            ->where($sWhere)
            ->execute("getSlaveField");

        $aStores = array();
        if ($iCount) {
            $aStores = $this->database()
                ->select('ynst.*, ystpk.name as package,' . Phpfox::getUserField())
                ->from(Phpfox::getT("ynstore_store"), 'ynst')
                ->join(Phpfox::getT("user"), 'u', 'ynst.user_id =  u.user_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ynst.store_id = ecd.product_id AND ecd.product_type = \'store\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id')
                ->join(Phpfox::getT('ynstore_store_package'), 'ystpk', 'ystpk.package_id = ynst.package_id')
                ->where($sWhere)
                ->order($order_by)
                ->group('ynst.store_id')
                ->limit($iPage, $iLimit, $iCount)
                ->execute("getRows");


            foreach ($aStores as $key => $aItem)
            {
                $aStores[$key] = $this->retrieveMoreInfoFromStore($aItem);
            }
        }

        return array($iCount, $aStores);
    }

    public function countStoreOfUserId($userId)
    {
        $sWhere = '';
        $sWhere .= ' AND st.module_id = \'ynsocialstore\' AND st.status != "deleted" AND st.user_id = ' . (int)$userId;
        $iCount = $this->database()
            ->select("COUNT(st.store_id)")
            ->from($this->_sTable, 'st')
            ->where('1=1' . $sWhere)
            ->innerJoin(Phpfox::getT('user'), 'u', 'u.user_id = st.user_id')
            ->execute("getSlaveField");

        return $iCount;
    }

    public function getAllPackages($active = null)
    {
        $sWhere = '';
        if ($active !== null) {
            $sWhere .= ' AND pkg.active = ' . (int)$active;
        }
        $aRows = $this->database()
            ->select('pkg.*')
            ->from(Phpfox::getT("ynstore_store_package"), 'pkg')
            ->where('1=1' . $sWhere)
            ->execute("getSlaveRows");

        $aCurrentCurrencies = Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies();
        $symbol = $aCurrentCurrencies[0]['symbol'];
        $defaultCurrencyId = $aCurrentCurrencies[0]['currency_id'];
        foreach ($aRows as $key => $value) {
            $package_id = $value['package_id'];
            $aRows[$key]['fee_display'] = Phpfox::getService('core.currency')->getCurrency($value['fee'], $defaultCurrencyId);
            $aRows[$key]['feature_store_fee_display'] = Phpfox::getService('core.currency')->getCurrency($value['feature_store_fee'], $defaultCurrencyId);
            $aRows[$key]['feature_product_fee_display'] = Phpfox::getService('core.currency')->getCurrency($value['feature_product_fee'], $defaultCurrencyId);
            $aRows[$key]['themes'] = json_decode($value['themes']);
        }

        return $aRows;
    }

    //TODO: This function will be used by product
    public function getStoreCustomFieldsInfo($iStoreId)
    {

        $aCustomFieldsInfo = $this->database()->select('ecf.phrase_var_name as field_phrase, ecv.option_id as option_id, ecv.value as value')
            ->from(Phpfox::getT('ecommerce_custom_value'), 'ecv')
            ->join(Phpfox::getT('ecommerce_custom_field'), 'ecf', 'ecf.field_id = ecv.field_id')
            ->where("ecv.product_type = 'store' AND ecv.product_id = " . $iStoreId)
            ->order('ecf.ordering')
            ->execute('getSlaveRows');

        return $aCustomFieldsInfo;
    }

    /**
     *  This function get information of store include: phone, fax, website
     * @param $iStoreId
     * @return mixed
     */
    public function getInfoByStoreId($iStoreId)
    {
        $aInfo = $this->database()->select('ynsinfo.info, ynsinfo.type, ynsinfo.title')
            ->from(Phpfox::getT('ynstore_store_infomation'), 'ynsinfo')
            ->where("ynsinfo.store_id = $iStoreId")
            ->execute('getSlaveRows');

        return $aInfo;
    }

    /**
     * This function get address of store
     * @param $iStoreId
     * @return mixed
     */
    public function getAddressByStoreId($iStoreId)
    {
        $aAddress = $this->database()->select('*')
            ->from(Phpfox::getT('ynstore_store_location'))
            ->where("store_id = $iStoreId")
            ->execute('getSlaveRows');

        return $aAddress;
    }

    public function getFAQByStoreId($iStoreId)
    {
        return $this->database()->select('*')
            ->from(Phpfox::getT('ynstore_store_faq'))
            ->where('is_active = 1 AND store_id = '. $iStoreId)
            ->execute('getSlaveRows');
    }

    /**
     * This function get title and is_active of main categories of store
     * @param $iStoreId
     * @return mixed
     */
    public function getMainCategoriesByStoreId($iStoreId)
    {
        $aMainCategories = $this->database()
            ->select('DISTINCT ec.title, ec.is_active, ec.category_id')
            ->from(Phpfox::getT('ecommerce_category_data'), 'ecd')
            ->join(Phpfox::getT("ecommerce_category"), 'ec', "ec.category_id = ecd.category_id AND ecd.product_id = $iStoreId AND ecd.is_main = 1 AND ecd.product_type = 'store' ")
            ->execute('getSlaveRows');

        return $aMainCategories;
    }

    public function getStoreForDetailById($iStoreId)
    {
        if (!is_numeric($iStoreId) && $iStoreId <= 0) {
            return null;
        }
        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' =  st.user_id OR (f.user_id = st.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }

        if (Phpfox::isModule('like'))
        {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynsocialstore_store\' AND l.item_id = st.store_id AND l.user_id = ' . Phpfox::getUserId());
        }


        $aRow = $this->database()->select('st.*, st.country_iso as store_country_iso, '.Phpfox::getUserField())
            ->from(Phpfox::getT('ynstore_store'), 'st')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = st.user_id')
            ->where("st.store_id = $iStoreId")
            ->limit(1)
            ->execute('getSlaveRow');

        if(!$aRow)
            return false;
        if (!isset($aRow['is_friend']))
        {
            $aRow['is_friend'] = 0;
        }
        $aRow['address'] = $this->getAddressByStoreId($iStoreId);
        $aRow['FAQs'] = $this->getFAQByStoreId($iStoreId);

        $aInfo = $this->getInfoByStoreId($iStoreId);

        foreach ($aInfo as $aItem)
        {
            if ($aItem['type'] == 'addinfo') {
                $aRow[$aItem['type']][] = array(
                    'question' => $aItem['title'],
                    'answer' => $aItem['info'],
                );
            } else {
                $aRow[$aItem['type']][] = $aItem['info'];
            }
        }


        $aRow = $this->retrieveMoreInfoFromStore($aRow);
        return $aRow;
    }

    public function getStoreById($iStoreId)
    {
        return $this->database()->select('ynstore.*')
            ->from(Phpfox::getT('ynstore_store'), 'ynstore')
            ->where("ynstore.store_id = $iStoreId")
            ->execute('getRow');
    }

    public function getAllCategories($iCategoryId = null)
    {
        $aCategories = $this->database()->select('mc.category_id, mc.title')->from(Phpfox::getT('ecommerce_category'), 'mc')->where('mc.parent_id = 0'.($iCategoryId === null ? '0' : (int)$iCategoryId).' AND mc.is_active = 1')->order('mc.ordering ASC')->execute('getRows');

        foreach ($aCategories as $iKey => $aCategory)
        {
                $aCategories[$iKey]['sub_1'] = $this->database()->select('mc.category_id, mc.title')->from(Phpfox::getT('ecommerce_category'), 'mc')->where('mc.parent_id = '.$aCategory['category_id'].' AND mc.is_active = 1')->order('mc.ordering ASC')->execute('getRows');

                foreach ($aCategories[$iKey]['sub_1'] as $iSubKey => $aSubCategory)
                {
                    $aCategories[$iKey]['sub_2'] = $this->database()->select('mc.category_id, mc.title')->from(Phpfox::getT('ecommerce_category'), 'mc')->where('mc.parent_id = '.$aSubCategory['category_id'].' AND mc.is_active = 1')->order('mc.ordering ASC')->execute('getRows');
                }
        }

        return $aCategories;
    }
    /**
     * @param $sFields  'name,store_id,vvv'
     * @param $iStoreId
     * @param $sTypeGet  getRow, getRows, getSlaveRow
     * @return mixed|null
     */
    public function getFieldsStoreById($sFields, $iStoreId, $sTypeGet) {
        $aFields = explode(',', $sFields);
        $error = false;
        $oDatabase = $this->database();
        $sTable = Phpfox::getT('ynstore_store');
        foreach ($aFields as $sField)
        {
            if (!$oDatabase->isField($sTable, $sField)) {
                $error = true;
            }
        }

        if ($error) {
            return null;
        } else {
            return $this->database()->select($sFields)
                ->from(Phpfox::getT('ynstore_store'))
                ->where("store_id = $iStoreId")
                ->execute($sTypeGet);
        }
    }

    public function getRecentStore($iLimit)
    {
        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' =  st.user_id OR (f.user_id = st.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }

        $aRows = $this->database()->select('st.name,st.module_id,st.short_description,st.store_id,st.logo_path,st.server_id,st.cover_server_id,st.categories,sl.address,sl.location,st.total_follow,st.total_orders,st.total_review, st.is_featured,st.status,st.rating,st.package_id,st.total_products,'.Phpfox::getUserField())
                    ->from($this->_sTable,'st')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = st.user_id')
                    ->leftjoin(Phpfox::getT('ynstore_store_location'),'sl','st.store_id = sl.store_id')
                    ->where('st.status="public" AND module_id ="ynsocialstore"')
                    ->order('st.time_stamp DESC')
                    ->group('st.store_id')
                    ->limit($iLimit)
                    ->execute('getSlaveRows');
        foreach ($aRows as $key => $aRow) {
            $aRows[$key] = $this->retrieveMoreInfoFromStore($aRows[$key]);
        }
        return $aRows;
    }

    public function getInvoice($iId)
    {
        $aPurchase = $this->database()->select('sp.*')
            ->from(Phpfox::getT('ecommerce_invoice'), 'sp')
            ->where('sp.invoice_id = ' . (int)$iId)
            ->execute('getRow');

        if (!isset($aPurchase['invoice_id'])) {
            return false;
        }

        $aCurrentCurrencies = Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies();
        $aPurchase['default_cost'] = $aPurchase['price'];
        $aPurchase['default_currency_id'] = $aCurrentCurrencies[0]['currency_id'];

        return $aPurchase;
    }

    public function getStoreForEdit($iStoreId)
    {
        $aItem = $this->database()->select('st.*')
            ->from($this->_sTable, 'st')
            ->join(Phpfox::getT('user'),'u','u.user_id = st.user_id')
            ->where('st.store_id = ' . (int)$iStoreId)
            ->execute('getSlaveRow');

        if(!$aItem)
        {
            return false;
        }
        $aItem['categories'] = json_decode($aItem['categories']);
        $aItem['location'] = $this->getAddressByStoreId($iStoreId);
        $aInfo = $this->getInfoByStoreId($iStoreId);
        foreach ($aInfo as $value)
        {
            $aItem[$value['type']][] = ['info' => $value['info'],'title' => $value['title']];
        }
        if($aItem['is_featured'])
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
        return $aItem;
    }

    public function getQuickStoreById($iStoreId)
    {
        $aRow = $this->database()
            ->select('db.*')
            ->from($this->_sTable, 'db')
            ->where('db.store_id = ' . $iStoreId)
            ->execute("getSlaveRow");

        return $aRow;
    }

    public function retrieveMoreInfoFromStore($aStore)
    {
        $aStore['categories'] = $this->database()->select('category_id,title,ordering')->from(Phpfox::getT('ecommerce_category'))->where('category_id IN ('.substr($aStore['categories'], 1, -1).')')->execute('getSlaveRows');

        $aStore['hiddencate'] = 0;
        if(count($aStore['categories']) > 1)
        {
            $aStore['hiddencate'] = count($aStore['categories']) - 1;
        }

        //Show location instead full address
        if (!empty($aStore['location'])) {
            $aStore['address'] = $aStore['location'];
        }

        $aStore['package_name'] = $this->database()->select('name')->from(Phpfox::getT('ynstore_store_package'))->where('package_id = '.$aStore['package_id'])->execute('getField');

        $aStore['canFeature'] = Phpfox::getService('ynsocialstore.permission')->canFeatureStore(false,$aStore['user_id'],$aStore['status']);
        $aStore['canClose'] = Phpfox::getService('ynsocialstore.permission')->canCloseStore($aStore['user_id'],$aStore['status']);
        $aStore['canReopen'] = Phpfox::getService('ynsocialstore.permission')->canReopenStore($aStore['user_id'],$aStore['status']);
        $aStore['canDelete'] = Phpfox::getService('ynsocialstore.permission')->canDeleteStore(false,$aStore['user_id']);
        $aStore['canApprove'] = Phpfox::getService('ynsocialstore.permission')->canApproveStore(false,$aStore['status']);
        $aStore['canDeny'] = Phpfox::getService('ynsocialstore.permission')->canDenyStore(false,$aStore['status']);
        $aStore['canPublish'] = Phpfox::getService('ynsocialstore.permission')->canPublishStore($aStore['user_id'],$aStore['status']);
        $aStore['canCreateProduct'] = Phpfox::getService('ynsocialstore.permission')->canCreateProduct($aStore);
        $aStore['canEdit'] = Phpfox::getService('ynsocialstore.permission')->canEditStore(false, $aStore['user_id']);
        $aStore['canDoAction'] = $aStore['canEdit'] || $aStore['canApprove'] || $aStore['canDeny'] || $aStore['canPublish'] || $aStore['canFeature'] > 0  || $aStore['canDelete'] || $aStore['canClose'] || $aStore['canReopen'] || $aStore['canCreateProduct'];
        return $aStore;
    }

    public function getStoreForMap($aConditions,$sOrder = 'dbus.store_id DESC', $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {

         $sConditions = ' 1=1 ';
        

        foreach ($aConditions as $aCondition) {

            if (strpos($aCondition, '%PRIVACY%') !== false) {
                $sConditions .= str_replace('%PRIVACY%', '0', $aCondition);
            } else {
                $sConditions .= $aCondition;
            }
        }


        $iCount = $this->database()
            ->select("COUNT( DISTINCT( dbus.store_id ) )")
            ->from($this->_sTable, 'dbus')
            ->leftjoin(Phpfox::getT('ynstore_store_location'), 'dbl', 'dbl.store_id = dbus.store_id')
            ->where($sConditions)
            ->execute("getSlaveField");

        $aStores = array();
        if ($iCount) {
            $aStores = $this->database()->select('dbus.name,dbus.logo_path,dbus.total_review,dbus.server_id,dbl.*,dbus.rating')
                ->from($this->_sTable, 'dbus')
                ->leftjoin(Phpfox::getT('ynstore_store_location'), 'dbl', 'dbl.store_id = dbus.store_id')
                ->where($sConditions)
                ->order($sOrder)
                ->group('dbus.store_id')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
        }

        if (count($aStores)) {

            foreach ($aStores as $key => $aStore) {
                if(!empty($aStore['logo_path']))
                { 
                    $aStores[$key]['url_image'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aStore['server_id'],
                        'path' => 'core.url_pic',
                        'file' => 'ynsocialstore/'.$aStore['logo_path'],
                        'ynsocialstore_overridenoimage' => true,
                        'suffix' => '_480_square',
                        'width' => 80,
                        'height' => 80,
                        'return_url' => true
                    ));
                }
                else
                {
                    $aStores[$key]['url_image'] = Phpfox::getParam('core.path_actual').'PF.Base/module/ynsocialstore/static/image/store_default.png';
                }
            }

        }
        return $aStores;
    }

    public function setAdvSearchConditions($aVals)
    {
        // Filter keywords
        if(isset($aVals['keywords']) && $aVals['keywords'] != '') {
            $this->search()->search('like%', 'st.name', $aVals['keywords']);
        }

        if(isset($aVals['location_address_lat']) && $aVals['location_address_lat'] != '' && isset($aVals['location_address_lng']) && $aVals['location_address_lng'] != '') {
            $aVals['radius'] = !empty($aVals['radius']) ? $aVals['radius'] : 100;
            if (Phpfox::getParam('ynsocialstore.default_distance_measurement_unit', 'mi') == 'km') {
                $aVals['radius'] = $aVals['radius'] * 0.621371;
            }
            $this->search()->setCondition("AND (3959 * acos(cos(radians('".$aVals['location_address_lat']."'))*cos( radians( sl.latitude ) ) * cos( radians( sl.longitude ) - radians('".$aVals['location_address_lng']."') ) + sin( radians('".$aVals['location_address_lat']."') ) * sin( radians( sl.latitude ) ) ) <= " . $aVals['radius'] .')');
        }

    }

    public function getAdvSearchConditions()
    {
        $aVals = array();

        $aVals['keywords'] = $this->request()->get('keywords');
        $aVals['location_address'] = $this->search()->get('location_address');
        $aVals['location_address_lat'] = $this->search()->get('location_address_lat');
        $aVals['location_address_lng'] = $this->search()->get('location_address_lng');
        $aVals['radius'] = $this->search()->get('radius');

        return $aVals;
    }

    public function getMostFollowedStores($iLimit)
    {
        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' =  st.user_id OR (f.user_id = st.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }
        $aRows = $this->database()->select('st.store_id, st.server_id,st.cover_server_id,st.name,st.cover_path,st.logo_path,sl.address,sl.location,st.total_follow,sl.longitude,sl.latitude')
            ->from($this->_sTable,'st')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = st.user_id')
            ->leftjoin(Phpfox::getT('ynstore_store_location'),'sl','st.store_id = sl.store_id')
            ->where('st.status="public" AND module_id ="ynsocialstore"')
            ->order('st.total_follow DESC')
            ->group('st.store_id')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        foreach ($aRows as &$aRow)
        {
            if (!empty($aRow['location'])) {
                $aRow['address'] = $aRow['location'];
            }
        }

        return $aRows;
    }

    public function getMostFavoriteStores($iLimit)
    {
        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' =  st.user_id OR (f.user_id = st.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }
        $aRows = $this->database()->select('st.store_id, st.server_id,st.cover_server_id,st.name,st.cover_path,st.logo_path,sl.address,sl.location,st.total_favorite,sl.longitude,sl.latitude')
            ->from($this->_sTable,'st')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = st.user_id')
            ->leftjoin(Phpfox::getT('ynstore_store_location'),'sl','st.store_id = sl.store_id')
            ->where('st.status="public" AND module_id ="ynsocialstore"')
            ->order('st.total_favorite DESC')
            ->group('st.store_id')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        foreach ($aRows as &$aRow)
        {
            if (!empty($aRow['location'])) {
                $aRow['address'] = $aRow['location'];
            }
        }

        return $aRows;
    }

    public function getFeaturedStore($iLimit)
    {
        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' =  st.user_id OR (f.user_id = st.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }
        $aRows = $this->database()->select('st.*,sl.address,sl.location,sl.longitude,sl.latitude,'.Phpfox::getUserField())
            ->from($this->_sTable,'st')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = st.user_id')
            ->leftjoin(Phpfox::getT('ynstore_store_location'),'sl','st.store_id = sl.store_id')
            ->where('st.status="public" AND module_id ="ynsocialstore" AND is_featured = 1')
            ->order('st.time_stamp DESC')
            ->group('st.store_id')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        foreach ($aRows as $key => $aRow)
        {
            $aRows[$key] = $this->retrieveMoreInfoFromStore($aRow);
        }

        return $aRows;
    }

    public function getAllFavorite($iStoreId, $iLimit = null, $iPage = null)
    {
        $iCount = $this->database()->select('COUNT(stf.user_id)')
            ->from(Phpfox::getT('ynstore_store_favorite'), 'stf')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = stf.user_id')
            ->where('stf.store_id = '.$iStoreId)
            ->execute('getSlaveField');

        $aRows = array();

        if ($iCount > 0) {
            $aRows = $this->database()->select(Phpfox::getUserField())
                ->from(Phpfox::getT('ynstore_store_favorite'), 'stf')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = stf.user_id')
                ->where('stf.store_id = '.$iStoreId)
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
        }

        return array($iCount, $aRows);
    }

    public function getAllFollowing($iStoreId, $iLimit = null, $iPage = null)
    {
        $iCount = $this->database()->select('COUNT(stf.user_id)')
            ->from(Phpfox::getT('ynstore_store_following'), 'stf')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = stf.user_id')
            ->where('stf.store_id = '.$iStoreId)
            ->execute('getSlaveField');

        $aRows = array();

        if ($iCount > 0) {
            $aRows = $this->database()->select(Phpfox::getUserField())
                ->from(Phpfox::getT('ynstore_store_following'), 'stf')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = stf.user_id')
                ->where('stf.store_id = '.$iStoreId)
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
        }

        return array($iCount, $aRows);
    }

    public function getCountStore($mConditions)
    {
        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' =  st.user_id OR (f.user_id = st.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }

        return $this->database()->select('COUNT( DISTINCT st.store_id)')
            ->from($this->_sTable, 'st')
            ->where($mConditions)
            ->execute('getField');
    }

    public function getToTalMyFavorite()
    {
        return $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('ynstore_store_favorite'),'fs')
            ->join($this->_sTable,'st','st.store_id = fs.store_id')
            ->where('st.status IN ("public","closed") and st.module_id like \'ynsocialstore\' and fs.user_id = '.Phpfox::getUserId())
            ->execute('getField');
    }

    public function getToTalMyFollowing()
    {

        return $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('ynstore_store_following'),'fs')
            ->join($this->_sTable,'st','st.store_id = fs.store_id')
            ->where('st.status IN ("public","closed") and st.module_id like \'ynsocialstore\' and fs.user_id = '.Phpfox::getUserId())
            ->execute('getField');
    }

    public function getFAQsByStoreId($iStoreId)
    {
        $aFAQs = $this->database()->select('stf.*')
            ->from(Phpfox::getT('ynstore_store_faq'), 'stf')
            ->where('stf.store_id = ' . $iStoreId)
            ->execute('getSlaveRows');

        return $aFAQs;
    }

    public function getFAQById($iFaqId)
    {
        $aFAQ = $this->database()->select('stf.*')
            ->from(Phpfox::getT('ynstore_store_faq'), 'stf')
            ->where('stf.faq_id = ' . $iFaqId)
            ->execute('getSlaveRow');

        return $aFAQ;
    }

    public function getOwnerEmail($iUserId)
    {
        return $this->database()->select('email')->from(Phpfox::getT('user'))->where('user_id = ' . $iUserId)->execute('getField');
    }

    public function getUserFullName($iUserId)
    {
        return $this->database()->select('full_name')->from(Phpfox::getT('user'))->where('user_id = ' . $iUserId)->execute('getField');
    }

    public function getStoreDetailToCompare($sStoreId)
    {
        $aStores = $this->database()->select('st.* ')
            ->from($this->_sTable, 'st')
            ->join(Phpfox::getT('user'),'u','st.user_id = u.user_id')
            ->where('st.store_id IN ('.$sStoreId.')')
            ->execute('getSlaveRows');
        if($aStores) {
            foreach ($aStores as $key => $aStore)
            {
                $aStores[$key] = $this->retrieveMoreInfoFromStore($aStore);
            }
        }
        return $aStores;
    }

    public function getStoresToCompare($sStoreId)
    {
        return $this->database()->select('st.name,st.store_id,st.logo_path,st.server_id')
                    ->from($this->_sTable,'st')
                    ->join(Phpfox::getT('user'),'u','u.user_id = st.user_id')
                    ->where('st.status != \'deleted\' AND st.store_id IN ('.$sStoreId.')')
                    ->execute('getSlaveRows');

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
                case 'categories':
                    $aFieldStatus['categories'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['categories'] = true;
                    }
                    break;
                case 'total_products':
                    $aFieldStatus['total_products'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['total_products'] = true;
                    }
                    break;
                case 'total_orders':
                    $aFieldStatus['total_orders'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['total_orders'] = true;
                    }
                    break;
                case 'total_views':
                    $aFieldStatus['total_views'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['total_views'] = true;
                    }
                    break;
                case 'total_reviews':
                    $aFieldStatus['total_reviews'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['total_reviews'] = true;
                    }
                    break;
                case 'payment_info':
                    $aFieldStatus['payment_info'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['payment_info'] = true;
                    }
                    break;
                case 'policy':
                    $aFieldStatus['policy'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['policy'] = true;
                    }
                    break;
                case 'buyer_protection':
                    $aFieldStatus['buyer_protection'] = false;
                    if ($valueaFields['enable']) {
                        $aFieldStatus['buyer_protection'] = true;
                    }
                    break;
            }
        }

        return $aFieldStatus;
    }

    public function getRandomPhotoForAllPhoto($iStoreId)
    {
        return $this->database()->select('*')
            ->from(Phpfox::getT('photo'))
            ->where('module_id = \'ynsocialstore\' AND group_id = '.$iStoreId)
            ->order('rand()')
            ->limit(1)
            ->execute('getRow');
    }

    public function cronUpdateStore()
    {
        $aStores = $this->database()->select('dbus.*')
            ->from($this->_sTable, 'dbus')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = dbus.user_id')
            ->where('dbus.expire_time > 0')
            ->execute('getSlaveRows');
        foreach($aStores as $key => $aStore) {
            if ((int)$aStore['expire_time'] < PHPFOX_TIME && $aStore['status'] != "expired")
            {
                $sLink = Phpfox_Url::instance()->makeUrl('ynsocialstore.store.manage-packages',['id' => $aStore['store_id']]);
                $sSubject = _p('your_store_store_name_package_has_expired',['store_name' => $aStore['name']]);
                $sText =  _p('email_content_store_is_expired',['store_name' => $aStore['name'],'link' => $sLink]);
                Phpfox::getService('ynsocialstore.process')->sendMail($aStore['user_id'], $sText, $sSubject);
                Phpfox::getService("notification.process")->add("ynsocialstore_expiredstore",$aStore['store_id'], $aStore['user_id'], $aStore['user_id'],true);
                $this->database()->update($this->_sTable, ['status' => 'expired'],'store_id ='. $aStore['store_id']);

            } elseif ((((int)$aStore['expire_time'] - PHPFOX_TIME) <= ($aStore['renew_before']*86400)) && $aStore['is_reminded'] == 0 ){

                $sLink = Phpfox_Url::instance()->makeUrl('ynsocialstore.store.manage-packages',['id' => $aStore['store_id']]);
                $sSubject = _p('your_store_store_name_package_is_going_to_expire',['store_name' => $aStore['name']]);
                $sText =  _p('email_content_going_to_expire',['store_name' => $aStore['name'],'date' => date('F j, Y',$aStore['expire_time']),'link' => $sLink]);
                Phpfox::getService('ynsocialstore.process')->sendMail($aStore['user_id'], $sText, $sSubject);
                $this->database()->update($this->_sTable,['is_reminded' => 1],'store_id ='. $aStore['store_id']);
            }

            if ($aStore['is_featured'] && $aStore['feature_end_time'] > 1 && $aStore['feature_end_time'] < PHPFOX_TIME)
            {
                $this->database()->update($this->_sTable,['is_featured' => 0],'store_id ='. $aStore['store_id']);
            }
        }
    }

    public function getAllUserStore($iUserId)
    {
        $aStores = $this->database()->select('st.store_id,st.name,st.package_id,st.status,st.user_id,st.total_products')
                        ->from($this->_sTable,'st')
                        ->join(Phpfox::getT('user'),'u','u.user_id = st.user_id')
                        ->where('st.module_id like \'ynsocialstore\' AND st.status NOT IN (\'deleted\',\'draft\') AND st.user_id = '.$iUserId)
                        ->execute('getSlaveRows');
        if(count($aStores))
        {
            foreach($aStores as $key => $aStore)
            {
                $iCheck = Phpfox::getService('ynsocialstore.permission')->canCreateProduct($aStore);
                if($iCheck != 2)
                {
                    $aStores[$key]['canAddProduct'] = false;
                }
                else{
                    $aStores[$key]['canAddProduct'] = true;
                }

            }
        }
        return $aStores;
    }

    public function getWeeklyHotSellers($iLimit)
    {
        if (Phpfox::isModule('friend') && Phpfox::getParam('core.friends_only_community')) {
            $this->database()->join(Phpfox::getT('friend'), 'f', Phpfox::getUserId() . ' =  st.user_id OR (f.user_id = st.user_id AND f.friend_user_id = ' . Phpfox::getUserId() . ')');
        }

        $iStartOfWeek = Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart());
        $iEndOfWeek = Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd());

        $aRows = $this->database()->select('st.*, sl.address, COUNT(eop.orderproduct_order_id) as total_orders')
            ->from($this->_sTable, 'st')
            ->join(Phpfox::getT('ecommerce_order_product'), 'eop', 'eop.orderproduct_parent_id = st.store_id')
            ->join(Phpfox::getT('ecommerce_order'),'eo','eo.order_id = eop.orderproduct_order_id AND eop.orderproduct_module like \'ynsocialstore\' AND eo.order_payment_status = \'completed\'')
            ->leftjoin(Phpfox::getT('ynstore_store_location'),'sl','st.store_id = sl.store_id')
            ->where('eo.order_creation_datetime >='.$iStartOfWeek.' AND eo.order_creation_datetime <='.$iEndOfWeek)
            ->group('eop.orderproduct_parent_id')
            ->order('total_orders DESC')
            ->limit($iLimit)
            ->execute('getRows');

        foreach ($aRows as $iKey => $aItem)
        {
            $aRows[$iKey] = $this->retrieveMoreInfoFromStore($aItem);
        }

        return $aRows;
    }

}
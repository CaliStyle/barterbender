<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Ecommerce extends Phpfox_Service {

    public function __construct()
    {
        
    }

    public function getCustomFieldByCategoryId($iCategoryId)
    {
        $sWhere = '';
        $sWhere .= ' AND ccd.category_id = ' . (int) $iCategoryId;
        $aFields = $this->database()
                ->select('cfd.*')
                ->from(Phpfox::getT("ecommerce_category_customgroup_data"), 'ccd')
                ->join(Phpfox::getT('ecommerce_custom_group'), 'cgr', ' ( cgr.group_id = ccd.group_id AND cgr.is_active = 1 ) ')
                ->join(Phpfox::getT('ecommerce_custom_field'), 'cfd', ' ( cfd.group_id = cgr.group_id ) ')
                ->where('TRUE ' . $sWhere)
                ->order('cgr.group_id ASC , cfd.ordering ASC, cfd.field_id ASC')
                ->execute("getSlaveRows");

        $aHasOption = Phpfox::getService('ecommerce.custom')->getHasOption();
        if (is_array($aFields) && count($aFields))
        {
            foreach ($aFields as $k => $aField)
            {
                if (in_array($aField['var_type'], $aHasOption))
                {
                    $aOptions = $this->database()->select('*')->from(Phpfox::getT('ecommerce_custom_option'))->where('field_id = ' . $aField['field_id'])->order('option_id ASC')->execute('getSlaveRows');
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

    public function getGlobalSetting()
    {
        $aRow = $this->database()
                ->select('gbs.*')
                ->from(Phpfox::getT("ecommerce_global_setting"), 'gbs')
                ->execute("getSlaveRow");
        
        if ($aRow)
        {
            $aRow['default_setting'] = (array) json_decode($aRow['default_setting']);
            $aRow['actual_setting'] = (array) json_decode($aRow['actual_setting']);
        }
        
        return $aRow;
    }

    public function getInvoice($iId)
    {
        $aPurchase = $this->database()->select('sp.*')
            ->from(Phpfox::getT('ecommerce_invoice'), 'sp')
            ->where('sp.invoice_id = ' . (int) $iId)
            ->execute('getRow');
            
        if (!isset($aPurchase['invoice_id']))
        {
            return false;
        }
        
        $aCurrentCurrencies = Phpfox::getService('ecommerce.helper')->getCurrentCurrencies();
        $aPurchase['default_cost'] = $aPurchase['price'];
        $aPurchase['default_currency_id'] = $aCurrentCurrencies[0]['currency_id'];
                    
        return $aPurchase;      
    }

    public function getQuickProductById($iProductId, $bForce = false)
    {
        $aItem = $this->database()->select('ep.*')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->where('ep.product_id = ' . (int) $iProductId)
            ->execute('getSlaveRow');

        return $aItem;  
    }

    public function getProductForEdit($iProductId, $bForce = false)
    {
        $aItem = $this->database()->select('ep.*')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->where('ep.product_id = ' . (int) $iProductId)
            ->execute('getSlaveRow');

        return $aItem;
    }

    public function getAdditionInfo($iProductId){

        $AdditionInfo = $this->database()->select('epuc.usercustomfield_title,epuc.usercustomfield_content')
                ->from(Phpfox::getT('ecommerce_product_usercustomfield'), 'epuc')
                ->where('epuc.product_id = '.$iProductId)
                ->execute('getRows');

        return $AdditionInfo;    
    }

    public function getImages($iProductId, $iLimit = null)
    {
        $aImages = $this->database()->select('epi.*')
            ->from(Phpfox::getT('ecommerce_product_image'),'epi')
            ->where('epi.product_id = '.$iProductId)
            ->order('epi.ordering ASC')
            ->limit($iLimit)
            ->execute('getSlaveRows');
        
        if($aImages)
        {
            foreach($aImages as $k=>$aImage)
            {
                $aImages[$k]['image'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aImage['server_id'],
                        'file' => $aImage['image_path'],
                        'path' => 'core.url_pic',
                        'suffix' => '_120',
                        'max_width' => '120',
                        'max_height' => '120'               
                    )
                );
            }
        }
        
        return $aImages;
    }

    public function getAlbumByProductId($iProductId, $aConds = array(), $aExtra = array(), $getData = true){
        $table = Phpfox::getT('photo_album'); 
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'pa')        
            ->where('pa.view_id = 0 AND pa.privacy IN(0) AND pa.total_photo > 0 AND pa.profile_id = 0 AND pa.module_id = \'ecommerce\' AND pa.group_id = ' . (int)$iProductId . ' AND ' . $sCond)
            ->execute('getSlaveField');     
        if($getData){
            if($iCount){

                if($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like'))
                {
                    $this->database()->select('l.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'l', ' (l.type_id = "photo_album" AND l.item_id = pa.album_id AND l.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("p.destination, p.server_id, pa.*, " . Phpfox::getUserField())
                    ->from($table, 'pa')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
                    ->leftJoin(Phpfox::getT('photo'), 'p', ' (p.album_id = pa.album_id AND pa.view_id = 0 AND p.is_cover = 1) ') 
                    ->where('pa.view_id = 0 AND pa.privacy IN(0) AND pa.total_photo > 0 AND pa.profile_id = 0 AND pa.module_id = \'ecommerce\' AND pa.group_id = ' . (int)$iProductId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');              
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getPhotoByProductId($iProductId, $aConds = array(), $aExtra = array(), $getData = true){
        $table = Phpfox::getT('photo'); 
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'photo')     
            ->where('photo.view_id = 0 AND photo.module_id = \'ecommerce\' AND photo.group_id = ' . (int)$iProductId . ' AND photo.privacy IN(0)' . ' AND ' . $sCond)
            ->execute('getSlaveField');     

        if($getData){
            if($iCount){

                if($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like'))
                {
                    $this->database()->select('l.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'l', ' (l.type_id = "photo" AND l.item_id = photo.photo_id AND l.user_id = ' . Phpfox::getUserId() . ') ');
                    $this->database()->select('adisliked.action_id as is_disliked, ')
                        ->leftJoin(Phpfox::getT('action'), 'adisliked', ' (adisliked.action_type_id = 2 AND adisliked.item_id = photo.photo_id AND adisliked.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()->select("pa.name AS album_name, pa.profile_id AS album_profile_id, ppc.name as category_name, ppc.category_id, photo.*, " . Phpfox::getUserField())
                    ->from($table, 'photo')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = photo.user_id')
                    ->leftJoin(Phpfox::getT('photo_album'), 'pa', ' (pa.album_id = photo.album_id) ') 
                    ->leftJoin(Phpfox::getT('photo_category_data'), 'ppcd', ' (ppcd.photo_id = photo.photo_id) ') 
                    ->leftJoin(Phpfox::getT('photo_category'), 'ppc', ' (ppc.category_id = ppcd.category_id) ') 
                    ->where('photo.view_id = 0 AND photo.module_id = \'ecommerce\' AND photo.group_id = ' . (int)$iProductId . ' AND photo.privacy IN(0) AND ' . $sCond)
                    ->group('photo.photo_id')
                    ->execute('getSlaveRows');
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getVideoByProductId($iProductId, $aConds = array(), $aExtra = array(), $getData = true){
       
        $table = Phpfox::getT('video'); 
        if(Phpfox::getService('auction.helper')->isAdvVideo()){
            $table = Phpfox::getT('channel_video'); 
        }
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($table, 'm')     
            ->where('m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'ecommerce\' AND m.item_id = ' . (int)$iProductId . ' AND m.privacy IN(0)'  . ' AND ' . $sCond)
            ->execute('getSlaveField');     
        if($getData){
            if($iCount){
                if($aExtra && isset($aExtra['limit'])) {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if($aExtra && isset($aExtra['order'])) {
                    $this->database()->order($aExtra['order']);
                }

                $aRows = $this->database()->select("m.*, " . Phpfox::getUserField())
                    ->from($table, 'm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                    ->where('m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'ecommerce\' AND m.privacy IN(0) AND m.item_id = ' . (int)$iProductId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');              
            }
        } else {
            return $iCount;
        }

        return array($aRows, $iCount);
    }


    public function getNumberOfItemInEcommerce($iProductId, $sType)
    {
        $iCount = 0;
        $aConds = array(' 1=1 ');
        $aExtra = array();
        $getData = false;

        switch ($sType) {
            case 'photos':
                if (Phpfox::getService('ecommerce.helper')->isPhoto())
                {
                    $iCount = $this->getPhotoByProductId($iProductId, $aConds, $aExtra, $getData);
                }
                break;
            case 'videos':
                if (Phpfox::getService('ecommerce.helper')->isVideo())
                {
                    $iCount = $this->getVideoByProductId($iProductId, $aConds, $aExtra, $getData);
                }
                break;
        }

        return $iCount;
    }

    public function getMyCartId(){

        $iCartId = $this->database()->select("ec.cart_id")
                        ->from(Phpfox::getT('ecommerce_cart'), 'ec')
                        ->where('ec.cart_user_id = '.(int)Phpfox::getUserId())
                        ->execute('getSlaveField');
        return $iCartId;
    }
    public function getMyCartData($sModule = 'auction',$iCartProductId = 0){

        if($sModule == 'ynsocialstore')
        {
            $aDataMyCart = $this->database()->select("ec.*,ecp.*,ep.*," . Phpfox::getUserField() . ',eu.title as uom_title')
                ->from(Phpfox::getT('ecommerce_cart'), 'ec')
                ->join(Phpfox::getT('ecommerce_cart_product'), 'ecp', 'ec.cart_id = ecp.cartproduct_cart_id')
                ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = ecp.cartproduct_product_id')
                ->join(Phpfox::getT('ynstore_store'), 'st', 'ep.item_id = st.store_id')
                ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ep.uom_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->where('ec.cart_user_id = ' . (int)Phpfox::getUserId() . ' AND ep.product_status = \'running\' AND st.status = \'public\' AND ecp.cartproduct_payment_status =\'init\' AND ecp.cartproduct_module like "' . $sModule . '"'.(($iCartProductId) ? ' AND ecp.cartproduct_id ='.$iCartProductId : ''))
                ->order('st.store_id DESC')
                ->execute('getSlaveRows');
        }
        else {
            $aDataMyCart = $this->database()->select("ec.*,ecp.*,ep.*," . Phpfox::getUserField() . ',eu.title as uom_title')
                    ->from(Phpfox::getT('ecommerce_cart'), 'ec')
                    ->join(Phpfox::getT('ecommerce_cart_product'), 'ecp', 'ec.cart_id = ecp.cartproduct_cart_id')
                    ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = ecp.cartproduct_product_id')
                    ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ep.uom_id')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                    ->where('ec.cart_user_id = ' . (int)Phpfox::getUserId() . ' AND ecp.cartproduct_payment_status =\'init\' AND ecp.cartproduct_module like "' . $sModule . '"')
                    ->execute('getSlaveRows');
        }
        $aData = array();
        $aProductElements = [];
        $aListMaxQuantityElement = [];
        $iCount = 0;
        $bIsOnlyDigital = true;
        /*maybe dont using this processing*/
        if(count($aDataMyCart)){
            foreach ($aDataMyCart as $key => $aItem) {
                if($aItem['cartproduct_module'] == 'auction'){
                    $aAuction = $this->database()->select("ep.end_time, epa.*")
                        ->from(Phpfox::getT('ecommerce_product'), 'ep')
                        ->leftjoin(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                        ->where('epa.product_id = ' . (int)$aItem['product_id'])
                        ->execute('getSlaveRow');

                    // If auction is completed and current user is not winner. Remove it
                    if ($aAuction['end_time'] <= PHPFOX_TIME && Phpfox::getUserId() != $aAuction['auction_won_bidder_user_id']) {
                        unset($aDataMyCart[$key]);
                        continue;
                    }
                    switch ($aItem['cartproduct_type']) {
                        case 'buy':
                            $aItem['cartproduct_price'] = $aAuction['auction_item_buy_now_price']; 
                            break;
                    }
                    $aItem['cartproduct_subtotal'] = number_format($aItem['cartproduct_quantity'] * $aItem['cartproduct_price'],2);
                    $keySeller = $aItem['user_id'];
                    $aItem['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aItem['creating_item_currency']);
                    $aData[$keySeller][] = $aItem;
                }
                if($aItem['cartproduct_module'] == 'ynsocialstore'){
                    $aProduct =  $this->database()->select("eps.discount_start_date,eps.discount_end_date,eps.discount_timeless,eps.discount_percentage,eps.min_order,eps.max_order,eps.product_type,eps.enable_inventory,eps.discount_price,eps.attribute_style,eps.attribute_name,epa.attribute_id,epa.title,epa.image_path,epa.quantity,epa.price,epa.server_id,epa.remain,epa.color")
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
                        $aStore = $this->database()->select('store_id,name,user_id,tax')->from(Phpfox::getT('ynstore_store'))->where('store_id = ' . $aItem['item_id'])->execute('getRow');
                        $quantity_in_cart_order = Phpfox::getService('ynsocialstore.product')->countOrderAndCartOfProductByUser($aItem['product_id'], (int)Phpfox::getUserId()) - $aItem['cartproduct_quantity'];
                        if($aProduct['product_type'] == 'physical')
                        {
                            $bIsOnlyDigital = false;
                        }
                        if($aProduct['enable_inventory'] && $aProduct['product_type'] == 'physical')
                        {
                            if($aItem['product_quantity_main'] > 0)
                            {
                                if($aItem['product_quantity'] > $aProduct['max_order'] && $aProduct['max_order'] > 0)
                                {
                                    if($aProduct['max_order'] > $quantity_in_cart_order)
                                    {
                                        $aProduct['max_quantity_can_add'] = $aProduct['max_order'] - $quantity_in_cart_order;
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
                                    $aProduct['max_quantity_can_add'] = $aProduct['max_order'] - $quantity_in_cart_order;
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
                        if (isset($aProductElements[$aItem['product_id']])) {
                            $aItem['element_list'] = $aProductElements[$aItem['product_id']];
                        } else {
                            if ($aProduct['product_type'] == 'physical') {
                                $aElements = Phpfox::getService('ynsocialstore.product')->getAllElements($aItem['product_id']);
                                if (count($aElements)) {
                                    $aElementsOnOrder = Phpfox::getService('ynsocialstore.product')->countOrderOfProduct($aItem['product_id']);
                                    $aNewElements = [];
                                    foreach ($aElements as $key => $aElement) {
                                        $iInUsed = 0;

                                        if (isset($aElementsOnOrder[$aElement['attribute_id']])) {
                                            $iInUsed = $iInUsed + $aElementsOnOrder[$aElement['attribute_id']];
                                        }
                                        $aElements[$key]['quantity_sold'] = $iInUsed;
                                        $aElements[$key]['remain_of_attribute'] = ($aElement['quantity'] > 0) ? ($aElement['quantity'] - $iInUsed) : 0;

                                        if ($aElement['quantity'] == 0 && $aProduct['max_quantity_can_add'] == 'unlimited') {
                                            $aElements[$key]['real_quantity_can_add'] = 'unlimited';
                                        } elseif ($aProduct['max_quantity_can_add'] !== 'unlimited' && ($aElement['quantity'] == 0 || $aElements[$key]['remain_of_attribute'] > $aProduct['max_quantity_can_add'])) {
                                            $aElements[$key]['real_quantity_can_add'] = $aProduct['max_quantity_can_add'];
                                        } elseif ($aElement['quantity'] > 0 && ($aElements[$key]['remain_of_attribute'] <= $aProduct['max_quantity_can_add'] || $aProduct['max_quantity_can_add'] == 'unlimited')) {
                                            $aElements[$key]['real_quantity_can_add'] = $aElements[$key]['remain_of_attribute'];
                                        }
                                        $aNewElements[$aElement['attribute_id']] = $aElements[$key];
                                        $aListMaxQuantityElement[$aElement['attribute_id']] = $aElements[$key]['real_quantity_can_add'];
                                    }
                                    $aItem['element_list'] = $aNewElements;
                                    $aProductElements[$aItem['product_id']] = $aNewElements;
                                } else {
                                    $aProductElements[$aItem['product_id']] = $aItem['element_list'] = [];
                                }
                            } else {
                                $aProductElements[$aItem['product_id']] = $aItem['element_list'] = [];
                            }
                        }
                        $aItem['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aItem['creating_item_currency']);
                        if (isset($aItem['element_list'][$aItem['cartproduct_attribute_id']])) {
                            $aItem['real_quantity_can_add'] = $aItem['element_list'][$aItem['cartproduct_attribute_id']]['real_quantity_can_add'];
                        } else{
                            $aItem['real_quantity_can_add'] = $aProduct['max_quantity_can_add'] ;
                        }
                        $aItem = array_merge($aItem, $aProduct);
                        $aData[$keySeller]['store'] = $aStore;
                        $aData[$keySeller]['items'][] = $aItem;
                    }
                }

            }
        }
        if($sModule == 'ynsocialstore')
            return array($iCount,$aListMaxQuantityElement, $aData,$bIsOnlyDigital);
        return $aData;
    }

    public function getCountNumberCartItem($sModule = 'auction'){
        $sWhere = '';
        if ($sModule == 'auction')
            $sWhere = ' AND end_time > ' . PHPFOX_TIME;

        $iNumberItem = $this->database()->select("COUNT(ecp.cartproduct_product_id) as item")
                ->from(Phpfox::getT('ecommerce_cart'), 'ec')
                ->join(Phpfox::getT('ecommerce_cart_product'), 'ecp', 'ec.cart_id = ecp.cartproduct_cart_id')
                ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = ecp.cartproduct_product_id')
                ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ep.uom_id')
                ->where('ec.cart_user_id = '.(int)Phpfox::getUserId().' AND ecp.cartproduct_payment_status =\'init\' AND ecp.cartproduct_module like \''.$sModule.'\'' . $sWhere)
                ->execute('getSlaveField');
        return $iNumberItem;
    }


    public function getAddressUserId($iUserId){
        $aAddresses = $this->database()->select("ea.*")
                ->from(Phpfox::getT('ecommerce_address'), 'ea')
                ->where('ea.address_user_id = '.(int)$iUserId)
                ->execute('getSlaveRows');
 
        if(count($aAddresses)){
            foreach($aAddresses as $keyAddress => $aAddress){
                 $aLocation = array();
                if (!empty($aAddress['address_customer_location_address']))
                {
                    $aLocation[] = $aAddress['address_customer_location_address'];
                }
				if (!empty($aAddress['address_customer_street']))
                {
                    $aLocation[] = $aAddress['address_customer_street'];
                }
                if (!empty($aAddress['address_customer_street_2']))
                {
                    $aLocation[] = $aAddress['address_customer_street_2'];
                }
                if (!empty($aAddress['address_customer_city']))
                {
                    $aLocation[] = $aAddress['address_customer_city'];
                }
                if (!empty($aAddress['address_customer_country_iso']))
                {
                    $aLocation[] = Phpfox::getService('core.country')->getCountry($aAddress['address_customer_country_iso']);
                }
                if ($aAddress['address_customer_country_child_id'])
                {
                    $aLocation[] = Phpfox::getService('core.country')->getChild($aAddress['address_customer_country_child_id']);
                }
                $sLocation = implode(', ', $aLocation);
                $aAddresses[$keyAddress]['sLocation'] = $sLocation;
                
            }
        }
        return $aAddresses;
    }

    public function getAddressById($iAddressId){
        $aAddress = $this->database()->select("ea.*")
                ->from(Phpfox::getT('ecommerce_address'), 'ea')
                ->where('ea.address_id = '.(int)$iAddressId)
                ->execute('getSlaveRow');

        return $aAddress;
    }

    public function getProductAddCallback($iId)
    {
        Phpfox::getService('pages')->setIsInPage();

        return array(
            'module' => 'pages',
            'item_id' => $iId,
            'table_prefix' => 'pages_'
        );
    }

    public function getFieldsComparison(){
        
    }
	public function getStaticPath(){
		$sCorePath = Phpfox::getParam('core.path');
		$sCorePath = str_replace("index.php".PHPFOX_DS, "", $sCorePath);
		$sCorePath .= 'PF.Base'.PHPFOX_DS;
		return $sCorePath;
	}

	public function getTransactionItem($iItemId, $sTemType)
    {
        switch ($sTemType)
        {
            case 'auction':
            case 'product':
                $aItem = $this->getQuickProductById($iItemId);
                $aItem['permalink'] = Phpfox_Url::instance()->permalink($aItem['module_id'].'.'.'detail', $aItem['product_id'], $aItem['name']);
                return $aItem;
                break;
            case 'store':
                if (Phpfox::isModule('ynsocialstore')) {
                    $aItem = Phpfox::getService('ynsocialstore')->getStoreById($iItemId);
                    $aItem['permalink'] = Phpfox_Url::instance()->permalink($aItem['module_id'].'.'.'store', $aItem['store_id'], $aItem['name']);
                    return $aItem;
                }
                break;
        }

        return array();
    }

}

?>
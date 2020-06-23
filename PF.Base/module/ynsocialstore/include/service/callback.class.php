<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/4/16
 * Time: 6:54 PM
 */
class Ynsocialstore_Service_Callback extends Phpfox_Service
{
    public function getMoreInfomationForProduct() {
        $callbackData = [
            'default_product_image' => Phpfox::getParam('core.path_actual').'PF.Base/module/ynsocialstore/static/image/product_default.jpg'
        ];
        return $callbackData;
    }
    
    public function getSiteStatsForAdmin($startTime, $endTime) {
        $storeConditions = ['AND status = "public"'];
        $productConditions = ['AND p.product_status = "running" AND p.module_id = "ynsocialstore"', 'AND s.status = "public"'];
        if($startTime > 0) {
            $productConditions[] = 'AND p.product_approved_datetime >= \''. db()->escape($startTime) . '\'';
            $storeConditions[] = 'AND time_stamp >= \''. db()->escape($startTime) .'\'';
        }
        if($endTime > 0) {
            $productConditions[] = 'AND p.product_approved_datetime <= \''. db()->escape($endTime) .'\'';
            $storeConditions[] = 'AND time_stamp <= \''. db()->escape($endTime) .'\'';
        }
        return [
            'merge_result' => true,
            'result' => [
                'ynsocialstore' => [
                    'phrase' => 'ynsocialstore.social_store',
                    'total' => db()->select('COUNT(*)')
                        ->from(Phpfox::getT('ynstore_store'))
                        ->where($storeConditions)
                        ->execute('getSlaveField')
                ],
                'ynsocialstore_product' => [
                    'phrase' => 'ynsocialstore.social_store_product',
                    'total' => db()->select('COUNT(*)')
                        ->from(Phpfox::getT('ecommerce_product'),'p')
                        ->join(Phpfox::getT('ynstore_store'),'s', 's.store_id = p.item_id')
                        ->where($productConditions)
                        ->execute('getSlaveField')
                ]
            ]
        ];
    }

    public function getSiteStatsForAdmins() {
        $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $storeConditions = [
            'AND status = "public"',
            'AND time_stamp >= \''. $today .'\''
        ];
        $productConditions = [
            'AND p.product_status = "running" AND p.module_id = "ynsocialstore" AND s.status = "public"',
            'AND p.product_approved_datetime >= \''. $today . '\''
        ];
        return [
            'merge_result' => true,
            'result' => [
                'ynsocialstore' => [
                    'phrase' => _p('social_store'),
                    'value' => db()->select('COUNT(*)')
                        ->from(Phpfox::getT('ynstore_store'))
                        ->where($storeConditions)
                        ->execute('getSlaveField')
                ],
                'ynsocialstore_product' => [
                    'phrase' => _p('ynsocialstore.social_store_product'),
                    'value' => db()->select('COUNT(*)')
                        ->from(Phpfox::getT('ecommerce_product'),'p')
                        ->join(Phpfox::getT('ynstore_store'),'s', 's.store_id = p.item_id')
                        ->where($productConditions)
                        ->execute('getSlaveField')
                ]
            ]
        ];
    }
    
    public function addPhoto($iId)
    {
        return array(
            'module' => 'ynsocialstore',
            'item_id' => $iId,
            'table_prefix' => 'ynstore_'
        );
    }

    public function getPhotoDetails($aPhoto)
    {
        // Phpfox::getService('ynsocialstore')->setIsInPage();

        $aRow = Phpfox::getService('ynsocialstore')->getFieldsStoreById('name,store_id',$aPhoto['group_id'],'getRow');

        if (!isset($aRow['store_id']))
        {
            return false;
        }

        // Phpfox::getService('ynsocialstore')->setMode();

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

        return array(
            'breadcrumb_title' => _p('social_store'),
            'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('ynsocialstore.store'),
            'module_id' => 'ynsocialstore',
            'item_id' => $aRow['store_id'],
            'title' => $aRow['name'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'photos/',
            'theater_mode' => _p('in_the_store_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
        );
    }
    public function paymentApiCallback($aParams){

        Phpfox::log('Module callback recieved: ' . var_export($aParams, true));
        Phpfox::log('Attempting to retrieve purchase from the database');


        $aInvoice = Phpfox::getService('ynsocialstore')->getInvoice($aParams['item_number']);


        if ($aInvoice === false)
        {
            Phpfox::log('Not a valid invoice');

            return false;
        }
        if($aInvoice['type'] == 'product_feature')
        {
            $aItem = Phpfox::getService('ynsocialstore.product')->getProductById($aInvoice['item_id']);
        }
        else {
            $aItem = Phpfox::getService('ynsocialstore')->getStoreById($aInvoice['item_id']);
        }
        if ($aItem === false)
        {
            Phpfox::log('Not a valid listing.');

            return false;
        }

        Phpfox::log('Purchase is valid: ' . var_export($aInvoice, true));

        if ($aParams['status'] == 'completed')
        {
            if ($aParams['total_paid'] == $aInvoice['price'])
            {
                Phpfox::log('Paid correct price');
            }
            else
            {
                Phpfox::log('Paid incorrect price');

                return false;
            }
        }
        else
        {
            Phpfox::log('Payment is not marked as "completed".');

            return false;
        }

        $this->database()->update(Phpfox::getT('ecommerce_invoice'), array(
            'status' => $aParams['status'],
            'param' => json_encode($aParams),
            'payment_method' => isset($aParams['gateway']) ? $aParams['gateway'] : '',
            'time_stamp_paid' => PHPFOX_TIME
        ), 'invoice_id = ' . $aInvoice['invoice_id']
        );
        // update data
        switch ($aInvoice['type']) {
            case 'store':
                // create new store or submit 1 DRAFT
                $aInvoice['invoice_data'] = (array)json_decode($aInvoice['invoice_data']);

                $pay_type = explode("|", $aInvoice['invoice_data']['pay_type']);
                $start_time = PHPFOX_TIME;
                foreach($pay_type as $val){
                    switch ($val) {
                        case 'package':
                            //update package info,if change

                            if(isset($aInvoice['invoice_data']['change_package_id'])){
                                Phpfox::getService('ynsocialstore.process')->updatePackageForStore($aInvoice['invoice_data']['change_package_id'],$aItem['store_id']);
                            }
                            // update status
                            $status = 'draft';
                            if($aItem['start_time'] == 0 || $aItem['expire_time'] == 0 ){
                                //still not approved
                                $status = 'draft';
                                if(Phpfox::getService('ynsocialstore.helper')->getUserParam('ynsocialstore.auto_approved_store',(int)$aItem['user_id'])){
                                    $status = 'public';
                                } else {
                                    $status = 'pending';
                                }
                            }
                            else{
                                //already approved
                                $status = 'public';
                            }

                            Phpfox::getService('ynsocialstore.process')->updateStoreStatus($aItem['store_id'], $status);

                            if($status == 'public'){
                                // call approve function
                                Phpfox::getService('ynsocialstore.process')->approveStoreByPackage($aItem['store_id'], null);
                            }
                            $aPackage = Phpfox::getService('ynsocialstore.package')->getPackageByStoreId($aItem['store_id']);
                            (($sPlugin = Phpfox_Plugin::get('ynsocialstore.service_callback_payment_store_publish__end')) ? eval($sPlugin) : false);
                            break;

                        case 'feature':
                            $feature_days = (int)$aInvoice['invoice_data']['feature_days'] + (int)$aItem['feature_day'];
                            Phpfox::getService('ynsocialstore.process')->updateStoreFeatureTime($aItem['store_id'], $aItem['feature_end_time'], $feature_days);

                            //case package free
                            if(!in_array("package", $pay_type)){

                                $status = 'draft';
                                if(Phpfox::getService('ynsocialstore.helper')->getUserParam('ynsocialstore.auto_approved_store',(int)$aItem['user_id'])){
                                    $status = 'public';
                                } else {
                                    $status = 'pending';
                                }

                                Phpfox::getService('ynsocialstore.process')->updateStoreStatus($aItem['store_id'], $status);

                                if($status == 'public'){
                                    // call approve function
                                    Phpfox::getService('ynsocialstore.process')->approveStoreByPackage($aItem['store_id'], null);
                                }

                            }
                            $aPackage = Phpfox::getService('ynsocialstore.package')->getPackageByStoreId($aItem['store_id']);
                            (($sPlugin = Phpfox_Plugin::get('ynsocialstore.service_callback_payment_store_feature__end')) ? eval($sPlugin) : false);
                            break;
                    }
                }

                break;
            case 'feature':
                // update featured time
                $aInvoice['invoice_data'] = (array)json_decode($aInvoice['invoice_data']);
                $pay_type = explode("|", $aInvoice['invoice_data']['pay_type']);

                foreach($pay_type as $val){
                    switch ($val) {
                        case 'feature':

                            $feature_days = 0;
                            /*not approved*/
                            if($aItem['feature_end_time'] == 0){

                                $end_time = 0;
                                $feature_days = (int)$aInvoice['invoice_data']['feature_days'] + (int)$aItem['feature_day'];
                            }
                            else{
                                /*already approved*/

                                if(PHPFOX_TIME < $aItem['feature_end_time']){	//still in feature,wanna expend featured time.
                                    $end_time =   $aItem['feature_end_time'] + (int)$aInvoice['invoice_data']['feature_days']*86400 ;
                                    $feature_days = (int)$aInvoice['invoice_data']['feature_days'] + (int)$aItem['feature_day'];
                                }
                                else{
                                    $start_time = PHPFOX_TIME;
                                    $feature_days = (int)$aInvoice['invoice_data']['feature_days'];
                                    $end_time =   $start_time + $feature_days*86400;
                                }
                            }

                            if($end_time >= 4294967295){
                                $end_time = 4294967295;
                            }

                            Phpfox::getService('ynsocialstore.process')->updateStoreFeatureTime($aItem['store_id'],$end_time, $feature_days);
                            $aPackage = Phpfox::getService('ynsocialstore.package')->getPackageByStoreId($aItem['store_id']);
                            (($sPlugin = Phpfox_Plugin::get('ynsocialstore.service_callback_payment_store_feature__end')) ? eval($sPlugin) : false);
                            break;
                    }
                }

                break;
            case 'product_feature':
                // update featured time
                $aStore = Phpfox::getService('ynsocialstore')->getStoreById($aItem['item_id']);
                $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);
                $aInvoice['invoice_data'] = (array)json_decode($aInvoice['invoice_data']);
                $pay_type = explode("|", $aInvoice['invoice_data']['pay_type']);

                foreach($pay_type as $val){
                    switch ($val) {
                        case 'publish':

                            //handle to publish with feature zero
                            if((int)$aItem['feature_day'] > 0){

                                if((int)$aItem['feature_day'] * $aPackage['feature_product_fee'] <= 0)  {
                                    $start_feature_time = 0;
                                    $end_feature_time = 0;

                                    $start_time = $aItem['start_time'];

                                    if($start_time < PHPFOX_TIME){/*in available time of auction*/
                                        $start_feature_time = PHPFOX_TIME;
                                    }
                                    else{/*start time of auction in future*/
                                        $start_feature_time = $start_time;

                                    }

                                    $end_feature_time = $start_feature_time + ((int)$aItem['feature_day'] * 86400);

                                    if($end_feature_time >= 4294967295){
                                        $end_feature_time = 4294967295;
                                    }


                                    $featureFee = doubleval(((int)$aItem['feature_day'] * Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_featured',(int)$aItem['user_id']) ));
                                    Phpfox::getService('ecommerce.process')->updateProductFeatureTime($aItem['product_id'], $start_feature_time, $end_feature_time,(int)$aItem['feature_day'],$featureFee);

                                }
                            }


                            //handle to update status when publish
                            $status = 'draft';
                            if(Phpfox::getService('ecommerce.helper')->getUserParam('ynsocialstore.auto_approved_product', (int)$aItem['user_id'])){
                                $status = 'approved';
                            } else {
                                $status = 'pending';
                            }


                            Phpfox::getService('ecommerce.process')->updateProductStatus($aItem['product_id'], $status);

                            if($status == 'approved'){
                                // call approve function
                                Phpfox::getService('ecommerce.process')->approveProduct($aItem['product_id'], null,'ynsocialstore_product');
                            }

                            break;

                        case 'feature':

                            //handle to update feature time
                            if($aItem['feature_end_time'] == 0){

                                $start_feature_time = PHPFOX_TIME;
                                $feature_days = (int)$aInvoice['invoice_data']['feature_days'];
                                $end_feature_time =   $start_feature_time + $feature_days*86400;
                            }
                            else{
                                /*already approved*/

                                if(PHPFOX_TIME < $aItem['feature_end_time']){	//still in feature,wanna expend featured time.
                                    $start_feature_time = $aItem['feature_start_time'];
                                    $end_feature_time =   $aItem['feature_end_time'] + (int)$aInvoice['invoice_data']['feature_days']*86400 ;
                                    //update feature day of product before payment
                                    $feature_days = (int)$aItem['feature_day'];
                                }
                                else{
                                    $start_feature_time = PHPFOX_TIME;
                                    $feature_days = (int)$aInvoice['invoice_data']['feature_days'];
                                    $end_feature_time =   $start_feature_time + $feature_days*86400;
                                }
                            }
                            if($end_feature_time >= 4294967295){
                                $end_feature_time = 4294967295;
                            }

                            $featureFee = doubleval(((int)$aItem['feature_day'] * $aPackage['feature_product_fee'] ));
                            Phpfox::getService('ecommerce.process')->updateProductFeatureTime($aItem['product_id'], $start_feature_time, $end_feature_time,(int)$feature_days,$featureFee);

                            if($aItem['product_status'] == 'draft') {
                                if (Phpfox::getService('ecommerce.helper')->getUserParam('ynsocialstore.auto_approved_product', (int)$aItem['user_id'])) {
                                    $status = 'running';
                                } else {
                                    $status = 'pending';
                                }
                            }
                            else{
                                $status = $aItem['product_status'];
                            }

                            Phpfox::getService('ecommerce.process')->updateProductStatus($aItem['product_id'], $status);

                            if(isset($pay_type[1]) && $pay_type[1] == 'update_feature'){

                            }
                            elseif($status == 'running'){
                                // call approve function
                                Phpfox::getService('ecommerce.process')->approveProduct($aItem['product_id'], null ,'ynsocialstore_product');
                            }


                            (($sPlugin = Phpfox_Plugin::get('ynsocialstore.service_callback_payment_product_feature__end')) ? eval($sPlugin) : false);
                            break;
                    }
                }
                break;
        }

        // send email (refer Marketplace module)

        Phpfox::log('Handling complete');
    }
    public function getProfileLink()
    {
        return 'profile.ynsocialstore';
    }
    public function getProfileMenu($aUser)
    {

        $aUser['total_ynsocialstore'] = $this->database()->select('COUNT(*)')->from(Phpfox::getT('ynstore_store'))->where('user_id = ' . (int) $aUser['user_id'] . ' AND status=\'public\' AND item_id = 0')->execute('getSlaveField');

        if (!Phpfox::getParam('profile.show_empty_tabs'))
        {
            if (!isset($aUser['total_ynsocialstore']))
            {
                return false;
            }

            if (isset($aUser['total_ynsocialstore']) && (int) $aUser['total_ynsocialstore'] === 0)
            {
                return false;
            }
        }

        $aSubMenu = array();

        $aMenus[] = array(
            'phrase' => _p('social_store'),
            'url' => 'profile.ynsocialstore',
            'total' => (int) (isset($aUser['total_ynsocialstore']) ? $aUser['total_ynsocialstore'] : 0),
            'sub_menu' => $aSubMenu,
            'icon' => 'feed/video.png',
            'icon_class' => 'ico ico-cart-o'
        );

        return $aMenus;
    }
    public function getAjaxProfileController()
    {
        return 'ynsocialstore.store.index';
    }

    public function getActivityFeedStore($aItem, $aCallback = null, $bIsChildItem = false)
    {

        if ($bIsChildItem)
        {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = s.user_id');
        }
        if(Phpfox::isModule('like'))
        {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynsocialstore_store\' AND l.item_id = s.store_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()->select('s.store_id, s.name, s.time_stamp, s.total_comment, s.total_like, s.cover_path,s.logo_path,s.is_featured, s.categories, s.user_id, s.server_id, s.cover_server_id, s.short_description,sl.address,sl.longitude,sl.latitude')
            ->from(Phpfox::getT('ynstore_store'), 's')
            ->leftjoin(Phpfox::getT('ynstore_store_location'),'sl','s.store_id = sl.store_id')
            ->where('s.store_id = ' . (int) $aItem['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['store_id']))
        {
            return false;
        }

        if ($bIsChildItem)
        {
            $aItem = array_merge($aRow, $aItem);
        }
        $aReturn = array(
            'feed_info' => _p('opened a new store'),
            'feed_title' => $aRow['name'],
            'feed_link' => Phpfox::permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']),
            'feed_content' => Phpfox::getLib('parse.output')-> shorten(strip_tags($aRow['short_description']),400,'...'),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/video.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'ynsocialstore_store',
            'like_type_id' => 'ynsocialstore_store',
            'image_path' => $aRow['cover_path'],
            'image_server_id' => $aRow['server_id'],
            'cover_path' => $aRow['cover_path'],
            'cover_server_id' => isset($aRow['cover_server_id']) ? $aRow['cover_server_id'] : 0,
            'logo_path' => $aRow['logo_path'],
            'is_featured' => $aRow['is_featured'],
            'user_id' => $aRow['user_id'],
            'server_id' =>  $aRow['server_id'],
            'address' => $aRow['address'],
            'latitude' => $aRow['latitude'],
            'longitude' => $aRow['longitude']
        );

        if (!empty($aRow['cover_path']))
        {
            $aRow['feed_image_banner'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['server_id'],
                    'path' => 'core.url_pic',
                    'file' => 'ynsocialstore/'.$aRow['cover_path'],
                    'suffix' => '_1024',
                    'max_width' => 300,
                    'max_height' => 300
                )
            );
        }

        $aReturn['feed_categories'] = $this->database()->select('category_id,title,ordering')->from(Phpfox::getT('ecommerce_category'))->where('category_id IN ('.substr($aRow['categories'], 1, -1).')')->execute('getSlaveRows');

        $aReturn['hiddencate'] = 0;
        if(count($aReturn['feed_categories']) > 1)
        {
            $aReturn['hiddencate'] = count($aReturn['feed_categories']) - 1;
        }
        $aReturn['is_following'] = Phpfox::getService('ynsocialstore.following')->isFollowing(Phpfox::getUserId(),$aRow['store_id']);
        $aReturn['load_block'] = 'ynsocialstore.store.feed-store';

        return array_merge($aReturn, $aItem);
    }
    public function canShareItemOnFeed(){}
    public function addLikeStore ($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('store_id, name, user_id')
            ->from(Phpfox::getT('ynstore_store'))
            ->where('store_id = ' . (int) $iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['store_id']))
        {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'ynsocialstore_store\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ynstore_store', 'store_id = ' . (int) $iItemId);

        if (!$bDoNotSendEmail)
        {
            $sLink = Phpfox::permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(Phpfox::getUserBy('full_name')._p(' liked your store.'))
                ->message(Phpfox::getUserBy('full_name')._p(' liked your store ').'"<a href="'.$sLink.'">'.$aRow['name'].'</a>"'._p(' To view this store follow the link below ').'<a href="'.$sLink.'">'.$sLink.'</a>"')
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('ynsocialstore_likestore', $aRow['store_id'], $aRow['user_id']);
        }
    }

    public function addLikeProduct($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('product_id, name, user_id')
            ->from(Phpfox::getT('ecommerce_product'))
            ->where('product_id = ' . (int) $iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'ynsocialstore_product\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int) $iItemId);

        if (!$bDoNotSendEmail)
        {
            $sLink = Phpfox::permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(Phpfox::getUserBy('full_name')._p(' liked your product.'))
                ->message(Phpfox::getUserBy('full_name')._p(' liked your product ').'"<a href="'.$sLink.'">'.$aRow['name'].'</a>"'._p(' To view this product follow the link below ').'<a href="'.$sLink.'">'.$sLink.'</a>"')
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('ynsocialstore_likeproduct', $aRow['product_id'], $aRow['user_id']);
        }
    }

    public function deleteLikeStore($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'ynsocialstore_store\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ynstore_store', 'store_id = ' . (int) $iItemId);
    }

    public function deleteLikeProduct($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'ynsocialstore_product\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int) $iItemId);
    }

    public function getNotificationLikestore($aNotification)
    {
        $aRow = $this->database()->select('e.store_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ynstore_store'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.store_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['store_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            $sPhrase = $sUsers._p(' liked ').Phpfox::getService('user')->gender($aRow['gender'])._p(' own store ').'"'.$sTitle.'"';
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = $sUsers._p(' liked your store ').'"'.$sTitle.'"';
        }
        else
        {
            $sPhrase = $sUsers._p(' liked ').'<span class="drop_data_user">'.$aRow['full_name'].'
		\'s</span> store "'.$sTitle.'"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationCommentstore($aNotification)
    {
        $aRow = $this->database()->select('e.store_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ynstore_store'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.store_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['store_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            $sPhrase = $sUsers._p(' commented ').Phpfox::getService('user')->gender($aRow['gender'])._p(' own store ').'"'.$sTitle.'"';
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = $sUsers._p(' commented your store ').'"'.$sTitle.'"';
        }
        else
        {
            $sPhrase = $sUsers._p(' commented ').'<span class="drop_data_user">'.$aRow['full_name'].'
        \'s</span> store "'.$sTitle.'"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'video')
        );
    }

    public function getNotificationLikeproduct($aNotification)
    {
        $aRow = $this->database()->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');


        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            $sPhrase = $sUsers._p(' liked ').Phpfox::getService('user')->gender($aRow['gender'])._p(' own product ').'"'.$sTitle.'"';
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = $sUsers._p(' liked your product ').'"'.$sTitle.'"';
        }
        else
        {
            $sPhrase = $sUsers._p(' liked ').'<span class="drop_data_user">'.$aRow['full_name'].'
		\'s</span> product "'.$sTitle.'"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getAjaxCommentVarStore()
    {
        return 'comment.can_post_comments';
    }
    public function getCommentItemStore($iId)
    {
        $aRow = $this->database()->select('store_id AS comment_item_id, user_id AS comment_user_id')
            ->from(Phpfox::getT('ynstore_store'))
            ->where('store_id = ' . (int) $iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], 0))
        {
            Phpfox_Error::set(_p('ecommerce.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }
    public function addCommentStore($aVals, $iUserId = null, $sUserName = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);

        $aStore = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy')
            ->from(Phpfox::getT('ynstore_store'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.store_id = ' . (int) $aVals['item_id'])
            ->execute('getSlaveRow');

        if ($iUserId === null)
        {
            $iUserId = Phpfox::getUserId();
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id']))
        {
            $this->database()->updateCounter('ynstore_store', 'total_comment', 'store_id', $aVals['item_id']);
        }

        // Send the user an email
        $sLink = Phpfox::permalink('ynsocialstore.store', $aStore['store_id'], $aStore['name']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aStore['user_id'],
                'item_id' => $aStore['store_id'],
                'owner_subject' => Phpfox::getUserBy('full_name')._p(' commented on your store ').$aStore['name'],
                'owner_message' => Phpfox::getUserBy('full_name')._p(' commented on your store ').'<a href="'.$sLink.'">'.$aStore['name'].'</a>"'._p(' To see the comment thread, follow the link below: ').'<a href="'.$sLink.'">'.$sLink.'</a>',
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'ynsocialstore_commentstore',
                'mass_id' => 'ynsocialstore',
                'mass_subject' => (Phpfox::getUserId() == $aStore['user_id']) ? (Phpfox::getUserBy('full_name')._p(' commented on ').Phpfox::getService('user')->gender($aStore['gender'])._p(' store.')) : Phpfox::getUserBy('full_name')._p(' commented on ').$aStore['full_name']._p('\'s store.'),
                'mass_message' =>( Phpfox::getUserId() == $aStore['user_id']) ? (Phpfox::getUserBy('full_name')._p(' commented on ').Phpfox::getService('user')->gender($aStore['gender'], 1)._p(' store ').'"<a href="'. $sLink.'">'.$aStore['name'].'</a>"'._p(' To see the comment thread, follow the link below:').'<a href="'.$sLink.'">'.$sLink.'</a>') : (Phpfox::getUserBy('full_name')._p(' commented on ').$aStore['full_name']._p('\'s store ').'"<a href="'. $sLink.'">'.$aStore['name'].'</a>"'._p(' To see the comment thread, follow the link below:').'<a href="'.$sLink.'">'.$sLink.'</a>'),
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }
    public function deleteCommentStore($iId)
    {
        $this->database()->update(Phpfox::getT('ynstore_store'), array('total_comment' => array('= total_comment -', 1)), 'store_id = ' . (int) $iId);
    }

    public function getFeedDetails($iItemId)
    {
        return array(
            'module' => 'ynsocialstore',
            'table_prefix' => 'ynstore_',
            'item_id' => $iItemId
        );
    }

    public function getCommentItem($iId)
    {
        $aRow = $this->database()->select('feed_comment_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from(Phpfox::getT('ynstore_feed_comment'))
            ->where('feed_comment_id = ' . (int) $iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
        {
            Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        $aRow['parent_module_id'] = 'ynsocialstore';

        return $aRow;
    }

    public function getActivityFeedComment($aItem)
    {
        if (Phpfox::isModule('like'))
        {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynsocialstore_comment\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('fc.*, st.store_id, st.name')
            ->from(Phpfox::getT('ynstore_feed_comment'), 'fc')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int) $aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['store_id']))
        {
            return false;
        }

        $sLink = Phpfox_Url::instance()->permalink(array('ynsocialstore.store', 'comment-id' => $aRow['feed_comment_id']), $aRow['store_id'], Phpfox::getLib('parse.input')->cleanTitle($aRow['name']));

        $aReturn = array(
            'no_share' => true,
            'feed_status' => $aRow['content'],
            'feed_link' => $sLink,
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/comment.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'ynsocialstore',
            'like_type_id' => 'ynsocialstore_comment',
            'parent_user_id' => 0
        );

        if (!empty($aRow['location_name'])) {
            $aReturn['location_name'] = $aRow['location_name'];
        }
        if (!empty($aRow['location_latlng'])) {
            $aReturn['location_latlng'] = json_decode($aRow['location_latlng'], true);
        }

        return $aReturn;
    }

    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.user_id, st.store_id, st.name, u.full_name, u.gender')
            ->from(Phpfox::getT('ynstore_feed_comment'), 'fc')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where('fc.feed_comment_id = ' . (int) $aVals['item_id'])
            ->execute('getSlaveRow');

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id']))
        {
            $this->database()->updateCounter('ynstore_feed_comment', 'total_comment', 'feed_comment_id', $aRow['feed_comment_id']);
        }

        // Send the user an email
        $sLink = Phpfox_Url::instance()->permalink(array('ynsocialstore.store', 'comment-id' => $aRow['feed_comment_id']), $aRow['store_id'], Phpfox::getLib('parse.input')->cleanTitle($aRow['name']));
        $sItemLink = Phpfox_Url::instance()->permalink('ynsocialstore.store', $aRow['store_id'], Phpfox::getLib('parse.input')->cleanTitle($aRow['name']));

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aRow['user_id'],
                'item_id' => $aRow['feed_comment_id'],
                'owner_subject' => _p('full_name_commented_on_a_comment_posted_on_the_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])),
                'owner_message' => _p('full_name_commented_on_one_of_your_comments_you_posted_on_the_event', array('full_name' => Phpfox::getUserBy('full_name'), 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'ynsocialstore_comment',
                'mass_id' => 'ynsocialstore',
                'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_one_of_gender_store_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1))) : _p('full_name_commented_on_one_of_row_full_name_s_store_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name']))),
                'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_one_of_gender_own_comments_on_the_store', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)) : _p('full_name_commented_on_one_of_row_full_name_s', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name'], 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)))
            )
        );
    }

    public function getFeedDisplay($iTemId)
    {
        return array(
            'module' => 'ynsocialstore',
            'table_prefix' => 'ynstore_',
            'ajax_request' => 'ynsocialstore.addFeedComment',
            'item_id' => $iTemId
        );
    }

    public function getAjaxCommentVar()
    {
        return null;
    }

	public function getNotificationReopenedstore($aNotification)
	{
		$aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
			->from(Phpfox::getT('ynstore_store'), 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.store_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if (!isset($aRow['store_id']))
		{
			return false;
		}

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('store_name_is_now_reopened',['store_name' => $aRow['name']]);

		$sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getNotificationClosedstore($aNotification)
	{
		$aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
			->from(Phpfox::getT('ynstore_store'), 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.store_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if (!isset($aRow['store_id']))
		{
			return false;
		}

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('store_name_is_now_temporarily_closed',['store_name' => $aRow['name']]);

		$sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

    public function getNotificationClosedProduct($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.product_id, v.privacy, v.product_status')
            ->from(Phpfox::getT('ecommerce_product'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.product_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['product_id'])) {
            return false;
        }

        $sPhrase = _p('product_name_is_now_temporarily_closed', ['product_name' => $aRow['name']]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

	public function getNotificationFollowstore($aNotification)
	{
		$aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
			->from(Phpfox::getT('ynstore_store'), 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.store_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if (!isset($aRow['store_id']))
		{
			return false;
		}

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('user_name_followed_your_store_store_name',['user_name' => $sUsers,'store_name' => $aRow['name']]);

		$sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}

	public function getNotificationFavoritestore($aNotification)
	{
		$aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
			->from(Phpfox::getT('ynstore_store'), 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.store_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if (!isset($aRow['store_id']))
		{
			return false;
		}

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('user_name_favourited_your_store_store_name',['user_name' => $sUsers,'store_name' => $aRow['name']]);

		$sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	public function getNotificationDenystore($aNotification)
	{
		$aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
			->from(Phpfox::getT('ynstore_store'), 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.store_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if (!isset($aRow['store_id']))
		{
			return false;
		}

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_store_store_name_is_denied_by_sender',['store_name' => $aRow['name'], 'sender' => $sUsers]);

		$sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	public function getNotificationApprovestore($aNotification)
	{
		$aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
			->from(Phpfox::getT('ynstore_store'), 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.store_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		if (!isset($aRow['store_id']))
		{
			return false;
		}

		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('your_store_store_name_is_approved_by_sender',['store_name' => $aRow['name'], 'sender' => $sUsers]);

		$sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

		return array(
			'link' => $sLink,
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	public function getTotalItemCount($iUserId)
	{
		return array(
			'field' => 'total_ynsocialstore',
			'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('ynstore_store'))->where('user_id = ' . (int) $iUserId . ' AND status=\'public\' AND item_id = 0')->execute('getSlaveField')
		);
	}
	public function globalSearch($sQuery, $bIsTagSearch = false)
	{
		(($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_service_callback_globalsearch__start')) ? eval($sPlugin) : false);
		$sCondition = 'v.product_status = "running" AND v.privacy = 1 AND v.product_creating_type ="ynsocialstore_product"';
		if ($bIsTagSearch == false)
		{
			$sCondition .= ' AND (v.name LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
		}

		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getService('ecommerce_product'), 'v')
			->where($sCondition)
			->execute('getSlaveField');

		$aRows = $this->database()->select('v.name, v.product_creation_datetime as time_stamp, ' . Phpfox::getUserField())
			->from(Phpfox::getService('ecommerce_product'), 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where($sCondition)
			->limit(10)
			->order('v.product_creation_datetime DESC')
			->execute('getSlaveRows');

		if (count($aRows))
		{
			$aResults = array();
			$aResults['total'] = $iCnt;
			$aResults['menu'] = _p('Search products');

			if ($bIsTagSearch == true)
			{

			}
			else
			{
				$aResults['form'] = '<form method="post" action="' . Phpfox_Url::instance()->makeUrl('ynsocialstore') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="search[submit]" value="' . _p('View more products') . '" class="search_button" /></div></form>';
			}

			foreach ($aRows as $iKey => $aRow)
			{
				$aResults['results'][$iKey] = array(
					'title' => $aRow['name'],
					'link' => Phpfox_Url::instance()->makeUrl($aRow['user_name'], array('ynsocialstore', $aRow['title_url'])),
					'image' => Phpfox::getLib('image.helper')->display(array(
																		   'server_id' => $aRow['server_id'],
																		   'title' => $aRow['full_name'],
																		   'path' => 'core.url_user',
																		   'file' => $aRow['user_image'],
																		   'suffix' => '_120',
																		   'max_width' => 75,
																		   'max_height' => 75
																	   )
					),
					'extra_info' => _p('blog.blog_created_on_time_stamp_by_full_name', array(
																										'link' => Phpfox_Url::instance()->makeUrl('ynsocialstore'),
																										'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
																										'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
																										'full_name' => $aRow['full_name']
																									)
					)
				);
			}
			(($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_service_callback_globalsearch__return')) ? eval($sPlugin) : false);
			return $aResults;
		}
		(($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_service_callback_globalsearch__end')) ? eval($sPlugin) : false);
	}
	public function getSearchInfo($aRow)
    {
        $aInfo = array();
        $aInfo['item_link'] = Phpfox_Url::instance()->permalink('ynsocialstore.store', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('social_store_store');
        $aInfo['icon'] = 'ico ico-cart-o';
        if(!empty($aRow['item_photo']))
        {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => 'ynsocialstore'.PHPFOX_DS.$aRow['item_photo'],
                    'path' => 'core.url_pic',
                    'suffix' => '_480_square',
                    'max_width' => '120',
                    'max_height' => '120'
                )
            );
        }
        else
        {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => 'ynsocialstore/static/image/store_default.png',
                    'path' => 'core.url_module',
                    'suffix' => '_480_square',
                    'max_width' => '120',
                    'max_height' => '120'
                )
            );
        }
        return $aInfo;
    }

    public function getSearchInfoProduct($aRow)
    {
        $aInfo = array();
        $aInfo['item_link'] = Phpfox_Url::instance()->permalink('ynsocialstore.product', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('social_store_product');

        if(!empty($aRow['item_photo']))
        {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => $aRow['item_photo'],
                    'path' => 'core.url_pic',
                    'suffix' => '_400_square',
                    'max_width' => '120',
                    'max_height' => '120'
                )
            );
        }
        else
        {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => 'ynsocialstore/static/image/product_default.jpg',
                    'path' => 'core.url_module',
                    'suffix' => '_480_square',
                    'max_width' => '120',
                    'max_height' => '120'
                )
            );
        }

        return $aInfo;
    }
	public function getSearchTitleInfo()
	{
		return array(
			'name' => _p('social_store_store')
		);
	}

    public function getSearchTitleInfoProduct()
    {
        return array(
            'name' => _p('social_store_product')
    );
    }
	public function globalUnionSearch($sSearch)
	{
        $this->database()->select('item.store_id AS item_id, item.name AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'ynsocialstore\' AS item_type_id, item.logo_path AS item_photo, item.server_id AS item_photo_server')
            ->from(Phpfox::getT('ynstore_store'), 'item')
            ->where($this->database()->searchKeywords('item.name', $sSearch) . ' AND item.status IN ("public","closed") AND item.privacy = 0')
            ->union();

        $sWhere = '';
        $sWhere .= ' and item.product_status IN ( \'running\',\'completed\',\'approved\') ';

        $this->database()->select('item.product_id AS item_id, item.name AS item_title, item.product_creation_datetime AS item_time_stamp, item.user_id AS item_user_id,  item.product_creating_type AS item_type_id, item.logo_path AS item_photo, item.server_id AS item_photo_server')
            ->from(Phpfox::getT('ecommerce_product'), 'item')
            ->where(' 1=1 ' . $sWhere . ' AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.name', $sSearch))
            ->union();
	}

	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

		return array(
			_p('Social Store (Stores)') => $aUser['activity_ynsocialstore_store'],
			_p('Social Store (Products)') => $aUser['activity_ynsocialstore_product']
		);
	}
	public function updateCounter($iId, $iPage, $iPageLimit)
	{
		if ($iId == 'ynsocialstore-total')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user'))
				->execute('getSlaveField');

			$aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(v.store_id) AS total_items')
				->from(Phpfox::getT('user'), 'u')
				->leftJoin(Phpfox::getT('ynstore_store'), 'v', 'v.user_id = u.user_id')
				->limit($iPage, $iPageLimit, $iCnt)
				->group('u.user_id')
				->execute('getSlaveRows');

			foreach ($aRows as $aRow)
			{
				$this->database()->update(Phpfox::getT('user_field'), array('total_ynsocialstore' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
			}

			return $iCnt;
		}
		elseif ($iId == 'ynsocialstore-activity')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user_activity'))
				->execute('getSlaveField');

			$aRows = $this->database()->select('m.user_id, m.activity_ynsocialstore_store, m.activity_points, m.activity_total, COUNT(v.store_id) AS total_items')
				->from(Phpfox::getT('user_activity'), 'm')
				->leftJoin(Phpfox::getT('ynstore_store'), 'v', 'v.user_id = m.user_id')
				->group('m.user_id')
				->limit($iPage, $iPageLimit, $iCnt)
				->execute('getSlaveRows');

			foreach ($aRows as $aRow)
			{
				$this->database()->update(Phpfox::getT('user_activity'), array(
					'activity_points' => (($aRow['activity_total'] - ($aRow['activity_points'] * Phpfox::getUserParam('ynsocialstore.points_ynsocialstore_store'))) + ($aRow['total_items'] * Phpfox::getUserParam('ynsocialstore.points_ynsocialstore_store'))),
					'activity_total' => (($aRow['activity_total'] - $aRow['activity_ynsocialstore_store']) + $aRow['total_items']),
					'activity_ynsocialstore_store' => $aRow['total_items']
				), 'user_id = ' . $aRow['user_id']);
			}

			return $iCnt;
		}
	}
	public function updateCounterList()
	{
		$aList = array();

		$aList[] =  array(
			'name' => _p('Users Social Store Count'),
			'id' => 'ynsocialstore-total'
		);

		$aList[] =  array(
			'name' => _p('Update Users Activity Social Store Points'),
			'id' => 'ynsocialstore-activity'
		);

		return $aList;
	}
	public function getPagePerms()
	{
		$aPerms = array();

		$aPerms['ynsocialstore.open_stores'] = _p('Who can open new store?');
		$aPerms['ynsocialstore.view_browse_stores'] = _p('Who can view stores?');

		return $aPerms;
	}
	public function getPageMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'ynsocialstore.view_browse_stores'))
		{
			return null;
		}

		$aMenus[] = array(
			'phrase' => _p('social_store'),
			'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'social-store/',
			'icon' => 'module/marketplace.png',
            'menu_icon' => 'ico ico-cart-o',
			'landing' => 'ynsocialstore.store'
		);

		return $aMenus;
	}
	public function getPageSubMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'ynsocialstore.open_stores'))
		{
			return null;
		}

		return array(
			array(
				'phrase' => _p('menu_ynsocialstore_open_new_store_4e4995678c4ffc394078afa1abde1310'),
				'url' => Phpfox_Url::instance()->makeUrl('ynsocialstore.store.storetype', array('module' => 'pages', 'item' => $aPage['page_id']))
			)
		);
	}

    public function addLikeComment($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.content, fc.user_id, st.store_id, st.name')
            ->from(Phpfox::getT('ynstore_feed_comment'), 'fc')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where('fc.feed_comment_id = ' . (int) $iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id']))
        {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'ynsocialstore_comment\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ynstore_feed_comment', 'feed_comment_id = ' . (int) $iItemId);

        if (!$bDoNotSendEmail)
        {
            $sLink = Phpfox_Url::instance()->permalink(array('ynsocialstore', 'comment-id' => $aRow['feed_comment_id']), $aRow['store_id'], $aRow['name']);
            $sItemLink = Phpfox_Url::instance()->permalink('ynsocialstore', $aRow['store_id'], $aRow['name']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array('ynsocialstore.full_name_liked_a_comment_you_made_on_the_store_name', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])))
                ->message(array('ynsocialstore.full_name_liked_a_comment_you_made_on_the_store_name_to_view_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'item_link' => $sItemLink, 'title' => $aRow['name'])))
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('ynsocialstore_comment_like', $aRow['feed_comment_id'], $aRow['user_id']);
        }
    }
    //It is posting feeds for comments made in a Page of type group set to registration method "invide only", this should not happen.
    public function deleteLikeComment($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'ynsocialstore_comment\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ynstore_feed_comment', 'feed_comment_id = ' . (int) $iItemId);
    }

    public function getNotificationComment_Like($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.store_id, e.name')
            ->from(Phpfox::getT('ynstore_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('ynstore_store'), 'e', 'e.store_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if(!count($aRow)){
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users']))
            {
                $sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_comment_on_the_store_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
            }
            else
            {
                $sPhrase = _p('users_liked_gender_own_comment_on_the_store_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
            }
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = _p('users_liked_one_of_your_comments_on_the_store_title', array('users' => $sUsers, 'title' => $sTitle));
        }
        else
        {
            $sPhrase = _p('users_liked_one_on_span_class_drop_data_user_row_full_name_s_span_comments_on_the_store_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink(array('directly_detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['store_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationComment($aNotification)
    {

        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, st.store_id, st.name')
            ->from(Phpfox::getT('ynstore_feed_comment'), 'fc')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.store_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = st.user_id')
            ->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if(!count($aRow)){
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users']))
            {
                $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_store_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' =>  $sTitle));
            }
            else
            {
                $sPhrase = _p('users_commented_on_gender_own_store_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
            }
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = _p('users_commented_on_your_store_title', array('users' => $sUsers, 'title' => $sTitle));
        }
        else
        {
            $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_store_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationAdminfeature($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
            ->from(Phpfox::getT('ynstore_store'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.store_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['store_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_store_store_name_is_featured_unlimited_time_by_sender',['store_name' => $aRow['name'], 'sender' => $sUsers]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationAdminunfeature($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
            ->from(Phpfox::getT('ynstore_store'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.store_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['store_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_store_store_name_is_un_featured_by_sender',['store_name' => $aRow['name'], 'sender' => $sUsers]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getActivityFeedProduct($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($bIsChildItem)
        {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = p.user_id');
        }
        if(Phpfox::isModule('like'))
        {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynsocialstore_product\' AND l.item_id = p.product_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()->select('eu.title as uom_title,ps.product_type,p.product_price,(p.product_price - ps.discount_price) as discount_display,ps.discount_percentage,ps.discount_price, ct.title as category_name,ct.category_id,p.product_id, p.name, p.product_creation_datetime as time_stamp, p.total_comment, p.total_like, p.user_id, p.logo_path, p.server_id, pt.description_parsed AS description, ys.store_id, ys.name AS store_name')
            ->from(Phpfox::getT('ecommerce_product'), 'p')
            ->join(Phpfox::getT('ecommerce_product_ynstore'), 'ps','p.product_id = ps.product_id')
            ->join(Phpfox::getT('ecommerce_product_text'),'pt','pt.product_id = p.product_id')
            ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = p.uom_id')
            ->join(Phpfox::getT('ecommerce_category_data'),'ctd','p.product_id = ctd.product_id AND ctd.product_type = \'ynsocialstore_product\' AND ctd.is_main = 1')
            ->join(Phpfox::getT('ecommerce_category'),'ct','ct.category_id = ctd.category_id ')
            ->join(Phpfox::getT('ynstore_store'),'ys', 'ys.store_id = p.item_id')
            ->where('p.product_id = ' . (int) $aItem['item_id'] . '')
            ->execute('getSlaveRow');
        if (!isset($aRow['product_id']))
        {
            return false;
        }

        if ($bIsChildItem)
        {
            $aItem = array_merge($aRow, $aItem);
        }
        $aDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $aReturn = array(
            'feed_info' => _p('sale_new_product_in_store', ['link' => '<a href="'.Phpfox::permalink('social-store.store',$aRow['store_id'], $aRow['store_name']) .'" target="_blank">'. $aRow['store_name'] .'</a>']),
            'feed_title' => $aRow['name'],
            'feed_link' => Phpfox::permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']),
            'feed_content' => Phpfox::getLib('parse.output')-> shorten(strip_tags($aRow['description']),400,'...'),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/blog.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'ynsocialstore_product',
            'like_type_id' => 'ynsocialstore_product',
            'image_path' => $aRow['logo_path'],
            'image_server_id' => $aRow['server_id'],
            'currency_symbol' => Phpfox::getService('core.currency')->getSymbol($aDefaultCurrency),
            'discount_display' => $aRow['discount_display'],
            'product_type' => $aRow['product_type'],
            'discount_percentage' => $aRow['discount_percentage'],
            'creating_item_currency' => $aRow['creating_item_currency'],
            'product_price'=> $aRow['product_price'],
            'logo_path' => $aRow['logo_path'],
            'category_name' => $aRow['category_name'],
            'category_id' => $aRow['category_id'],
            'uom_title' => $aRow['uom_title'],
            'server_id' => $aRow['server_id'],
            'discount_price' => $aRow['discount_price'],
        );

        if (!empty($aRow['cover_path']))
        {
            $aRow['feed_image_banner'] = Phpfox::getLib('image.helper')->display(array(
                                                                                     'server_id' => $aRow['server_id'],
                                                                                     'path' => 'core.url_pic',
                                                                                     'file' => $aRow['logo_path'],
                                                                                     'suffix' => '_100',
                                                                                     'max_width' => 100,
                                                                                     'max_height' => 100
                                                                                 )
            );
        }
        $aReturn['load_block'] = 'ynsocialstore.product.feed-product';
        return array_merge($aReturn, $aItem);
    }
    public function getAjaxCommentVarProduct()
    {
        return 'ynsocialstore.can_post_comment_on_product';
    }
    public function getCommentItemProduct($iId)
    {
        $aRow = $this->database()->select('product_id AS comment_item_id, user_id AS comment_user_id')
            ->from(Phpfox::getT('ecommerce_product'))
            ->where('product_id = ' . (int) $iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], 0))
        {
            Phpfox_Error::set(_p('ecommerce.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }
    public function addCommentProduct($aVals, $iUserId = null, $sUserName = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);

        $aProduct = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.product_id, v.privacy')
            ->from(Phpfox::getT('ecommerce_product'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.product_id = ' . (int) $aVals['item_id'])
            ->execute('getSlaveRow');

        if ($iUserId === null)
        {
            $iUserId = Phpfox::getUserId();
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id']))
        {
            $this->database()->updateCounter('ecommerce_product', 'total_comment', 'product_id', $aVals['item_id']);
        }

        // Send the user an email
        $sLink = Phpfox::permalink('ynsocialstore.product', $aProduct['product_id'], $aProduct['name']);

        Phpfox::getService('comment.process')->notify(array(
                                                        'user_id' => $aProduct['user_id'],
                                                        'item_id' => $aProduct['product_id'],
                                                        'owner_subject' => Phpfox::getUserBy('full_name')._p(' commented on your product ').$aProduct['name'],
                                                        'owner_message' => Phpfox::getUserBy('full_name')._p(' commented on your product ').'"<a href="'.$sLink.'">'.$aProduct['name'].'</a>"'._p(' To see the comment thread, follow the link below: ').'<a href="'.$sLink.'">'.$sLink.'</a>',
                                                        'owner_notification' => 'comment.add_new_comment',
                                                        'notify_id' => 'ynsocialstore_commentproduct',
                                                        'mass_id' => 'ynsocialstore',
                                                        'mass_subject' => (Phpfox::getUserId() == $aProduct['user_id']) ? (Phpfox::getUserBy('full_name')._p(' commented on ').Phpfox::getService('user')->gender($aProduct['gender'])._p(' product.')) : Phpfox::getUserBy('full_name')._p(' commented on ').$aProduct['full_name']._p('\'s product.'),
                                                        'mass_message' =>( Phpfox::getUserId() == $aProduct['user_id']) ? (Phpfox::getUserBy('full_name')._p(' commented on ').Phpfox::getService('user')->gender($aProduct['gender'], 1)._p(' product ').'"<a href="'. $sLink.'">'.$aProduct['name'].'</a>"'._p(' To see the comment thread, follow the link below:').'<a href="'.$sLink.'">'.$sLink.'</a>') : (Phpfox::getUserBy('full_name')._p(' commented on ').$aProduct['full_name']._p('\'s product ').'"<a href="'. $sLink.'">'.$aProduct['name'].'</a>"'._p(' To see the comment thread, follow the link below:').'<a href="'.$sLink.'">'.$sLink.'</a>'),
                                                    )
        );

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }
    public function deleteCommentProduct($iId)
    {
        $this->database()->update(Phpfox::getT('ecommerce_product'), array('total_comment' => array('= total_comment -', 1)), 'product_id = ' . (int) $iId);
    }
    public function getNotificationCommentproduct($aNotification)
    {
        $aRow = $this->database()->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            $sPhrase = _p('user_name_commented_gender_own_product',['user_name'=> $sUsers,'gender' => Phpfox::getService('user')->gender($aRow['gender']), 'title'=> $sTitle]);
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = _p('user_name_commented_on_your_product_title', ['user_name' => $sUsers, 'title' => $sTitle]);
        }
        else
        {
            $sPhrase = _p('ecommerce.users_commented_on_span_class_drop_data_user_row_full_name_s_span_product_title',['users'=> $sUsers,'row_full_name'=> $aRow['full_name'],'title'=> $sTitle]);
            $sPhrase = $sUsers._p(' commented ').'<span class="drop_data_user">'.$aRow['full_name'].'
        \'s</span> product "'.$sTitle.'"';
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'video')
        );
    }
    public function getNotificationReviewstore($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
            ->from(Phpfox::getT('ynstore_store'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.store_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['store_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('user_name_reviewed_your_store_store_name',['user_name' => $sUsers,'store_name' => $aRow['name']]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aRow['store_id'], $aRow['name']).'reviews';

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationReviewproduct($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.product_id, v.privacy,v.product_status')
            ->from(Phpfox::getT('ecommerce_product'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('user_name_reviewed_your_product_store_name',['user_name' => $sUsers,'product_name' => $aRow['name']]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']).'tab_reviews';

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationFavoriteproduct($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.product_id, v.privacy,v.product_status')
            ->from(Phpfox::getT('ecommerce_product'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('user_name_favorited_your_product_product_name',['user_name' => $sUsers,'product_name' => $aRow['name']]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationApproveproduct($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.product_id, v.privacy,v.product_status')
            ->from(Phpfox::getT('ecommerce_product'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_product_product_name_is_approved_by_sender',['product_name' => $aRow['name'], 'sender' => $sUsers]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationDenyproduct($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.product_id, v.privacy,v.product_status')
            ->from(Phpfox::getT('ecommerce_product'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_product_product_name_is_denied_by_sender',['product_name' => $aRow['name'], 'sender' => $sUsers]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationSellproduct($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.product_id, v.privacy,v.product_status,st.name as store_name')
            ->from(Phpfox::getT('ecommerce_product'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->join(Phpfox::getT('ynstore_store'),'st','v.item_id = st.store_id')
            ->where('v.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('store_store_name_sell_new_product_product_name',['store_name' => $aRow['store_name'],'product_name' => $aRow['name']]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationAdminfeatureproduct($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.product_id, v.privacy,v.product_status')
            ->from(Phpfox::getT('ecommerce_product'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_product_product_name_is_feature_unlimited_by_sender',['product_name' => $aRow['name'], 'sender' => $sUsers]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getNotificationAdminunfeatureproduct($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.product_id, v.privacy,v.product_status')
            ->from(Phpfox::getT('ecommerce_product'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_product_product_name_is_un_feature_by_sender',['product_name' => $aRow['name'], 'sender' => $sUsers]);

        $sLink = Phpfox::getLib('url')->permalink('ynsocialstore.product', $aRow['product_id'], $aRow['name']);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function getRedirectCommentProduct($iId)
    {
        return $this->getFeedRedirectProduct($iId);
    }

    public function getReportRedirectProduct($iId)
    {
        return $this->getFeedRedirectProduct($iId);
    }
    public function getFeedRedirectProduct($iId, $iChild = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);

        $aProduct = $this->database()->select('ep.product_id, ep.name')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->where('ep.product_creating_type like \'ynsocialstore_product\' AND ep.product_id = ' . (int) $iId)
            ->execute('getSlaveRow');

        if (!isset($aProduct['product_id']))
        {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);

        return Phpfox::permalink('ynsocialstore.product', $aProduct['product_id'], $aProduct['name']);
    }

    public function getItemBuyFromById($iStoreId)
    {
        $aStore = $this->database()->select('*')
            ->from(Phpfox::getT('ynstore_store'))
            ->where('store_id = '.$iStoreId)
            ->execute('getRow');

        return $aStore;
    }
    public function getNotificationExpiredstore($aNotification)
    {
        $aRow = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, v.name, v.store_id, v.privacy,v.status')
            ->from(Phpfox::getT('ynstore_store'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.store_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['store_id']))
        {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sPhrase = _p('your_store_store_name_package_has_expired',['store_name' => $aRow['name']]);

        $sLink = Phpfox::getLib('url')->makeUrl('ynsocialstore.store.manage-packages',['id' => $aRow['store_id']]);

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @return array
     */
    public function getUploadParamsStore_Logo() {
        return [
            'label' => _p('logo'),
            'max_size' => null,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'ynsocialstore' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'ynsocialstore' . PHPFOX_DS,
            'thumbnail_sizes' => array(90, 140, 480),
            'param_name' => 'logo',
            'field_name' => 'temp_file_logo',
            'remove_field_name' => 'remove_logo'
        ];
    }

    /**
     * @return array
     */
    public function getUploadParamsStore_Cover() {
        return [
            'label' => _p('cover'),
            'max_size' => null,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'ynsocialstore' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'ynsocialstore' . PHPFOX_DS,
            'thumbnail_sizes' => array(480, 1024),
            'param_name' => 'cover',
            'field_name' => 'temp_file_cover',
            'remove_field_name' => 'remove_cover'
        ];
    }

    public function getUploadParamsProduct($aParams = null)
    {
        $iRemainImage = $aParams['remain_upload'];
        $iMaxFileSize = Phpfox::getUserParam('ecommerce.max_size_for_icons');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'sending' => '$Core.ynsocialstore.dropzoneOnSending',
            'success' => '$Core.ynsocialstore.dropzoneOnSuccess',
            'queuecomplete' => '$Core.ynsocialstore.dropzoneQueueComplete',
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('ecommerce.frame-upload'),
            'component_only' => true,
            'max_file' => $iRemainImage,
            'js_events' => $aEvents,
            'upload_now' => "true",
            'submit_button' => '#js_listing_done_upload',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'ynecommerce' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'ynecommerce' . PHPFOX_DS,
            'update_space' => false,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'style' => '',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number', ['number' => $iRemainImage])
            ],
            'thumbnail_sizes' => array(100, 120, 200, 400, 1024),
            'no_square' => true,
        ];
    }
}
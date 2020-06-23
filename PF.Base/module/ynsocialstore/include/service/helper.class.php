<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/4/16
 * Time: 9:08 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Service_Helper extends Phpfox_Service
{
    private $_bLoadStatic = false;
    private $_aBusinessType;
    private $_aStatus = array(
        'draft' => 'draft',
        'pending' => 'pending',
        'denied' => 'denied',
        'running' => 'public',
        'paused' => 'closed',);
    public function __construct()
    {
        $this->_aBusinessType = array(
                '1' => _p('sole_proprietor'),
                '2' => _p('partnership'),
                '3' => _p('company'),
                '4' => _p('franchise'),
                '5' => _p('limited_liability'),
                '6' => _p('others')
            );

    }

    public function getProductStatus($sStatus)
    {
        return $this->_aStatus[$sStatus];
    }

    public function getProductStatusKey($sStatus)
    {
        return array_search($sStatus, $this->_aStatus);
    }


    public function isNumeric($val)
    {
        if (strlen(trim($val)) == 0) {
            return false;
        }

        if (!is_numeric($val) || $val < 0) {
            return false;
        }

        return true;
    }
    public function buildMenu()
    {
        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
            $aFilterMenu = [
                _p('all_products') => '',
                _p('all_stores') => 'ynsocialstore.store',
            ];
            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend') && !Phpfox::getUserBy('profile_page_id')) {
                $aFilterMenu[_p('stores_of_friends')] = 'ynsocialstore.store.view_friend';
            }

            // Don't show if not login
            if (Phpfox::isUser(false)) {
                $aFilterMenu[(($iCount = Phpfox::getService('ynsocialstore')->getToTalMyFavorite()) > 0) ? _p('favourite_stores').'<span class="count-item">'.$iCount.'</span>' : _p('favourite_stores') ] = 'ynsocialstore.store.view_favorite';
                $aFilterMenu[(($iCount = Phpfox::getService('ynsocialstore')->getToTalMyFollowing()) > 0) ? _p('following_stores').'<span class="count-item">'.$iCount.'</span>' : _p('following_stores')] = 'ynsocialstore.store.view_follow';
                if(Phpfox::getParam('ynsocialstore.what_did_friend_buy'))
                {
                    $aFilterMenu[_p('what_did_friends_buy')] = 'friendbuy';
                }
                $aFilterMenu[_p('seller_section')] = 'ynsocialstore.statistic';
                $aFilterMenu[_p('buyer_section')] = 'ynsocialstore.my-cart';
            }

            $aFilterMenu[(($iCount = Phpfox::getService('ynsocialstore')->getCountStore('st.is_featured = 1 AND st.status = \'public\'')) > 0) ? _p('featured_stores').'<span class="count-item">'.$iCount.'</span>' : _p('featured_stores')] = 'ynsocialstore.store.view_featured';
            $aFilterMenu[(($iCount = Phpfox::getService('ynsocialstore.product')->getCountProduct('st.module_id = \'ynsocialstore\' AND st.status = "public" AND ep.product_status = "running" and (ep.feature_end_time = 1 OR ep.feature_end_time >'.PHPFOX_TIME.')')) > 0 ) ? _p('featured_products').'<span class="count-item">'.$iCount.'</span>' : _p('featured_products')] = 'featuredprod';
            if(Phpfox::getUserParam('ynsocialstore.can_approve_store') && ($iCount = Phpfox::getService('ynsocialstore')->getCountStore('st.status = \'pending\'')) > 0)
            {
                $aFilterMenu[_p('pending_stores').'<span class="count-item">'.$iCount.'</span>'] = 'ynsocialstore.store.view_pending';
            }
            if((Phpfox::getUserParam('ynsocialstore.can_approve_product') || Phpfox::isAdmin()) && ($iCount = Phpfox::getService('ynsocialstore.product')->getCountProduct('st.status IN ("public", "closed") AND ep.product_status = "pending"')) > 0)
            {
                $aFilterMenu[_p('pending_products').'<span class="count-item">'.$iCount.'</span>'] = 'pendingprod';
            }
            Phpfox_Template::instance()->buildSectionMenu('ynsocialstore', $aFilterMenu, false);
        }
    }

    public function loadStoreJsCss() {
        if ($this->_bLoadStatic) return true;
        $this->_bLoadStatic = true;
        Phpfox::getLib('template')
            ->setHeader('cache' ,array( 
                'jquery.magnific-popup.js'  => 'module_ynsocialstore',
                'jquery.validate.js'    => 'module_ynsocialstore',
                'ynsocialstore.js' => 'module_ynsocialstore',
                'ynsocialstorehelper.js' => 'module_ynsocialstore',
                'jquery.wookmark.js' => 'module_ynsocialstore',
                'gmaps.js' => 'module_ynsocialstore',
                'clipboard.min.js' => 'module_ynsocialstore',
                'magnific-popup.css' => 'module_ynsocialstore',
                'colorpicker/js/colpick.js' => 'static_script',
            )) 
            ->setPhrase( array(  // phrase for JS
                'ynsocialstore.please_enter_location',
                'this_field_is_required',
                'please_enter_a_valid_url_for_example_http_example_com',
                'please_enter_a_value_with_a_valid_extension',
                'please_enter_at_least_0_characters',
                'please_enter_a_value_greater_than_or_equal_to_0',
                'please_enter_a_valid_number',
                'please_enter_no_more_than_0_characters',
                'please_enter_only_digits',
                'please_enter_a_valid_email_address',
                'ynsocialstore.category',
                'ynsocialstore.short_description',
                'ynsocialstore.contact_information',
                'ynsocialstore.branches',
                'ynsocialstore.description',
                'ynsocialstore.from',
                'ynsocialstore.to',
                'ynsocialstore.get_directions',
                'ynsocialstore.featured',
                'ynsocialstore.social_store',
                'ynsocialstore.cannot_load_google_api_library_please_reload_the_page_and_try_again',
                'ynsocialstore.compare',
                'ynsocialstore.please_select_more_than_one_entry_for_the_comparison',
                'ynsocialstore.add_to_compare',
                'ynsocialstore.remove_from_compare',
                'ynsocialstore.phone',
                'ynsocialstore.fax',
                'ynsocialstore.email',
                'ynsocialstore.website',
                'ynsocialstore.are_you_sure_you_want_to_delete_this_store',
                'ynsocialstore.are_you_sure_you_want_to_delete_this_product',
                'ynsocialstore.yes',
                'ynsocialstore.no',
                'ynsocialstore.are_you_sure_you_want_to_delete_stores_that_you_selected',
                'ynsocialstore.are_you_sure_you_want_to_delete_products_that_you_selected',
                'ynsocialstore.address_is_required',
                'ynsocialstore.stores',
                'ynsocialstore.edit_faq',
                'ynsocialstore.are_you_sure',
                'ynsocialstore.no_stores_to_compare',
                'ynsocialstore.you_cannot_post_a_review_without_rating',
                'ynsocialstore.are_you_sure_want_to_delete_this_product_this_action_cannot_be_reverted',
                'ynsocialstore.are_you_sure_want_to_delete_these_products_this_action_cannot_be_reverted',
                'ynsocialstore.no_products_to_compare',
                'ynsocialstore.l_day',
                'ynsocialstore.l_day_s',
                'ynsocialstore.off',
                'ynsocialstore.remain',
                'ynsocialstore.available_in_stock',
                'ynsocialstore.unlimited',
                'ynsocialstore.additional_information',
                'ynsocialstore.warning_when_edit_stock_quantity_of_product',
                'ynsocialstore.warning_change_stock_quantity_have_unlimited_attribute',
                'ynsocialstore.are_you_sure_want_to_remove_this_item',
                'ynsocialstore.are_you_sure_want_to_remove_all_items',
                'ynsocialstore.you_can_add_maximum_quantity_item_s_for_this_product'
            ));
    }   
    public function getJsSetupParams() {
        return array(
            'fb_small_loading_image_url' => Phpfox::getLib('template')->getStyle('image', 'ajax/add.gif'),
            'ajax_file_url' => Phpfox::getParam('core.path') . 'static/ajax.php',           
            'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
        );
    }

    public function getCurrentCurrencies($sGateway = 'paypal', $sDefaultCurrency = '') {
        
        $aFoxCurrencies = Phpfox::getService('core.currency')->getForBrowse();

        $sDefaultCurrency = $sDefaultCurrency ? $sDefaultCurrency : Phpfox::getService('core.currency')->getDefault();
        $aDefaultCurrency = array();
        $aResults = array();
        foreach($aFoxCurrencies as $aCurrency)
        {
            if ($aCurrency['is_default'] == '1'){
                        $aResults[] = $aCurrency;             
            }
        }

        return $aResults;
    }     
    public function getMoneyText($sAmount, $sCurrency) {
        return $sCurrency . $sAmount;
    }  
    public function getAllBusinessType()
    {
        return $this->_aBusinessType;
    }  
    public function getBusinessTypeById($iTypeId)
    {
        return $this->_aBusinessType[$iTypeId];
    }
    public function isHavingFeed($type_id, $item_id){
        $aFeed = $this -> database()
                    -> select('feed.*')
                    ->from(Phpfox::getT("feed"), 'feed')
                    ->where('type_id = \'' . $type_id . '\' AND item_id = ' . (int)$item_id)
                    -> execute("getSlaveRow");

        if(isset($aFeed['feed_id'])){
            return true;
        }

        return false;
    }

    public function getModuleIdPhoto(){
        $sController = 'photo';
        if($this->isAdvPhoto()){
            $sController = 'advancedphoto';
        }

        return $sController;
    }

    public function isAdvPhoto()
    {
        return Phpfox::isModule('advancedphoto');
    }

    public function setSessionBeforeAddItemFromSubmitForm($iStoreId, $type)
    {
        $iCurrentUserId = Phpfox::getUserId();
        $_SESSION[Phpfox::getParam('core.session_prefix')]['ynsocialstore']['add_new_item'][$iCurrentUserId]['store_id'] = $iStoreId;
        $_SESSION[Phpfox::getParam('core.session_prefix')]['ynsocialstore']['add_new_item'][$iCurrentUserId]['type'] = $type;
    }

    public function getParamsSearchStore($aParentModule = null,$bIsUserProfile = false, $aUser = array())
    {
        $is_manage = ($this->request()->get('req2') == 'manage-store');

        return array(
            'type' => 'store',
            'field' => 'st.store_id',
            'search_tool' => array(
                'table_alias' => 'st',
                'search' => array(
                    'action' => ($is_manage ? Phpfox_Url::instance()->makeUrl('ynsocialstore.manage-store') : ($aParentModule === null ? ($bIsUserProfile === true ? Phpfox_Url::instance()->makeUrl($aUser['user_name'], array('ynsocialstore', 'view' => Phpfox_Request::instance()->get('view'))) : Phpfox_Url::instance()->makeUrl('ynsocialstore.store', array('view' => Phpfox_Request::instance()->get('view')))) : $aParentModule['url'] . 'ynsocialstore/view_' . Phpfox_Request::instance()->get('view') . '/')),
                    'default_value' => _p('search_store_dot'),
                    'name' => 'search',
                    'field' => array('st.name')
                ),
                'sort' => array(
                    'latest' => array('st.time_stamp', _p('latest')),
                    'a-z' => array('st.name', _p('a_z'), 'ASC'),
                    'z-a' => array('st.name', _p('z_a')),
                    'most-favorited' => array('st.total_favorite', _p('most_favorited')),
                    'most-following' => array('st.total_follow', _p('most_following')),
                    'most-purchased' => array('st.total_orders', _p('most_purchased'))
                ),
                'show' => array(12, 24, 36, 48)
            )
        );
    }

    public function getParamsSearchProduct($bIsMostPurchared = false)
    {
        $is_manage = ($this->request()->get('req2') == 'manage-product');

        return array(
            'type' => 'product',
            'field' => 'ecp.product_id',
            'search_tool' => array(
                'table_alias' => $bIsMostPurchared ? 'eo':'ecp',
                'search' => array(
                    'action' => ($is_manage ? Phpfox_Url::instance()->makeUrl('ynsocialstore.manage-product') : Phpfox_Url::instance()->makeUrl('ynsocialstore', array('view' => Phpfox_Request::instance()->get('view')))),
                    'default_value' => _p('search_product'),
                    'name' => 'search',
                    'field' => array('ecp.name')
                ),
                'sort' => array(
                    'latest' => array('ecp.product_creation_datetime', _p('latest')),
                    'a-z' => array('ecp.name', _p('a_z'), 'ASC'),
                    'z-a' => array('ecp.name', _p('z_a'), 'DESC'),
                    'most-liked' => array('ecp.total_like', _p('most_liked')),
                    'most-viewed' => array('ecp.total_view', _p('most_viewed')),
                    'most-purchased' => array('total_orders', _p('most_purchased')),
                    'super-deal' => array('is_product_discounting DESC, eps.discount_percentage ', _p('super_deal'), 'DESC'),
                    'price-increase' => array('ecp.product_price', _p('price_increase'), 'ASC'),
                    'price-decrease' => array('ecp.product_price', _p('price_decrease'), 'DESC'),
                ),
                'show' => array(12, 24, 36, 48),
                'when_field' => $bIsMostPurchared ? 'order_creation_datetime':'product_creation_datetime'
            )
        );
    }

    public function getUserParam($sParam, $iUserId) {

        $iGroupId = $this->getUserBy('user_group_id', $iUserId);

        return Phpfox::getService('user.group.setting')->getGroupParam($iGroupId, $sParam);

    }

    private function getUserBy($sVar, $iUserId ) {

        $result = $this->_getUserInfo($iUserId);
        if (isset($result[$sVar]))
        {
            return $result[$sVar];
        }

        return false;
    }

    private function _getUserInfo($iUserId) {
        $aRow = $this->database()->select('u.*')
            ->from(Phpfox::getT('user'), 'u')
            ->where('u.user_id = ' . $iUserId)
            ->execute('getRow');
        if(!$aRow) {
            return false;
        }

        $aRow['age'] = Phpfox::getService('user')->age(isset($aRow['birthday']) ? $aRow['birthday'] : '');
        $aRow['location'] = $aRow['country_iso']; // we will improve it later to deal with cities
        $aRow['language'] = $aRow['language_id'];
        return $aRow;
        // $this->_aUser = $aRow;
    }

    public function getNormalSelectQuery()
    {
        return 'ecp.product_status, ecp.user_id, ecp.creating_item_currency,
                ecp.product_id, ecp.feature_start_time, ecp.feature_end_time, 
                ecp.privacy, eps.link, ecp.server_id, ecp.logo_path, 
                ecp.total_like, ecp.product_price, ecp.name as product_name, 
                eps.discount_price, eps.discount_percentage, ecp.total_orders, 
                eps.product_type, st.store_id, st.name as store_name, st.privacy as store_privacy, 
                eu.title as uom_title, (ecp.product_price - eps.discount_price) as discount_display, 
                eps.discount_start_date, eps.discount_end_date, eps.discount_timeless';
    }
}
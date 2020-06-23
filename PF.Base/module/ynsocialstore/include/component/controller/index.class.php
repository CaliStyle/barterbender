<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Controller_Index extends Phpfox_Component
{
	public function process()
	{
        if($this->request()->get('req1') == 'pages' && $this->request()->getInt('req2'))
        {
            return Phpfox_Module::instance()->setController('ynsocialstore.store.index');
        }

	    // Redirect to detail store page after upload an image
        if($this->request()->get('req3') == 'photo' || $this->request()->get('req3') == 'advancedphoto') {
            $id = $this->request()->get('req2');
            $aStoreName = Phpfox::getService('ynsocialstore')->getFieldsStoreById('name',$id, 'getField');
            $this->url()->permalink('ynsocialstore.store', $id, $aStoreName, true, '', array('photos'));
        }

        if (in_array($this->request()->get('req2'), array('product', 'detail')) && $this->request()->getInt('req3'))
        {
            return Phpfox_Module::instance()->setController('ynsocialstore.product.detail');
        }

        if ($this->request()->get('req2') == 'manage-orders')
        {
            return Phpfox_Module::instance()->setController('ynsocialstore.all-sales');
        }

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_index_process_start')) ? eval($sPlugin) : false);

        $bIsHomepage = $this->_checkIsInHomePage();
        $this->setParam('hideBlock', !$bIsHomepage);

        $this->template()
            ->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setTitle(_p('social_store'));

        $sort = $this->request()->get('sort');
        if ($sort == 'most-purchased') {
            $this->search()->set(Phpfox::getService('ynsocialstore.helper')->getParamsSearchProduct(true));
        } else {
            $this->search()->set(Phpfox::getService('ynsocialstore.helper')->getParamsSearchProduct());
        }


        $this->_checkIsThisACategoryRequestAndHandleIt();

        $bIsAdvSearch = false;

        if($this->request()->get('flag_advancedsearchproduct'))
        {
            $bIsAdvSearch = true;
        }


        // search ajax
        $bA = Phpfox_Request::instance()->get("bIsAdvSearch");

        if ($bA)
        {
            if ($this->search()->getPage()>1)
            {
                $aVals = $_SESSION[Phpfox::getParam('core.session_prefix')."ynsocialstore_product_searchAdv"];
                $bIsAdvSearch = true;

            }
        }

        if($bIsAdvSearch){
            $oServiceProduct = Phpfox::getService('ynsocialstore.product');

            $aVals = $oServiceProduct->getAdvSearchConditions();

            if ($this->search()->getPage() <= 1)
            {
                $_SESSION[Phpfox::getParam('core.session_prefix')."ynsocialstore_product_searchAdv"] = $aVals;
            }

            $oServiceProduct->setAdvSearchConditions($aVals);

            $this->template()->assign(array(
                'aForms' => $aVals,
            ));
        }
        else {
            $this->template()->assign(array(
                'aForms' => array(),
            ));
        }

        $aBrowseParams = array(
            'module_id' => 'ynsocialstore.product',
            'alias' => 'ecp',
            'field' => 'product_id',
            'table' => Phpfox::getT('ecommerce_product'),
            'hide_view' => array('my')
        );

        if($this->request()->get('sort') == 'super-deal') {
            $aBrowseParams['select'] = 'IF(eps.discount_end_date > '. PHPFOX_TIME .', 1, 0) AS is_product_discounting, ';
        }

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_index_process_search')) ? eval($sPlugin) : false);

        $this->search()->setCondition('AND ecp.module_id like \'ynsocialstore\' AND st.module_id = \'ynsocialstore\'');

        $sView = $this->request()->get('view');
        switch ($sView) {
            case 'featuredprod':
                $this->search()->setCondition('AND st.status = \'public\' AND ecp.product_status = \'running\' AND (ecp.feature_end_time = 1 OR ecp.feature_end_time >'.PHPFOX_TIME .')');
                break;
            case 'pendingprod':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND ecp.product_status = \'pending\'');
                break;
            case 'recent':
                $sRecentlyViewed = Phpfox::getCookie('ynsocialstore_recently_viewed_product');
                if(trim($sRecentlyViewed) != '')
                {
                    $this->search()->setCondition('AND ecp.product_id IN ('.$sRecentlyViewed.')  AND ecp.product_status = "running"' );
                }
                break;
            case 'friendbuy':
                Phpfox::isUser(true);

                if (!Phpfox::getParam('ynsocialstore.what_did_friend_buy'))
                    $this->url()->send('ynsocialstore');

                $sBoughtByFriends = Phpfox::getService('ynsocialstore.product')->getListProductIdsBoughtByFriends($this->search()->getPage(), $this->search()->getDisplay());
                if (!empty($sBoughtByFriends)) $this->search()->setCondition('AND ecp.product_id IN ('.$sBoughtByFriends.')  AND ecp.product_status IN (\'running\', \'paused\')');
                else $this->search()->setCondition(' AND 1 = 0');
                break;
            default:
                $sCondition = "AND ecp.product_status = 'running' AND ecp.privacy IN (%PRIVACY%)";
                $this->search()->setCondition($sCondition);
                break;
        }

        $this->search()->setCondition(' AND st.status IN (\'public\', \'closed\')');

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->setPagingMode(Phpfox::getParam('ynsocialstore.ynstore_product_paging_mode', 'loadmore'));
        $this->search()->browse()->params($aBrowseParams)->execute();
        $aProducts = $this->search()->browse()->getRows();

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_index_process_middle')) ? eval($sPlugin) : false);

        $this->template()
            ->assign(array(
                    'aProducts' => $aProducts,
                    'sView' => $sView,
                    'bIsHomepage' => $bIsHomepage,
                    'iPage' => $this->search()->getPage(),
                )
            );

        $this->template()->setPhrase(array(
            'ynsocialstore.are_you_sure',
            'ynsocialstore.yes',
            'ynsocialstore.no',
            'ynsocialstore.confirm_feature_product_unlimited',
            'ynsocialstore.are_you_sure_want_to_delete_this_product_this_action_cannot_be_reverted',
            'ynsocialstore.are_you_sure_want_to_delete_these_products_this_action_cannot_be_reverted'
        ));

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        Phpfox::getService('ynsocialstore.helper')->buildMenu();

        $aModerationMenu = [];
        $bShowModeration = false;
        if(Phpfox::isAdmin()){
            $aModerationMenu[] = [
                'phrase' => _p('core.delete'),
                'action' => 'deleteProduct'
            ];

            $bShowModeration = true;
        }

        if(Phpfox::getUserParam('ynsocialstore.can_approve_product') && $sView == 'pendingprod'){
            $aModerationMenu[] = [
                'phrase' => _p('approve'),
                'action' => 'approveProduct'
            ];

            $aModerationMenu[] = [
                'phrase' => _p('deny'),
                'action' => 'denyProduct'
            ];

            $bShowModeration = true;
        }

        if(Phpfox::getUserParam('ynsocialstore.can_feature_product') && $sView == 'featuredprod'){
            $aModerationMenu[] = [
                'phrase' => _p('un_featured'),
                'action' => 'unfeatureProduct'
            ];

            $bShowModeration = true;
        }

        $this->setParam('global_moderation', [
            'name' => 'ynstore-product',
            'ajax' => 'ynsocialstore.moderation',
            'menu' => $aModerationMenu
        ]);

        $this->template()
            ->setHeader('cache', array(
                           'owl.carousel.min.js' => 'module_ynsocialstore',
                           'owl.carousel.css' => 'module_ynsocialstore',
                               )
            );

        $this->template()->assign(array(
                'bShowModeration' => $bShowModeration,
            )
        );

        if ($this->search()->isSearch()){
            $aSearch = $this->request()->get('search');

            $this->setParam('aSearch', array(
                    'when' => $this->request()->get('when', 'all-time'),
                    'show' => $this->search()->getDisplay(),
                    'sort' => $this->request()->get('sort', 'latest'),
                    'search' => isset($aSearch['search']) ? $aSearch['search'] : $this->request()->get('keywords'),
                )
            );
        }

        Phpfox::getLib('pager')->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        Phpfox::getService('ynsocialstore.helper')->buildMenu();

        if(!defined('PHPFOX_IS_PAGES_VIEW') && !defined('PHPFOX_PAGES_ITEM_TYPE') && !defined('PHPFOX_IS_USER_PROFILE')){
            $canCreateProduct = Phpfox::getService('ynsocialstore')->checkUserStores();
            if($canCreateProduct) {
                sectionMenu(_p('ynsocialstore_sell_new_product'), 'social-store/add');
            }
            sectionMenu(_p('ynsocialstore_open_new_store'), 'social-store/store/storetype');
        }

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_index_process_end')) ? eval($sPlugin) : false);
	}

    private function _checkIsThisACategoryRequestAndHandleIt()
    {
        // check category request and set corresponding condition
        if ($this->request()->get('req2') == 'category')
        {
            if ($aCategory = Phpfox::getService('ecommerce.category')->getForEdit($this->request()->getInt('req3'))) {
                if ($aParentCategory = Phpfox::getService('ecommerce.category')->getForEdit($aCategory['parent_id'])) {
                    if ($aGrandParentCategory = Phpfox::getService('ecommerce.category')->getForEdit($aParentCategory['parent_id'])) {
                        $sPhrase = (\Core\Lib::phrase()->isPhrase($aGrandParentCategory['title']) ? _p($aGrandParentCategory['title']) : Phpfox::getLib('locale')->convert($aGrandParentCategory['title']));
                        $this->template()->setBreadCrumb($sPhrase, $this->url()->permalink(array('ynsocialstore.store.category', 'view' => $this->request()->get('view')), $aGrandParentCategory['category_id'], $sPhrase));
                    }

                    $sPhrase = (\Core\Lib::phrase()->isPhrase($aParentCategory['title']) ? _p($aParentCategory['title']) : Phpfox::getLib('locale')->convert($aParentCategory['title']));
                    $this->template()->setBreadCrumb($sPhrase, $this->url()->permalink(array('ynsocialstore.store.category', 'view' => $this->request()->get('view')), $aParentCategory['category_id'], $sPhrase));
                }

                $iCategory = $this->request()->getInt('req3');
                $this->_setConditionByCategory($iCategory);

                $sPhrase = (\Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title']));
                $this->template()->setTitle($sPhrase);
                $this->template()->setBreadCrumb($sPhrase, $this->url()->makeUrl('current'), true);
                
                $this->search()->setFormUrl($this->url()->permalink(array('ynsocialstore.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['title']));
            }
        }
    }

    private function _setConditionByCategory($iCategory)
    {
        $sCategories = $iCategory;

        $sChildIds = Phpfox::getService('ecommerce.category')->getChildIds($iCategory);
        if (!empty($sChildIds)) {
            $sCategories .= ',' . $sChildIds;
        }

        $this->search()->setCondition('AND ecd.category_id IN(' . $sCategories . ')');
    }

    private function _checkIsInHomePage() {
        $bIsInHomePage = false;
        $sTempView = $this->request()->get('view', false);

        if ($sTempView == "" && !$this->request()->get('search-id')
            && !$this->request()->get('bIsAdvSearch')
            && !$this->request()->get('sort')
            && !$this->request()->get('when')
            && !$this->request()->get('type')
            && !$this->request()->get('show')
            && !$this->request()->get('search')
            && $this->request()->get('req2') == '') {
            $bIsInHomePage = true;
        }

        return $bIsInHomePage;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.ynsocialstore_component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
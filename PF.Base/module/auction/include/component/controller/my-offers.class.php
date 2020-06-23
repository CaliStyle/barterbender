<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_My_Offers extends Phpfox_Component {

    public function process()
    {
        $this->template()
                ->setTitle(_p('auctions'))
                ->setBreadCrumb(_p('auctions'), $this->url()->makeUrl('auction'));
        
        $iPage = $this->request()->getInt('page');
        
        $this->search()->set(array(
            'type' => 'auction',
            'field' => 'ep.product_id',
            'search_tool' => array(
                'table_alias' => 'ep',
                'search' => array(
                    'action' => $this->url()->makeUrl('auction.my-offers'),
                    'default_value' => _p('search_auctions'),
                    'name' => 'search',
                    'field' => array('ep.name')
                ),
                'sort' => array(
                    'newest' => array('ep.product_creation_datetime', _p('newest')),
                    'oldest' => array('ep.product_creation_datetime', _p('oldest'), 'ASC'),
                    'a-z' => array('ep.name', _p('a_z'), 'ASC'),
                    'z-a' => array('ep.name', _p('z_a'))
                ),
                'show' => array(12, 24, 36),
                'when_field' => 'product_creation_datetime'
            )
                )
        );
        
        $iLimit = $this->search()->getDisplay();
        
        $this->search()->setCondition('AND ep.start_time <= ' . PHPFOX_TIME);
        $this->search()->setCondition('AND (ep.product_status = "running" OR ep.product_status = "approved" OR ep.product_status = "bidden" OR ep.product_status = "completed")');
        $this->search()->setCondition('AND ep.product_quantity > 0');
        
        
        if ($this->search()->get('advsearch'))
        {
            $this->_setAdvSearchConditions();
        }
        
        list($iCnt, $aProducts) = Phpfox::getService('auction')->getMyOffersAuctions($this->search()->getConditions(), $this->search()->getSort(), $this->search()->getPage(), $iLimit);
        
        $aAuctionId = array();
        $aUserId = array();
        $iCntNotBanned = 0;
        foreach ($aProducts as $iKey => $aProduct)
        {
            $aAuctionId[] = $aProduct['product_id'];
            $aUserId[] = $aProduct['user_id'];
            if ($aProduct['user_group_id'] != 5) {
                $iCntNotBanned++;
            }

            if (empty($aProducts[$iKey]['logo_path'])) {
                $aProducts[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }
        $aUserId = array_unique($aUserId);
        
        $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->getSellerSettings($aUserId);
        
        if ($aAuctionId)
        {
            $aOffers = Phpfox::getService('auction.offer')->getOffers(Phpfox::getUserId(), $aAuctionId);
            
            $aOfferData = array();
            foreach ($aOffers as $iKey => $aOffer)
            {
                $aOfferData[$aOffer['auctionoffer_product_id']] = $aOffer;
            }
            
            foreach ($aProducts as $iKey => $aProduct)
            {
                if (isset($aOfferData[$aProduct['product_id']]))
                {
                    $aProducts[$iKey]['aOffer'] = $aOfferData[$aProduct['product_id']];
                    
                        $aSellerSettings[$aProduct['user_id']]['time_complete_transaction'] = isset($aSellerSettings[$aProduct['user_id']]['time_complete_transaction'])?$aSellerSettings[$aProduct['user_id']]['time_complete_transaction']:7;
                        $sDuration = Phpfox::getService('auction.helper')->getDuration(PHPFOX_TIME, $aProducts[$iKey]['aOffer']['auctionoffer_approved_datetime'] + $aSellerSettings[$aProduct['user_id']]['time_complete_transaction'] * 86400);
                        $aProducts[$iKey]['aOffer']['time_left'] = (!empty($sDuration) ? $sDuration . ' ' . _p('left') : '');
                    
                }
            }
        }
        
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $this->search()->getSearchTotal($iCnt)));
        
        Phpfox::getService('auction.helper')->buildSectionMenu();
        
        Phpfox::getService('auction.helper')->loadAuctionJsCss();
        
        $this->template()->assign(array('aProducts' => $aProducts, 'iTotalAuctions' => $iCnt, 'iTotalAuctionsNotBanned' => $iCntNotBanned));
    }

    private function _setAdvSearchConditions()
    {
        $sKeyword = $this->search()->get('keyword');
        $aCategory = $this->search()->get('category');
        $sSort = $this->search()->get('sort');
        $sView = $this->request()->get('view');
        
        $aCategory = array_filter(array_unique($aCategory));
        $sCategories = '';
        if ($aCategory)
        {
            $sCategories = implode(',', $aCategory);
        }
        
        $aForms = array(
            'keyword' => $sKeyword,
            'categories' => $sCategories,
            'sort' => $sSort,
            'advancedsearch' => true
        );

        $this->template()
                ->setHeader(array(
							'<script type="text/javascript">$Behavior.initAdvancedSearchForCategory = function(){  var aCategories = explode(\',\', \'' . $sCategories . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); } }</script>'
						)
					)
                ->assign('aForms', $aForms);

        if (!empty($sKeyword))
        {
            $this->search()->setCondition('AND ep.name LIKE "%' . Phpfox::getLib('parse.input')->clean($sKeyword) . '%" ');
        }

        $sViewCategory = $this->request()->get('category');
        $aViewCategory = explode(",", $sViewCategory);

        if (is_array($aCategory) && !empty($aCategory))
        {
            $iChild = $aCategory[0];
            foreach ($aCategory as $k => $iCategory)
            {
                if (Phpfox::getService('ecommerce.category')->isChild($iCategory, $iChild))
                {
                    $iChild = $iCategory;
                }
            }
            
            $this->_setConditionByCategory($iChild);

            $this->setParam('category', $iChild);
        }

        if (count($aCategory))
        {
            Phpfox::getService('auction.process')->saveLastingSearch($aCategory);
        }
    }
    
    private function _setConditionByCategory($iCategory)
    {
        $sCategories = $iCategory;

        $sChildIds = Phpfox::getService('ecommerce.category')->getChildIds($iCategory);
        if (!empty($sChildIds))
        {
            $sCategories .= ',' . $sChildIds;
        }

        $this->search()->setCondition('AND ecd.category_id IN(' . $sCategories . ')');
    }
    
    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynauction.component_controller_my_offers_clean')) ? eval($sPlugin) : false);
    }

}

?>
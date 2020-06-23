<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_My_Won_Bids extends Phpfox_Component {

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
                    'action' => $this->url()->makeUrl('auction.my-won-bids'),
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

        
        $iPage = $this->request()->getInt('page') != '' ? $this->request()->getInt('page') : 1;
        $iLimit = 10;

        $this->search()->setCondition('AND ep.end_time < ' . PHPFOX_TIME);
        $this->search()->setCondition('AND ep.product_status = "completed"');
        $this->search()->setCondition('AND epa.auction_won_bidder_user_id = ' . Phpfox::getUserId());
        
        if ($this->search()->get('advsearch'))
        {
            $this->_setAdvSearchConditions();
        }
        
        list($iCnt, $aProducts) = Phpfox::getService('auction')->getMyWonBidsAucitons($this->search()->getConditions(), $this->search()->getSort(), $this->search()->getPage(), $iLimit);
   
        $aSellerId = array();

        foreach ($aProducts as $aProduct)
        {
            $aSellerId[] = $aProduct['user_id'];
        }

        $aProductSeller = array();
        $aSellerInfo = array();
        $aMultiCartIds = array();
        $aTotalCarts = array();
        if ($aSellerId)
        {
            $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->getSellerSettings($aSellerId);
            foreach ($aProducts as $iKey => $aProduct)
            {
                    $iExpiredDayBid = isset($aSellerSettings[$aProduct['user_id']]['time_complete_transaction'])?($aSellerSettings[$aProduct['user_id']]['time_complete_transaction']):7;
                    (int)$aProduct['auction_number_transfer'] += 1;
                    $iEndTimeForBuying = $aProduct['auction_number_transfer']*$iExpiredDayBid * 24 * 3600 + $aProduct['end_time'];
                    $aProducts[$iKey]['iEndTimeForBuying'] = $iEndTimeForBuying;
                    $aProducts[$iKey]['time_left_for_buying'] = Phpfox::getService('auction.helper')->getDuration(PHPFOX_TIME, $iEndTimeForBuying);
                    $aProductSeller[$aProduct['user_id']][] = $aProducts[$iKey];
                    $aSellerInfo[$aProduct['user_id']] = Phpfox::getService('user')->get($aProduct['user_id']);
                    $aMultiCartIds[$aProduct['user_id']][] = $aProduct['product_id'];
                    $aTotalCarts[$aProduct['user_id']]['total_cart_price'] = isset($aTotalCarts[$aProduct['user_id']]['total_cart_price'])?$aTotalCarts[$aProduct['user_id']]['total_cart_price']:0;
                    $aTotalCarts[$aProduct['user_id']]['total_cart_price'] += $aProduct['auction_won_bid_price'];
                    if (empty($aProducts[$iKey]['logo_path'])) {
                        $aProducts[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
                    }
            }
        }
        
        foreach ($aMultiCartIds as $key => $iAuctionIds) {
            $aMultiCartIds[$key] = implode("|", $iAuctionIds);
        }

        $aProductSeller = array_splice($aProductSeller, ($iPage - 1)*$iLimit ,$iLimit);
      

        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => 2 ));
        
        Phpfox::getService('auction.helper')->buildSectionMenu();
        
        Phpfox::getService('auction.helper')->loadAuctionJsCss();
        
        Phpfox::getService('auction.process')->autoUpdateStatusAuctions();
        
        $this->template()
                ->setHeader('cache', array(
                    'magnific-popup.css'       => 'module_auction',
                    'jquery.magnific-popup.js' => 'module_auction'
                ))
                ->assign(
                        array(
                             'aProducts'       => $aProducts,
                             'iTotalAuctions' => $iCnt,
                             'aProductSeller' => $aProductSeller,
                             'aMultiCartIds'   => $aMultiCartIds,
                             'aSellerInfo'    => $aSellerInfo,
                             'aTotalCarts'    => $aTotalCarts,
                             ));
    
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
        (($sPlugin = Phpfox_Plugin::get('ynauction.component_controller_my_won_bids_clean')) ? eval($sPlugin) : false);
    }

}

?>
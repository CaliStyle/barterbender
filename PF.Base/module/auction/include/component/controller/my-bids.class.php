<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_My_Bids extends Phpfox_Component {

    public function process()
    {
        Phpfox::isUser(true);
        $this->template()
                ->setTitle(_p('auctions'))
                ->setBreadCrumb(_p('auctions'), $this->url()->makeUrl('auction'))
                ->setBreadCrumb(_p('buyer_section'), $this->url()->makeUrl('auction.my-bids'));
        
        $iPage = $this->request()->getInt('page');
        
        $this->search()->set(array(
            'type' => 'auction',
            'field' => 'ep.product_id',
            'search_tool' => array(
                'table_alias' => 'ep',
                'search' => array(
                    'action' => $this->url()->makeUrl('auction.my-bids'),
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
        
        $this->search()->setCondition('AND ep.end_time > ' . PHPFOX_TIME);
        $this->search()->setCondition('AND ep.start_time <= ' . PHPFOX_TIME);
        $this->search()->setCondition('AND (ep.product_status = "running" OR ep.product_status = "bidden")');
        
        if ($this->search()->get('advsearch'))
        {
            $this->_setAdvSearchConditions();
        }
        
        list($iCnt, $aProducts) = Phpfox::getService('auction')->getMyBidsAuctions($this->search()->getConditions(), $this->search()->getSort(), $this->search()->getPage(), $iLimit);
        
        $aUserId = array();
        $aCategoryId = array();
        $iCntNotBanned = 0;
        foreach ($aProducts as $aProduct)
        {
            $aUserId[] = $aProduct['user_id'];
            $aCategoryId[] = $aProduct['category_id'];
            if ($aProduct['user_group_id'] != 5) {
                $iCntNotBanned++;
            }
        }
        
        if (count($aUserId) && count($aCategoryId))
        {
            $aSettings = Phpfox::getService('auction.bidincrement')->getSettings($aUserId, $aCategoryId);
            foreach ($aProducts as $iKey => $aProduct)
            {
                $aBidIncrement = array();
                if (isset($aSettings[$aProduct['user_id']][$aProduct['category_id']]['user']))
                {
                    $aBidIncrement = $aSettings[$aProduct['user_id']][$aProduct['category_id']]['user']['data_increasement'];
                }
                elseif (isset($aSettings[$aProduct['user_id']][$aProduct['category_id']]['default']))
                {
                    $aBidIncrement = $aSettings[$aProduct['user_id']][$aProduct['category_id']]['default']['data_increasement'];
                }
                if (isset($aBidIncrement['from']) && $aBidIncrement['from'])
                {
                    foreach ($aBidIncrement['from'] as $iFromKey => $iFromValue)
                    {
                        if ($iFromValue <= $aProduct['auction_latest_bid_price'] && $aProduct['auction_latest_bid_price'] <= $aBidIncrement['to'][$iFromKey])
                        {
                            $aProducts[$iKey]['fSuggestBidPrice'] = $aProduct['auction_latest_bid_price'] + $aBidIncrement['increment'][$iFromKey];
                        }
                    }
                }
                if (empty($aProducts[$iKey]['logo_path'])) {
                    $aProducts[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
                }
            }
        }
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $this->search()->getSearchTotal($iCnt)));
        Phpfox::getService('auction.helper')->buildSectionMenu();
        Phpfox::getService('auction.helper')->loadAuctionJsCss();
		$bCanBidAuction = Phpfox::getService('auction.permission')->canBidAuction();
        $this->template()->assign(array('bCanBidAuction' => $bCanBidAuction, 'aProducts' => $aProducts, 'iTotalAuctions' => $iCnt, 'iTotalAuctionsNotBanned' => $iCntNotBanned));
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
        (($sPlugin = Phpfox_Plugin::get('ynauction.component_controller_my_bids_clean')) ? eval($sPlugin) : false);
    }

}

?>
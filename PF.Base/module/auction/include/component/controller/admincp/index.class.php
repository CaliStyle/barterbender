<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Admincp_Index extends Phpfox_Component {

    public function process()
    {
        if ($aDeleteIds = $this->request()->getArray('id'))
		{
			if (Phpfox::getService('auction.process')->deleteMultiple($aDeleteIds))
			{
				$this->url()->send('admincp.auction', null, _p('auctions_successfully_deleted'));
			}
		}
        
        if ($iApproveId = $this->request()->getInt('approveid'))
		{
			if (Phpfox::getService('ecommerce.process')->approveProduct($iApproveId, null ,'auction'))
			{
			    Phpfox::getService('ecommerce.process')->updateProductStatus($iApproveId, 'approved');
				$this->url()->send('admincp.auction', null, _p('auctions_successfully_approved'));
			}
		}
		
        if ($iDenyId = $this->request()->getInt('denyid'))
		{
			if (Phpfox::getService('ecommerce.process')->deny($iDenyId))
			{
				$this->url()->send('admincp.auction', null, _p('auctions_successfully_denied'));
			}
		}
        
        if ($iDeleteId = $this->request()->getInt('deleteid'))
		{
			if (Phpfox::getService('ecommerce.process')->delete($iDeleteId))
			{
				$this->url()->send('admincp.auction', null, _p('auctions_successfully_deleted'));
			}
		}
		
		$iPage = $this->request()->getInt('page');
		
		$aPages = array(5, 10, 15, 20);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
		}		
		
		$aSorts = array(
			'product_creation_datetime' => _p('create_time'),
			'total_view' => _p('total_view'),
			'total_like' => _p('total_like'),
			'product_price' => _p('product_price'),
			'product_quantity' => _p('product_quantity')
		);
		
        $aProductStatus = array('draft', 'pending', 'denied', 'running', 'bidden', 'completed', 'deleted', 'approved');
        
        $aProductStatusOptions = array('any' => _p('all'));
        foreach ($aProductStatus as $sStatus)
        {
            $aProductStatusOptions[$sStatus] = _p('' . $sStatus);
        }
        
        $aCategories = Phpfox::getService('ecommerce.category')->getParentCategory();

        $aCategoriesOptions = array(array(_p('all'), ''));
        foreach ($aCategories as $aCategory)
        {
            $aCategoriesOptions[$aCategory['category_id']] = array(Phpfox::getLib('locale')->convert($aCategory['title']), 'AND ec.category_id = ' . $aCategory['category_id']);
        }

		$aFilters = array(
			'search' => array(
				'type' => 'input:text',
				'search' => "AND ep.name LIKE '%[VALUE]%'"
			),	
			'user' => array(
				'type' => 'input:text',
				'search' => "AND (u.user_name LIKE '%[VALUE]%' || u.full_name LIKE '%[VALUE]%')"
			),
            'product_status' => array(
				'type' => 'select',
				'options' => $aProductStatusOptions
			),
            'category_id' => array(
				'type' => 'select',
				'options' => $aCategoriesOptions
			),
            'featured' => array(
				'type' => 'select',
				'options' => array(
                    'any' => _p('all'),
                    'featured' => _p('featured'),
                    'not_featured' => _p('not_featured')
                )
			),
			'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'default' => '10'
			),
			'sort' => array(
				'type' => 'select',
				'options' => $aSorts,
				'default' => 'product_creation_datetime',
				'alias' => 'ep'
			),
			'sort_by' => array(
				'type' => 'select',
				'options' => array(
					'DESC' => _p('core.descending'),
					'ASC' => _p('core.ascending')
				),
				'default' => 'DESC'
			)
		);		
		
		$oSearch = Phpfox::getLib('search')->set(array(
				'type' => 'auctions',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		

		$iLimit = $oSearch->getDisplay();
		
        $sProductStatus = $oSearch->get('product_status');
        if (!empty($sProductStatus) && $sProductStatus != 'any')
        {
            $oSearch->setCondition('AND ep.product_status = "' . $sProductStatus . '"');
        }
        
        $iCategoryId = $oSearch->get('category_id');
        if (!empty($iCategoryId))
        {
            $oSearch->setCondition('AND ec.category_id = ' . $iCategoryId);
        }
        
        $sFeatured = $oSearch->get('featured');
        if ($sFeatured == 'featured')
        {
            $oSearch->setCondition('AND ep.feature_start_time < ' . PHPFOX_TIME);
            $oSearch->setCondition('AND ep.feature_end_time > ' . PHPFOX_TIME);
            $oSearch->setCondition('AND (ep.product_status = \'approved\' OR ep.product_status = \'bidden\' OR ep.product_status = \'running\' ) ');
        }
        elseif ($sFeatured == 'not_featured')
        {
            $oSearch->setCondition('AND (ep.feature_start_time > ' . PHPFOX_TIME . ' OR ep.feature_end_time < ' . PHPFOX_TIME . ')');
        }


		list($iCnt, $aAuctions) = Phpfox::getService('auction')->get($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $iLimit);
		
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));
		
        $this->template()
                ->setTitle(_p('manage_auctions'))
                ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('auction'), $this->url()->makeUrl('admincp.app',['id' => '__module_auction']))
                ->setBreadcrumb(_p('manage_auctions'), $this->url()->makeUrl('admincp.auction'))
                ->assign(array(
                        'aAuctions' => $aAuctions
                    )
                )
                ->setPhrase(array(
                    'auction.are_you_sure_you_want_to_deactivate_this_auction',
                    'auction.are_you_sure_you_want_to_un_feature_this_auction',
                    'auction.are_you_sure_you_want_to_delete_this_auction',
                    'auction.are_you_sure_you_want_to_delete_these_auctions',
                    'auction.yes',
                    'auction.no',
                    'auction.confirm'))
                ->setHeader('cache', array(
                    'magnific-popup.css' => 'module_auction',
                    'ynauction_backend.css' => 'module_auction',
                    'jquery.magnific-popup.js' => 'module_auction',
                    'ynauction_admin.js' => 'module_auction'			
                )			
            );
    }

}

?>
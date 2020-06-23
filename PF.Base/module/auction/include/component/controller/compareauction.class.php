<?php

defined('PHPFOX') or exit('NO DICE!');


class Auction_Component_Controller_compareauction extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('auction.helper')->buildMenu();
		$auction_ids = $this->request()->get('auction', '');
		$category_id = $this->request()->get('category', '');
		// $auction_ids = base64_decode($auction_ids);
		$auction_ids_cookie = Phpfox::getCookie('ynauction_compare_name');
		if($auction_ids_cookie == '' || (int)$category_id <= 0){
			$this->url()->send('auction');
			return false;
		}

		$aCategory = array();
		$aAuctionCompare = array();
        $aListOfAuctionIdToCompareCookie = explode(',', $auction_ids_cookie);
        foreach ($aListOfAuctionIdToCompareCookie as $key => $iAuctionId) {
            if($category = Phpfox::getService('auction')->getLastChildCategoryIdOfAuction($iAuctionId)){
            	if($category_id == $category['category_id']){
	            	$aAuctionCompare[] = Phpfox::getService('auction')->getAuctionById($iAuctionId);
            	}
            	$aAuction = $iAuctionId;
                if(isset($aCategory[$category['category_id']])){
                	$aCategory[$category['category_id']]['list_auction'][] = $aAuction;
                } else {
                    $aCategory[$category['category_id']] = array(
                        'data' => $category, 
                        'list_auction' => array($aAuction), 
                    );                                            
                }
            }
        }

        if(isset($aCategory[$category_id]) == false || isset($aCategory[$category_id]['list_auction']) == false || count($aCategory[$category_id]['list_auction']) < 2){
			$this->url()->send('auction');
			return false;
        }

        $parent_category_id = Phpfox::getService('ecommerce.category')->getParentId($category_id);
        $aCustomFields = Phpfox::getService('ecommerce')->getCustomFieldByCategoryId($parent_category_id);

        foreach ($aCategory as $key => $aCategoryItem) {
        	if(isset($aCategoryItem['list_auction'])){
	        	$aCategory[$key]['total_auction'] = count($aCategoryItem['list_auction']);
        	} else {
        		$aCategory[$key]['total_auction'] = 0;
        	}
        }

        $aFields =  Phpfox::getService('ecommerce')->getFieldsComparison();
        
        $aFields = array(
            array(
                    'comparison_id' => 1,
                    'comparison_name' => _p('name'),
                    'is_active' => 1,
                ),
            array(
                    'comparison_id' => 2,
                    'comparison_name' => _p('reserve_price'),
                    'is_active' => 1,
                ),

            array(
                    'comparison_id' => 3,
                    'comparison_name' => _p('number_of_bids'),
                    'is_active' => 1,
                ),
            array(
                    'comparison_id' => 4,
                    'comparison_name' => _p('number_of_orders'),
                    'is_active' => 1,
                ),
            array(
                    'comparison_id' => 5,
                    'comparison_name' => _p('number_of_views'),
                    'is_active' => 1,
                ),
            array(
                    'comparison_id' => 6,
                    'comparison_name' => _p('seller'),
                    'is_active' => 1,
                ),
            array(
                    'comparison_id' => 7,
                    'comparison_name' => _p('custom_field'),
                    'is_active' => 1,
                ),
            array(
                    'comparison_id' => 8,
                    'comparison_name' => _p('description'),
                    'is_active' => 1,
                )
        );
      
        foreach ($aAuctionCompare as $key => $value) {

            $aAuctionCompare[$key]['total_orders'] = isset($aAuctionCompare[$key]['total_orders'])?$aAuctionCompare[$key]['total_orders']:0;
			$aAuctionCompare[$key]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aAuctionCompare[$key]['creating_item_currency']);
            $aCustomData = array();
        	$aCustomDataTemp = Phpfox::getService('ecommerce.custom')->getCustomFieldByProductId($value['product_id']);
            if(count($aCustomFields)){
                foreach ($aCustomFields as $aField) {
                        foreach ($aCustomDataTemp as $aFieldValue) {
                            if($aField['field_id'] == $aFieldValue['field_id']){
                                $aCustomData[] = $aFieldValue;
                            }
                        }
                }
            }

            $aAuctionCompare[$key]['total_customdata'] = count($aCustomData);
            $aAuctionCompare[$key]['list_customdata'] = ($aCustomData);
            $aAuctionCompare[$key]['description'] = html_entity_decode($aAuctionCompare[$key]['description']);
            if (empty($aAuctionCompare[$key]['logo_path'])) {
                $aAuctionCompare[$key]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }
        // echo '<pre>';
        // print_r($aFields);
        // print_r($aCategory);
        // print_r($aCustomFields);
        // print_r($aAuctionCompare);
        // die;
        
        // total_score, total_rating
        // total_member = total_like
        // total_follow
        // total_reviews
        // contact detail: email, total_phone/list_phone, total_website/list_website
        // address: location_title - location_address
        // operating hours: location_title - location_address
        // custom fields: total_customdata/list_customdata
        // short description: short_description_parsed


        $aFieldStatus = Phpfox::getService('auction')->doComparisonField($aFields);
		// check permission 
		$sCompareLink = Phpfox::permalink('auction.compareauction', null, null);
		$this->template()->assign(array(
            'aFields' => $aFields, 
			'aFieldStatus' => $aFieldStatus, 
			'aCategory' => $aCategory, 
			'aAuctionCompare' => $aAuctionCompare, 
			'category_id' => $category_id, 
			'sCompareLink' => $sCompareLink, 
			'aCustomFields' => $aCustomFields, 
		));

		$this->template()->setHeader(
            array('jquery.rating.css' => 'style_css')
            )
			->setBreadcrumb(_p('module_menu'), $this->url()->makeUrl('auction'))
			// ->setBreadcrumb( _p('compare'), '')
			->setBreadcrumb( '', '', true)
			// ->setBreadcrumb(_p('create_new_auction'), $this->url()->makeUrl('auction.auctiontype'), true)
			; 

		$this->template()->setTitle(_p('compare'));

		Phpfox::getService('auction.helper')->loadAuctionJsCss();
	}
}
?>
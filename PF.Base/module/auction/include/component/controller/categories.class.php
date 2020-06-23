<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Categories extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('auction.helper')->buildMenu();
        $this->template()->setBreadCrumb(_p('auctions'), $this->url()->makeUrl('auction'));
        $this->template()->setTitle(_p('auctions'));

          $this->search()->set(array(
            'type' => 'auction',
            'field' => 'ep.product_id',
            'search_tool' => array(
                'table_alias' => 'ep',
                'search' => array(
                    'action' => $this->url()->makeUrl('auction.index', array()),
                    'default_value' => _p('search_auctions'),
                    'name' => 'search',
                    'field' => array('ep.name', 'ept.description'),
                ),
                'sort' => array(
                    'top-orders' => array('ep.total_orders', _p('top_orders')),
                    'newest' => array('ep.product_creation_datetime', _p('newest')),
                    'oldest' => array('ep.product_creation_datetime', _p('oldest'), 'ASC'),
                    'a-z' => array('ep.name', _p('a_z'), 'ASC'),
                    'z-a' => array('ep.name', _p('z_a')),
                    'most-liked' => array('ep.total_like', _p('most_liked'))
                ),
                'show' => array(12, 24, 36),
                'when_field' => 'product_creation_datetime'
            )
                )
        );

        $aAlfabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

        $aControllerCategories = Phpfox::getService('ecommerce.category')->getAllCategories();
        
        $iLimitNumberOfCategories = Phpfox::getParam('auction.max_items_sub_categories_list_display');
        
        $this->template()->assign(array(
            'aControllerCategories' => $aControllerCategories,
            'aAlfabet' => $aAlfabet,
            'iLimitNumberOfCategories' => $iLimitNumberOfCategories
                ));
        Phpfox::getService('auction.helper')->loadAuctionJsCss();
	
    }
}
?>
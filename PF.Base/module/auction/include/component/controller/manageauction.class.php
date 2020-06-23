<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_ManageAuction extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('auction.helper')->buildMenu();
        $iPage = $this->request()->getInt('page');
        $iLimit = 10;
        list($iCnt, $aAuctions) = Phpfox::getService('auction')->getAuctionOfSeller(array('ep.user_id = '.(int)Phpfox::getUserId()), 'ep.product_creation_datetime DESC', $iPage, $iLimit);

        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));

        $this->template()
                ->setTitle(_p('manage_auctions'))
                ->setBreadcrumb(_p('manage_auctions'), $this->url()->makeUrl('auction.manageauction'));
                

        $this->template()->assign(array(
                    'aAuctions' => $aAuctions,
                    'iPage' => $iPage,
                ))
        ->setPhrase(array(
                    'auction.are_you_sure_you_want_to_delete_this_auction',
                    'auction.are_you_sure_you_want_to_close_this_auction_notice_it_cannot_be_re_opened',
                    'auction.yes',
                    'auction.no',
                    'auction.confirm'))
                ->setHeader('cache', array(
                    'magnific-popup.css' => 'module_auction',
                    'jquery.magnific-popup.js' => 'module_auction',
                    'ynauction.js' => 'module_auction'            
                ));;

        Phpfox::getService('auction.helper')->loadAuctionJsCss();


	}
}
?>
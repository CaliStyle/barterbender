<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Statistic extends Phpfox_Component {

    public function process()
    {
        Phpfox::isUser(true);
		Phpfox::getService('auction.helper')->buildMenu();
        $this->template()->setBreadcrumb(_p('auctions'), $this->url()->permalink('auction',null))
                         ->setBreadcrumb(_p('seller_section'), $this->url()->permalink('auction.statistic',null));

        $this->template()
                ->setTitle(_p('statistic'));
        
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId());
        $iTotalAuctions = (int)Phpfox::getService('auction')->getTotalMyAuction(Phpfox::getUserId()); 
        $iTotalBids = (int)Phpfox::getService('auction.bid')->getTotalBidOfMyAuction(); 
        $iTotalOrders = (int)Phpfox::getService('ecommerce.order')->getTotalManageOrders('auction');
        $fTotalSales = (float)Phpfox::getService('ecommerce.order')->getTotalSaleOfMyItem('auction');
        $fTotalCommissions = (float)Phpfox::getService('ecommerce.order')->getTotalCommissionOfMyItem('auction');
		$iTotalSoldAuctions = (int)Phpfox::getService('ecommerce.order')->getTotalSoldOfMyItem('auction');
		$iTotalLikes = (int)Phpfox::getService('auction')->getTotalLikes();
        $iTotalViews = (int)Phpfox::getService('auction')->getTotalViews();
        $aForms = array();
        $this->template()
				->setPhrase(array(
					'ecommerce.publish_fee',
					'ecommerce.commission_fee',
					'ecommerce.featured_fee',
					'ecommerce.number_of_products_sold'
				))
                ->setHeader('cache', array(
                    'jquery.flot.js' => 'module_ecommerce',
                    'jquery.flot.time.js' => 'module_ecommerce',
                    'jquery.flot.stack.js' => 'module_ecommerce'
                ))
                ->assign(array(
            'iTotalAuctions' => $iTotalAuctions,
            'iTotalBids' => $iTotalBids,
            'iTotalOrders' => $iTotalOrders,
            'fTotalSales' => $fTotalSales,
            'fTotalCommissions' => $fTotalCommissions,
            'iTotalSoldAuctions' => $iTotalSoldAuctions,
            'iTotalLikes' => $iTotalLikes,
            'iTotalViews' => $iTotalViews,
            'aForms' => $aForms
        ));

        Phpfox::getService('auction.helper')
            ->loadAuctionJsCss();
    }

    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynecommerce.component_controller_statistic_clean')) ? eval($sPlugin) : false);
    }

}

?>
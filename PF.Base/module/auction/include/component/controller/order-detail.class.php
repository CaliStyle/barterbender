<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Order_Detail extends Phpfox_Component {

    public function process()
    {
    	Phpfox::getService('auction.helper')->buildMenu();
     	$iOrderId = $this->request()->get('req3'); 	
        $aOrder = Phpfox::getService('ecommerce.order')->getOrder($iOrderId);
        if (!$aOrder)
        {
            return;
        }

        if($aOrder['seller_user_id'] == Phpfox::getUserId()){
	      	$this->template()->setBreadcrumb(_p('auction'), $this->url()->permalink('auction',null))
	      					 ->setBreadcrumb(_p('seller_section'), $this->url()->permalink('auction.statistic',null));
	        
        }
      	elseif($aOrder['buyer_user_id'] == Phpfox::getUserId()){

      	   $this->template()->setBreadcrumb(_p('auction'), $this->url()->permalink('auction',null));
        	
      	}

       	return Phpfox::getLib('module')->setController('ecommerce.order-detail');

    }


}

?>
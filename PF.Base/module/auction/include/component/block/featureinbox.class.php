<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_featureinbox extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
        $iProductId = $this->getParam('iProductId');
        $aEditedAuction = Phpfox::getService('auction')->getAuctionForEdit($iProductId);
        /*get featured or not*/
        if( $aEditedAuction['feature_start_time'] <= PHPFOX_TIME &&  $aEditedAuction['feature_end_time'] >= PHPFOX_TIME
               && ( $aEditedAuction['product_status'] == 'approved' || $aEditedAuction['product_status'] == 'running' || $aEditedAuction['product_status'] == 'bidden')
            ){
                $aEditedAuction['featured'] = true;
            }
            else{
                $aEditedAuction['featured'] = false;
            }

        if($aEditedAuction['feature_end_time'] != 0)
        {
            if($aEditedAuction['feature_end_time'] > $aEditedAuction['end_time']){

                $aEditedAuction['feature_end_time'] = $aEditedAuction['end_time'];
            }
            $aEditedAuction['expired_date'] = Phpfox::getService('ecommerce.helper')->convertTime($aEditedAuction['feature_end_time']);   
            $aEditedAuction['start_date'] = Phpfox::getService('ecommerce.helper')->convertTime($aEditedAuction['feature_start_time']);   
        }



		$this->template()->assign(array(
				'iProductId' => $iProductId,
				'sFormUrl' => $this->url()->makeUrl('auction.edit') .'id_'.$iProductId, 
				'aEditedAuction' => $aEditedAuction,
				'iDefaultFeatureFee' => (int)Phpfox::getUserParam('auction.how_much_is_user_worth_for_auction_featured'),
				'aCurrentCurrencies' => Phpfox::getService('ecommerce.helper')->getCurrentCurrencies(),
			)
		);
		return 'block';
	}

}

?>

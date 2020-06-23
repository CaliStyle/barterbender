<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Watch_Process extends Phpfox_Service {

    public function addToWatchList($iProductId)
    {
        if (Phpfox::getService('auction.watch')->hasAddedInWatchList($iProductId))
        {
            return false;
        }
        
        $aInsert = array(
			'product_id' => (int) $iProductId,
			'user_id' => Phpfox::getUserId(),
			'time_stamp' => PHPFOX_TIME
		);
		
        return $this->database()->insert(Phpfox::getT('ecommerce_watch'), $aInsert);
    }

    public function removeFromWatchList($iProductId)
    {
        return $this->database()->delete(Phpfox::getT('ecommerce_watch'), 'product_id = ' . (int) $iProductId . ' AND user_id = ' . Phpfox::getUserId());
    }
	
	public function removeProductFromWatchList($iProductId)
    {
        return $this->database()->delete(Phpfox::getT('ecommerce_watch'), 'product_id = ' . (int) $iProductId);
    }
}

?>
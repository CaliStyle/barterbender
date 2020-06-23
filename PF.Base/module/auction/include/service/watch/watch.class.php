<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Watch_Watch extends Phpfox_Service {
    
    public function hasAddedInWatchList($iAuctionId)
    {
        if (!Phpfox::isUser())
        {
            return false;
        }
        
        $aCond = array(
			'AND ew.product_id = ' . (int) $iAuctionId,
			'AND ew.user_id = ' . Phpfox::getUserId()
		);
		
        $aRow = $this->database()
                ->select('ew.watch_id')
                ->from(Phpfox::getT('ecommerce_watch'), 'ew')
                ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = ew.product_id')
				->where($aCond)
                ->execute("getSlaveRow");
        
        return isset($aRow['watch_id']);
    }
            
}

?>
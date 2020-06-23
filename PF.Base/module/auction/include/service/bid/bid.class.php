<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Bid_Bid extends Phpfox_Service {

    public function getBidByBidId($iBidId)
    {
        $aRow = $this->database()
                        ->select('eab.*')
                        ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                        ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = eab.auctionbid_product_id')
                        ->where('eab.auctionbid_id = ' . (int) $iBidId)
                        ->execute('getRow');
        
        return $aRow;
    }
    
    public function getLatestBidByProductId($iProductId)
    {
        $aRow = $this->database()
                        ->select('eab.*')
                        ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                        ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = eab.auctionbid_product_id')
                        ->where('ep.product_id = ' . (int) $iProductId)
                        ->order('eab.auctionbid_id DESC')
                        ->execute('getRow');
        
        return $aRow;
    }
    
    public function getTotalBids()
    {
        $aConds= array(
            'AND ep.end_time > '.PHPFOX_TIME,
            'AND ep.start_time <= '.PHPFOX_TIME,
            'AND (ep.product_status = "running" OR ep.product_status = "bidden")',
            );
        $aConds[] = 'AND ep.product_status != "deleted"';
        $aConds[] = 'AND u.user_group_id != 5';

        $sQuery = $this->database()
                ->select('ep.product_id')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'eab.auctionbid_product_id = ep.product_id AND eab.auctionbid_user_id = ' . Phpfox::getUserId())
                ->where($aConds)
                ->group('eab.auctionbid_product_id')
                ->execute('');

        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');
        return $iCnt;
    }

  public function getTotalBidOfMyAuction()
  {
        $aConds = array(
                'AND ep.product_status != "deleted"',
                'AND ep.user_id = '.Phpfox::getUserId()
                 );
        
        $aAuctions = $this->database()
                ->select('ep.product_id, epa.auction_total_bid')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa','epa.product_id = ep.product_id')
                ->where($aConds)
                ->execute('getSlaveRows');
        $iCnt = 0;
        if (count($aAuctions)) {
            foreach ($aAuctions as $aAuction) {
                $iCnt += $aAuction['auction_total_bid'];
            }
        }                

        return $iCnt;
    }

    public function getNextPersionBid($iProductId)
    {
        $aBids = $this->database()->select('eab.*')
                        ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                        ->where('eab.auctionbid_product_id = ' .(int) $iProductId)
                        ->order('eab.auctionbid_price DESC')
                       ->execute('getSlaveRows');
        
        return $aBids;
    }

	public function hasBidder($iProductId)
	{
		return $this->database()
                        ->select('COUNT(eab.auctionbid_id)')
                        ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                        ->where('eab.auctionbid_product_id = ' . (int) $iProductId)
                        ->execute('getSlaveField');
	}
	
    public function getTotalBidders($iProductId)
    {
        $sQuery = $this->database()
                        ->select('COUNT(eab.auctionbid_user_id)')
                        ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                        ->where('eab.auctionbid_product_id = ' . (int) $iProductId)
                        ->group('eab.auctionbid_user_id')
                        ->execute('');
						
        $iTotal = $this->database()->select('count(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');
		
        return $iTotal;
    }
    
    public function getTotalBidsByAuctionId($iAuctionId)
    {
        $iTotal = $this->database()
                        ->select('COUNT(eab.auctionbid_id)')
                        ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                        ->where('eab.auctionbid_product_id = ' . (int) $iAuctionId)
                        ->execute('getSlaveField');
                
        return $iTotal;
    }
    
    public function get($aConds, $sSort = 'u.full_name ASC', $iPage = '', $iLimit = '')
    {
        $iCnt = $this->database()
                ->select('COUNT(eab.auctionbid_id)')
                ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = eab.auctionbid_user_id')
                ->where($aConds)
                ->execute('getSlaveField');
        
        $aRows = array();
        
        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('eab.auctionbid_id, eab.auctionbid_price, eab.auctionbid_currency, eab.auctionbid_creation_datetime, u.user_name, u.full_name')
                ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = eab.auctionbid_user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getRows');
            
            foreach ($aRows as $iKey => $aRow)
            {
                $aRows[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aRow['auctionbid_currency']);
            }
        }
        
        return array($iCnt, $aRows);
    }
}

?>
<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Offer_Offer extends Phpfox_Service
{   
    public function getOfferByOfferId($iOfferId)
    {
        $aOffer = $this->database()
                ->select('eao.*')
                ->from(Phpfox::getT('ecommerce_auction_offer'), 'eao')
                ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = eao.auctionoffer_product_id')
                ->where('eao.auctionoffer_id = ' . (int) $iOfferId)
                ->execute('getRow');

        return $aOffer;
    }

    public function getTotalOffers()
    {
        $aConds = array
        (
            'AND ep.start_time <= '.PHPFOX_TIME,
            'AND (ep.product_status = "running" OR ep.product_status = "approved" OR ep.product_status = "bidden" OR ep.product_status = "completed")',
            'AND ep.product_quantity > 0'
        );
        $aConds[] = 'AND ep.product_status != "deleted"';
        $aConds[] = 'AND u.user_group_id != 5';
     

        $sProductIdInCart = Phpfox::getService('auction')->getProductOfferInMyCart();
        if($sProductIdInCart != ''){
            $aConds[] = "AND eao.auctionoffer_product_id NOT IN (".$sProductIdInCart.")";
        }

        $sQuery = $this->database()
                ->select('ep.product_id')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->join(Phpfox::getT('ecommerce_auction_offer'), 'eao', 'eao.auctionoffer_product_id = ep.product_id AND eao.auctionoffer_user_id = ' . Phpfox::getUserId().' AND eao.auctionoffer_status != 3 AND eao.auctionoffer_status != 4')
                 
                ->where($aConds)
                ->group('eao.auctionoffer_product_id')
                ->execute('');  
        

        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');
        return $iCnt;
    }
	
    public function getOffers($iUserId, $aProductId)
    {
        if (!$aProductId)
        {
            return array();
        }
        
        $aConds = array(
            'AND t2.auctionoffer_product_id = t1.auctionoffer_product_id',
            'AND t2.auctionoffer_user_id = ' . (int) $iUserId,
            'AND t2.auctionoffer_product_id IN (' . implode(',', $aProductId) . ')'
        );
        
        $sQuery = 'SELECT t1.* '
                . 'FROM ' . Phpfox::getT('ecommerce_auction_offer') . ' AS t1 '
                . 'LEFT OUTER JOIN ' . Phpfox::getT('ecommerce_auction_offer') . ' AS t2 '
                . 'ON ( t1.auctionoffer_product_id = t2.auctionoffer_product_id '
                . 'AND t1.`auctionoffer_user_id` = t2.`auctionoffer_user_id` '
                . 'AND (t1.auctionoffer_creation_datetime < t2.auctionoffer_creation_datetime)) '
                . 'WHERE t1.`auctionoffer_user_id` = ' . (int) $iUserId . ' '
                . 'AND t1.`auctionoffer_product_id` IN (' . implode(',', $aProductId) . ') '
                . 'AND t2.auctionoffer_product_id IS NULL;';
        
        $aRows = $this->database()->getRows($sQuery);
        
        $aStatusTitle = array(
            0 => _p('pending'),
            1 => _p('approved'),
            2 => _p('denied'),
            3 => _p('completed'),
            4 => _p('expired')
        );
        
        foreach ($aRows as $iKey => $aRow)
        {
            $aRows[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aRow['auctionoffer_currency']);
            $aRows[$iKey]['status_title'] = isset($aStatusTitle[$aRow['auctionoffer_status']]) ? $aStatusTitle[$aRow['auctionoffer_status']] : _p('pending');
        }
        
        return $aRows;
    }
    
    public function get($aConds, $sSort = 'u.full_name ASC', $iPage = '', $iLimit = '')
    {
        $iCnt = $this->database()
                ->select('COUNT(eao.auctionoffer_id)')
                ->from(Phpfox::getT('ecommerce_auction_offer'), 'eao')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = eao.auctionoffer_user_id')
                ->where($aConds)
                ->execute('getSlaveField');
        
        $aRows = array();
        
        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('eao.*, u.user_name, u.full_name')
                ->from(Phpfox::getT('ecommerce_auction_offer'), 'eao')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = eao.auctionoffer_user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getRows');
            
            $aStatusTitle = array(
                0 => _p('pending'),
                1 => _p('approved'),
                2 => _p('denied'),
                3 => _p('expired'),
                4 => _p('success')
            );
            
            foreach ($aRows as $iKey => $aRow)
            {
                $aRows[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aRow['auctionoffer_currency']);
                $aRows[$iKey]['status_title'] = isset($aStatusTitle[$aRow['auctionoffer_status']]) ? $aStatusTitle[$aRow['auctionoffer_status']] : _p('pending');
            }
        }
        
        return array($iCnt, $aRows);
    }
    
    public function canMakeOffer($iUserId, $iProductId)
    {
        $aConds = array(
            'AND eao.auctionoffer_user_id = ' . (int) $iUserId,
            'AND eao.auctionoffer_product_id = ' . (int) $iProductId,
            'AND ((eao.auctionoffer_status = 0 OR eao.auctionoffer_status = 1) AND eao.auctionoffer_status !=  3 AND eao.auctionoffer_status !=  4 )'
        );
        
        $iCnt = $this->database()
                ->select('COUNT(eao.auctionoffer_id)')
                ->from(Phpfox::getT('ecommerce_auction_offer'), 'eao')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = eao.auctionoffer_user_id')
                ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = eao.auctionoffer_product_id')
                ->where($aConds)
                ->execute('getSlaveField');

        return ($iCnt == 0);
    }
            
}
?>
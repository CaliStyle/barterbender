<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Bidincrement_Bidincrement extends Phpfox_Service
{   
    public function getSetting($iCategoryId = 0, $sType = 'default', $iUserId = 0)
    {
		$aCond = array(
			'AND ebi.category_id = ' . (int) $iCategoryId,
			'AND ebi.user_id = ' . (int) $iUserId,
			'AND ebi.type_increasement = "' . ($sType == 'default' ? 'default' : 'user') . '"'
		);
		
        $aRow = $this->database()
                ->select('ebi.*')
                ->from(Phpfox::getT('ecommerce_bid_increasement'), 'ebi')
				->where($aCond)
                ->execute("getSlaveRow");
        
        if ($aRow)
        {
            $aRow['data_increasement'] = (array) json_decode($aRow['data_increasement']);
        }
        
        return $aRow;
    }
	
    public function getSettings($aUserId, $aCategoryId)
    {
        if (count($aCategoryId) == 0 || count($aUserId) == 0)
        {
            return array();
        }
        
		$aCond = array(
			'AND ebi.category_id IN (' . implode(',', $aCategoryId) . ')',
			'AND ebi.user_id IN (' . implode(',', $aUserId) . ')'
		);
		
        $aRows = $this->database()
                ->select('ebi.*')
                ->from(Phpfox::getT('ecommerce_bid_increasement'), 'ebi')
				->where($aCond)
                ->execute("getRows");
        
        $aResult = array();
        
        if ($aRows)
        {
            foreach ($aRows as $iKey => $aRow)
            {
                $aRow['data_increasement'] = (array) json_decode($aRow['data_increasement']);
                
                $aResult[$aRow['user_id']][$aRow['category_id']][$aRow['type_increasement']] = $aRow;
            }
            
        }
        
        return $aResult;
    }
}
?>
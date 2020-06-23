<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Bidincrement_Process extends Phpfox_Service
{
	public function deleteSetting($iCategoryId = 0, $sType = 'default', $iUserId = 0)
    {
		$sCond = 'category_id = ' . (int) $iCategoryId . ' AND user_id = ' . (int) $iUserId . ' AND type_increasement = "' . ($sType == 'default' ? 'default' : 'user') . '"';
		
        $this->database()->delete(Phpfox::getT('ecommerce_bid_increasement'), $sCond);
    }
    
    public function addSetting($aVals, $isAdminCp = null)
    {
        $iCategoryId = isset($aVals['category_id']) ? (int) $aVals['category_id'] : 0;
        $user_id = isset($aVals['user_id']) ? (int) $aVals['user_id'] : Phpfox::getUserId();
		
		if(isset($isAdminCp) && $isAdminCp)
		{
			$user_id = 0;
		}
		
        if ($iCategoryId == 0)
        {
            return false;
        }
        
        $aInsert = array(
			'category_id' => $iCategoryId,
			'data_increasement' => isset($aVals['data_increasement']) ? json_encode($aVals['data_increasement']) : '',
			'user_id' => $user_id,
			'type_increasement' => (isset($aVals['type_increasement']) && $aVals['type_increasement'] == 'default') ? 'default' : 'user',
			'create_timestamp' => PHPFOX_TIME
		);
		
        $id = $this->database()->insert(Phpfox::getT('ecommerce_bid_increasement'), $aInsert);

        return $id;
    }
	
	public function editSetting($aVals)
    {
		$iDataId = isset($aVals['data_id']) ? (int) $aVals['data_id'] : 0;
		
        $iCategoryId = isset($aVals['category_id']) ? (int) $aVals['category_id'] : 0;
        if ($iCategoryId == 0)
        {
            return false;
        }
        
        $aUpdate = array(
			'category_id' => $iCategoryId,
			'data_increasement' => isset($aVals['data_increasement']) ? json_encode($aVals['data_increasement']) : '',
			'type_increasement' => (isset($aVals['type_increasement']) && $aVals['type_increasement'] == 'default') ? 'default' : 'user',
			'update_timestamp' => PHPFOX_TIME
		);
		
        return $this->database()->update(Phpfox::getT('ecommerce_bid_increasement'), $aUpdate, 'data_id = ' . $iDataId);
     }
}
?>
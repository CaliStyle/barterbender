<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Bidincrementcontent extends Phpfox_Component {

    public function process()
    {
		$iCategoryId = $this->getParam('iCategoryId');
		$isAdminCp = $this->getParam('isAdminCp');
		$aBidIncrementSetting = array();
		if($isAdminCp){
			$iUserId = 0;
        	$aBidIncrementSetting = Phpfox::getService('auction.bidincrement')->getSetting($iCategoryId, $sType = 'default', 0);
		} else {
			$iUserId = Phpfox::getUserId();
        	$aBidIncrementSetting = Phpfox::getService('auction.bidincrement')->getSetting($iCategoryId, $sType = 'user', $iUserId);
		}
		
		$aBidIncrementData = array();
		if (isset($aBidIncrementSetting['data_increasement']['from']))
		{
			foreach ($aBidIncrementSetting['data_increasement']['from'] as $iKey => $fValue)
			{
				$aBidIncrementData[] = array(
					'from' => $fValue,
					'to' => $aBidIncrementSetting['data_increasement']['to'][$iKey],
					'increment' => $aBidIncrementSetting['data_increasement']['increment'][$iKey]
				);
			}
		}
		$aBidIncrementSetting['data_increasement'] = $aBidIncrementData;
		
		$this->template()->assign(array('aForms' => $aBidIncrementSetting,'isAdminCp' => $isAdminCp));
    }

}

?>

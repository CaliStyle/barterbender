<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Block_Bidincrementcontent extends Phpfox_Component {

    public function process()
    {
		Phpfox::isUser(true);
		
		$iCategoryId = $this->getParam('iCategoryId');
		
        $aBidIncrementSetting = Phpfox::getService('auction.bidincrement')->getSetting($iCategoryId, $sType = 'user', $iUserId = Phpfox::getUserId());
		
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
		
		$this->template()->assign(array('aForms' => $aBidIncrementSetting));
    }

}

?>

<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

// Add and edit request both go here 
class Socialad_Component_Block_Report_Short_List extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iAdId = $this->getParam('iShortListAdId');
		$aDatas = Phpfox::getService('socialad.ad.statistic')->getStatisticByDayOfAd($iAdId, $iLimit = 7);


		$aGroupDatas = array();
		if(count($aDatas)){
			foreach ($aDatas as $key => $aData) {
					$aGroupDatas[$aData['time_stamp']][] = $aData;
			}			
		}

		$aDatas = array();
		if(count($aGroupDatas)){
			foreach ($aGroupDatas as $keyGroup => $aGroupData) {
				if(count($aGroupData)){
					$aDefaultData = $aGroupData[0];
					foreach ($aGroupData as $keyData => $aData) {
						if($aData['statistic_id'] > $aDefaultData['statistic_id']){
							$aDefaultData = $aData;
						}		
					}
					$aDatas[] = $aDefaultData;
				}
			}
		}

		$this->template()->assign(array(
			'aSaDatas' => $aDatas
		));
	}


	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}


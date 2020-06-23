<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Tag
 * @version 		$Id: cloud.class.php 6877 2013-11-12 11:18:43Z Miguel_Espinoza $
 */
class FoxFeedsPro_Component_Block_Cloud extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$sType = 'foxfeedspro_news';
		$aTags = Phpfox::getService('foxfeedspro')->getTagCloud(10);

		$aTagsFont = array();
		if(count($aTags) > 0){
			foreach ($aTags as $aTag) {
				 $aTagsFont[$aTag['key']] = $aTag['value'];
			}
			
			$maxValue = max($aTagsFont);
			$minValue = min($aTagsFont);
			$step = ($maxValue - $minValue) / 4;		
			
			foreach ($aTags as $key =>$aTag) {
				if($aTag['value'] <= $minValue){
						$aTags[$key]['font'] = 12;
				}
				else
				if($aTag['value'] <= ($minValue + $step) && $aTag['value'] > $minValue){
						$aTags[$key]['font'] = 15;
				}
				else
				if($aTag['value'] <= ($minValue + $step * 3) && $aTag['value'] > ($minValue + $step * 2) ){
						$aTags[$key]['font'] = 17;
				}
				else
				if($aTag['value'] < ($maxValue) && $aTag['value'] > ($minValue + $step * 3) ){
						$aTags[$key]['font'] = 19;
				}
				else
				if($aTag['value'] >= ($maxValue)){
						$aTags[$key]['font'] = 21;
				}
				else{
					$aTags[$key]['font'] = 12;
				}
			}

			shuffle($aTags);
		}
		else{
			$aTags = array();
		}
		$this->template()->assign(array(
			'aTags' => $aTags
			)
		);
	}
}

?>
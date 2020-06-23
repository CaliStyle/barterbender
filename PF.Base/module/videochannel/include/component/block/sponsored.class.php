<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Sponsored extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (!Phpfox::isModule('ad'))
		{
			return false;
		}		
		
		if (defined('PHPFOX_IS_GROUP_VIEW'))
		{
		    return false;
		}
		
		$aSponsorVideo = Phpfox::getService('videochannel')->getRandomSponsored();
		if (empty($aSponsorVideo))
		{
		    return false;
		}
		
		// update the views count
		Phpfox::getService('ad.process')->addSponsorViewsCount($aSponsorVideo['sponsor_id'], 'videochannel');
		
		$this->template()->assign(array(
				'sHeader' => _p('videochannel.sponsored_video'),
				'aSponsorVideo' => $aSponsorVideo,
				'aFooter' => array(_p('videochannel.encourage_sponsor') => $this->url()->makeUrl('profile.videochannel', array('sponsor' => 1)))
			)
		);
		
		if (!empty($aSponsorVideo['destination'])) // its an uploaded vid
		{
		    $sPath = (preg_match("/\{file\/videos\/(.*)\/(.*)\.flv\}/i", $aSponsorVideo['destination'], $aMatches) ? Phpfox::getParam('core.path') . str_replace(array('{', '}'), '', $aMatches[0]) : Phpfox::getParam('videochannel.url') . $aSponsorVideo['destination']);
		    $this->template()->assign(array(
					'sPath' => $sPath
				)
			);
		}
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('videochannel.component_block_spotlight_clean')) ? eval($sPlugin) : false);
	}
}

?>
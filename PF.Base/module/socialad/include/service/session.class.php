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

class Socialad_Service_Session extends Phpfox_Service
{
	private $_prefix = 'socialad_';
	public function __construct() {
		$this->_oSession = Phpfox::getLib('session');
	}
	public function set($sName, $value) {
		$this->_oSession->set($this->_prefix. $sName, $value); 
	}

	public function get($sName) {
		return $this->_oSession->get($this->_prefix. $sName);
	}

	// I mixed login of setting into this function for convience, it should be more into logic code
	public function shouldShow($sName, $iNumberOfTime) {
		// stupid empty function in session library of Phpfox prevents us from using 0 in session, so we plus 1 for all
		$iNumberOfTime = $iNumberOfTime + 1; 
		$iCurrent = $this->get($sName) ? $this->get($sName) : 1;
		if($iCurrent >= $iNumberOfTime) {
			$this->set($sName, 1);
			return true;
		} else {
			$iCurrent += 1;
			$this->set($sName, $iCurrent);
			return false;
		}
	}

	public function shouldShowFeed() {
		$sName = 'feed_view_count';
		$iNumberOfTime = Phpfox::getParam('socialad.number_user_view_to_display_ad');
		return $this->shouldShow($sName, $iNumberOfTime);
	}

	public function shouldShowHtml() {
		$sName = 'html_view_count';
		$iNumberOfTime = Phpfox::getParam('socialad.number_user_view_to_display_html_ads');
		return $this->shouldShow($sName, $iNumberOfTime);
	}

	public function shouldShowBanner() {
		$sName = 'banner_view_count';
		$iNumberOfTime = Phpfox::getParam('socialad.number_user_view_to_display_banner_ads');
		return $this->shouldShow($sName, $iNumberOfTime);
	}
}




<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Browse extends Phpfox_Service {

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->_sTable = Phpfox::getT('fundraising_campaign');
	}

	public function query() {
		$this->database()->select('campaign_text.description_parsed AS description, ');

		$this->database()->join(Phpfox::getT('fundraising_text'), 'campaign_text', 'campaign_text.campaign_id = campaign.campaign_id');

		if (Phpfox::isUser() && Phpfox::isModule('like')) {
			$this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'fundraising\' AND lik.item_id = campaign.campaign_id AND lik.user_id = ' . Phpfox::getUserId());
		}
	}

	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false) {
		if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
			$this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = campaign.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
		}

		if ($this->request()->get((defined('PHPFOX_IS_USER_PROFILE') ? 'req3' : 'req2')) == 'category') {
			$this->database()
					->innerJoin(Phpfox::getT('fundraising_campaign_category'), 'fundraising_campaign_category', 'fundraising_campaign_category.campaign_id = campaign.campaign_id');

			if (!$bIsCount)
			{
				$this->database()->group('campaign.campaign_id');
			}
		}

		if($this->request()->get('view') && $this->request()->get('view') == 'idonated')
		{
			$this->database()->join(Phpfox::getT('fundraising_donor'), 'donor', 'donor.campaign_id = campaign.campaign_id');
		}
	}

	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments) {
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('fundraising.service_browse__call')) {
			eval($sPlugin);
			return;
		}

		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

}

?>
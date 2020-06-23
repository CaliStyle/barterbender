<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Fundraising extends Phpfox_Service {

	public function __construct() {
		$this->_sTable = Phpfox::getT('fundraising_campaign');
		$this->_aErrorStatus = array(
			'invalid_permission' => array(
				'code' => 1,
				'phrase' => '' . _p('invalid_permission_or_closed_campaign')
			),
			'cannot_publish_because_inactive' => array(
				'code' => 2,
				'phrase' => '' . _p('cannot_publish_because_inactive')
			),
			'cannot_view_because_inactive' => array(
				'code' => 3,
				'phrase' => '' . _p('cannot_view_because_inactive')
			),
		);
			
	}

	private $_aBadgeStatus = array(
		'donate_button' => 1,
		'donors' => 2,
		'both' => 3,
		'none' => 4
	);

	public function convertToShortTypeAmount($iAmount)
	{
		if($iAmount >= 1000 && $iAmount < 1000000 )
		{
			$iAmount = round($iAmount / 1000, 1) . 'k';
		}
		else if ($iAmount >= 1000000)
		{
			$iAmount = round($iAmount / 1000000, 1) . 'M';
		}

		return $iAmount;
		
	}
	
	/**
	 * 1: $2.00 
		2: 2.00$ 
		3: 2.00 USD 
		4: $2.00 USD 
	 * @param int $iAmount
	 * @param string $sCurrency
	 * @return string
	 */
	public function getCurrencyText($iAmount, $sCurrency)
	{
		return Phpfox::getService('core.currency')->getCurrency($iAmount, $sCurrency);
	}

	public function getAllErrorStatus()
	{
		return $this->_aErrorStatus;
	}
	
	/**
	 * 
	 * @return error status code and phrase if having or false
	 */
	public function getErrorStatusNumber($sName)
	{
		if(isset($this->_aErrorStatus[$sName]))
		{
			return $this->_aErrorStatus[$sName]['code'];
		}

		return false;
	}
	

	/**
	 * get callback to add feed in page 
	 * 
	 * @param int $iCampaignId
	 */
	public function getFundraisingAddCallback($iCampaignId)
	{
		Phpfox::getService('pages')->setIsInPage();
		
		return array(
			'module' => 'pages',
			'item_id' => $iCampaignId,
			'table_prefix' => 'pages_'
		);
	}

	public function getAllBadgeStatus()
	{
		return $this->_aBadgeStatus;
	}
	
	public function getBadgeStatusNumber($sName)
	{
		if(isset($this->_aBadgeStatus[$sName]))
		{
			return $this->_aBadgeStatus[$sName];
		}

		return false;
	}

	public function getFrameUrl($iCampaignId, $iStatus = 3)
	{
		if(!$iCampaignId)
		{
			return false;
		}
		$sCorePath = Phpfox::getService('fundraising') -> getStaticPath();
		$sFrameUrl = $sCorePath . 'module/fundraising/static/campaign-badge.php?id=' . $iCampaignId . '&status=' . $iStatus;

		return $sFrameUrl;
	}

	public function getBadgeCode($sFrameUrl)
	{
		return "<iframe src=\"{$sFrameUrl}\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:300px; height:600px;\" allowTransparency=\"true\">;</iframe>";
	}
	/**
	 * check in the list of friends what friend user has invited then make the short list
	 * @TODO: none 
	 * <pre>
	 * Phpfox::getService('fundraising')->isAlreadyInvited($iCampaignId, $aFriendList);
	 * </pre>
	 * @by minhta
	 * @param int $iCampaignId 
	 * @param int $aFriends list of user's friend
	 * @return
	 */
	public function isAlreadyInvited($iCampaignId, $aFriends) {
		if ((int) $iCampaignId === 0) {
			return false;
		}

		if (is_array($aFriends)) {
			if (!count($aFriends)) {
				return false;
			}

			$sIds = array();
			foreach ($aFriends as $aFriend) {
				if (!isset($aFriend['user_id'])) {
					continue;
				}

				$sIds[] = $aFriend['user_id'];
			}

			$aInvites = $this->database()->select('invited_id, donor_id, invited_user_id')
					->from(Phpfox::getT('fundraising_invited'))
					->where('campaign_id = ' . (int) $iCampaignId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
					->execute('getSlaveRows');

			$aCache = array();
			foreach ($aInvites as $aInvite) {
				$aCache[$aInvite['invited_user_id']] = ($aInvite['donor_id'] > 0 ? _p('signed') : _p('invited'));
			}

			if (count($aCache)) {
				return $aCache;
			}
		}

		return false;
	}

	/**
	 * parse text for showing on form based on the campaign
	 * it will replace some predefined symbol by the corresponding text
	 * @TODO: none 
	 * <pre>
	 * Phpfox::getService('fundraising')->parseVar($textm, $campaign);
	 * </pre>
	 * @by minhta
	 * @param string $sToBeParsedText the text to be parsed 
	 * @param array $aCampaign the corresponding campaign
	 * @return
	 */
	public function parseVar($sToBeParsedText, $aCampaign) {
		$aReplace = array('[title]', '[campaign_url]', '[financial_goal]', '[short_description]', '[description]',
			'[start_time]', '[end_time]', '[total_amount]', '[full_name]'
		);

		$oDate = Phpfox::getLib('date');
		$sUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
		$aLink = Phpfox::getLib('url')->permalink('fundraising', $aCampaign['campaign_id'], $aCampaign['title']);
		$sLink = '<a href="' . $aLink . '" title = "' . $aCampaign['title'] . '" target="_blank">' . $aLink . '</a>';

		$aVar = array($aCampaign['title'], $sLink, $aCampaign['financial_goal'], $aCampaign['short_description'], $aCampaign['description'],
			$oDate->convertTime($aCampaign['start_time']), $oDate->convertTime($aCampaign['end_time']), $aCampaign['total_amount'], $sUser['full_name']
		);
		$sToBeParsedText = str_replace($aReplace, $aVar, $sToBeParsedText);
		return $sToBeParsedText;
	}

	/**
	 * get statistic for module petition, it doesn't include statistic in a page
	 * @TODO: complete later 
	 * <pre>
	 * </pre>
	 * @by minhta
	 * @return array
	 */
	public function getStats() {
		$aStats = array();
		$oCustomCache = Phpfox::getService('fundraising.cache');
		$sKey = 'site_stats' ;
		$sType = 'site_stats';
		
		if (!($aStats = $oCustomCache->get($sKey, $sType)))
		{

			$aStats['ongoing'] = (int) $this->database()->select('COUNT(*)')
											->from($this->_sTable)
											->where('is_approved = 1 AND status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') . ' AND module_id = "fundraising" AND is_active = 1 ')
											->execute('getSlaveField');
			$aStats['reached'] = (int) $this->database()->select('COUNT(*)')
											->from($this->_sTable)
											->where('is_approved = 1 AND status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('reached') . ' AND module_id = "fundraising" AND is_active = 1 ')
											->execute('getSlaveField');
			$aStats['closed'] = (int) $this->database()->select('COUNT(*)')
											->from($this->_sTable)
											->where('is_approved = 1 AND status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('closed') . ' AND module_id = "fundraising" AND is_active = 1 ')
											->execute('getSlaveField');
			$aStats['expired'] = (int) $this->database()->select('COUNT(*)')
											->from($this->_sTable)
											->where('is_approved = 1 AND status = ' . Phpfox::getService('fundraising.campaign')->getStatusCode('expired') . ' AND module_id = "fundraising" AND is_active = 1 ')
											->execute('getSlaveField');

			$aStats['ongoing'] = number_format($aStats['ongoing'], 0, '.', ',');
			$aStats['reached'] = number_format($aStats['reached'], 0, '.', ',');
			$aStats['closed'] = number_format($aStats['closed'], 0, '.', ',');
			$aStats['expired'] = number_format($aStats['expired'], 0, '.', ',');

			$oCustomCache->set($sKey, $aStats);
		}

		return $aStats;
	}

	/**
     * Build main menu of module
	 */
	public function buildMenu() {
		$aFilterMenu = array(
			_p('all_fundraisings') => '',
		);

		if (Phpfox::isUser()) {
            $iMyTotal = $this->getMyCampaignTotal();
            $iMyTotal = ($iMyTotal >= 100) ? '99+' : $iMyTotal;
            $aFilterMenu[_p('my_fundraisings') . ($iMyTotal ? ('<span class="my count-item">' . $iMyTotal . '</span>') : '')] = 'my';

            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')) {
                $aFilterMenu[_p('friends_fundraisings')] = 'friend';
            }

            if (Phpfox::getUserParam('fundraising.can_approve_campaigns')) {
                $iPendingTotal = Phpfox::getService('fundraising.campaign')->getTotalPendings();

                if ($iPendingTotal) {
                    $aFilterMenu[_p('pending_fundraisings') . (Phpfox::getUserParam('fundraising.can_approve_campaigns') ? '<span class="pending count-item">' . $iPendingTotal . '</span>' : 0)] = 'pending';
                }
            }

            $aFilterMenu[_p('my_donated_campaigns')] = 'idonated';

        }

		$aFilterMenu[_p('featured_campaigns')] = 'featured';
		$aFilterMenu[_p('reached_campaigns')] = 'reached';
		$aFilterMenu[_p('expired_campaigns')] = 'expired';
		$aFilterMenu[_p('closed_campaigns')] = 'closed';
		Phpfox::getLib('template')->buildSectionMenu('fundraising', $aFilterMenu);
	}

    public function getMyCampaignTotal()
    {
        $sWhere = 'user_id = ' . Phpfox::getUserId();
        $aModules = [];
        if (!Phpfox::isModule('groups')) {
            $aModules[] = 'groups';
        }
        if (!Phpfox::isModule('pages')) {
            $aModules[] = 'pages';
        }
        $sWhere .= (!empty($aModules) ? ' AND (module_id NOT IN ("' . implode('","',
                $aModules) . '") OR module_id is NULL)' : '');

        return (int)db()->select('COUNT(campaign_id)')->from($this->_sTable)->where($sWhere)->execute('getSlaveField');
    }

	/**
	 * get news list for detail block
	 * @by datlv
	 * <pre>
	 * Phpfox::getService('fundraising')->getNews($iId);
	 * </pre>
	 * @param $iId
	 * @param $iLimit
	 * @return array()
	 */
	public function getNews($iId, $iLimit = null) {
		$aRows = $this->database()->select('fn.*')
				->from(Phpfox::getT('fundraising_news'), 'fn')
				->where('fn.campaign_id = ' . (int) $iId)
				->order('fn.time_stamp DESC')
				->execute('getSlaveRows');

		$iTotal = count($aRows);
		return array($iTotal, $aRows);
	}

	/**
	 * we only support paypal
	 * @by datlv
	 * @return array(currency)
	 */
	public function getCurrentCurrencies($sGateway = 'paypal', $sDefaultCurrency = '') {
		
		$aFoxCurrencies = Phpfox::getService('core.currency')->getForBrowse();
		$oGateway = Phpfox::getService('fundraising.gateways')->load($sGateway);
		$aSupportedCurrencies = $oGateway->getSupportedCurrencies();

		$sDefaultCurrency = $sDefaultCurrency ? $sDefaultCurrency : Phpfox::getService('core.currency')->getDefault();
		$aDefaultCurrency = array();
		$aResults = array();
		foreach($aFoxCurrencies as $aCurrency)
		{
			if(in_array($aCurrency['currency_id'], $aSupportedCurrencies) )		
			{
				if($aCurrency['currency_id'] == $sDefaultCurrency)
				{
					$aDefaultCurrency = $aCurrency;
				}
				else
				{
					$aResults[] = $aCurrency;
				}
			}
		}

		array_unshift($aResults, $aDefaultCurrency);

		return $aResults;
		//}
	}

	/**
	 * get all image of this campaign
	 * @by datlv
	 * @TODO : complete later
	 * <pre>
	 * Phpfox::getService('fundraising')->getImages($iId)
	 * </pre>
	 * @param $iId
	 * @return array() images of campaign
	 */
	public function getImages($iId) {
		$aRows = $this->database()->select('fi.*')
				->from(Phpfox::getT('fundraising_image'), 'fi')
				->where('fi.campaign_id = ' . (int) $iId)
				->order('fi.ordering DESC')
				->execute('getSlaveRows');

		return $aRows;
	}

	// --------------------------datlv

	public function __call($sMethod, $aArguments) {
		if ($sPlugin = Phpfox_Plugin::get('fundraising.service_fundraising__call')) {
			return eval($sPlugin);
		}

		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
	
	public function getStaticPath(){
		$sCorePath = Phpfox::getParam('core.path');
		$sCorePath = str_replace("index.php".PHPFOX_DS, "", $sCorePath);
		$sCorePath .= 'PF.Base'.PHPFOX_DS;
		return $sCorePath;
	}
}
?>


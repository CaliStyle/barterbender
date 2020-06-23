<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Service_Browse extends Phpfox_Service 
{
	private $_iChannelId = null;
	
	private $_sCategory = null;	
	
	private $_aCallback = false;
	
	private $_sTag = null;
	
	private $_bFull = false;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('channel_video');
	}
	
	public function query()
	{
		
	}
	
	public function processRows(&$aRows)
	{
		foreach ($aRows as $iKey => $aRow)
		{				
			$aRows[$iKey]['link'] = ($this->_aCallback !== false ? Phpfox::getLib('url')->makeUrl($this->_aCallback['url'][0], array_merge($this->_aCallback['url'][1], array($aRow['title']))) : Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']));
		}
	}
	
	public function channel($iChannelId)
	{
		$this->_iChannelId = $iChannelId;		
		return $this;
	}
	
	public function category($sCategory)
	{
		$this->_sCategory = $sCategory;
		
		return $this;
	}
	
	public function callback($aCallback)
	{
		$this->_aCallback = $aCallback;
		
		return $this;
	}	
	
	public function tag($sTag)
	{
		$this->_sTag = $sTag;
		
		return $this;
	}
	
	public function full($bFull)
	{
		$this->_bFull = $bFull;
		
		return $this;
	}
	
	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
	{
		if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
		{
			$this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = m.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());	
		}		
		
		if ($this->_sTag != null)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = m.video_id AND tag.category_id = \''.(defined('PHPFOX_GROUP_VIEW') ? 'video_group' : 'videochannel').'\' AND tag_text = "'. $this->_sTag.'"');
			if (!$bIsCount)
			{
				$this->database()->group('m.video_id');
			}			
		}

        if ($this->_sCategory !== null)
		{
            $this->database()->innerJoin(Phpfox::getT('channel_category'), 'mc', 'mc.category_id = mc.category_id');
			$this->database()->innerJoin(Phpfox::getT('channel_category_data'), 'mcd', 'mcd.video_id = m.video_id');
			
			if (!$bIsCount)
			{
				$this->database()->group('m.video_id');
			}
		}
		
		if ($this->_iChannelId !== null)
		{
			$this->database()->join(Phpfox::getT('channel_channel_data'), 'chd', 'chd.video_id = m.video_id And chd.channel_id = '.$this->_iChannelId);			
			if (!$bIsCount)
			{
				$this->database()->group('m.video_id');
			}
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
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('videochannel.service_browse__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>
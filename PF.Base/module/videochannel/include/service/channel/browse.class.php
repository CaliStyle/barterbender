<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Service_Channel_Browse extends Phpfox_Service 
{	
	private $_sCategory = null;	
	
	private $_aCallback = false;
	
	private $_sTag = null;
	
	private $_bFull = false;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('channel_channel');
	}
	
	public function query()
	{
		
	}
	
	public function processRows(&$aRows)
	{
		if(!empty($aRows))
		{
			foreach ($aRows as $iKey => $aRow)
			{
				$cId = $aRow['channel_id'];
				$aVideo = Phpfox::getService('videochannel.channel.process')->getVideosBelongChannel($cId, 1);
                if (isset($aVideo[0]))
                {
                    $aRows[$iKey]['video_image'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aVideo[0]['image_server_id'],
                        'path' => 'core.url_pic',
                        'file' => $aVideo[0]['image_path'],
                        'suffix' => '_120',
                        'return_url' => true
                            )
                    );
                }
                else
				{
					$aRows[$iKey]['video_image']= Phpfox::getParam('core.path_file') . 'module/videochannel/static/image/no_item.jpg';
				}
				$aRows[$iKey]['en_title'] = base64_encode($aRows[$iKey]['title']);			
				$aRows[$iKey]['en_summary'] = base64_encode($aRows[$iKey]['summary']);
				$aRows[$iKey]['en_url'] = base64_encode($aRows[$iKey]['url']);
				$aRows[$iKey]['en_video_image'] = base64_encode($aRows[$iKey]['video_image']);
				$aRows[$iKey]['isExist'] = $cId;
				$aRows[$iKey]['isBrowse'] = true;
				$aRows[$iKey]['link'] = ($this->_aCallback !== false ? Phpfox::getLib('url')->makeUrl($this->_aCallback['url'][0], array_merge($this->_aCallback['url'][1], array($aRow['title']))) : Phpfox::permalink('videochannel.channel', $aRow['channel_id'],$aRow['title']));
			}	
		}		
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
		if ($this->_sCategory !== null)
		{		
			$this->database()->innerJoin(Phpfox::getT('channel_category_data'), 'mcd', 'mcd.channel_id = m.channel_id')
						->innerJoin(Phpfox::getT('channel_category'),'mc','mc.category_id = mcd.category_id');
			if (!$bIsCount)
			{
				$this->database()->group('m.channel_id');
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
		if ($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_browse__call'))
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

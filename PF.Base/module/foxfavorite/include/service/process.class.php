<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		YouNetCo Company
 * @author  		MinhNTK
 * @package 		FoxFavorite_Module
 */
class FoxFavorite_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('foxfavorite');
	}
	
	public function add($sTypeId, $iItemId)
	{
		
		if($sTypeId == 'profile')
		{
			$iItemId = phpfox::getService('foxfavorite')->getUserIdFromUserName($iItemId);
						
		}
		$iCount = $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where('type_id = \'' . $this->database()->escape($sTypeId) . '\' AND item_id = ' . (int) $iItemId . ' AND user_id = ' . Phpfox::getUserId())
			->execute('getSlaveField');
	
		if (!$iCount)
		{
			$sModule = $sTypeId;
			if (strpos($sModule, '_'))
			{
				$aParts = explode('_', $sModule);
				$sModule = $aParts[0];
			}

			if (!Phpfox::isModule($sModule))
			{
				return Phpfox_Error::set(_p('favorite.not_a_valid_module'));
			}
			
			$sNotification = '';
			switch($sModule)
			{
				case 'profile':
					$aItem = phpfox::getLib('database')->select('full_name as title, user_id')
					->from(phpfox::getT('user'))
					->where('user_id = '.$iItemId)
					->execute('getRow');
					$sNotification = 'foxfavorite_favorprofile';
				break;
				case 'pages':
					$sItemId = 'page_id';
					$aItem = phpfox::getLib('database')->select('title, user_id')
							->from(phpfox::getT($sModule))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favorpages';
					break;
				case 'marketplace':
					$sItemId = 'listing_id';
					$aItem = phpfox::getLib('database')->select('title, user_id')
							->from(phpfox::getT($sModule))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favormarketplace';
					break;
				case 'poll':
					$sItemId = $sModule.'_id';
					$aItem = phpfox::getLib('database')->select('question as title, user_id')
							->from(phpfox::getT($sModule))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favorpoll';
					break;
				case 'music':
					$sItemId = 'song_id';
					$aItem = phpfox::getLib('database')->select('title, user_id')
							->from(phpfox::getT('music_song'))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favormusic';
					break;
				case 'karaoke':
                    $sKaraokeType = Phpfox::getLib('database')->select('item_type')->from(Phpfox::getT('karaoke_favorite'))->where('favorite_id='.$iItemId)->execute('getField');
					if($sKaraokeType == 'song')
                    {
    					$aItem = phpfox::getLib('database')->select('s.title, s.user_id')
                            ->from(phpfox::getT('karaoke_song'), 's')
    						->join(Phpfox::getT('karaoke_favorite'), 'f', 's.song_id = f.item_id')
    						->where('f.favorite_id = '.$iItemId)
    						->execute('getRow');
    					$sNotification = 'foxfavorite_favorkaraoke';
					}
                    else
                    {
    					$aItem = phpfox::getLib('database')->select('r.title, r.user_id')
                            ->from(phpfox::getT('karaoke_recording'), 'r')
    						->join(Phpfox::getT('karaoke_favorite'), 'f', 'r.recording_id = f.item_id')
    						->where('f.favorite_id = '.$iItemId)
    						->execute('getRow');
    					$sNotification = 'foxfavorite_favorkaraoke';
                    }
                    break;
				case 'videochannel':
					$sItemId = 'video_id';
					$aItem = phpfox::getLib('database')->select('title, user_id')
							->from(phpfox::getT('channel_video'))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favorvideochannel';
					break;
                case 'v':
                    $sItemId = 'video_id';
                    $sTypeId = 'video';
                    $aItem = phpfox::getLib('database')->select('title, user_id')
                        ->from(phpfox::getT('video'))
                        ->where($sItemId.' = '.$iItemId)
                        ->execute('getRow');
                    $sNotification = 'foxfavorite_favorvideo';
                    break;
				case 'fevent':
					$sItemId = 'event_id';
					$aItem = phpfox::getLib('database')->select('title, user_id')
							->from(phpfox::getT('fevent'))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favorfevent';
					break;
				case 'advancedmarketplace':
					$sItemId = 'listing_id';
					$aItem = phpfox::getLib('database')->select('title, user_id')
							->from(phpfox::getT('advancedmarketplace'))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favoradvancedmarketplace';
					break;
				case 'advancedphoto':
					$sItemId = 'photo_id';
					$aItem = phpfox::getLib('database')->select('title, user_id')
							->from(phpfox::getT('photo'))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favoradvancedphoto';
					break;
				case 'contest':
					$sItemId = 'contest_id';
					$aItem = phpfox::getLib('database')->select('contest_name as title, user_id')
							->from(Phpfox::getT('contest'))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					break;
				case 'resume':
					$sItemId = 'resume_id';
					$aItem = phpfox::getLib('database')->select('headline as title, user_id')
							->from(phpfox::getT('resume_basicinfo'))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favorresume';
					break;
				case 'jobposting':
					$sItemId = 'job_id';
					$aItem = phpfox::getLib('database')->select('title, user_id')
							->from(phpfox::getT('jobposting_job'))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favorjob';
					break;
				case 'foxfeedspro':
					$sItemId = 'item_id';
					$aItem = phpfox::getLib('database')->select('item_title as title, user_id')
							->from(phpfox::getT('ynnews_items'))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favorfoxfeedspro';
					break;
				case 'musicsharing':
					$sItemId = 'song_id';
					$aItem = phpfox::getLib('database')->select('s.title, a.user_id')
                            ->from(Phpfox::getT('m2bmusic_album_song'), 's')
                            ->join(Phpfox::getT('m2bmusic_album'), 'a', 'a.album_id = s.album_id')
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favormusicsharing';
					break;
				case 'directory':
					$sItemId = 'business_id';
					$aItem = phpfox::getLib('database')->select('s.name as title, s.user_id')
                            ->from(Phpfox::getT('directory_business'), 's')
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favordirectory';
					break;
				case 'auction':
					$sItemId = 'product_id';
					$aItem = phpfox::getLib('database')->select('e.name as title, e.user_id')
                            ->from(Phpfox::getT('ecommerce_product'), 'e')
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favorauction';
					break;
				default: //..., coupon, petition
					$sItemId = $sModule.'_id';
					$aItem = phpfox::getLib('database')->select('title, user_id')
							->from(phpfox::getT($sModule))
							->where($sItemId.' = '.$iItemId)
							->execute('getRow');
					$sNotification = 'foxfavorite_favor'.$sModule;
				break;
			}
					
			$iId = $this->database()->insert($this->_sTable, array(
					'type_id' => $sTypeId,
					'item_id' => (int) $iItemId,
					'user_id' => Phpfox::getUserId(),
					'time_stamp' => PHPFOX_TIME,
					'title'=>$aItem['title']
				)
			);
			
            if($iId)
			{
				if(Phpfox::isModule('feed'))
				{
				    $tempModule = $sModule;
				    switch ($tempModule) {
                        case 'music':
                            $tempModule = 'song';
                            break;
                        default:
                            break;
                    }
					$sFeed = 'foxfavorite_'.$tempModule;
					$bIsDefaultModule = phpfox::getService('foxfavorite')->isDefaultModules($sModule);
					if($bIsDefaultModule)
					{
						$iFeedId = Phpfox::getService('feed.process')->add($sFeed, $iId);
						if($sModule == 'pages')
						{
							$iFeedId = Phpfox::getService('feed.process')->add('pages_favorite', $iId);
						}
					}
					else
					{
						if($sModule != 'videochannel')
                        {
                            $iFeedId = Phpfox::getService('feed.process')->add($sFeed, $iId);
                        }
					}
					
				}
				
                $aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(b.favorite_id) AS total_items')
					->from(Phpfox::getT('user'), 'u')
					->leftJoin(Phpfox::getT('foxfavorite'), 'b', 'b.user_id = u.user_id')
					->where('u.user_id = '.Phpfox::getUserId())
					->execute('getRow');
                
                Phpfox::getLib('database')->update(phpfox::getT('user_field'), array('total_foxfavorite'=>$aRows['total_items']), 'user_id = '.phpfox::getUserId());
				
                $sUserNotification = phpfox::getLib('database')->select('un.user_notification')
					->from(phpfox::getT('user_notification'), 'un')
					->where('un.user_notification = "foxfavorite.user_favorited_my_item" and un.user_id = '.$aItem['user_id'])
					->execute('getField');

				if(empty($sUserNotification) && !Phpfox::getService('foxfavorite')->isFunctionedModule($sModule, 'notify_owner'))
				{
                    Phpfox::getService('notification.process')->add($sNotification, $iId, $aItem['user_id']);
				}
			}
			
			return true;
		}
		
		return Phpfox_Error::set(_p('this_item_is_already_in_your_favorites_list'));
	}
	
	public function delete($iId)
	{
		$aFavorite = phpfox::getLib('database')->select('f.*')
					->from($this->_sTable, 'f')
					->where('favorite_id = '.$iId)
					->execute('getRow');
		if(empty($aFavorite))
		{
			return false;
		}
		$this->database()->delete($this->_sTable, 'favorite_id = ' . (int) $iId . ' AND user_id = ' . Phpfox::getUserId());
		$iCount = phpfox::getService('foxfavorite')->getTotalAvailFavoritesOfUser(phpfox::getUserId());
		
		$this->database()->update(phpfox::getT('user_field'), array('total_foxfavorite' => $iCount), 'user_id = '.phpfox::getUserId());
		$sFeed = 'foxfavorite_'.$aFavorite['type_id'];
		if(phpfox::isModule('feed'))
		{
			Phpfox::getService('feed.process')->delete($sFeed, $iId);
		}
	}
	
	public function UnFavorite($sModule, $iItemId)
	{
		if($sModule == 'profile')
		{
			$iItemId = phpfox::getService('foxfavorite')->getUserIdFromUserName($iItemId);
		}
		$iFavoriteId = phpfox::getLib('database')->select('favorite_id')
			->from(phpfox::getT('foxfavorite'))
			->where('type_id = "'.$sModule.'" and item_id = '.$iItemId.' and user_id ='.phpfox::getUserId())
			->execute('getSlaveField');

		if($iFavoriteId)
		{
			$this->delete($iFavoriteId);
		}
	}
	
	public function migrateFavoriteData()
	{
		if(!phpfox::isModule('favoritepages'))
		{
			phpfox::getLib('url')->send('admincp.foxfavorite.page');	
			return false;
		}
		$iCnt = 0;
		$aPageFavorites = phpfox::getLib('database')->select('f.page_id as item_id, user_id, time_stamp')
						->from(phpfox::getT('favoritepages'), 'f')
						->execute('getRows');
		if(empty($aPageFavorites))
		{
			return;
		}
		$sInsert = 'INSERT INTO '.phpfox::getT('foxfavorite').' (`type_id`, `user_id`, `item_id`, `time_stamp`, `title`) VALUES ';
		foreach($aPageFavorites as $iKey => $aItem)
		{
			$sPageTitle = $this->getExistPageTitle($aItem['item_id'], $aItem['user_id']);
			if($sPageTitle == false)
			{
				continue;
			}
			$iCount++;
			$sInsert .= "('pages', '".$aItem['user_id']."', '".$aItem['item_id']."', '".$aItem['time_stamp']."', '".$sPageTitle."'),";
		}
		if($iCount == 0)
		{
			phpfox::getLib('url')->send('admincp.foxfavorite.page', null, _p('foxfavorite.migration_succeed'));
		}
		$sInsert = substr($sInsert, 0, strlen($sInsert) - 1);
		$this->database()->query($sInsert);
		$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user'))
				->execute('getSlaveField');		
		$aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(b.favorite_id) AS total_items')
			->from(Phpfox::getT('user'), 'u')
			->leftJoin(Phpfox::getT('foxfavorite'), 'b', 'b.user_id = u.user_id')
			->group('u.user_id')
			->execute('getSlaveRows');		
			//die(d($aRows));	
		foreach ($aRows as $aRow)
		{
			$this->database()->update(Phpfox::getT('user_field'), array('total_foxfavorite' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
		}
		phpfox::getLib('url')->send('admincp.foxfavorite.page', null, _p('foxfavorite.migration_succeed'));
	}
	
	public function getExistPageTitle($iItemId, $iUserId)
	{
		$aPage = phpfox::getLib('database')->select('f.*')
				->from(phpfox::getT('foxfavorite'), 'f')
				->where('f.type_id = "pages" and f.item_id = '.$iItemId.' and f.user_id = '.$iUserId)
				->execute('getRow');
		if(!empty($aPage))
		{
			return false;
		}
		
		$sTitle = phpfox::getLib('database')->select('p.title')
				->from(phpfox::getT('pages'), 'p')
				->where('p.page_id = '.$iItemId)
				->execute('getField');
		return $sTitle;
	}
	public function updateSetting($sModule, $iActive)
	{
	    if ($sModule == 'v') {
	        $sModule = 'video';
        }
		phpfox::getLib('database')->update(phpfox::getT('foxfavorite_setting'), array('is_active'=>$iActive), 'module_id = "'.$sModule.'"');
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
		if ($sPlugin = Phpfox_Plugin::get('foxfavorite.service_process__call'))
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
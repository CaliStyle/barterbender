<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'videochannel' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'libs.class.php');



class Videochannel_Service_Videochannel extends Younet_Service
{
    /**
     *
     * @var Videochannel_Service_Category_Category 
     */
    public $oVideoChannelCategory;
    
	private $_aExt = array(
		'mpg' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'wmv' => 'video/x-ms-wmv',
		'avi' => 'video/avi',
		'mov' => 'video/quicktime',
		'flv' => 'video/x-flv'
		// 'mp4' => 'video/mp4',
		// '3gp' => 'video/3gpp'
	);

	private $_aCallback = false;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('channel_video');
        
        $this->oVideoChannelCategory = Phpfox::getService('videochannel.category');
	}

	public function callback($aCallback)
	{
		$this->_aCallback = $aCallback;

		return $this;
	}
                
    public function isFavourite($iItemId)
    {

        $aRow = $this->database()
                ->select("*")
                ->from(Phpfox::getT('favorite'))
                ->where("type_id = 'videochannel'"
                    . " AND item_id = " . $iItemId . " AND user_id = " . Phpfox::getUserId())
                ->execute('getRows');

        if (count($aRow) > 0)
        {
            return true;
        }
        return false;
    }
        
    public function addToFavorite($sTypeId, $iItemId)
    {

        if (!$this->database()->select('COUNT(*)')
                        ->from(Phpfox::getT('favorite'))
                        ->where('type_id = \'' . $this->database()->escape($sTypeId) . '\' AND item_id = ' . (int) $iItemId . ' AND user_id = ' . Phpfox::getUserId())
                        ->execute('getSlaveField')
        )
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

            if (!Phpfox::callback($sTypeId . '.verifyFavorite', $iItemId))
            {
                return Phpfox_Error::set(_p('favorite.unable_to_add_this_item_as_a_favorite_due_to_privacy'));
            }

            $this->database()->insert(Phpfox::getT('favorite'), array(
                'type_id' => $sTypeId,
                'item_id' => (int) $iItemId,
                'user_id' => Phpfox::getUserId(),
                'time_stamp' => PHPFOX_TIME
                    )
            );

            return true;
        }

        return Phpfox_Error::set(_p('favorite.this_item_is_already_in_your_favorites_list'));
    }
    public function check_480_image_for_slide($aVideo)
    {

         $aSites = array(
                'youtube' => 'YouTube',
                'myspace' => 'MySpace Video',
                'break' => 'Break',
                'metacafe' => 'Metacafe'
        );
         //youtuve only
        $sImageLocation = str_replace('%s', '_480',
            substr(Phpfox::getLib('file')->getBuiltDir(Phpfox::getParam('core.dir_pic')) , 0, -8) . $aVideo['image_path']);
        if(!file_exists($sImageLocation) && $aVideo['is_stream'] == 1)
        {
            $sUrl = $this->database()->select('video_url')
                                     ->from(Phpfox::getT('channel_video_embed'))
                                     ->where('video_id = ' . $aVideo['video_id'])
                                     ->execute('getSlaveRow');
            Phpfox::getService('videochannel.grab')->get($sUrl['video_url']);
            if(Phpfox::getService('videochannel.grab')->image($aVideo['video_id'], 'videochannel', $sImageLocation))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    public function getUserTotalVideos($iUserId)
    {
        $iTotal = $this->database()->select("count(*) total")
                                   ->from(Phpfox::getT('channel_video'))
                                   ->where("user_id = " . $iUserId . " AND module_id = 'videochannel' ")
                                   ->execute('getRows');

        return $iTotal[0]['total'];

    }

	public function getFileExt($bDisplay = false)
	{
		if ($bDisplay === true)
		{
			$sExts = '';
			$iCnt = 0;
			foreach (array_keys($this->_aExt) as $sExt)
			{
				$iCnt++;
				if ($iCnt == count($this->_aExt))
				{
					$sExts .= ' or ';
				}
				elseif ($iCnt != 1)
				{
					$sExts .= ', ';
				}
				$sExts .= strtoupper($sExt);
			}
			return $sExts;
		}
		return array_keys($this->_aExt);
	}
     
    public function getTopViewed($iLimit, $sModule, $iItem)
    {
        $sQuery = ($sModule == null) ? "'videochannel'" : ( "'$sModule'" . 'AND item_id = ' . $iItem);
        $aRows = $this->database()
                ->select('v.*, ' . Phpfox::getUserField() . ', cvt.text')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->leftJoin(Phpfox::getT('channel_video_text'), 'cvt', 'cvt.video_id = v.video_id')
                ->where("v.module_id = " . $sQuery . " AND v.privacy = 0 AND v.view_id = 0")
                ->order('v.total_view DESC')
                ->limit($iLimit)
                ->execute('getRows');

        foreach ($aRows as &$aRow)
        {
            $sText = $aRow['text'];
            if (strlen($sText) > 200)
            {
                $aRow['text'] = substr($sText, 0, 250) . " ...";
            }
          //  $aRow['title'] = (Phpfox::getService('videochannel.libs.Encoding'))toUTF8($aRow['title']);
          //  $aRow['title'] = Encoding::toUTF8($aRow['title']);
        }

        if (!isset($aRows[0]['video_id']))
        {
            $aRows = null;
        }
        return $aRows;
    }
        
    public function getTopRated($iLimit, $sModule = null, $iItem = null)
    {
        $sQuery = ($sModule == null) ? "'videochannel' AND v.item_id = 0" : ( "'$sModule'" . 'AND v.item_id = ' . $iItem);
        $aRows = $this->database()->select('v.*, ' . Phpfox::getUserField() . ', cvt.text')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->leftJoin(Phpfox::getT('channel_video_text'), 'cvt', 'cvt.video_id = v.video_id')
                ->where("v.in_process = 0 AND v.view_id = 0 AND v.module_id = " . $sQuery . " AND v.privacy = 0")
                ->order('v.total_score DESC')
                ->limit($iLimit)
                ->execute('getRows');
        foreach ($aRows as &$aRow)
        {
            $sText = $aRow['text'];
            if (strlen($sText) > 200)
            {
                $aRow['text'] = substr($sText, 0, 250) . " ...";
            }
        }
        if (!isset($aRows[0]['video_id']))
        {
            $aRows = null;
        }
        return $aRows;
    }

    public function getMostDiscussedVideos($iLimit, $sModule = null, $iItem = null)
	{
        $sQuery = ($sModule == null) ? "'videochannel'" : ( "'$sModule'" . 'AND item_id = ' . $iItem);
        $aRows = $this->database()->select('v.*, ' . Phpfox::getUserField() . ', cvt.text')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->leftJoin(Phpfox::getT('channel_video_text'), 'cvt', 'cvt.video_id = v.video_id')
                ->where("v.module_id = " . $sQuery . " AND v.privacy = 0 AND v.view_id = 0")
                ->order('v.total_comment DESC')
                ->limit($iLimit)
                ->execute('getRows');
        foreach($aRows as &$aRow)
        {
            $sText = $aRow['text'];
            if(strlen($sText) > 200)
            {
                $aRow['text'] = substr($sText, 0, 250) . " ...";

            }
        }
        if(!isset($aRows[0]['video_id']))
        {
            $aRows = null;
        }
        return $aRows;
	}
        
    public function getFeaturedVideos($iLimit, $sModule, $iItem)
	{
        $sQuery = (($sModule == null) ? "'videochannel'" : ( "'$sModule'" . 'AND item_id = ' . $iItem));        
        $aRows = $this->database()->select('v.*, ' . Phpfox::getUserField() . ', cvt.text')
            ->from(Phpfox::getT('channel_video'), 'v')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->leftJoin(Phpfox::getT('channel_video_text'), 'cvt', 'cvt.video_id = v.video_id')
            ->where("v.is_featured = 1 AND v.module_id = " . $sQuery  . " AND v.privacy = 0 AND v.view_id = 0")
			->order('RAND()')
            ->limit($iLimit)
            ->execute('getRows');
        foreach($aRows as &$aRow)
        {
            $sText = $aRow['text'];
            if(strlen($sText) > 200)
            {
                $aRow['text'] = substr($sText, 0, 250) . " ...";

            }
        }
        if(!isset($aRows[0]['video_id']))
        {
            $aRows = null;
        }
        return $aRows;
	}
        
    public function getTopRecent($iLimit, $sModule = null, $iItem = null)
	{
        $sQuery = (($sModule == null) ? "'videochannel'" : ( "'$sModule'" . 'AND item_id = ' . $iItem));
        $aRows = $this->database()->select('v.*, ' . Phpfox::getUserField() . ', cvt.text')
                ->from(Phpfox::getT('channel_video'), 'v')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->leftJoin(Phpfox::getT('channel_video_text'), 'cvt', 'cvt.video_id = v.video_id')
                ->where("v.module_id = " . $sQuery  . " AND v.privacy = 0 AND v.view_id = 0")
                ->order('v.time_stamp DESC')
                ->limit($iLimit)
                ->execute('getRows');
        foreach($aRows as &$aRow)
        {
            $sText = $aRow['text'];
            if(strlen($sText) > 200)
            {
                $aRow['text'] = substr($sText, 0, 250) . " ...";
            }
        }
		return $aRows;
	}
	public function getVideo($sVideo, $bUseId = false)
	{
		$bUseId = true;		
        // Add condition of track module.
		if (Phpfox::isModule('track'))
		{
			$this->database()->select("video_track.item_id AS video_is_viewed, ")->leftJoin(Phpfox::getT('channel_video_track'), 'video_track', 'video_track.item_id = v.video_id AND video_track.user_id = ' . Phpfox::getUserBy('user_id'));			
		}
        // Add condition of friend module.
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = v.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}		
        // Add condition of like module.
		if (Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'videochannel\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId());
		}
        // Get video.
		$aVideo = $this->database()->select('v.*, ' . (Phpfox::getParam('core.allow_html') ? 'vt.text_parsed' : 'vt.text') . ' AS text, u.user_name, rate_id AS has_rated, ' . Phpfox::getUserField())
			->from($this->_sTable, 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->leftJoin(Phpfox::getT('channel_video_text'), 'vt', 'vt.video_id = v.video_id')
			->leftJoin(Phpfox::getT('channel_video_rating'), 'vr', 'vr.item_id = v.video_id AND vr.user_id = ' . Phpfox::getUserId())
			->where(($bUseId ? 'v.video_id = ' . (int) $sVideo : 'v.module_id = \'' . ($this->_aCallback !== false ? $this->_aCallback['module'] : 'videochannel') . '\' AND v.item_id = ' . ($this->_aCallback !== false ? (int) $this->_aCallback['item'] : 0) . ' AND v.title_url = \'' . $this->database()->escape($sVideo) . '\''))
			->execute('getSlaveRow');
        // Validate.
        if (!isset($aVideo['video_id']))
		{
			return false;
		}
		if ($aVideo['view_id'] != '0')
		{
			if ($aVideo['view_id'] == '2' && ($aVideo['user_id'] == Phpfox::getUserId() || Phpfox::getUserParam('videochannel.can_approve_videos')))
			{
                // Do nothing.
			}
			else
			{
				return false;
			}
		}        
        // Set extend information.
		$aVideo['breadcrumb'] = $this->oVideoChannelCategory->getCategoriesById($aVideo['video_id'], null, $aVideo['module_id'], $aVideo['item_id'], $this->_aCallback);
		$aVideo['bookmark'] = ($this->_aCallback !== false ? Phpfox::getLib('url')->makeUrl($this->_aCallback['url'][0], array_merge($this->_aCallback['url'][1], array('videochannel', $aVideo['title']))) : Phpfox::permalink('videochannel', $aVideo['video_id'], $aVideo['title']));
		$aVideo['embed'] = '';
        $aVideo['video_url'] = '';
        // Get the embed for video.
		if ($aVideo['is_stream'])
		{
			$aEmbedVideo = $this->database()->select('video_url, embed_code')
				->from(Phpfox::getT('channel_video_embed'))
				->where('video_id = ' . $aVideo['video_id'])
				->execute('getslaveRow');
            $aVideo['video_url'] = $aEmbedVideo['video_url'];
			if (empty($aEmbedVideo['embed_code']))
			{
				if (!Phpfox::getService('videochannel.grab')->get($aEmbedVideo['video_url']))
				{
					return Phpfox_Error::display(_p('videochannel.not_a_valid_video_to_display'));
				}
				$aEmbedVideo['embed_code'] = Phpfox::getService('videochannel.grab')->embed();

				$this->database()->update(Phpfox::getT('channel_video_embed'), array('embed_code' => $aEmbedVideo['embed_code']), 'video_id = ' . $aVideo['video_id']);
			}
			$aVideo['embed_code'] = $aEmbedVideo['embed_code'];
            if (preg_match('/youtube/i', $aEmbedVideo['video_url']) || preg_match('/youtu\.be/i', $aEmbedVideo['video_url']))
            {
                preg_match('/value="http:\/\/(.*?)"/i', $aVideo['embed_code'], $aMatches);
                if (Phpfox::getParam('videochannel.allow_youtube_iframe'))
                {
                    if (isset($aMatches[1]))
                    {
                        $sTempUrl = trim($aMatches[1]);
                        $aUrlFind = array(
                            '&amp;fs=1',
                            '&amp;fs=0',
                            '&fs=1',
                            '&fs=0',
                            '&amp;rel=1',
                            '&amp;rel=0',
                            '&rel=1',
                            '&rel=0',
                            '&amp;autoplay=1',
                            '&amp;autoplay=0',
                            '&autoplay=1',
                            '&autoplay=0'
                        );
                        $sNewTempUrl = str_replace('/v/', '/embed/', str_replace($aUrlFind, '', $sTempUrl)) . (Phpfox::getParam('videochannel.embed_auto_play') ? '?autoplay=1' : '');
                        $aVideo['embed_code'] = '<iframe width="420" height="345" src="http://' . $sNewTempUrl . '" frameborder="0" ' . (Phpfox::getParam('videochannel.full_screen_with_youtube') ? ' allowfullscreen ' : '') . ' ></iframe>';
                    }
                    elseif (preg_match('/src="http:\/\/(.*?)"/i', $aVideo['embed_code'], $aMatches))
                    {
                        $sNewTempUrl = str_replace('/v/', '/embed/', trim($aMatches[1]) . '&wmode=transparent') . (Phpfox::getParam('videochannel.embed_auto_play') ? '?autoplay=1' : '');
                        $aVideo['embed_code'] = '<iframe width="420" height="345" src="http://' . $sNewTempUrl . '" frameborder="0" ' . (Phpfox::getParam('videochannel.full_screen_with_youtube') ? ' allowfullscreen ' : '') . '></iframe>';
                    }
                }
                else
                {
                    if (isset($aMatches[1]))
                    {
                        $sTempUrl = trim($aMatches[1]);
                        $aUrlFind = array(
                            '&amp;fs=1',
                            '&amp;fs=0',
                            '&fs=1',
                            '&fs=0',
                            '&amp;rel=1',
                            '&amp;rel=0',
                            '&rel=1',
                            '&rel=0',
                            '&amp;autoplay=1',
                            '&amp;autoplay=0',
                            '&autoplay=1',
                            '&autoplay=0'
                        );
                        $sNewTempUrl = str_replace($aUrlFind, '', $sTempUrl) . (Phpfox::getParam('videochannel.embed_auto_play') ? '?autoplay=1' : '') . (Phpfox::getParam('videochannel.full_screen_with_youtube') ? '&amp;fs=1' : '');
                        $aVideo['embed_code'] = str_replace($sTempUrl, $sNewTempUrl, $aVideo['embed_code']);
                    }
                    elseif (preg_match('/src="http:\/\/(.*?)"/i', $aVideo['embed_code'], $aMatches))
                    {
                        $sTempUrl = trim($aMatches[1]);
                        $sNewTempUrl = $sTempUrl . '&wmode=transparent' . (Phpfox::getParam('videochannel.embed_auto_play') ? '&autoplay=1' : '') . (Phpfox::getParam('videochannel.full_screen_with_youtube') ? '&fs=1' : '');
                        $aVideo['embed_code'] = str_replace($sTempUrl, $sNewTempUrl, $aVideo['embed_code']);
                    }
                }
            }
			$aBlock1 = Phpfox::getLib('module')->getModuleBlocks(1);
			$aAdBlock1 = Phpfox::isModule('ad') ? Phpfox::getService('ad')->getForBlock(1, false, false) : null;
			if($aBlock1 || $aAdBlock1)
			{
				$aVideo['embed_code'] = preg_replace('/width="([0-9]+)"/', 'width="480"', $aVideo['embed_code']);
				$aVideo['embed_code'] = preg_replace('/height="([0-9]+)"/', 'height="320"', $aVideo['embed_code']);
			}
			else
			{
				$aVideo['embed_code'] = preg_replace('/width=\"(.*?)\"/i', 'width="640"', $aVideo['embed_code']);
				$aVideo['embed_code'] = preg_replace('/height=\"(.*?)\"/i', 'height="390"', $aVideo['embed_code']);
			}
			$aVideo['embed_code'] = preg_replace_callback('/<object(.*?)>(.*?)<\/object>/is', array($this, '_embedWmode'), $aVideo['embed_code']);
			$aVideo['embed'] = htmlspecialchars($aVideo['embed_code']);
		}
        // Check callback.
		if ($this->_aCallback !== false && isset($this->_aCallback['url_home']) && isset($aVideo['breadcrumb']) && is_array($aVideo['breadcrumb']) && count($aVideo['breadcrumb']))
		{
			$sHomeUrl = '/' . $this->_aCallback['url_home'][0] . '/' . implode('/', $this->_aCallback['url_home'][1]) . '/videochannel/';
			foreach ($aVideo['breadcrumb'] as $iKey => $aCategory)
			{
				$aVideo['breadcrumb'][$iKey][1] = preg_replace('/^http:\/\/(.*?)\/videochannel\/(.*?)$/i', 'http://\\1' . $sHomeUrl . '\\2', $aCategory[1]);
			}
		}
        // Get tag list.
		if (Phpfox::isModule('tag'))
		{
			$aTags = Phpfox::getService('tag')->getTagsById('videochannel' . ($aVideo['module_id'] == 'videochannel' ? '' : '_' . $aVideo['module_id']), $aVideo['video_id']);
			if (isset($aTags[$aVideo['video_id']]))
			{
				$aVideo['tag_list'] = $aTags[$aVideo['video_id']];
			}
		}
        // Get the total videos of user.
		$aVideo['total_user_videos'] = $this->database()->select('COUNT(*)')->from($this->_sTable)->where('in_process = 0 AND view_id = 0 AND item_id = 0 AND user_id = ' . (int) $aVideo['user_id'])->execute('getSlaveField');
        // Set default for friend video.
		if (!isset($aVideo['is_friend']))
		{
			$aVideo['is_friend'] = 0;
		}
		// allow to increase views of video everytime it runs
		$this->database()->updateCounter('channel_video', 'total_view', 'video_id', $aVideo['video_id']);
        // Add plugin.
		(($sPlugin = Phpfox_Plugin::get('videochannel.service_video_getvideo')) ? eval($sPlugin) : null);
		return $aVideo;
	}

	public function getForEdit($iId, $bForce = false)
	{
		$this->database()->select('v.*, vt.text, u.user_name')
			->from($this->_sTable, 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->leftJoin(Phpfox::getT('channel_video_text'), 'vt', 'vt.video_id = v.video_id')
    		->where('v.video_id = ' . (int) $iId);
    
		$aVideo = $this->database()->execute('getSlaveRow');

		if (isset($aVideo['video_id']))
		{
			if ((($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_edit_own_video')) || Phpfox::getUserParam('videochannel.can_edit_other_video')) || $bForce === true)
			{
                // Get all categories
				$aVideo['categories'] = $this->oVideoChannelCategory->getCategoryIds($aVideo['video_id']);
				$aVideo['video_url'] = Phpfox::permalink('videochannel', $aVideo['video_id'], $aVideo['title']);

				if (Phpfox::isModule('tag'))
				{
					$aTags = Phpfox::getService('tag')->getTagsById('videochannel' . ($aVideo['module_id'] == 'videochannel' ? '' : '_' . $aVideo['module_id']), $aVideo['video_id']);
					if (isset($aTags[$aVideo['video_id']]))
					{
						$aVideo['tag_list'] = '';
						foreach ($aTags[$aVideo['video_id']] as $aTag)
						{
							$aVideo['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
						}
						$aVideo['tag_list'] = trim(trim($aVideo['tag_list'], ','));
					}
				}

				return $aVideo;
			}
		}

		return Phpfox_Error::display(_p('videochannel.unable_to_find_the_video_you_plan_to_edit'));
	}

	public function getForProfileBlock($iUserId, $iLimit = 6)
	{	  
		$oServiceVideoBrowse = Phpfox::getService('videochannel.browse');

		$oServiceVideoBrowse->condition('m.in_process = 0 AND m.view_id = 0 AND m.user_id = ' . (int) $iUserId)
			->size($iLimit)
			->execute();

		return $oServiceVideoBrowse;
	}

	public function getForParentBlock($sModule, $iItem, &$aVideoParent, $iLimit = 6)
	{
		$oServiceVideoBrowse = Phpfox::getService('videochannel.browse');

		$oServiceVideoBrowse->condition('m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'' . $this->database()->escape($sModule) . '\' AND m.item_id = ' . (int) $iItem)
			->callback($aVideoParent)
			->size($iLimit)
			->execute();

		return $oServiceVideoBrowse;
	}

	public function getNew($iLimit = 3)
	{
		$oServiceVideoBrowse = Phpfox::getService('videochannel.browse');

		$oServiceVideoBrowse->condition('m.in_process = 0 AND m.view_id = 0 AND module_id = "videochannel" ')
			->size($iLimit)
			->order('m.time_stamp DESC')
			->execute();

		return $oServiceVideoBrowse;
	}

	public function verify($sIds, $bUseVideoImage = false)
	{
		$aVideos = $this->database()->select('v.*, ve.embed_code, ' . Phpfox::getUserField())
			->from($this->_sTable, 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->leftJoin(Phpfox::getT('channel_video_embed'), 've', 've.video_id = v.video_id')
			->where('v.video_id IN(' . $sIds . ') AND v.user_id = ' . Phpfox::getUserId())
			->execute('getSlaveRows');

		$aCache = array();
		foreach ($aVideos as $aVideo)
		{
			(($sPlugin = Phpfox_Plugin::get('videochannel.service_video_verify')) ? eval($sPlugin) : null);

			if ($bUseVideoImage === true)
			{
				$sImage = Phpfox::getLib('image.helper')->display(array(
							'server_id' => $aVideo['image_server_id'],
							'path' => 'core.url_pic',
							'file' => $aVideo['image_path'],
							'suffix' => '_120',
							'max_width' => 120,
							'max_height' => 120
					)
				);

				$aCache[$aVideo['video_id']] = '<a href="' . Phpfox::getLib('url')->makeUrl($aVideo['user_name'], $aVideo['title_url']) . '" title="' . Phpfox::getLib('parse.output')->clean($aVideo['title']) . '">' . $sImage . '</a>';
			}
			else
			{
				$aCache[$aVideo['video_id']] = $aVideo['embed_code'];
			}
		}

		return $aCache;
	}

	public function requirementCheck(&$aVals)
	{		
		if (!isset($aVals['ffmpeg_path']) || (isset($aVals['ffmpeg_path']) && empty($aVals['ffmpeg_path'])) ||
		    !isset($aVals['mencoder_path']) || (isset($aVals['mencoder_path']) && empty($aVals['mencoder_path'])) 		    	)
		{
			return false;
		}
		$aOutput= '';
		exec($aVals['ffmpeg_path'] . ' 2>&1', $aOutput);
	
		$bPass = false;
		foreach ($aOutput as $sOutput)
		{
			if (preg_match("/ffmpeg version/i", $sOutput))
			{
				$bPass = true;
				break;
			}
		}
		
		if (!$bPass)
		{
			return Phpfox_Error::set(implode('<br />', $aOutput));	
		}

		exec($aVals['mencoder_path'] . ' 2>&1', $aOutput);		
		
		foreach ($aOutput as $sOutput)
		{
			if (preg_match("/mplayer Team/i", $sOutput))
			{
				$bPass = true;
				break;
			}
		}		

		if (!$bPass)
		{
			return Phpfox_Error::set(implde('<br />', $aOutput));	
		}

		return true;
	}

	public function getSpotlight()
	{
		$sCacheId = $this->cache()->set('videochannel_spotlight');

		// if (!($aVideos = $this->cache()->get($sCacheId)))
		{
			$aVideos = $this->database()->select('v.*, ve.embed_code, ' . Phpfox::getUserField())
				->from($this->_sTable, 'v')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
				->leftJoin(Phpfox::getT('channel_video_embed'), 've', 've.video_id = v.video_id')
				->where('v.in_process = 0 AND v.view_id = 0 AND v.is_spotlight = 1')
				->execute('getRows');

			foreach ($aVideos as $iKey => $aVideo)
			{
				if ($aVideo['is_stream'])
				{
					$aVideo['embed_code'] = preg_replace('/width="(.*?)"/i', 'class="video_embed"', $aVideo['embed_code']);
					$aVideo['embed_code'] = preg_replace('/height="(.*?)"/i', '', $aVideo['embed_code']);
					$aVideos[$iKey]['link'] = Phpfox::permalink('videochannel', $aVideo['video_id'], $aVideo['title']);

					$aVideos[$iKey]['embed_code'] = $aVideo['embed_code'];
				}
			}

			$this->cache()->save($sCacheId, $aVideos);
		}

		return (isset($aVideos[rand(0, (count($aVideos) - 1))]) ? $aVideos[rand(0, (count($aVideos) - 1))] : false);
	}
	
	public function getRandomSponsored()
	{
		$sCacheId = $this->cache()->set('videochannel_sponsored');
		if (!($aVideos = $this->cache()->get($sCacheId)))
		{
			// what to do with total_view?
			$aVideos = $this->database()->select('s.*, v.*, ve.embed_code, ' . Phpfox::getUserField())
				->from($this->_sTable, 'v')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
				->join(Phpfox::getT('ad_sponsor'),'s','s.item_id = v.video_id')
				->leftJoin(Phpfox::getT('channel_video_embed'), 've', 've.video_id = v.video_id')
				->where('v.in_process = 0 AND v.view_id = 0 AND v.is_sponsor = 1 AND s.module_id = "videochannel"')
				->execute('getRows');

			foreach ($aVideos as $iKey => $aVideo)
			{
				if ($aVideo['is_stream'])
				{
					$aVideo['embed_code'] = preg_replace('/width="(.*?)"/i', 'width="248"', $aVideo['embed_code']);
					$aVideo['embed_code'] = preg_replace('/height="(.*?)"/i', 'width="183"', $aVideo['embed_code']);

					$aVideos[$iKey]['embed_code'] = $aVideo['embed_code'];
				}
			}

			$this->cache()->save($sCacheId, $aVideos);
		}
		
		$aVideos = Phpfox::getService('ad')->filterSponsor($aVideos);
				
		return (isset($aVideos[rand(0, (count($aVideos) - 1))]) ? $aVideos[rand(0, (count($aVideos) - 1))] : false);

	}

	public function getUserVideos($iUserId)
	{
		$this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.item_id = 0 AND m.privacy IN(%PRIVACY%) AND m.user_id = ' . (int) $iUserId);
		$this->search()->set(array('prepare' => false, 'type' => 'channel_video', 'search_tool' => array('show' => array(Phpfox::getParam('videochannel.total_my_videos')), 'sort' => array('latest' => array('m.time_stamp', 'Latest')))));
		
		$aBrowseParams = array(
			'module_id' => 'videochannel',
			'alias' => 'm',
			'field' => 'video_id',
			'table' => Phpfox::getT('channel_video'),
			'hide_view' => array('pending', 'my')				
		);	

		$this->search()->browse()->params($aBrowseParams)->execute();	
		
		return array($this->search()->browse()->getCount(), $this->search()->browse()->getRows());
	}
	
	public function getFeatured()
	{
		static $aFeatured = null;
		static $iTotal = null;
		
		if ($aFeatured !== null)
		{
			return array($iTotal, $aFeatured);
		}
		
		$aFeatured = array();
		$sCacheId = $this->cache()->set('videochannel_featured');		
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('v.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('channel_video'), 'v')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
				->where("v.is_featured = 1 And v.privacy = 0 AND v.module_id = 'videochannel'")			
				->execute('getSlaveRows');
			
			$this->cache()->save($sCacheId, $aRows);
		}
		
		$iTotal = 0;
		if (is_array($aRows) && count($aRows))
		{
			$iTotal = count($aRows);
			shuffle($aRows);
			foreach ($aRows as $iKey => $aRow)
			{
				if ($iKey === 4)
				{
					break;
				}
				
				$aFeatured[] = $aRow;
			}
		}
		
		return array($iTotal, $aFeatured);
	}
    
    public function getRelatedVideosSuggestions($iVideoId, $sTitle, $bFindSuggestions = false, $bProcess = false, $iCategory = 0)
    {
        // Get video.
        $aVideo = $this->getVideoSimple($iVideoId);
        
        if (count($aVideo) == 0)
        {
            return array(0, array());
        }
        
        // Get parent module.
        $sModuleId = $aVideo['module_id'];
        // Get page id.
        $iPageId = $aVideo['item_id'];
        
        $strExtend = '';

        // Category condition.
        if ($iCategory > 0)
        {
            $strExtend = ' AND mcd.category_id = ' . (int) $iCategory;
            Phpfox::getService('videochannel.browse')->category($iCategory);
        }
        
        // Parent Module condition.
        if ($iPageId > 0 && $sModuleId == 'pages')
        {
            $strExtend .= ' AND m.module_id LIKE "' . $sModuleId . '"';
            
            $strExtend .= ' AND m.item_id = ' . (int) $iPageId;
        }
        else
        {
            $strExtend .= ' AND m.module_id LIKE "videochannel"';
            
            $strExtend .= ' AND m.item_id = 0';
        }
        
        $this->search()->setCondition($strExtend . ' AND m.in_process = 0 AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) ' . ($bProcess ? '' : 'AND ' . $this->searchKeywords('m.title', $sTitle)));

        $arSet = array(
            'prepare' => false,
            'type' => 'videochannel',
            'filters' => [
                'display' => [
                    'default' => Phpfox::getParam('videochannel.total_related_videos',10)
                ],
				'sort' => [
					'default' => 'm.time_stamp'
				],
				'sort_by' => [
					'default' => 'DESC'
				]
            ]
        );

        $this->search()->set($arSet)->setCondition('AND m.video_id != ' . $iVideoId);

        $aBrowseParams = array(
            'module_id' => 'videochannel',
            'alias' => 'm',
            'field' => 'video_id',
            'table' => Phpfox::getT('channel_video'),
            'hide_view' => array('pending', 'my'),
        );
        
        $this->search()->browse()->params($aBrowseParams)->execute();

        $aRows = $this->search()->browse()->getRows();
        
        if (!count($aRows) && $bFindSuggestions)
        {
            $this->search()->clear();
            return $this->getRelatedVideosSuggestions($iVideoId, $sTitle, false, true, $iCategory);
        }
        return array($this->search()->browse()->getCount(), $aRows);
    }

    /**
	 * Build search params for keywords.
	 * 
	 * @param string $sField Field to search
	 * @param string $sStr Keywords to use
	 * @return string Returns an SQL ready search statement
	 */
	public function searchKeywords($sField, $sStr)
	{
		if (is_array($sField))
		{
			$sQuery = '';
			$iIteration = 0;
			foreach ($sField as $sNewField)
			{
				$iIteration++;
				if ($iIteration != 1)
				{
					$sQuery .= ' OR ';
				}
				$sQuery .= $this->searchKeywords($sNewField, $sStr);
			}
			
			return $sQuery;
		}
		
        $sStr = str_replace('.', ' ', $sStr);
        $sStr = str_replace(',', ' ', $sStr);
        $sStr = str_replace(';', ' ', $sStr);
        $sStr = str_replace(':', ' ', $sStr);
        $sStr = str_replace('+', ' ', $sStr);
        $sStr = str_replace('=', ' ', $sStr);
        $sStr = str_replace('\\', ' ', $sStr);
        $sStr = str_replace('|', ' ', $sStr);
        
		$aWords = explode(' ', $sStr);
		
		$sQuery = ' (';
		if (count($aWords))
		{
			$iIteration = 0;
            
			foreach ($aWords as $sWord)
			{
				$sWord = strtolower(trim($sWord));
                
				if (strlen($sWord) < 3)
				{
					continue;
				}
				
                $iIteration++;
				if ($iIteration != 1)
				{
					$sQuery .= ' OR ';
				}
				$sQuery .= 'LOWER(' . $sField . ') LIKE \'%' . Phpfox::getLib('database')->escape($sWord) . '%\'';
			}
		}
		
		if (!$iIteration)
		{
			return 'LOWER(' . $sField . ') LIKE \'%' . Phpfox::getLib('database')->escape($sStr) . '%\' ';
		}
		
		$sQuery .= ') ';
		
		return $sQuery;
	}
    
	public function getRelatedVideos($iVideoId, $sTitle, $iPage = 0, $bFindSuggestions = false, $bProcess = false)
	{
		Phpfox::getLib('request')->set('page', $iPage);
				
		$oServiceVideoBrowse = Phpfox::getService('videochannel.browse');
		
		$this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.item_id = 0 AND m.privacy IN(%PRIVACY%) ' . ($bProcess ? '' : 'AND ' . $this->database()->searchKeywords('m.title', $sTitle)));
        
        $arSet = array(
            'prepare' => false, 
            'type' => 'videochannel', 
            'search_tool' => array(
                'show' => array(Phpfox::getParam('videochannel.total_related_videos')), 
                'sort' => array(
                    'latest' => array('m.time_stamp', 'Latest')
                    )
                )
            );
        
        $this->search()->set($arSet)->setCondition('AND m.video_id != ' . $iVideoId);
		
		$aBrowseParams = array(
			'module_id' => 'videochannel',
			'alias' => 'm',
			'field' => 'video_id',
			'table' => Phpfox::getT('channel_video'),
			'hide_view' => array('pending', 'my')				
		);	

		$this->search()->browse()->params($aBrowseParams)->execute();	
			
		$aRows = $this->search()->browse()->getRows();
		
		if (!count($aRows) && $bFindSuggestions)
		{			
			$this->search()->clear();
			
			return $this->getRelatedVideos($iVideoId, $sTitle, 0, false, true);	
		}
			
		return array($this->search()->browse()->getCount(), $aRows);
	}

	public function getPendingTotal()
	{
		return $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where('view_id = 2')
			->execute('getSlaveField');
	}
	
	public function getForRssFeed()
	{
		$aConditions = array();
		$aConditions[] = "v.in_process = 0 AND v.view_id = 0 AND v.module_id = 'videochannel' AND v.item_id = 0";
		$aRows = $this->database()->select('u.user_name, u.full_name, vt.text_parsed as text, v.title, v.video_id, v.time_stamp')
			->from(Phpfox::getT('channel_video'),'v')
			->join(Phpfox::getT('user'),'u', 'u.user_id = v.user_id')
			->leftJoin(Phpfox::getT('channel_video_text'),'vt','vt.video_id = v.video_id')
			->where($aConditions)
			->limit(Phpfox::getParam('rss.total_rss_display'))
			->order('v.time_stamp DESC')
			->execute('getSlaveRows');		   

		foreach ($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['link'] = Phpfox::permalink('videochannel', $aRow['video_id'], $aRow['title']);
			$aRows[$iKey]['creator'] = $aRow['full_name'];
			$aRows[$iKey]['description'] = (isset($aRow['text']) ? $aRow['text'] : '');
		}		
		
		return $aRows;
	}
        
        public function isChannelOwner($iId)
        {
            $aRow = $this->database()->select('*')
                                     ->from(Phpfox::getT('channel_channel'))
                                     ->where('user_id = ' . Phpfox::getUserId() . ' AND channel_id = ' . $iId)
                                     ->execute('getRows');
            
            if(count($aRow) > 0)
            {
                return true;
            }
            
            return false;
        }
        
        public function getCanAddChannel($sModule = 'videochannel', $iItem = 0)
        {
            if($sModule)
            {
                if($sModule == 'videochannel')
                {
                    return Phpfox::getUserParam('videochannel.can_add_channels', true);
                }
                
                else if ($sModule == 'pages') 
                {
                    if((($this->isPageOwner($iItem) || (Phpfox::getUserBy('profile_page_id') == $iItem)) && Phpfox::getUserParam('videochannel.can_add_channel_on_page', true)) || Phpfox::isAdmin())
                    {
                        return true;
                    }
                    else
                    {
                        $this->url()->send('videochannel');
                    }
                }
            }
            
            return Phpfox::getUserParam('videochannel.can_add_channels', true);
            
        }
        
        public function getCanAddChannelInPage($sModule, $iItem, $aVals, $sPerm)
        {
            if($sModule && $iItem)
            {
                if($sModule == 'pages')
                {
                    return Phpfox::getService('pages')->hasPerm($iItem, $sPerm);
                }
            } 
            else if($aVals) 
            {
                if(isset($aVals['callback_module']))
                {
                    if($aVals['callback_module'] == 'pages')
                         return Phpfox::getService('pages')->hasPerm($aVals['callback_item_id'], $sPerm);
                }
            } 

            
            return false;
        }
        
        public function getIsInPageModule($sModule, $iItem, $aVals)
        {
            $aResult = array();
            if($sModule && $iItem)
            {
                if($sModule == 'pages')
                {
                    $aResult['module_id'] = $sModule;
                    $aResult['item_id'] = $iItem;
                }
            } 
            else if($aVals) 
            {
                if(isset($aVals['callback_module']))
                {
                    if($aVals['callback_module'] == 'pages')
                    {
                        $aResult['module_id'] = $aVals['callback_module'];
                        $aResult['item_id'] = $aVals['callback_item_id'];
                    }
                }
            } 

            
            return $aResult;
        }

        public function getIsInPage($sModule, $iItem, $aVals)
        {
            if($sModule && $iItem)
            {
                if($sModule == 'pages')
                {
                    return true;
                }
            } 
            else if($aVals) 
            {
                if(isset($aVals['callback_module']))
                {
                    if($aVals['callback_module'] == 'pages')
                         return true;
                }
            } 

            
            return false;
        }
        
        public function isPageOwner($iId)
        {
            $aPage = Phpfox::getService('pages')->getPage($iId);
            if($aPage['user_id'] == Phpfox::getUserId())
            {
                return true;
            }
            
            return false;
        }
        
        public function getVideoSimple($iId)
        {
            $aRow = $this->database()->select('*')
                                     ->from(Phpfox::getT('channel_video'))
                                     ->where('video_id = ' . (int) $iId)
                                     ->execute('getRow');
            
            return $aRow;
        }
        
        public function getUserTotalFavourite($iUserId)
        {
            $iTotal = $this->database()->select("count(*) total")
                                       ->from(Phpfox::getT('favorite'))
                                       ->where("user_id = " . $iUserId . " AND type_id ='videochannel'")
                                       ->execute('getRows');
            
            return $iTotal[0]['total'];
        }
        
        public function getChannelInfo($iId)
        {
             $aRow = $this->database()->select('*')
                                     ->from(Phpfox::getT('channel_channel'))
                                     ->where('channel_id = ' . $iId)
                                     ->execute('getRows');
            
            return $aRow[0];
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
		if ($sPlugin = Phpfox_Plugin::get('videochannel.service_video__call'))
		{
			return eval($sPlugin);
		}

		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
	
    private function _embedWmode($aMatches)
    {
    	return '<object ' . $aMatches[1] . '><param name="wmode" value="transparent"></param>' . str_replace('<embed ', '<embed  wmode="transparent" ', $aMatches[2]) . '</object>';
    }

	public function getFeedVideos($iFeedId) {
		$iFeedId = (int)$iFeedId;
		$feedRows = $this->database()->select('*')->from(Phpfox::getT('videochannel_feed'))->where('feed_id = ' . $iFeedId)->execute('getSlaveRows');
		$aVideos = [];
		foreach($feedRows as $key => $feedRow) {
			$aVideo = $this->getVideo($feedRow['video_id']);
			$aVideo['link'] = Phpfox::permalink('videochannel', $aVideo['video_id'], $aVideo['title']);
            if($key == 0){
				preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $aVideo['video_url'], $matches);
				if(isset($matches[1]))
				{
					$aVideo['video_code'] = $matches[1];
				}
				else{
					$aVideo['video_code'] = '';
				}
			}
			else{
                $aVideo['video_code'] = '';
			}
			if($aVideo)
				$aVideos[] = $aVideo;
		}
		return $aVideos;
	}
    public function uploadVideo($aVals)
    {
        if (Phpfox::isModule('groups')) {
            Phpfox::getService('groups')->setIsInPage();
        }

        return array(
            'module' => $aVals['callback_module'],
            'item_id' => (is_array($aVals) && isset($aVals['callback_item_id']) ? $aVals['callback_item_id'] : (int) $aVals)
        );
    }
}

?>

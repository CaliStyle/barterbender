<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'videochannel' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'libs.class.php');

class Videochannel_Service_Process extends Phpfox_Service 
{
	private $_aCategories = array();
	
    /**
     *
     * @var Videochannel_Service_Grab 
     */
    public $oVideoChannelGrab;
    
    /**
     *
     * @var Feed_Service_Process 
     */
    public $oSerFeedProcess;
    
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('channel_video');	
        
        $this->oVideoChannelGrab = Phpfox::getService('videochannel.grab');
	}

	public function isInChannel($iVideoId)
	{
	  $aChannels = $this->database()->select('chd.*')
						  ->from(Phpfox::getT('channel_channel_data'),'chd')
						  ->where('video_id = ' . (int) $iVideoId)
						  ->execute('getRows');
	  return count($aChannels);
	}
	
        public function unfavouriteVideo($iItemId)
        {

            $this->database()->delete(Phpfox::getT('favorite'), 'item_id = ' . $iItemId . " AND type_id = 'videochannel' AND user_id = ". Phpfox::getUserId());

            return true;
        }
        
	public function addShareVideo($aVals, $bReturnId = false)
	{
		define('PHPFOX_FORCE_IFRAME', true);
		
        // Get the category value to insert.
		if (isset($aVals['category']) && count($aVals['category']))
		{
		    if(empty($aVals['category'][0]))
		    {
				return Phpfox_Error::set(_p('videochannel.provide_a_category_this_video_will_belong_to'));
		    }
		    else
			{
				foreach ($aVals['category'] as $iCategory)
				{		
					if (empty($iCategory))
					{
						continue;
					}
					
					if (!is_numeric($iCategory))
					{
						continue;
					}

					// Make sure that while user choose categories. Admin not deactivate or delete categories.
					$aCategory = Phpfox::getService('videochannel.category')->getForEdit($iCategory);

					if (isset($aCategory['is_active']) && $aCategory['is_active']) {
                        $this->_aCategories[] = $iCategory;
                    } else
						continue;
                }
			}
		}

        if(empty($this->_aCategories))
        {
            return Phpfox_Error::set(_p('videochannel.provide_a_category_this_video_will_belong_to'));
        }

        // Get embed code of the video.
		if (!($sEmbed = $this->oVideoChannelGrab->embed()))
		{
			return Phpfox_Error::set(_p('videochannel.unable_to_embed_this_video_due_to_privacy_settings'));
		}
	
        
        
        // Call plugin.
		if ($sPlugin = Phpfox_Plugin::get('videochannel.service_process_addsharevideo__start'))
		{
			eval($sPlugin);
		}		
		
        // Set the default privacy.
		if (!isset($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		
        // Set the default privacy comment.
		if (!isset($aVals['privacy_comment']))
		{
			$aVals['privacy_comment'] = 0;
		}		
		
        // Check the callback of upload video.
		$sModule = 'videochannel';
		$iItem = 0;	
		$aCallback = null;
		
		if (isset($aVals['callback_module']))
		{
			if(Phpfox::hasCallback($aVals['callback_module'], 'uploadVideo')){
				$aCallback = Phpfox::callback($aVals['callback_module'] . '.uploadVideo', $aVals);
			}
			else{
				$aCallback = Phpfox::getService('videochannel')->uploadVideo($aVals);
			}
			$sModule = $aCallback['module'];
			$iItem = $aCallback['item_id'];			
		}

        
        // Get published time on video.
        $sTimeStamp = $this->oVideoChannelGrab->getTimeStamp();
        
        // Compose data.
		$aSql = array(
			'is_stream' => 1,
			'view_id' => (($sModule == 'videochannel' && Phpfox::getUserParam('videochannel.approve_video_before_display')) ? 2 : 0),
			'module_id' => $sModule,
			'item_id' => (int) $iItem,
			'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
			'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
			'user_id' => Phpfox::getUserId(),				
			'time_stamp' => PHPFOX_TIME,
			'featured_time' => 0
		);		
		
        // Get title of video.
		if ($sTitle = Phpfox::getService('videochannel.grab')->title())
		{
			$bAddedTitle = true;
			$aSql['title'] = $this->preParse()->clean($sTitle, 255);
		}
		
        // Get the duration time.
		if ($sDuration = Phpfox::getService('videochannel.grab')->duration())
		{
			$aSql['duration'] = $sDuration;
		}		
		
        // Insert data to video table.
		$iId = $this->database()->insert($this->_sTable, $aSql);
		
        // If insert failure, return false.
		if (!$iId)
		{
			return false;
		}
		
        // Get update data.
		$aUpdate = array();
		
        // Get image of video.
		if (Phpfox::getService('videochannel.grab')->image($iId, $sModule))
		{
			$sImageLocation = Phpfox::getLib('file')->getBuiltDir(Phpfox::getParam('core.dir_pic')) . md5($iId.'videochannel') . '%s.jpg';
			
			$aUpdate['image_path'] = str_replace(Phpfox::getParam('core.dir_pic'), '', $sImageLocation);
			$aUpdate['image_server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');			
		}
		
        // Set the default title if the title value is not valid.
		if (!isset($bAddedTitle))
		{
			$aUpdate['title'] = $iId;
			$sTitle = $iId;
		}
		
        // Update data for video.
		if (count($aUpdate))
		{
			$this->database()->update($this->_sTable, $aUpdate, 'video_id = ' . $iId);
		}
		
        // Insert embed code for video.
		$this->database()->insert(Phpfox::getT('channel_video_embed'), array(
				'video_id' => $iId,
				'video_url' => $aVals['url'],
				'embed_code' => $sEmbed
			)
		);
		
        // Get the description.
		if (($sDescription = Phpfox::getService('videochannel.grab')->description()))
		{
			$this->database()->insert(Phpfox::getT('channel_video_text'), array(
					'video_id' => $iId,
					'text' => $this->preParse()->clean($sDescription),
					'text_parsed' =>$this->preParse()->prepare($sDescription)
				)
			);
		}		
		
        // Check if it has image.
		if (!Phpfox::getService('videochannel.grab')->hasImage())
		{
			$bReturnId = true;
		}
		
        // Inser the categories if they are available.
		if (isset($this->_aCategories) && count($this->_aCategories))
		{
			foreach ($this->_aCategories as $iCategoryId)
			{
				$this->database()->insert(Phpfox::getT('channel_category_data'), array('video_id' => $iId, 'category_id' => $iCategoryId, 'channel_id' => 0));
			}		
		}
		
        // Get the callback of convert video.
		$aCallback = null;
		if(($sModule == 'pages' || $sModule == 'groups') && Phpfox::hasCallback($sModule, 'getFeedDetails'))
		{
			$aCallback = Phpfox::callback($sModule . '.getFeedDetails', array('item_id' => $iId));
		}
		elseif ($sModule != 'videochannel' && Phpfox::hasCallback($sModule, 'convertVideo'))
		{
			$aCallback = Phpfox::callback($sModule . '.convertVideo', array('item_id' => $iId));
		}
        // Add feed when add video.
		if (Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY') && Phpfox::getUserParam('videochannel.approve_video_before_display') == false)
		{
			$this->oSerFeedProcess = Phpfox::getService('feed.process');
            
            $this->oSerFeedProcess->callback($aCallback)->add('videochannel', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0), ($aCallback === null ? 0 : $aVals['callback_item_id']));
		}
			
		// Update user activity
		Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'videochannel');
		
        // Update privacy for video.
		if ($aVals['privacy'] == '4')
		{
			Phpfox::getService('privacy.process')->add('videochannel', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
		}		
		
        // Update tag for video.
		if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list']))))
		{
			Phpfox::getService('tag.process')->add('videochannel', $iId, Phpfox::getUserId(), $aVals['tag_list']);
		}			

		// Plugin call
		if ($sPlugin = Phpfox_Plugin::get('videochannel.service_process_addsharevideo__end'))
		{
			eval($sPlugin);
		}
		
		return $iId;
	}
	
	public function process($sFileName, $sModule = null, $iItem = null)
	{
		if (empty($sFileName))
		{
			return Phpfox_Error::set(_p('videochannel.select_a_video_to_upload'));
		}
		
		$sExts = implode('|', Phpfox::getService('videochannel')->getFileExt());
		
		if (!preg_match("/^(.*?)\.({$sExts})$/i", $sFileName))
		{
			return Phpfox_Error::set(_p('videochannel.not_a_valid_file_we_only_allow_sallow', array('sAllow' => implode(', ', Phpfox::getService('videochannel')->getFileExt()))));
		}
		
		if ($sModule !== null && $iItem !== null && Phpfox::hasCallback($sModule, 'uploadVideo'))
		{
			$aCallback = Phpfox::callback($sModule . '.uploadVideo', $iItem);
			
			if ($aCallback !== false)
			{
				$sModule = $aCallback['module'];
				$iItem = $aCallback['item'];
			}
			else 
			{
				$sModule = null;
				$iItem = null;
			}
		}
		else if (defined('PHPFOX_GROUP_VIEW'))
		{
			$sModule = 'group';
		}
		$iId = $this->database()->insert($this->_sTable, array(
				'view_id' => 1,
				'module_id' => ($sModule !== null ? $sModule : 'videochannel'),
				'item_id' => ($iItem !== null ? (int) $iItem : 0),
				'privacy' => 0,				
				'user_id' => Phpfox::getUserId(),				
				'time_stamp' => PHPFOX_TIME
			)
		);
		
		if (!$iId)
		{
			return false;
		}
		
		return $iId;
	}
	
	public function add($aVals, $aVideo = null)
	{
		if ($aVideo === null)
		{
			if (!isset($_FILES['video']))
			{
				return Phpfox_Error::set(_p('videochannel.select_a_video'));
			}
            // Load the video file upload.
			$aUploadVideo = Phpfox::getLib('file')->load('video', Phpfox::getService('videochannel')->getFileExt(), Phpfox::getUserParam('videochannel.video_file_size_limit'));
			if ($aUploadVideo === false)
			{
				return false;
			}
		}
		if (!empty($aVals['video_title']))
		{
			$aVals['title'] = $aVals['video_title'];
		}
        // Check callback.
		$aCallback = null;
		if (isset($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'uploadVideo'))
		{
			$aCallback = Phpfox::callback($aVals['callback_module'] . '.uploadVideo', $aVals);	
		}
		$aSql = array(
			'user_id' => Phpfox::getUserId(),
			'parent_user_id' => (isset($aVals['parent_user_id']) ? (int) $aVals['parent_user_id'] : '0'),
			'in_process' => 1,
			'view_id' => 0,
			'item_id' => ($aCallback === null ? (isset($aVals['parent_user_id']) ? (int) $aVals['parent_user_id'] : '0') : $aCallback['item_id']),
			'module_id' => ($aCallback === null ? 'videochannel' : $aCallback['module']),
			'title' => (empty($aVals['title']) ? null : $this->preParse()->clean($aVals['title'], 255)),
			'privacy' => (int) (isset($aVals['privacy']) ? (int) $aVals['privacy'] : 0),
			'privacy_comment' => (int) (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0),
			'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
			'file_ext' => $aVideo['ext'],
			'time_stamp' => PHPFOX_TIME
		);
		if (empty($aVals['title']))
		{
			$aSql['title'] = preg_replace("/^(.*?)\.(.*?)$/i", "\\1", $aVideo['name']);
		}
		$iId = $this->database()->insert($this->_sTable, $aSql);
		if ($aVideo === null)
		{		
			$sFileName = Phpfox::getLib('file')->upload('video', Phpfox::getParam('core.dir_pic'), $iId, true, 0644, true, false);
		}
		if (!empty($aVals['status_info']))
		{
			$aVals['text'] = $aVals['status_info'];
		}
		if ($aVideo === null)
		{
			$this->database()->update($this->_sTable, array('destination' => $sFileName, 'file_ext' => $aUploadVideo['ext']), 'video_id = ' . (int) $iId);
		}
		$this->database()->insert(Phpfox::getT('channel_video_text'), array(
				'video_id' => $iId,
				'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
				'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))				
			)
		);
		if (isset($aVals['privacy']) && $aVals['privacy'] == '4')
		{
			Phpfox::getService('privacy.process')->add('videochannel', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));			
		}
        if (isset($aVals['category']) && count($aVals['category']))
		{
			foreach ($aVals['category'] as $iCategory)
			{		
				if (empty($iCategory))
				{
					continue;
				}
				if (!is_numeric($iCategory))
				{
					continue;
				}			
				$this->_aCategories[] = $iCategory;
			}
			foreach ($this->_aCategories as $iCategoryId)
			{
				$this->database()->insert(Phpfox::getT('channel_category_data'), array('video_id' => $iId, 'category_id' => $iCategoryId, 'channel_id' => 0));
        	}
        }

		// Add tags for the photo
		if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['text']))
		{
			Phpfox::getService('tag.process')->add('videochannel', $iId, Phpfox::getUserId(), $aVals['text'], true);
		}
		else
		{
			if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list']))))
			{
				Phpfox::getService('tag.process')->add('videochannel', $iId, Phpfox::getUserId(), $aVals['tag_list']);
			}		
		}

		// plugin call
		if ($sPlugin = Phpfox_Plugin::get('videochannel.service_process_add__end'))
		{
			eval($sPlugin);
		}
		return $iId;
	}
	/**
     * @see Phpfox_Database_Driver_Mysql
     * @param type $iId
     * @param type $aVals
     * @return boolean
     */
	public function update($iId, $aVals)
	{		
		if (isset($aVals['category']) && count($aVals['category']))
		{
		    if(empty($aVals['category'][0]))
		    {
				return Phpfox_Error::set(_p('videochannel.provide_a_category_this_video_will_belong_to'));
		    }
		    else{
				foreach ($aVals['category'] as $iCategory)
				{		
					if (empty($iCategory))
					{
						continue;
					}
					
					if (!is_numeric($iCategory))
					{
						continue;
					}			
					
					$this->_aCategories[] = $iCategory;
				}
		    }
		}		
		// Get the update video.
		$aVideo = $this->database()->select('v.video_id, v.privacy, v.privacy_comment, v.view_id, v.is_viewed, v.user_id, vt.video_id AS text_id, v.module_id')
			->from($this->_sTable, 'v')
			->leftJoin(Phpfox::getT('channel_video_text'), 'vt', 'vt.video_id = v.video_id')
			->where('v.video_id = ' . (int) $iId)
			->execute('getRow');
		if (!isset($aVideo['video_id']))
		{
			return Phpfox_Error::set(_p('videochannel.unable_to_find_the_video_you_plan_to_edit'));
		}
		// Check banned.
		Phpfox::getService('ban')->checkAutomaticBan(isset($aVals['title']) ? $aVals['title'] : '' . isset($aVals['text']) ? $aVals['text'] : '');
		// Check permission.
		if (($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_edit_own_video')) || Phpfox::getUserParam('videochannel.can_edit_other_video'))
		{
            // Check title.
			if (!isset($aVals['title']) || Phpfox::getLib('parse.format')->isEmpty($aVals['title']))
			{
				return Phpfox_Error::set(_p('videochannel.provide_a_title_for_this_video'));
			}		
			// Check title.
			$aSql = array(
				'title' => $this->preParse()->clean($aVals['title'], 255)		
			);
			// Check privacy.
			if (isset($aVals['privacy']))
			{
				$aSql['privacy'] = (int) $aVals['privacy'];
			}
			else
			{
				$aVals['privacy'] = $aVideo['privacy'];
			}
            // Update view.
			if ($aVideo['is_viewed'] == '0')
			{
				$aSql['is_viewed'] = '1';				
			}

			// Update image.
			$aVideoImage = $this->database()->select('image_path, image_server_id')
				->from($this->_sTable)
				->where('video_id = '.$iId)
				->execute('getSlaveRow');

            if (!empty($aVals['temp_file'])) {
                $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
                if (!empty($aFile)) {
                    $aVideoImage['image_path'] = $aFile['path'];
                    $aVideoImage['image_server_id'] = $aFile['server_id'];
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                    $this->database()->update($this->_sTable,array('image_server_id' => $aVideoImage['image_server_id'], 'image_path'=> $aVideoImage['image_path']),'video_id='.$iId);
                }
            }

            // Update data for video.
			$this->database()->update($this->_sTable, $aSql, 'video_id = ' . $aVideo['video_id']);
            // Update text video.
			$aVideoText = $this->database()->select('vt.*')
                    ->from(Phpfox::getT('channel_video_text'), 'vt')
                    ->where('vt.video_id = ' . (int) $aVideo['video_id'])
                    ->execute('getRow');
            if ($aVideoText)
            {
                $this->database()->update(Phpfox::getT('channel_video_text'), array(
                        'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
                        'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))
                    ), 'video_id = ' . $aVideo['video_id']
                );
            }
            else
            {
                // If the video text does not exist, insert the new one.
                $this->database()->insert(Phpfox::getT('channel_video_text'), array(
                    'video_id ' => $aVideo['video_id'],
                    'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
                    'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))
                    )
                );
            }	
            // Delete old categories of video.

			//if(!$aVals['is_instant_edit'])
			if (!isset($aVals['is_instant_edit']))
			{
				$this->database()->delete(Phpfox::getT('channel_category_data'), 'video_id = ' . (int) $iId);			
			}

            // Update categories.
			if (isset($this->_aCategories) && count($this->_aCategories))
			{				
				$this->database()->delete(Phpfox::getT('channel_channel_data'), 'video_id = ' . (int) $iId);
				
				foreach ($this->_aCategories as $iCategoryId)
				{
					$this->database()->insert(Phpfox::getT('channel_category_data'), array('video_id' => $iId, 'category_id' => $iCategoryId, 'channel_id' => 0));
				}		
			}
			// Update feed.
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('videochannel', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0)) : null);

			// Update tags.
			if (Phpfox::isModule('tag') && !defined('PHPFOX_GROUP_VIEW'))
			{
				if (Phpfox::getParam('tag.enable_hashtag_support'))
				{
					Phpfox::getService('tag.process')->update('videochannel' . ($aVideo['module_id'] != 'videochannel' ? '_' . $aVideo['module_id'] : ''), $iId, $aVideo['user_id'], $aVals['text'], true);
				}
				else
				{
					Phpfox::getService('tag.process')->update('videochannel' . ($aVideo['module_id'] != 'videochannel' ? '_' . $aVideo['module_id'] : ''), $iId, $aVideo['user_id'], (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
				}
			}			

			// Update privacy.
			if (Phpfox::isModule('privacy'))
			{
				if ($aVals['privacy'] == '4')
				{
					Phpfox::getService('privacy.process')->update('videochannel', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
				}
				else 
				{
					Phpfox::getService('privacy.process')->delete('videochannel', $iId);
				}			
			}
			return true;
		}
		
		return Phpfox_Error::set(_p('videochannel.invalid_permissions'));
	}
	
	public function isViewed($iId)
	{
		$this->database()->update(Phpfox::getT('channel_video'), array('is_viewed' => '1'), 'video_id = ' . (int) $iId);
	}
	
	public function delete($iId = null, &$aVideo = null)
	{
		if ($aVideo === null)
		{
			$aVideo = $this->database()->select('v.video_id, v.module_id, v.item_id, v.is_featured, v.is_sponsor, v.user_id, v.destination, v.image_path')
				->from($this->_sTable, 'v')
				->where(($iId === null ? 'v.view_id = 1 AND v.user_id = ' . Phpfox::getUserId() : 'v.video_id = ' . (int) $iId))
				->execute('getRow');
				
			if (!isset($aVideo['video_id']))
			{
				return Phpfox_Error::set(_p('videochannel.unable_to_find_the_video_you_plan_to_delete'));
			}
			
			if ($aVideo['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aVideo['item_id']))
			{
				$bOverPass = true;
			}
		}
		else 
		{
			$bOverPass = true;
		}
		
		if (Phpfox::isUser(true) &&
			(($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_delete_own_video')) || Phpfox::getUserParam('videochannel.can_delete_other_video') || isset($bOverPass))
		)
		{		
			$iFileSize = 0;			
						
			if (!empty($aVideo['destination']))
			{
				$sVideo = Phpfox::getParam('core.dir_pic') . sprintf($aVideo['destination'], '');
				if (file_exists($sVideo))
				{
					$iFileSize += filesize($sVideo);
					
					@unlink($sVideo);				
				}
			}
			
			if (!empty($aVideo['image_path']))
			{
				$sImage = Phpfox::getParam('core.dir_pic') . sprintf($aVideo['image_path'], '');
				if (file_exists($sImage))
				{
					$iFileSize += filesize($sImage);
					if ($iFileSize > 0 )
					{
							@unlink($sImage);
					}
				}
			}
			
			if ($iFileSize > 0)
			{
				Phpfox::getService('user.space')->update($aVideo['user_id'], 'video', $iFileSize, '-');	
			}
			
			if (Phpfox::isUser() && Phpfox::isModule('notification'))
			{
				Phpfox::getService('notification.process')->delete('videochannel', $aVideo['video_id'], $aVideo['user_id']);
				Phpfox::getService('notification.process')->delete('videochannel_like', $aVideo['video_id'], $aVideo['user_id']);
			}
			
			(Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem(null, $aVideo['video_id'], 'videochannel') : null);			
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('videochannel', $aVideo['video_id']) : null);
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('videochannel_comment', $aVideo['video_id']) : null);
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('videochannel_like', $aVideo['video_id']) : null);			
									
			(Phpfox::isModule('tag') ? Phpfox::getService('tag.process')->deleteForItem($aVideo['user_id'], $aVideo['video_id'], 'videochannel') : null);
			
			$iChannelId = (int)$this->database()->select('channel_id')
												   ->from(Phpfox::getT('channel_channel_data'))
												   ->where('video_id = ' . (int)$aVideo['video_id'])
												   ->execute('getSlaveField');
												   
			$this->database()->delete(Phpfox::getT('channel_video'), 'video_id = ' . $aVideo['video_id']);
			$this->database()->delete(Phpfox::getT('channel_category_data'), 'video_id = ' . $aVideo['video_id']);
			$this->database()->delete(Phpfox::getT('channel_channel_data'), 'video_id = ' . $aVideo['video_id']);
			$this->database()->delete(Phpfox::getT('channel_video_rating'), 'item_id = ' . $aVideo['video_id']);
			$this->database()->delete(Phpfox::getT('channel_video_text'), 'video_id = ' . $aVideo['video_id']);
				
		      // Update user activity
			Phpfox::getService('user.activity')->update($aVideo['user_id'], 'videochannel', '-');
			
		    $removeVideo = $this->database()->select('*')
					  ->from(Phpfox::getT('channel_video_embed'))
					  ->where('video_id=' . $aVideo['video_id'])
					  ->execute('getRow');
			if(count($removeVideo))
			{								
				if(!(Phpfox::getService('videochannel.channel.process')->isVideoRemoved($removeVideo['video_url'],$iChannelId)))
				{													   
					$this->database()->insert(Phpfox::getT('channel_video_remove'), array(
							    'video_url' => $removeVideo['video_url'],
							    'embed_code' => $removeVideo['embed_code'],
								'channel_id' => $iChannelId
							    ));    	
				}		      	
			}
		      
			
			$this->database()->delete(Phpfox::getT('channel_video_embed'), 'video_id = ' . $aVideo['video_id']);
			
			
			if (isset($aVideo['is_featured']) && $aVideo['is_featured'] == 1)
			{				
				$this->cache()->remove('videochannel_featured');
			}
						
			if (isset($aVideo['is_sponsor']) && $aVideo['is_sponsor'] == 1)
			{
				$this->cache()->remove('videochannel_sponsored');
			}
			return true;
		}
		
		return Phpfox_Error::set(_p('videochannel.invalid_permissions'));
	}
	
	public function deleteImage($iId)
	{
		$aVideo = $this->database()->select('v.video_id, v.user_id, v.destination, v.image_path')
			->from($this->_sTable, 'v')
			->where('v.video_id = ' . (int) $iId)
			->execute('getRow');
			
		if (!isset($aVideo['video_id']))
		{
			return Phpfox_Error::set(_p('videochannel.unable_to_find_the_video_image_you_plan_to_delete'));
		}
		
		if (($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_delete_own_video')) || Phpfox::getUserParam('videochannel.can_delete_other_video'))
		{		
			$iFileSize = 0;			
			if (!empty($aVideo['image_path']))
			{
				$sImage = Phpfox::getParam('core.dir_pic') . sprintf($aVideo['image_path'], '');
				if (file_exists($sImage))
				{
					$iFileSize += filesize($sImage);
					
					@unlink($sImage);				
				}
			}
			
			if ($iFileSize > 0)
			{
				Phpfox::getService('user.space')->update($aVideo['user_id'], 'video', $iFileSize, '-');	
			}			
			
			$this->database()->update($this->_sTable, array('image_path' => null, 'image_server_id' => 0), 'video_id = ' . $aVideo['video_id']);
			
			return true;
		}
		
		return Phpfox_Error::set(_p('videochannel.invalid_permissions'));
	}	
	
	public function feature($iId, $iType)
	{
		Phpfox::isUser(true);
        
		Phpfox::getUserParam('videochannel.can_feature_videos_', true);
		
        $aParams = array(
            'is_featured' => ($iType ? '1' : '0'), 
            'featured_time' => ($iType ? PHPFOX_TIME : 0)
        );
        
		$this->database()->update($this->_sTable, $aParams, 'video_id = ' . (int) $iId);
		
		$this->cache()->remove('videochannel_featured');
		
		return true;
	}
	
	public function spotlight($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('videochannel.can_spotlight_videos', true);
		
		$this->database()->update($this->_sTable, array('is_spotlight' => ($iType ? '1' : '0')), 'video_id = ' . (int) $iId);
		
		$this->cache()->remove('videochannel_spotlight');
		
		return true;
	}	

	public function sponsor($iId, $iType)
	{
	  
	    if (!Phpfox::getUserParam('videochannel.can_sponsor_videochannel') && !Phpfox::getUserParam('videochannel.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK'))
	    {
			return Phpfox_Error::set('Hack attempt?');
	    }
	    
	    $iType = (int)$iType;
	    if ($iType != 0 && $iType != 1)
	    {
			return Phpfox_Error::set('iType: ' . d($iType, true));
	    }
	    
	    if ($sPlugin = Phpfox_Plugin::get('videochannel.service_process_sponsor__end')){return eval($sPlugin);}
	    
	    $this->database()->update($this->_sTable, array('is_featured' => 0, 'is_sponsor' => $iType), 'video_id = ' . (int)$iId);
	    
		$this->cache()->remove('videochannel_sponsored');
	    
	    return true;	    
	}
	
	public function approve($iId)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('videochannel.can_approve_videos', true);
		
		$aVideo = $this->database()->select('v.video_id, v.view_id, v.title, v.privacy, v.privacy_comment, v.image_path, v.image_server_id, ' . Phpfox::getUserField())
			->from($this->_sTable, 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.video_id = ' . (int) $iId)
			->execute('getRow');
			
		if (!isset($aVideo['video_id']))
		{
			return Phpfox_Error::set(_p('videochannel.unable_to_find_the_video_you_want_to_approve'));
		}
		
		if ($aVideo['view_id'] == '0')
		{
			return false;
		}
		
		$this->database()->update($this->_sTable, array('view_id' => '0', 'time_stamp' => PHPFOX_TIME), 'video_id = ' . $aVideo['video_id']);
		
		if (Phpfox::isModule('notification'))
		{
			Phpfox::getService('notification.process')->add('videochannel_approved', $aVideo['video_id'], $aVideo['user_id']);
		}

		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink('videochannel', $aVideo['video_id'], $aVideo['title']);
		Phpfox::getLib('mail')->to($aVideo['user_id'])
			->subject(array('videochannel.your_video_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
			->message(array('videochannel.your_video_has_been_approved_on_site_title_n_nto_view_this_video_follow_the_link_below_n_a_href', array('site_title' => Phpfox::getParam('core.site_title'), 'sLink' => $sLink)))
			->notification('videochannel.video_is_approved')
			->send();			
			
		((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? Phpfox::getService('feed.process')->add('videochannel', $iId, $aVideo['privacy'], $aVideo['privacy_comment'], 0, $aVideo['user_id']) : null);
			
		// Update user activity
		Phpfox::getService('user.activity')->update($aVideo['user_id'], 'videochannel');

		return true;	
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
		if ($sPlugin = Phpfox_Plugin::get('videochannel.service_process__call'))
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

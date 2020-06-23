<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
class Document_Service_Callback extends Phpfox_Service 
{
    /**
     * Class constructor
     */    
    public function __construct()
    {    
        $this->_sTable = Phpfox::getT('document');
    }
    
    public function getAjaxCommentVar()
    {
        return 'document.can_add_comment_on_document';
    } 

	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.document_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'document\' AS item_type_id, item.image_url AS item_photo, 0 AS item_photo_server')
			->from(Phpfox::getT('document'), 'item')
			->where('item.view_id = 0 AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch))
			->union();
	}
	
	public function getSearchTitleInfo()
	{
		return array(
			'name' => _p('document')
		);
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('document', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = _p('document');

		if (!empty($aRow['item_photo']))
        {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aRow['item_photo_server'],
                'path' => 'core.url_pic',
                'file' => 'document' . PHPFOX_DS . $aRow['item_photo'],
                'suffix' => '_400_square',
                'max_width' => 120,
                'max_height' => 120
            ));
        }
        else
        {
            $aInfo['item_display_photo'] = '<img src="' . Phpfox::getParam('core.path_file') . 'module/document/static/image/no_image.png' . '" style="max-width:150px; max-height:150px;" />';
        }

		return $aInfo;
	}
    
    public function getCommentItem($iId)
    {

        $aRow = $this->database()->select('document_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from($this->_sTable)
            ->where('document_id = ' . (int) $iId)
            ->execute('getSlaveRow');


        $aRow['comment_view_id'] = 0;
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}
            
        return $aRow;
    }
    
    //phpfox v3
    public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'document.view_browse_documents') || !Phpfox::getUserParam('document.can_view_documents')) {
            return null;
        }
        
        $aMenus[] = array(
            'phrase' => _p("document.documents"),
            'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'document/',
            'icon' => 'feed/document.jpg',
            'landing' => 'document',
            'menu_icon' => 'ico ico-address-book-o'
        );
        return $aMenus;
    }
    
    //phpfox v3    
    public function getPageSubMenu($aPage)
    {
        
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'document.share_documents'))
        {
            return null;
        }        
         
        return array(
            array(
                'phrase' => _p("document.add_document_link_title"),
                'url' => Phpfox::getLib('url')->makeUrl('document.add', array('module' => 'pages', 'item' => $aPage['page_id']))
            )
        );
    }
    
    //phpfox v3
    public function getProfileMenu($aUser)
    {

        if (!isset($aUser['total_document']))
        {
            return false;
        }
        
        if (isset($aUser['total_document']) && (int) $aUser['total_document'] === 0)
        {
            return false;
        }
                
        $aMenus[] = array(
            'phrase' => _p('documents'),
            'url' => 'profile.document',
            'total' => (int) $aUser['total_document'],
            'icon' => 'feed/document.jpg',
            'icon_class' => 'ico ico-address-book-o'
        );        
        
        return $aMenus;
    }
     
    //phpfox v3   
    public function getTotalItemCount($iUserId)
    {
        return array(
            'field' => 'total_document',
            'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('document'))->where('view_id = 0 AND item_id = 0 AND user_id = ' . (int) $iUserId)->execute('getSlaveField')
        );    
    }               
    /*
    public function getRssTitle($iId)
    {
        $aRow = $this->database()->select('title')
            ->from($this->_sTable)
            ->where('document_id = ' . (int) $iId)
            ->execute('getSlaveRow');
        
        return 'Comments on: ' . $aRow['title'];
    }
    */
    public function getRedirectComment($iId)
    {
        return $this->getFeedRedirect($iId);
    }
    
    public function getReportRedirect($iId)
    {
        $aDocument = $this->database()->select('d.document_id, d.title')
            ->from(Phpfox::getT('document'), 'd')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
            ->where('d.document_id = ' . (int) $iId)
            ->execute('getSlaveRow');        
            
        if(!isset($aDocument['document_id']))
        {
            return false;
        }
        
        return Phpfox::permalink('document', $aDocument['document_id'], $aDocument['title']);
    }
    
    public function getCommentItemName()
    {
        return 'document';
    }
    /*
    public function getItemView()
    {
        
        if (Phpfox::getLib('request')->get('req3') != '')
        {
            return true;
        }
    }
    */
    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        $aRow = $this->database()->select('m.document_id, m.title, m.title_url, u.full_name, u.user_id, u.gender, u.user_name')
            ->from($this->_sTable, 'm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->where('m.document_id = ' . (int) $aVals['item_id'])
            ->execute('getSlaveRow');
            
        if (!isset($aRow['document_id']))
        {
            return false;
        }
           
		
		if ($iUserId === null)
		{
			$iUserId = Phpfox::getUserId();
		}
		
        if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('document', 'total_comment', 'document_id', $aVals['item_id']);
		}
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('document_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);
        
        
        $sLink = Phpfox::permalink('document', $aRow['document_id'], $aRow['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['document_id'],
				'owner_subject' => _p('full_name_commented_on_your_document_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])),
				'owner_message' => _p('full_name_commented_on_your_document_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_document',
				'mass_id' => 'document',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_gender_document', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' =>  Phpfox::getService('user')->gender($aRow['gender'], 1))) : _p('full_name_commented_on_document_full_name_s_document', array('full_name' => Phpfox::getUserBy('full_name'), 'document_full_name' => $aRow['full_name']))),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_gender_document_message', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'link' => $sLink, 'title' => $aRow['title'])) : _p('full_name_commented_on_document_full_name_s_document_message', array('full_name' => Phpfox::getUserBy('full_name'), 'document_full_name' => $aRow['full_name'], 'link' => $sLink, 'title' => $aRow['title'])))
			)
		);
		
		(($sPlugin = Phpfox_Plugin::get('document.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }
    public function getRatingData($iId)
    {
        return array(
            'field' => 'document_id',
            'table' => 'document',
            'table_rating' => 'document_rating'
        );
    }
    
    public function onDeleteUser($iUser)
    {
        // get all the documents by this user
        $aDocuments = $this->database()
            ->select('document_id')
            ->from($this->_sTable)
            ->where('user_id = ' . (int)$iUser)
            ->execute('getSlaveRows');

        foreach ($aDocuments as $aDocument)
        {
            Phpfox::getService('document.process')->delete($aDocument['document_id']);
        }
        
        // delete the tracks
        $this->database()->delete(Phpfox::getT('document_track'), 'user_id = ' . $iUser );
    }
    
    public function verifyFavorite($iItemId)
    {
        $aItem = $this->database()->select('i.document_id')
            ->from(Phpfox::getT('document'), 'i')
            ->where('i.document_id = ' . (int) $iItemId . ' AND i.view_id = 0')
            ->execute('getSlaveRow');
            
        if (!isset($aItem['document_id']))
        {
            return false;
        }

        return true;
    }
    public function canShareItemOnFeed(){}
    //phpfox v3
    public function getActivityFeed($aRow, $aCallback = null, $bIsChildItem = false)
    {
		if (Phpfox::isUser()) 
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'document\' AND l.item_id = d.document_id AND l.user_id = ' . Phpfox::getUserId());
		}else
        {
            return false;
        }
        
		if ($bIsChildItem)
		{
			$this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = d.user_id');
		}
		
        $aRow = $this->database()->select('d.document_id, d.module_id, d.title, d.title_url, d.time_stamp, d.total_comment, d.total_like, l.like_id AS is_liked, dt.text_parsed')
            ->from(Phpfox::getT('document'), 'd')
            ->join(Phpfox::getT('document_text'), 'dt', 'dt.document_id = d.document_id')
            ->where('d.document_id = ' . (int) $aRow['item_id'])
            ->execute('getSlaveRow');
        //    ->execute('');
        if (!isset($aRow['document_id']))
        {
            return false;
        }
        	
        $aReturn =  array_merge(array(
            'feed_title' => $aRow['title'],
            'feed_info' => _p('shared_a_document'),
            'feed_link' => Phpfox::permalink('document', $aRow['document_id'], $aRow['title']),
            'feed_content' => $aRow['text_parsed'],
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/document.jpg', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'document',
            'like_type_id' => 'document'
        ), $aRow);
        return $aReturn;
    }    
            
    public function getActivityFeedComment($aRow)
	{
		if (Phpfox::isUser())
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
		}else
        {
            return false;
        }
		
		$aItem = $this->database()->select('b.document_id, b.title, b.time_stamp, b.total_comment, b.total_like, c.total_like, ct.text_parsed AS text, ' . Phpfox::getUserField())
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
			->join(Phpfox::getT('document'), 'b', 'c.type_id = \'document\' AND c.item_id = b.document_id AND c.view_id = 0')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('c.comment_id = ' . (int) $aRow['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aItem['document_id']))
		{
			return false;
		}
		
		$sLink = Phpfox::permalink('document', $aItem['document_id'], $aItem['title']);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aItem['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') :50));
		$sUser = '<a href="' . Phpfox::getLib('url')->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
		$sGender = Phpfox::getService('user')->gender($aItem['gender'], 1);
		
		if ($aRow['user_id'] == $aItem['user_id'])
		{
			$sMessage = _p('posted_a_comment_on_gender_document_a_href_link_title_a', array('gender' => $sGender, 'link' => $sLink, 'title' => $sTitle));
		}
		else
		{			
			$sMessage = _p('posted_a_comment_on_user_name_s_document_a_href_link_title_a', array('user_name' => $sUser, 'link' => $sLink, 'title' => $sTitle));
		}
		(($sPlugin = Phpfox_Plugin::get('document.component_service_callback_getactivityfeedcomment__1')) ? eval($sPlugin) : false);
		
		return array(
			'no_share' => true,
			'feed_info' => $sMessage,
			'feed_link' => $sLink,
			'feed_status' => $aItem['text'],
			'feed_total_like' => $aItem['total_like'],
			'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/document.jpg', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],
			'like_type_id' => 'feed_mini'
		);		
	}	
		
	public function getFavorite($aFavorites)
    {
        $oServiceDocumentBrowse = Phpfox::getService('document.browse');        
        
        $oServiceDocumentBrowse->condition('m.document_id IN(' . implode(',', $aFavorites) . ') AND m.view_id = 0')
            ->execute();    
            
        $aDocuments = $oServiceDocumentBrowse->get();
        
        foreach ($aDocuments as $iKey => $aDocument)
        {
            $aDocuments[$iKey]['link'] = Phpfox::getLib('url')->makeUrl('document.view', array( $aDocument['title_url']));
        }
        
        return array(
            'title' => _p('documents'),
            'items' => $aDocuments
        );        
    }
    
    //phpfox v3
    public function getCommentNotification($aNotification)
    {
        $aRow = $this->database()->select('d.document_id, d.title, u.user_id, u.gender, u.user_name, u.full_name')    
            ->from(Phpfox::getT('document'), 'd')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
            ->where('d.document_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
            
        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
        {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' ' . _p('commented_on') . ' ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' ' . _p('document') . ' "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())
        {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' ' . _p('commented_on_your_document') . ' "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        }
        else 
        {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' ' . _p('commented_on') . ' <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> ' . _p('document') . ' "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        }
            
        return array(
            'link' => Phpfox::getLib('url')->permalink('document', $aRow['document_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'document.jpg', 'document')
        );
    }    
    public function getCommentNotificationFeed($aRow)
    {
        return array(
            'message' => _p('full_name_wrote_a_comment_on_your_document', array(
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                    'full_name' => $aRow['full_name'],
                    'link' => Phpfox::getLib('url')->makeUrl('document', array('redirect' => $aRow['item_id'])),
                    'title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')    
                )
            ),
            'link' => Phpfox::getLib('url')->makeUrl('document', array('redirect' => $aRow['item_id'])),
            'path' => 'core.url_user',
            'suffix' => '_50'
        );    
    }
    public function getActivityPointField()
    {
        return array(
            _p('documents') => 'activity_document'
        );
    }
    
    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
        
        return array(
            _p('documents') => $aUser['activity_document']
        );
    }        
    
    public function getTags($sTag, $aConds = array(), $sSort = '', $iPage = '', $sLimit = '')
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_gettags__start')) ? eval($sPlugin) : false);
        $aDocuments = array();
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('document'), 'document')
            ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = document.document_id")
            ->where($aConds)
            ->execute('getSlaveField');    

        if ($iCnt)
        {
            $aRows = $this->database()->select("m.*, " . (Phpfox::getParam('core.allow_html') ? "m.description" : "m.description") ." AS text, " . Phpfox::getUserField())
                ->from(Phpfox::getT('document'), 'm')
                ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = m.document_id")
                ->join(Phpfox::getT('user'), 'u', 'm.user_id = u.user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $sLimit, $iCnt)                
                ->execute('getSlaveRows');    
                        
            if (count($aRows))
            {
                foreach ($aRows as $aRow)
                {
                    $aDocuments[$aRow['document_id']] = $aRow;
                }                        
            }
        }        
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_gettags__end')) ? eval($sPlugin) : false);
        return array($iCnt, $aDocuments);
    }
    public function getTagSearch($aConds = array(), $sSort)
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_gettagsearch__start')) ? eval($sPlugin) : false);
        $aRows = $this->database()->select("m.document_id AS id")
            ->from(Phpfox::getT('document'), 'm')
            ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = m.document_id")
            ->where($aConds)
            ->order($sSort)    
            ->group('document.document_id')
            ->execute('getSlaveRows');                            
        
        $aSearchIds = array();
        foreach ($aRows as $aRow)
        {
            $aSearchIds[] = $aRow['id'];
        }        
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_gettagsearch__end')) ? eval($sPlugin) : false);
        return $aSearchIds;        
    }    
    
    public function getTagCloud()
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_gettagcloud__start')) ? eval($sPlugin) : false);
        return array(
            'link' => 'document',
            'category' => 'document'
        );
    }
    
    public function getTagLinkProfile($aUser)
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_gettaglinkprofile__start')) ? eval($sPlugin) : false);
        return Phpfox::getService('user')->getLink($aUser['user_id'], $aUser['user_name'], array('document', 'tag'));
    } 
    
    public function getLink($aParams)
    {
        $aDocument = $this->database()->select('u.user_name, d.title_url')
            ->from(Phpfox::getT('document'),'d')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
            ->where('d.document_id = ' . (int)$aParams['item_id'])
            ->execute('getSlaveRow');
        if (empty($aDocument))
        {
        return false;
        }
        return Phpfox::getLib('url')->makeUrl($aDocument['user_name'].'.document.' . $aDocument['title_url'] );
    }
    
    public function updateCounterList()
    {
        $aList = array();        
        
        $aList[] =    array(
            'name' => 'Update document "view" count',
            'id' => 'document-view-count'
        );    
        
        $aList[] =    array(
            'name' => 'Update Tags (Documents)',
            'id' => 'document-tag-update'
        );            

        return $aList;
    } 
  
           
    public function getSqlTitleField()
    {
        return array(
            array(
                'table' => 'document',
                'field' => 'title'
            ),
            array(
                'table' => 'document_category',
                'field' => 'name'
            )
        );
    }
    public function reparserList()
    {
        return array(
            'name' => _p('document_text'),
            'table' => 'document',
            'original' => 'description',
            'parsed' => 'description',
            'item_field' => 'document_id'
        );
    }
    public function getTagLink()
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_gettaglink__start')) ? eval($sPlugin) : false);
        return Phpfox::getLib('url')->makeUrl('document.tag');
    }
    
    public function addTrack($iId, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_addtrack__start')) ? eval($sPlugin) : false);
        $this->database()->insert(Phpfox::getT('document_track'), array(
                'item_id' => (int) $iId,
                'user_id' => Phpfox::getUserBy('user_id'),
                'time_stamp' => PHPFOX_TIME
            )
        );
        $this->database()->updateCounter('document', 'total_view', 'document_id', $iId);     
    }
    
    public function getLatestTrackUsers($iId, $iUserId)
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_getlatesttrackusers__start')) ? eval($sPlugin) : false);
        $aRows = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('document_track'), 'track')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = track.user_id')
            ->where('track.item_id = ' . (int) $iId . ' AND track.user_id != ' . (int) $iUserId)
            ->order('track.time_stamp DESC')
            ->limit(0, 6)
            ->execute('getSlaveRows');
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_getlatesttrackusers__end')) ? eval($sPlugin) : false);
        return (count($aRows) ? $aRows : false);        
    }
    public function getTagTypeProfile()
    {
        return 'document';
    }
    
    public function getTagType()
    {
        return 'document';
    }
    public function getFeedRedirect($iId, $iChild = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);
        $aDocument = $this->database()->select('m.document_id, m.title_url, u.user_id, u.user_name')
            ->from($this->_sTable, 'm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->where('m.document_id = ' . (int) $iId)
            ->execute('getSlaveRow');        
            
        if (!isset($aDocument['document_id']))
        {
            return false;
        }        
            
        if (Phpfox::getParam('core.is_personal_site'))
        {
            return Phpfox::getLib('url')->makeUrl('document', $aDocument['title_url']);
        }    
        
        if ($iChild > 0)
        {
            return Phpfox::getLib('url')->makeUrl($aDocument['user_name'], array('document', $aDocument['title_url'], 'comment' => $iChild, '#comment-view'));    
        }

        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);
        return Phpfox::getLib('url')->makeUrl($aDocument['user_name'], array('document', $aDocument['title_url']));
    }
    
    public function getNewsFeed($aRow, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_getnewsfeed__start')) ? eval($sPlugin) : false);
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');        
         
        $aRow['text'] = _p('owner_full_name_added_a_new_document_a_href_title_link_title_a',
            array(
                'owner_full_name' => $aRow['owner_full_name'], 
                'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']), 
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'title_link' => $aRow['link']                
            )
        );
        
        $aRow['icon'] = 'module/blog.png';
        $aRow['enable_like'] = true;

        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_getnewsfeed__end')) ? eval($sPlugin) : false);
        return $aRow;
    }    
    //phpfox v2  
    public function updateCommentText($aVals, $sText)
    {
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('comment_document', $aVals['item_id'], $sText, $aVals['comment_id']) : null);
    }    
   
    public function getAttachmentField()
    {
        return array('document', 'document_id');
    }
     
    public function getItemName($iId, $sName)
    {
        return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">' . _p('on_name_s_document', array('name' => $sName)) . '</a>';
    }    
    
    public function deleteComment($iId)
    {
		$this->database()->update($this->_sTable, array('total_comment' => array('= total_comment -', 1)), 'document_id = ' . (int) $iId);
        //$this->database()->updateCounter('document', 'total_comment', 'document_id', $iId, true);
    } 
       
    public function legacyRedirect($aRequest)
    {
        if (isset($aRequest['req2']))
        {
            switch ($aRequest['req2'])
            {
                case 'view':
                    if (isset($aRequest['id']))
                    {                
                        $aItem = Phpfox::getService('core')->getLegacyUrl(array(
                            'url_field' => 'title_url',
                                'table' => 'document',
                                'field' => 'upgrade_document_id',
                                'id' => $aRequest['id']
                            )
                        );
                        
                        if ($aItem !== false)
                        {
                            return array($aItem['user_name'], array('document', $aItem['title_url']));
                        }                                            
                    }
                    break;
                default:
                    return 'document';
                    break;
            }
        }
        
        return false;
    } 
    
    //phpfox v3
    public function getProfileLink()
    {
        return 'profile.document';
    }         
       
    public function getDashboardLinks()
    {
        return array(
            'submit' => array(
                'phrase' => _p('add_document_link_title'),
                'link' => 'document.add',
                'image' => 'misc/page_white_add.png'
            ),
            'edit' => array(
                'phrase' => _p('manage_documents'),
                'link' => 'document.index',
                'image' => 'misc/page_white_edit.png'
            )
        );
    }   
    public function getNotificationFeedapproved($aRow)
    {
        return array(
            'message' => _p('your_document_document_title_has_been_approved', array('document_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
            'link' => Phpfox::getLib('url')->makeUrl('document', array('redirect' => $aRow['item_id']))        );        
    }
  
    public function getFeedRedirectFeedLike($iId, $iChildId = 0)
    {
        return $this->getFeedRedirect($iChildId);
    }
    
    public function getNewsFeedFeedLike($aRow)
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);
        if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
        {
            $aRow['text'] = _p('a_href_user_link_full_name_a_likes_their_own_a_href_link_document_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
                    'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
                    'link' => $aRow['link']
                )
            );
        }
        else 
        {
            $aRow['text'] = _p('a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_document_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
                    'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
                    'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
                    'link' => $aRow['link']            
                )
            );
        }
        
        $aRow['icon'] = 'misc/thumb_up.png';
        (($sPlugin = Phpfox_Plugin::get('document.component_service_callback_ondeleteuser__end')) ? eval($sPlugin) : false);
        return $aRow;                
    }        
    public function getNotificationFeednotifylike($aRow)
    {        
        return array(
            'message' => _p('a_href_user_link_full_name_a_likes_your_a_href_link_document_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                    'link' => Phpfox::getLib('url')->makeUrl('document', array('redirect' => $aRow['item_id']))
                )
            ),
            'link' => Phpfox::getLib('url')->makeUrl('document', array('redirect' => $aRow['item_id']))            
        );                
    } 
    public function sendLikeEmail($iItemId)
    {
        return _p('a_href_user_link_full_name_a_likes_your_a_href_link_document_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
                    'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
                    'link' => Phpfox::getLib('url')->makeUrl('document', array('redirect' => $iItemId))
                )
            );
    }  
    
    
    public function pendingApproval()
    {
        return array(
            'phrase' => _p('documents'),
            'value' => Phpfox::getService('document')->getPendingTotal(),
            'link' => Phpfox::getLib('url')->makeUrl('document', array('view' => 'pending'))
        );
    }
    public function tabHasItems($iUser)
    {
        $iCount = $this->database()->select('COUNT(user_id)')
                ->from($this->_sTable)
                ->where('user_id = ' . (int)$iUser)
                ->execute('getSlaveField');
        return $iCount > 0;
    }             
    public function getCommentNewsFeed($aRow)
    {        
        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');        
        
        if ($aRow['owner_user_id'] == $aRow['item_user_id'])
        {
            $aRow['text'] = _p('a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_document_a', array(
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                    'title_link' => $aRow['link']
                )
            );
        }
        else 
        {
            if ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
            {
                $aRow['text'] = _p('a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_document_a', array(
                        'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                        'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                        'title_link' => $aRow['link']                
                    )
                );
            }
            else 
            {
                $aRow['text'] = _p('a_href_user_link_full_name_a_added_a_new_comment_on_a_href_item_user_link_item_user_name', array(
                        'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                        'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                        'title_link' => $aRow['link'],
                        'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id'])),
                        'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name'])
                    )
                );
            }
        }
            
        $aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
        
        return $aRow;
    } 
    
    //phpfox v3
    public function getNotificationApproved($aNotification)
    {
        $aRow = $this->database()->select('d.document_id, d.title, d.user_id, u.gender, u.full_name')    
            ->from(Phpfox::getT('document'), 'd')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
            ->where('d.document_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');    

        if (!isset($aRow['document_id']))
        {
            return false;
        }
        
        $sPhrase = _p('your_document_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
            
        return array(
            'link' => Phpfox::getLib('url')->permalink('document', $aRow['document_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'document.jpg', 'document'),
            'no_profile_image' => true
        );            
    }

    public function getDocumentDetails($aItem)
    {
        if (empty($aItem['module_id']) || $aItem['module_id'] == 'document') {
            return null;
        }

        $aResult = null;
        switch ($aItem['module_id']) {
            case 'pages':
                $aResult = $this->getDocumentDetailsInPage($aItem);
                break;
            case 'groups':
                $aResult = $this->getDocumentDetailsInGroup($aItem);
                break;
        }

        return $aResult;
    }

    public function getDocumentDetailsInGroup($aItem)
    {
        Phpfox::getService('groups')->setIsInPage();

        $aRow = Phpfox::getService('groups')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getService('groups')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('groups'),
            'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('groups'),
            'module_id' => 'groups',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'document/',
            'theater_mode' => 'In the page <a href="' . $sLink . '">' . $aRow['title'] . '</a>'
        );
    }

    public function getDocumentDetailsInPage($aItem)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('pages'),
            'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'document/',
            'theater_mode' => 'In the page <a href="' . $sLink . '">' . $aRow['title'] . '</a>'
        );
    }
    
    //phpfox v3
    // Add Like then getNotification to show
    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('document_id, title, user_id')
            ->from(Phpfox::getT('document'))
            ->where('document_id = ' . (int) $iItemId)
            ->execute('getSlaveRow');        
            
        if (!isset($aRow['document_id']))
        {
            return false;
        }
        
        $this->database()->updateCount('like', 'type_id = \'document\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'document', 'document_id = ' . (int) $iItemId);    
        
        if (!$bDoNotSendEmail)
        {
            $sLink = Phpfox::permalink('document', $aRow['document_id'], $aRow['title']);
            
            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(Phpfox::getUserBy('full_name') . " " . _p('liked_your_document') . " \"" . $aRow['title'] . "\"")
                ->message(Phpfox::getUserBy('full_name') . " " . _p('liked_your_document') . " \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\"\n" . _p('to_view_this_document_follow_the_link_below') . "\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
                ->send();
                    
            Phpfox::getService('notification.process')->add('document_like', $aRow['document_id'], $aRow['user_id']);                
        }        
    }
    //phpfox v3
    public function getNotificationLike($aNotification)
    {
        $aRow = $this->database()->select('d.document_id, d.title, d.user_id, u.gender, u.full_name')    
            ->from(Phpfox::getT('document'), 'd')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
            ->where('d.document_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');
            
        if (!isset($aRow['document_id']))
        {
            return false;
        }            
        
        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'])
        {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' ' . _p('liked') . ' ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' ' . _p('own_document') . ' "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        }
        elseif ($aRow['user_id'] == Phpfox::getUserId())        
        {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' ' . _p('liked_your_document') . ' "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        }
        else 
        {
            $sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' ' . _p('liked') . ' <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> ' . _p('document') . ' "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
        }
            
        return array(
            'link' => Phpfox::getLib('url')->permalink('document', $aRow['document_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'document.jpg', 'document')
        );    
    }
	
	public function getCommentNotificationTag($aNotification)
	{
		$aRow = $this->database()->select('b.document_id, b.title, u.user_name, u.full_name')
					->from(Phpfox::getT('comment'), 'c')
					->join(Phpfox::getT('document'), 'b', 'b.document_id = c.item_id')
					->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
					->where('c.comment_id = ' . (int)$aNotification['item_id'])
					->execute('getSlaveRow');
					
		if(!isset($aRow['document_id']))
		{
			return false;
		}
		
		$sPhrase = _p('user_name_tagged_you_in_a_comment_in_a_document', array('user_name' => $aRow['full_name']));
		
		return array(
			'link' => Phpfox::getLib('url')->permalink('document', $aRow['document_id'], $aRow['title']) . 'comment_' .$aNotification['item_id'],
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	
    public function deleteLike($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'document\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'document', 'document_id = ' . (int) $iItemId);
    }
    //End of Like
    
    //page privacy    
    public function getPagePerms()
    {
        $aPerms = array();
        
        $aPerms['document.share_documents'] = _p('who_can_create_documents');
        $aPerms['document.view_browse_documents'] = _p('who_can_view_browse_documents');
        
        return $aPerms;
    }

    public function getGroupPerms()
    {
        $aPerms = array();
        $aPerms['document.share_documents'] = _p('who_can_create_documents');
        $aPerms['document.view_browse_documents'] = _p('who_can_view_browse_documents');
        return $aPerms;
    }
    
    public function canViewPageSection($iPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($iPage, 'document.view_browse_documents'))
        {
            return false;
        }
        
        return true;
    }
	
	public function getCountFoxFavorite($iUserId)
	{
		if(!phpfox::isModule('foxfavorite'))
		{
			return false;
		}
		$iCnt = 0;
		$iCnt = phpfox::getLib('database')->select('count(*)')
				->from(Phpfox::getT('document'), 'd')
				->join(Phpfox::getT('foxfavorite'), 'f', 'f.item_id = d.document_id')
				->where('f.user_id = '.$iUserId.' and type_id = "document" and d.view_id = 0')
				->execute('getField');
		return $iCnt;
	}
	
	public function getFoxFavorite($aFavorites)
	{
		if(!phpfox::isModule('foxfavorite'))
		{
			return false;
		}
		$aItems = $this->database()->select('*, '.phpfox::getUserField())
			->from(Phpfox::getT('document'), 'd')
			->join(phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
			->where('d.document_id IN(' . implode(',', $aFavorites) . ')  and d.view_id = 0')
			->execute('getSlaveRows');


		foreach ($aItems as $iKey => $aItem)
		{
 			$aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array(
					//'server_id' => $aItem['server_id'],
					'path' => 'core.url_user',
					'file' => $aItem['user_image'],
					'suffix' => '_75',
					'max_width' => 75,
					'max_height' => 75
				)
			);		

			$aItems[$iKey]['link'] = Phpfox::permalink('document', $aItem['document_id'], $aItem['title']);
			$aItems[$iKey]['title'] = $aItem['title'];
			//$aItems[$iKey]['extra_info'] = 'Added on '.$aItem['time_stamp'];
			$aItems[$iKey]['time_stamp'] = $aItem['time_stamp'];
		}

		return array(
			'title' => "Document",
			'items' => $aItems
		);
	}
	
	public function getActivityFeedFoxFavorite($aItem, $aCallback = null)
    {     
		
        if(!phpfox::isModule('foxfavorite'))
		{
			return false;
		}
		phpfox::getService('foxfavorite')->getItemIdByFavoriteItemId($aItem);
        $aRow = $this->database()->select('d.document_id, d.module_id, d.title, d.title_url, d.time_stamp, d.total_comment, d.total_like, l.like_id AS is_liked, dt.text_parsed')
            ->from(Phpfox::getT('document'), 'd')
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'document\' AND l.item_id = d.document_id AND l.user_id = ' . Phpfox::getUserId())
            ->leftJoin(Phpfox::getT('document_text'), 'dt', 'dt.document_id = d.document_id')
            ->where('d.document_id = ' . (int) $aItem['item_id'])
            ->execute('getSlaveRow');
		
        if (!isset($aRow['document_id']))
        {
            return false;
        }
        
        if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'document.view_browse_documents'))
        {
            return false;
        } 
                                             
        $aReturn =  array(
            'feed_title' => $aRow['title'],
            'feed_info' => _p('favored_a_document'),
            'feed_link' => Phpfox::permalink('document', $aRow['document_id'], $aRow['title']),
            'feed_content' => $aRow['text_parsed'],
            //'total_comment' => $aRow['total_comment'],
            //'feed_total_like' => $aRow['total_like'],
            //'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],            
            //'enable_like' => true,            
            //'comment_type_id' => 'document',
            //'like_type_id' => 'document'            
        );
        return $aReturn;
    } 
	
	public function getNotificationFavorfoxfavorite($aNotification)
	{
		if(!phpfox::isModule('foxfavorite'))
		{
			return false;
		}
		phpfox::getService('foxfavorite')->getItemIdByFavoriteItemId($aNotification);

		$aRow = $this->database()->select('d.document_id, d.title, d.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('document'), 'd')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
			->where('d.document_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['document_id']))
		{
			return false;
		}
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sPhrase = _p('user_favored_your_document_title', array(
				'users'=>$sUsers,
				'title'=>$aRow['title']
				));

		return array(
			'link' => Phpfox::getLib('url')->permalink('document', $aRow['document_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/favorite.png', 'return_url' => true))
		);
	}
	
	public function checkFeedShareLink()
	{
		if (!Phpfox::getUserParam('document.add_new_document'))
		{
			return false;
		}
	}

    public function getGroupMenu($aGroup)
    {
        if (!Phpfox::getService('groups')->hasPerm($aGroup['page_id'],
            'document.view_browse_documents') || !Phpfox::getUserParam('document.can_view_documents')) {
            return null;
        }

        $aMenus[] = [
            'phrase' => _p('Document'),
            'url' => Phpfox::getService('groups')->getUrl($aGroup['page_id'], $aGroup['title'], $aGroup['vanity_url']) . 'document/',
            'icon' => 'module/document.png',
            'landing' => 'document',
            'menu_icon' => 'ico ico-address-book-o'
        ];

        return $aMenus;
    }

    public function getGroupSubMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'document.add') ) {
            return null;
        }

        return array(
            array(
                'phrase' => _p('Create New Document'),
                'url' => Phpfox::getLib('url')->makeUrl('document.add',
                    array('module_id' => 'groups', 'item' => $aPage['page_id']))
            )
        );
    }

    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = array();
        $aCond[] = 'view_id = 0';
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)$this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('document'))
            ->where($aCond)
            ->execute('getSlaveField');

        return array(
            'phrase' => _p('documents'),
            'total' => $iCnt,
            'icon' => 'ico ico-address-book-o'
        );
    }

    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return array(
            'phrase' => _p('documents'),
            'value' => $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('document'))
                ->where('view_id = 0 AND time_stamp >= ' . $iToday)
                ->executeField()
        );
    }

    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotal = db()->select('COUNT(*)')
            ->from(':document')
            ->where('user_id ='.(int)$iUserId)
            ->execute('getField');
        return [
            'total_name' => _p('document.document'),
            'total_value' => $iTotal,
            'type' => 'item'
        ];
    }

    public function getUploadParams()
    {
        $iMaxSize = Phpfox::getUserParam('document.document_max_image_size');

        return array(
            'max_size' => ($iMaxSize > 0 ? $iMaxSize / 1024 : null),
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'document' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'document' . PHPFOX_DS,
            'thumbnail_sizes' => array(50, 100, 240, 400),
            'label' => _p('display_photo'),
        );
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = Phpfox::getService('document')->getPendingTotal();
        return [
            'message'=> _p('you_have_total_pending_documents', ['total'=>$iTotalPending]),
            'value' => $iTotalPending,
            'link' => \Phpfox_Url::instance()->makeUrl('document', array('view' => 'pending'))
        ];
    }
}  
?>

<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Block_Display extends Phpfox_Component 
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {            
        $iUserId = $this->getParam('user_id');
        $bIsCustomFeedView = false;
        $sCustomViewType = null;        
        
        if (PHPFOX_IS_AJAX && ($iUserId = $this->request()->get('profile_user_id')))
        {
            if (!defined('PHPFOX_IS_USER_PROFILE'))
            {
                define('PHPFOX_IS_USER_PROFILE', true);
            }
            $aUser = Phpfox::getService('user')->get($iUserId);
            
            $this->template()->assign(array(
                    'aUser' => $aUser
                )
            );
        }    
        
        if (PHPFOX_IS_AJAX && $this->request()->get('callback_module_id'))
        {
            $aCallback = Phpfox::callback($this->request()->get('callback_module_id') . '.getFeedDisplay', $this->request()->get('callback_item_id'));
            $this->setParam('aFeedCallback', $aCallback);
        }
        
        $aFeedCallback = $this->getParam('aFeedCallback', null);
		$aFeedCallback['feed_comment'] = 'fevent_comment';
        
        $bIsProfile = (is_numeric($iUserId) && $iUserId > 0);
        
        if ($this->request()->get('feed') && $bIsProfile)
        {
            switch ($this->request()->get('flike'))
            {
                default:
                    if ($sPlugin = Phpfox_Plugin::get('feed.component_block_display_process_flike'))
                    {
                        eval($sPlugin);
                    }                    
                    break;
            }
        }
        
        if (defined('PHPFOX_IS_USER_PROFILE') && !Phpfox::getService('user.privacy')->hasAccess($iUserId, 'feed.view_wall'))
        {            
            return false;            
        }
        
        if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'pages.share_updates'))
        {
            $aFeedCallback['disable_share'] = true;
        }        
        
        $iFeedPage = $this->request()->get('page', 0);
        
        if ($this->request()->getInt('status-id') 
            || $this->request()->getInt('comment-id') 
            || $this->request()->getInt('link-id')
            || $this->request()->getInt('poke-id')
            || $this->request()->getInt('feed')
        )
        {
            $bIsCustomFeedView = true;
            if ($this->request()->getInt('status-id'))
            {
                $sCustomViewType = _p('feed.status_update_iid',array('iId' => $this->request()->getInt('status-id')));
            }
            elseif ($this->request()->getInt('link-id'))
            {
                $sCustomViewType = _p('feed.link_iid',array('iId' => $this->request()->getInt('link-id')));
            }        
            elseif ($this->request()->getInt('poke-id'))
            {
                $sCustomViewType = _p('feed.poke_iid',array('iId' =>$this->request()->getInt('poke-id')));
            }            
            elseif ($this->request()->getInt('comment-id'))
            {
                $sCustomViewType = _p('feed.wall_comment_iid',array('iId' => $this->request()->getInt('comment-id')));
                
                Phpfox::getService('notification.process')->delete('feed_comment_profile', $this->request()->getInt('comment-id'), Phpfox::getUserId());
            }
            elseif ($this->request()->getInt('feed'))
            {
                $sCustomViewType = _p('feed.feed');
            }
        }
        
        $aRows = Phpfox::getService('feed')->callback($aFeedCallback)->get(($bIsProfile > 0 ? $iUserId : null), ($this->request()->get('feed') ? $this->request()->get('feed') : null), $iFeedPage);
        
        if (($this->request()->getInt('status-id') 
                || $this->request()->getInt('comment-id') 
                || $this->request()->getInt('link-id')
                || $this->request()->getInt('poke-id')
            ) 
            && isset($aRows[0]))
        {
            $aRows[0]['feed_view_comment'] = true;
            $this->setParam('aFeed', array_merge(array('feed_display' => 'view', 'total_like' => $aRows[0]['feed_total_like']), $aRows[0]));    
        }    
        
        (($sPlugin = Phpfox_Plugin::get('feed.component_block_display_process')) ? eval($sPlugin) : false);        
        
        if ($bIsCustomFeedView && !count($aRows) && $bIsProfile)
        {
            $aUser = $this->getParam('aUser');
            
            $this->url()->send($aUser['user_name'], null, _p('feed.the_activity_feed_you_are_looking_for_does_not_exist'));
        }
        
        $iUserid = ($bIsProfile > 0 ? $iUserId : null);
        foreach ($aRows as $key => $aRow) {
            if(isset($aRow['parent_user'])){
                unset($aRows[$key]['parent_user']);
            }
        }
        $this->template()->assign(array(                
                'aFeeds' => $aRows,
                'iFeedNextPage' => ($iFeedPage + 1),
                'iFeedCurrentPage' => $iFeedPage,
                'iTotalFeedPages' => 1,
                'aFeedVals' => $this->request()->getArray('val'),
                'sCustomViewType' => $sCustomViewType,
                'aFeedStatusLinks' => Phpfox::getService('feed')->getShareLinks(),
                'aFeedCallback' => $aFeedCallback,
                'bIsCustomFeedView' => $bIsCustomFeedView,
                'sCustomClassName' => 'ync-block'
            )
        );    
        
        if ($bIsProfile)
        {            
            if (!Phpfox::getService('user.privacy')->hasAccess($iUserId, 'feed.display_on_profile'))
            {
                return false;
            }            
        }
                
        return 'block';
    }

    public function clean()
    {
        $this->template()->clean(array(
                'sHeader',
                'aFeeds',
                'sBoxJsId'
            )
        );
    }    
}

?>
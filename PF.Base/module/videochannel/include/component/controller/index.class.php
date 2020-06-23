<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Index extends Phpfox_Component {

    public function process()
    {
        $this->search()->browse()->setPagingMode(Phpfox::getParam('videochannel.videochannel_paging_mode', 'loadmore'));
        $this->_checkLegacyItem();
        $aTopViewed = null;
        Phpfox::getUserParam('videochannel.can_access_videos', true);
        //for page only
        $aParentModule = $this->getParam('aParentModule');
        $iItem = (isset($aParentModule['item_id']) ? $aParentModule['item_id'] : 0);
        $sModule = (isset($aParentModule['module_id']) ? $aParentModule['module_id'] : 'videochannel');
        //because only user can add channel can see channel list, so we use this variable to consider showing channel list
        $bCanAddChannelInPage = false;
        $aPage = [];
        if ($aParentModule['module_id'] == 'pages' || $aParentModule['module_id'] == 'groups')
        {
            if ($iItem != 0)
            {
                if (Phpfox::isAdmin())
                {
                    $bCanAddChannelInPage = true;
                }
            }
            // Fix bug timeline on page.
            $aPage = Phpfox::getService($aParentModule['module_id'])->getPage($iItem);
            $aCustomSubMenus = array();
            if (Phpfox::isAdmin())
            {
                $aCustomSubMenus[] = array(
                    'phrase' => _p('videochannel.upload_share_a_video'),
                    'url' => Phpfox::getLib('url')->makeUrl('videochannel.add', array('module' => 'pages', 'item' => $iItem)),
                    'showAddButton' => true
                );
                $aCustomSubMenus[] = array(
                    'phrase' => _p('videochannel.add_a_channel'),
                    'url' => Phpfox::getLib('url')->makeUrl('videochannel.channel.add', array('module' => 'pages', 'item' => $iItem)),
                    'showAddButton' => false
                );
            }
            else
            {
                if (Phpfox::getUserParam('videochannel.can_upload_video_on_page'))
                {
                    if (Phpfox::getService('pages')->hasPerm($iItem, 'videochannel.share_videos'))
                    {
                        $aCustomSubMenus[] = array(
                            'phrase' => _p('videochannel.upload_share_a_video'),
                            'url' => Phpfox::getLib('url')->makeUrl('videochannel.add', array('module' => 'pages', 'item' => $iItem)),
                            'showAddButton' => true
                        );
                    }
                }
                if (Phpfox::getUserId() == $aPage['user_id'] && Phpfox::getUserParam('videochannel.can_add_channel_on_page'))
                {
                    $aCustomSubMenus[] = array(
                        'phrase' => _p('videochannel.add_a_channel'),
                        'url' => Phpfox::getLib('url')->makeUrl('videochannel.channel.add', array('module' => 'pages', 'item' => $iItem)),
                        'showAddButton' => false
                    );
                }
            }
            $this->template()->assign(array('bIsUserTimeLine' => true, 'aCustomSubMenus' => $aCustomSubMenus));
        }
        else
        {
            $this->template()->assign(array('bIsUserTimeLine' => false, 'aCustomSubMenus' => array()));
        }
        $this->redirect();
        $this->delete();
        if ($aParentModule === null && $this->request()->getInt('req2'))
        {
            return Phpfox::getLib('module')->setController('videochannel.view');
        }
		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
        {
            $bIsUserProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        }
        else
        {
            $bIsUserProfile = $this->getParam('bIsProfile');
			$aUser = $this->getParam('aUser');
        }

        $sView = $this->request()->get('view');
        $sCategory = null;
        $this->setParam('sTagType', 'videochannel');
        if ($sView == 'channels' || $sView == 'all_channels')
        {

            if (!Phpfox::getUserParam('videochannel.can_add_channels') && ($bCanAddChannelInPage == false))
            {
                $this->url()->send('videochannel');
            }

            $oServiceVideoBrowse = Phpfox::getService('videochannel.channel.browse');

            $arSet = array(
                'type' => 'channel_channel',
                'field' => 'm.channel_id',
                'search_tool' => array(
                    'table_alias' => 'm',
                    'search' => array(
                        'action' => ($aParentModule === null ? ($bIsUserProfile === true ? $this->url()->makeUrl($aUser['user_name'],
                            array('videochannel', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('videochannel',
                            array('view' => $this->request()->get('view')))) : $aParentModule['url'] . 'videochannel?view=' . $this->request()->get('view')),
                        'default_value' => _p('videochannel.search_channels'),
                        'name' => 'search',
                        'field' => 'm.title'
                    ),
                    'sort' => array(
                        'latest' => array('m.time_stamp', _p('videochannel.latest'))
                    ),
                    'show' => array(5, 10, 15)
                )
            );
            $this->search()->set($arSet);
            
            $aBrowseParams = array(
                'module_id' => 'videochannel.channel',
                'alias' => 'm',
                'field' => 'channel_id',
                'table' => Phpfox::getT('channel_channel'),
                'hide_view' => array('pending', 'my')
            );

            $arParam = array(
                'name' => 'videochannel',
                'ajax' => 'videochannel.channel.moderation',
                'menu' => array(
                    array(
                        'phrase' => _p('videochannel.delete'),
                        'action' => 'deleteChannel'
                    ),
                    array(
                        'phrase' => _p('videochannel.auto_update'),
                        'action' => 'autoUpdate'
                    )
                )
            );
            $this->setParam('global_moderation', $arParam);
        }
        else
        {
            $oServiceVideoBrowse = Phpfox::getService('videochannel.browse');
            $arSet = array(
                'type' => 'channel_video',
                'field' => 'm.video_id',
                'search_tool' => array(
                    'table_alias' => 'm',
                    'search' => array(
                        'action' => ($aParentModule === null ? ($bIsUserProfile === true ? $this->url()->makeUrl($aUser['user_name'],
                            array('videochannel', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('videochannel',
                            array('view' => $this->request()->get('view')))) : $aParentModule['url'] . 'videochannel?view=' . $this->request()->get('view')),
                        'default_value' => _p('videochannel.search_videos'),
                        'name' => 'search',
                        'field' => 'm.title'
                    ),
                    'sort' => array(
                        'latest' => array('m.time_stamp', _p('videochannel.latest')),
                        'featured' => array('is_featured', _p('videochannel.featured')),
                        'most-viewed' => array('m.total_view', _p('videochannel.most_viewed')),
                        'most-liked' => array('m.total_like', _p('videochannel.most_liked')),
                        'most-talked' => array('m.total_comment', _p('videochannel.most_discussed'))
                    ),
                    'show' => array(12, 16, 20, 24)
                )
            );
            $this->search()->set($arSet);

            $aBrowseParams = array(
                'module_id' => 'videochannel',
                'alias' => 'm',
                'field' => 'video_id',
                'table' => Phpfox::getT('channel_video'),
                'hide_view' => array('pending', 'my')
            );

            $arParam = array(
                'name' => 'videochannel',
                'ajax' => 'videochannel.moderation',
                'menu' => array(
                    array(
                        'phrase' => _p('videochannel.delete'),
                        'action' => 'delete'
                    ),
                    array(
                        'phrase' => _p('videochannel.approve'),
                        'action' => 'approve'
                    )
                )
            );
            $this->setParam('global_moderation', $arParam);
        }

        switch ($sView) {
            case 'pending':
                if (Phpfox::getUserParam('videochannel.can_approve_videos'))
                {
                    $this->search()->setCondition('AND m.view_id = 2');
                }
                break;

            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId() . ' AND  m.module_id = \'' . $sModule . '\' AND m.item_id = ' . $iItem);
                break;

            case 'channels' :
                Phpfox::isUser(true);

                //mm i add a page condition here 
                if (defined('PHPFOX_IS_PAGES_VIEW'))
                {
                    if (Phpfox::isAdmin() || $bCanAddChannelInPage)
                    {
                        $this->search()->setCondition('AND  m.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int) $aParentModule['item_id'] . ' AND m.user_id = ' . Phpfox::getUserId() . ' AND m.privacy IN(%PRIVACY%)');
                    }
                    else
                    {
                        Phpfox::isAdmin(true);
                    }
                }
                else
                {
                    if (Phpfox::isAdmin() || Phpfox::getUserParam('videochannel.can_add_channels'))
                    {
                        $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId() . ' AND m.item_id = 0 AND  m.module_id = \'' . $sModule . '\'');
                    }
                    else
                    {
                        Phpfox::isAdmin(true);
                    }
                }

                $this->template()->assign('bIsChannel', true);
                break;

            case 'all_channels' :
                Phpfox::isUser(true);

                //mm i add a page condition here 
                if (defined('PHPFOX_IS_PAGES_VIEW'))
                {
                    if (Phpfox::isAdmin() || $bCanAddChannelInPage)
                    {
                        $this->search()->setCondition('AND  m.module_id = \'' . (isset($aParentModule['module_id']) ? $aParentModule['module_id'] : 'videochannel') . '\' AND m.item_id = ' . (int) $aParentModule['item_id'] . ' AND m.privacy IN(%PRIVACY%)');
                    }
                    else
                    {
                        Phpfox::isAdmin(true);
                    }
                }
                else
                {
                    if (Phpfox::isAdmin(true))
                    {
                        $this->search()->setCondition(' AND m.item_id = 0 AND m.privacy IN(%PRIVACY%)');
                    }
                }

                $this->template()->assign('bIsChannel', true);

                break;

            case 'favorite':
                Phpfox::isUser(true);
                if (defined('PHPFOX_IS_PAGES_VIEW'))
                {

                    $this->search()->setCondition(' AND m.module_id = \'' . $sModule .
                            '\' AND m.item_id = ' . $iItem .
                            ' AND m.video_id IN (SELECT f.item_id FROM ' . Phpfox::getT('favorite') . ' f WHERE f.user_id =' . Phpfox::getUserId() . ' )');


                }
                else
                {

                    $this->search()->setCondition(' AND m.module_id = \'videochannel\' AND m.video_id IN (SELECT f.item_id FROM ' . Phpfox::getT('favorite') . ' f WHERE f.user_id =' . Phpfox::getUserId() . ' )');

                }
                break;

            default:
                if ($bIsUserProfile)
                {
                    $this->search()->setCondition('AND m.module_id = \'videochannel\' AND  m.in_process = 0 AND m.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND m.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ') AND m.user_id = ' . (int) $aUser['user_id']);
                }
                else
                {
                    if (defined('PHPFOX_IS_PAGES_VIEW'))
                    {
                        $this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int) $aParentModule['item_id'] . ' AND m.privacy IN(%PRIVACY%)');
                    }
                    else
                    {
                        $this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'videochannel\' AND m.item_id = 0 AND m.privacy IN(%PRIVACY%)');
                    }
                }
                break;
        }

        $sTagSearchValue = null;
        if ($this->request()->get('req2') == 'tag' && $this->request()->get('req3'))
        {
            $sCategory = null;
            $sTagSearchValue = $this->request()->get('req3');
        }
		
		if($bIsUserProfile)
		{
			if ($this->request()->get('req3') == 'category')
	        {
	            $sCategory = $this->request()->getInt('req4');
	            $this->search()->setCondition('AND mcd.category_id = ' . (int) $sCategory);
	        }
		}
		else {
			if ($this->request()->get('req2') == 'category')
	        {
	            $sCategory = $this->request()->getInt('req3');
	            $this->search()->setCondition('AND mcd.category_id = ' . (int) $sCategory);
	        }
		}

        //Make sure that this category is valid
        $aCategory = Phpfox::getService('videochannel.category')->getForEdit((int) $sCategory);
        if (isset($aCategory['is_active']) && $aCategory['is_active'] == 0)
            return Phpfox_Module::instance()->setController('error.404');

        // For page display list video in category.
        if ($this->request()->get('req1') == 'pages' && $this->request()->get('req3') == 'videochannel' && $this->request()->get('req4') == 'category')
        {
            $sCategory = $this->request()->getInt('req5');
            $this->search()->setCondition('AND mcd.category_id = ' . (int) $sCategory);
        }

        $this->setParam('sCategory', $sCategory);
        if ($this->request()->getInt('sponsor') == 1)
        {
            $this->search()->setCondition('AND m.is_sponsor != 1');
            Phpfox::addMessage(_p('videochannel.sponsor_help'));
        }

        if ($sView == 'featured')
        {
            $this->search()->setCondition('AND m.is_featured = 1 AND m.privacy = 0');
        }

        $oServiceVideoBrowse->category($sCategory)->tag($sTagSearchValue);


        $this->search()->setContinueSearch(true);
        $this->search()->browse()->params($aBrowseParams)->execute();

        if (defined('PHPFOX_IS_USER_PROFILE'))
        {
            $this->template()->setMeta('description', _p('videochannel.full_name_s_videos_full_name_has_total_video_s', array('full_name' => $aUser['full_name'], 'total' => $this->search()->browse()->getCount())));
        }

        if ($sView == 'channels' || $sView == 'all_channels')
        {
            $this->template()->assign('aChannels', $this->search()->browse()->getRows());
        }
        else
        {

            $this->template()->assign('aVideos', $this->search()->browse()->getRows());
        }

        $bCanUploadVideo = (int) Phpfox::getUserParam('videochannel.can_upload_videos');
        $sJs = '';
        if (!$bCanUploadVideo)
        {
            if ($aParentModule['module_id'] != 'pages')
            {
                $sJs .= "<script type='text/javascript'>
                    \$Behavior.VideoChannelRemoveMenuSection3 = function() {
                        \$('#section_menu ul li').first().remove();
                    }
                </script>";
            }
        }

        $bCanAddChannel = (int) Phpfox::getUserParam('videochannel.can_add_channels');
        if (!$bCanAddChannel)
        {
            if ($aParentModule['module_id'] != 'pages')
            {
                $sJs .= "<script type='text/javascript'>
                    \$Behavior.VideoChannelRemoveMenuSection4 = function() {
                        \$('.breadcrumbs_menu ul li').last().remove();
                    }
                    </script>";
            }
        }

        $this->template()->setTitle(($bIsUserProfile ? _p('videochannel.full_name_s_videos', array('full_name' => $aUser['full_name'])) : _p('videochannel.videochannel')));

        $this->template()->setBreadcrumb(_p('videochannel.videochannel'), (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'], 'videochannel') : $this->url()->makeUrl('videochannel')));

        $this->template()->setMeta('keywords', Phpfox::getParam('videochannel.video_meta_keywords'));
        $this->template()->setMeta('description', Phpfox::getParam('videochannel.video_meta_description'));

        $arHeader = array(
            'pager.css' => 'style_css',
            'videochannel.js' => 'module_videochannel',
            'channel.js' => 'module_videochannel',

            'jquery.cycle.all.js' => 'module_videochannel',
            'jhslide.js' => 'module_videochannel'

        );
        $this->template()->setHeader('cache', $arHeader);

        $sImageOnError = Phpfox::getParam('core.path') . 'module/videochannel/static/image/noimage.png';

        $sSortTitle = '';
        if ($sView == "" && !isset($aParentModule['module_id']) && !$this->request()->get('search-id')
                && !$this->request()->get('sort')
                && $this->request()->get('req2') == '')
        {
            if (!defined('PHPFOX_IS_USER_PROFILE'))
            {
                $aTopViewed = Phpfox::getService('videochannel')->getFeaturedVideos(5, null, null);
                $sSortTitle = _p('videochannel.latest');

                if ($aTopViewed)
                {
                    foreach ($aTopViewed as $aVideo)
                    {
                        Phpfox::getService('videochannel')->check_480_image_for_slide($aVideo);
                    }
                }
            }
        }

        switch ($this->request()->get('sort')) {
            case 'latest' :
                $sSortTitle = _p('videochannel.latest');
                break;

            case 'featured' :
                $sSortTitle = _p('videochannel.featured');
                break;

            case 'top-rated' :
                $sSortTitle = _p('videochannel.top_rated');
                break;

            case 'most-viewed' :
                $sSortTitle = _p('videochannel.most_viewed');
                break;

            case 'most-liked' :
                $sSortTitle = _p('videochannel.most_liked');
                break;

            case 'most-talked' :
                $sSortTitle = _p('videochannel.most_discussed');
                break;
        }
        // in page 2, it load ajax and no need to load slider
        $page =  $this->request()->get('page');
        if ($page > 1)
        {
            $aTopViewed = false;
            $sSortTitle=false;
        }else
        {
            $page = 1;
        }
        $error_folder = false;
        if (phpfox::isAdmin())
        {
            $folderVideo = PHPFOX_DIR ."file". PHPFOX_DS ."pic". PHPFOX_DS ."video";
            if (!file_exists($folderVideo))
            {
                $error_folder = "please create folder PF.Base/file/pic/video and mod 777";
            }
        }

        $arAssign = array(
            'sLinkPendingVideos' => $this->url()->makeUrl('videochannel.pending'),
            'sView' => $sView,
            'sPublicPhotoView' => $sView,
            'sJs' => $sJs,
            'bCanAddChannelInPage' => $bCanAddChannelInPage,
            'sModuleId' => ($aParentModule['module_id']) ? ($aParentModule['module_id']) : 'videochannel',
            'iItem' => ($aParentModule['item_id']) ? $aParentModule['item_id'] : 0,
            'sImageOnError' => $sImageOnError,
            'aSlideShowVideos' => $aTopViewed,
            'sCorePath' => Phpfox::getParam('core.path'),
            'sSortTitle' => $sSortTitle,
            'bIsUserProfile' => $bIsUserProfile,
            'error_folder'=>$error_folder,
            'current_page'=>$page
        );

        $this->template()->assign($arAssign);

        $this->_buildSectionMenu($sModule,$aPage);

        if ($sCategory !== null)
        {
            $aCategories = Phpfox::getService('videochannel.category')->getParentBreadcrumb($sCategory);

            $iCnt = 0;
            if (count($aCategories)) {
                foreach ($aCategories as $aCategory)
                {
                    $iCnt++;

                    $this->template()->setTitle($aCategory[0]);

                    // Get the link for category.
                    $strLink = '';
                    if ($this->request()->get('req1') == 'pages' && $this->request()->get('req3') == 'videochannel' && $this->request()->get('req4') == 'category')
                    {
                        $strLink = $this->url()->makeUrl('pages', array($this->request()->getInt('req2'), 'videochannel', 'category', $aCategory[2], $this->url()->cleanTitle($aCategory[0])));
                    }
                    else
                    {
                        $strLink = $aCategory[1];
                    }

                    $this->template()->setBreadcrumb($aCategory[0], $strLink, ($iCnt === count($aCategories) ? true : false));
                }
            } else {
                $this->template()->setBreadCrumb(_p('Un-Categories'), 'current', true);
            }
        }

        foreach ((array) $this->search()->browse()->getRows() as $aVideo)
        {
            $this->template()->setMeta('keywords', $this->template()->getKeywords($aVideo['title']));
        }

        if (!empty($sTagSearchValue))
        {
            $this->template()->setBreadcrumb(_p('videochannel.topic') . ': ' . $sTagSearchValue, $this->url()->makeUrl('videochannel.tag', $sTagSearchValue), true);
        }

        Phpfox::getLib('pager')->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()));
    }

    protected function redirect()
    {
        if (($iRedirectId = $this->request()->getInt('redirect'))
                && ($aVideo = Phpfox::getService('videochannel')->getVideo($iRedirectId, true))
                && $aVideo['module_id'] != 'videochannel'
                && Phpfox::hasCallback($aVideo['module_id'], 'getVideoRedirect')
        )
        {
            if (($sForward = Phpfox::callback($aVideo['module_id'] . '.getVideoRedirect', $aVideo['video_id'])))
            {
                $this->url()->forward($sForward);
            }
        }

        if (($iRedirectId = $this->request()->getInt('redirect')) && ($aVideo = Phpfox::getService('videochannel')->getVideo($iRedirectId, true)))
        {
            $this->url()->send($aVideo['user_name'], array('videochannel', $aVideo['title_url']));
        }
    }

    protected function delete()
    {
        if (($iDeleteId = $this->request()->getInt('delete')))
        {
            if (Phpfox::getService('videochannel.process')->delete($iDeleteId))
            {
                $this->url()->send('videochannel', null, _p('videochannel.video_successfully_deleted'));
            }
        }
    }

    protected function _checkLegacyItem()
    {
        if ($this->request()->get('req2') == 'category' && ($sLegacyTitle = $this->request()->get('req3')) && !is_numeric($sLegacyTitle) && !empty($sLegacyTitle))
        {
            $arLegacyItem = array(
                'field' => array('category_id', 'name'),
                'table' => 'channel_category',
                'redirect' => 'videochannel.category',
                'title' => $sLegacyTitle,
                'search' => 'name_url'
            );
            $aLegacyItem = Phpfox::getService('core')->getLegacyItem($arLegacyItem);
        }
    }

    protected function _buildSectionMenu($sModule,$aPage)
    {
        $aFilterMenu = array();

        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW'))
        {
            $aFilterMenu = array(
                _p('videochannel.all_videos') => '',
                _p('videochannel.my_videos') => 'my',
                _p('videochannel.my_favorites') => 'favorite'
            );

            if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community'))
            {
                $aFilterMenu[_p('videochannel.friends_videos')] = 'friend';
            }

            list($iTotalFeatured, $aFeatured) = Phpfox::getService('videochannel')->getFeatured();

            if ($iTotalFeatured)
            {
                $aFilterMenu[_p('videochannel.featured_videos') . '<span class="count-item">' . $iTotalFeatured . '</span>'] = 'featured';
            }

            if (Phpfox::getUserParam('videochannel.can_approve_videos'))
            {
                $iPendingTotal = Phpfox::getService('videochannel')->getPendingTotal();

                if ($iPendingTotal)
                {
                    $aFilterMenu[_p('videochannel.pending') . (Phpfox::getUserParam('videochannel.can_approve_videos') ? '<span class="count-item">' . $iPendingTotal . '</span>' : 0)] = 'pending';
                }
            }

            if (Phpfox::getUserParam('videochannel.can_add_channels'))
            {
                $aFilterMenu[_p('videochannel.my_channels')] = 'channels';
            }

            if (Phpfox::isAdmin())
            {
                $aFilterMenu[_p('videochannel.all_channels')] = 'all_channels';
            }

            $this->template()->buildSectionMenu('videochannel', $aFilterMenu);
        }
        elseif(defined('PHPFOX_IS_PAGES_VIEW')){
            if ($this->request()->get('req3') == 'videochannel' || $this->request()->get('req2') == 'videochannel')
            {
                $aFilterMenu[_p('videochannel.all_videos')] = '';
                $aFilterMenu[_p('videochannel.my_videos')] = 'my';

                $aFilterMenu[_p('videochannel.my_favorites')] = 'favorite';

                if (Phpfox::isAdmin())
                {
                    $aFilterMenu[_p('videochannel.my_channels')] = 'channels';

                    $aFilterMenu[_p('videochannel.all_channels')] = 'all_channels';
                }
                else
                {
                    if (Phpfox::getUserId() == $aPage['user_id'] && Phpfox::getUserParam('videochannel.can_add_channel_on_page'))
                    {
                        $aFilterMenu[_p('videochannel.my_channels')] = 'channels';

                        $aFilterMenu[_p('videochannel.all_channels')] = 'all_channels';
                    }
                }
            }
            $this->template()->buildSectionMenu($sModule.'.'.$aPage['page_id'].'.videochannel', $aFilterMenu);
        }
    }

    

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_index_clean')) ? eval($sPlugin) : false);
    }

}

?>

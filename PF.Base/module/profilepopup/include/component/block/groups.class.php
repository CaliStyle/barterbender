<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_IS_PAGES_VIEW', true);
/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
class ProfilePopup_Component_Block_Groups extends Phpfox_Component
{

        /**
         * Class process method wnich is used to execute this component.
         */
        public function process()
        {

                //      get parameters
                $sM = $this->request()->get('m');
                $sModule = $this->request()->get('module');
                $sName = $this->request()->get('name');
                $sMatchType = $this->request()->get('match_type');
                $sMatchID = trim($this->request()->get('match_id'), '/');
                $sMatchName = $this->request()->get('match_name');

                //      init
                $oProfilePopup = Phpfox::getService('profilepopup');
                $oUser = Phpfox::getService('user');

                //      check groups exist
            $iIsGroups = 1;
                $aGroup = Phpfox::getService('groups')->getForView($sMatchID);
                if($aGroup['parent_category_name']) {
                    $aGroup['category_name'] = _p($aGroup['parent_category_name']) . ($aGroup['category_name'] ? '/' . _p($aGroup['category_name']) : '');
                }
                if (!($aGroup) || $aGroup['view_id'] != '0' || $aGroup['view_id'] == '2')
                {
                        $this->template()->assign(array(
                                'iIsGroups' => $iIsGroups
                                )
                        );

                        return;
                }

                //       get page in User table
                $aUser = $oUser->getByUserName($sMatchName);

                //      check can view groups
                $iIsCanView = 1;
                $bCanViewGroup = true;
                if (Phpfox::getUserBy('profile_page_id') > 0)
                {
                        //      pass check privacy settings
                } else if (Phpfox::getUserBy('profile_page_id') <= 0 && Phpfox::isModule('privacy'))
                {
                        $bCanViewGroup = Phpfox::getService('privacy')->check('groups', $aGroup['page_id'], $aGroup['user_id'], $aGroup['privacy'], (isset($aGroup['is_friend']) ? $aGroup['is_friend'] : 0), true);
                        if (!$bCanViewGroup)
                        {
                                $iIsCanView = 0;
                        }
                } else
                {
                        $iIsCanView = 0;
                }

                if ($iIsCanView == 0)
                {
                        $this->template()->assign(array(
                                'iIsGroups' => $iIsGroups,
                                'iIsCanView' => $iIsCanView,
                                'aGroup' => $aGroup
                                )
                        );

                        return;
                }

                //      check privacy
                if (isset($aGroup['is_admin']) && $aGroup['is_admin'])
                {
                        define('PHPFOX_IS_PAGE_ADMIN', true);
                }

                //      get permission
                $sShowJoinedFriend = Phpfox::getParam('profilepopup.show_joined_friend_in_groups') ? '1' : '0';
                $iNumberOfJoinedFriend = intval(Phpfox::getParam('profilepopup.number_of_joined_friend_in_groups'));

                //      get popup item
                $aAllItems = array();
                $aAllItems = $oProfilePopup->getAllItems(null, 'groups');
                $iLen = count($aAllItems);
				$showCoverPhoto = false;
                for ($idx = 0; $idx < $iLen; $idx++)
                {
                	// check show cover photo
                	if($aAllItems[$idx]['name'] == 'cover_photo'&& $aAllItems[$idx]['is_display'] == 1){
                		$showCoverPhoto = true;
                	}
					
                        //      language name
                        $aAllItems[$idx]['lang_name'] = _p('profilepopup.' . $aAllItems[$idx]['phrase_var_name']);
                }

                $iJoinedFriendTotal = 0;
                $aJoinedFriend = array();
                if ($sShowJoinedFriend === '1')
                {
                        list($iJoinedFriendTotal, $aJoinedFriend) = $oProfilePopup->getJoinedFriendInGroups($aGroup['page_id'], $iNumberOfJoinedFriend);
                }

                $iShorten = intval(Phpfox::getParam('profilepopup.profilepopup_length_in_index'));

                //      integrate with Fox Favorite
                if (Phpfox::isModule('foxfavorite') && Phpfox::isUser())
                {
                        $sFFModule = 'pages';
                        $iFFItemId = $aGroup['page_id'];
                        $iFFViewId = phpfox::getUserBy('view_id');

                        $bFFPass = true;
                        if (!Phpfox::getService('foxfavorite')->isAvailModule($sFFModule) || empty($aGroup) || $iFFViewId != 0)
                        {
                                $bFFPass = false;
                        }

                        if ($bFFPass === true)
                        {
                                $bFFIsAlreadyFavorite = Phpfox::getService('foxfavorite')->isAlreadyFavorite($sFFModule, $aGroup['page_id']);
                                $this->template()->assign(array(
                                        'bFFIsAlreadyFavorite' => $bFFIsAlreadyFavorite,
                                        'sFFModule' => $sFFModule,
                                        'iFFItemId' => $iFFItemId
                                        )
                                );
                        }
                }
				//	get cover photo
				if(Phpfox::isModule('photo') && isset($aGroup['cover_photo_id']) && $showCoverPhoto == true)
				{
					$aCoverPhoto = Phpfox::getService('photo')->getCoverPhoto($aGroup['cover_photo_id']);
					if (!isset($aCoverPhoto['photo_id']))
					{
						$aCoverPhoto = null;
					}
					
					if(null != $aCoverPhoto)
					{
		                $this->template()->assign(array(
		                        'aCoverPhoto' => $aCoverPhoto
		                        )
	                	);
					}
				}

                $this->template()->assign(array(
                        'iIsGroups' => $iIsGroups,
                        'iIsCanView' => $iIsCanView,
                        'aGroup' => $aGroup,
                        'aAllItems' => $aAllItems,
                        'sShowJoinedFriend' => $sShowJoinedFriend,
                        'iNumberOfJoinedFriend' => $iNumberOfJoinedFriend,
                        'iJoinedFriendTotal' => $iJoinedFriendTotal,
                        'aJoinedFriend' => $aJoinedFriend,
                        'sBookmarkType' => 'pages',
                        'sBookmarkUrl' => urlencode($aGroup['link']),
                        'sBookmarkTitle' => urlencode($aGroup['title']),
                        'sBookmarkDisplay' => 'menu',
                        'bIsFirstLink' => $this->getParam('first'),
                        'sFeedShareId' => $aGroup['page_id'],
                        'sShareModuleId' => 'pages',
                        'bEnableCachePopup' => Phpfox::getParam('profilepopup.enable_cache_popup'),
                        'iShorten' => $iShorten,
                        'coverPhotoPosition' => $aGroup['cover_photo_position']*0.3865
                        )
                );
                //      end
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {
                (($sPlugin = Phpfox_Plugin::get('profilepopup.component_block_pages_clean')) ? eval($sPlugin) : false);
        }

}


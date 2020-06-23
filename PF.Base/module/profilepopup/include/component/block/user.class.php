<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
class ProfilePopup_Component_Block_User extends Phpfox_Component
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
                $oUser = Phpfox::getService('user');
                $oProfilePopup = Phpfox::getService('profilepopup');
                $iNumberOfMutualFriend = intval(Phpfox::getParam('profilepopup.number_of_mutual_friend'));
                $iNumberOfMutualFriend = $iNumberOfMutualFriend < 0 ? 0 : $iNumberOfMutualFriend;

                $aUser = $oProfilePopup->getByUserName($sMatchName);

                $sUserProfileImage = "";
                $isFBUser = false;
                $aUserImage = json_decode($aUser['user_image'],true);
                if($aUserImage && isset($aUserImage['fb']))
                {
                    $isFBUser = true;
                    $sUserProfileImage = '<img data-object="fb" data-src="'.$aUserImage['fb'].'" src="//graph.facebook.com/'.$aUserImage['fb'].'/picture?type=square&width=100&height=100" class="_image__120_square image_object  fb_built">'; 
                }
                $this->template()->assign(array(
                        'isFBUser' => $isFBUser,
                        'sUserProfileImage' => $sUserProfileImage,
                        ));
                
                $iIsUser = 1;
                if (isset($aUser['user_id']) === false)
                {
                        $this->template()->assign(array(
                                'iIsUser' => $iIsUser
                                )
                        );

                        return;
                }

                $bIsPage = ($aUser['profile_page_id'] > 0 ? true : false);
                if ($bIsPage)
                {
                        $aUser['page'] = Phpfox::getService('pages')->getPage($aUser['profile_page_id']);
                }

                //      check basic information viewing permission
                $iIsCanViewBasicInfo = 1;
                if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'profile.basic_info'))
                {
                        $iIsCanViewBasicInfo = 0;
                }
                $iIsCanViewProfileInfo = 1;
                if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'profile.profile_info'))
                {
                        $iIsCanViewProfileInfo = 0;
                }
                $iIsCanViewMutualFriends = 1;
                if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'profile.view_friend'))
                {
                        $iIsCanViewMutualFriends = 0;
                }
                $iIsCanViewLocation = 1;
                if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'friend.view_location'))
                {
                        $iIsCanViewLocation = 0;
                }

                $aUser['birthday_time_stamp'] = $aUser['birthday'];
                $aUser['birthday'] = $oUser->age($aUser['birthday']);

                if(!empty($aUser['gender'])) {
                    if($aUser['gender'] == 127 && !empty($aUser['custom_gender'])) {
                        $customGenders = unserialize($aUser['custom_gender']);
                        if(is_array($customGenders)) {
                            if(count($customGenders) > 2)
                            {
                                $sLastGender = $customGenders[count($customGenders) - 1];
                                unset($customGenders[count($customGenders) - 1]);
                                $aUser['gender_name'] = implode(', ', $customGenders). ' '. _p('and') .' '. $sLastGender;
                            }
                            else
                            {
                                $aUser['gender_name'] = implode(' '. _p('and'). ' ', $customGenders);
                            }
                            $aUser['gender_name'] = Phpfox::getLib('parse.output')->clean($aUser['gender_name']);
                        }
                    }
                    else {
                        $aUser['gender_name'] = $oUser->gender($aUser['gender']);
                    }
                }

                $aUser['birthdate_display'] = $oUser->getProfileBirthDate($aUser);
                $aUser['location'] = _p(Phpfox::getService('core.country')->getCountry($aUser['country_iso']), 'country');
                if (isset($aUser['country_child_id']) && $aUser['country_child_id'] > 0)
                {
                        $aUser['location_child'] = Phpfox::getService('core.country')->getChild($aUser['country_child_id']);
                }

                $aUser['is_friend'] = false;
                $iTotal = 0;
                $aMutual = array();
                if ($aUser['user_id'] != Phpfox::getUserId() && !$bIsPage)
                {
                        if (Phpfox::isUser())
                        {
                                $aUser['is_friend'] = Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $aUser['user_id']);
                                if (!$aUser['is_friend'])
                                {
                                        $aUser['is_friend'] = (Phpfox::getService('friend.request')->isRequested(Phpfox::getUserId(), $aUser['user_id']) ? 2 : false);
                                }
                        }

                        list($iTotal, $aMutual) = Phpfox::getService('friend')->getMutualFriends($aUser['user_id'], $iNumberOfMutualFriend);
                }

                $bShowBDayInput = false;
                if (!empty($aUser['birthday']))
                {
                        $iDays = Phpfox::getLib('date')->daysToDate($aUser['birthday'], null, false);
                } else
                {
                        $iDays = 999;
                }

                if ($iDays < 1 && $iDays > 0)
                {
                        $bShowBDayInput = true;
                }
                (($sPlugin = Phpfox_Plugin::get('profilepopup.component_block_popup_1')) ? eval($sPlugin) : false);

                //      get latest status
                $aAllItems = $oProfilePopup->getAllItems();
                $aDataUserCustomField = $oProfilePopup->getDataUserCustomFieldByUserID(intval($aUser['user_id']));
                $aDataUserCutomFieldMutipleValue = $oProfilePopup->getDataUserCutomFieldMutipleValueByUserID(intval($aUser['user_id']));
                $iLen = count($aAllItems);
				$showCoverPhoto = false;

                for ($idx = 0; $idx < $iLen; $idx++)
                {
                	// check show cover photo
                	if($aAllItems[$idx]['name'] == 'cover_photo' && $aAllItems[$idx]['is_display'] == 1){
                		$showCoverPhoto = true;
                	}
                    //      language name
                    $aAllItems[$idx]['lang_name'] = '';
                    if (intval($aAllItems[$idx]['is_custom_field']) == 1)
                    {
                        if(Phpfox::getLib('locale')->isPhrase($aAllItems[$idx]['phrase_var_name'])){
                            $aAllItems[$idx]['lang_name'] = _p($aAllItems[$idx]['phrase_var_name']);
                        } else {
                            $aAllItems[$idx]['is_display'] = 0;
                        }
                    } else
                    {
                            $aAllItems[$idx]['lang_name'] = _p('profilepopup.' . $aAllItems[$idx]['phrase_var_name']);
                    }

                    //      custom field with content
                    $aAllItems[$idx]['cf_content'] = '';
                    if (isset($aDataUserCustomField['cf_' . $aAllItems[$idx]['name']]) === true)
                    {
                            $aAllItems[$idx]['cf_content'] = $aDataUserCustomField['cf_' . $aAllItems[$idx]['name']];
                    }

                    //      custom field with mutiple value
                    foreach ($aDataUserCutomFieldMutipleValue as $iKey => $sVal)
                    {
                            if ($aAllItems[$idx]['name'] == $sVal['field_name'])
                            {
                                    $aAllItems[$idx]['cf_content'] .= _p($sVal['phrase_var_name']) . ', ';
                                    //break;
                            }
                    }
                    $aAllItems[$idx]['cf_content'] = rtrim($aAllItems[$idx]['cf_content'], ", ");
                }

                $aStatus = $oProfilePopup->getLatestStatusByUserID(intval($aUser['user_id']));

                $aRelationshipStatus = $oProfilePopup->getRelationshipStatusByUserID(intval($aUser['user_id']));

                if (isset($aRelationshipStatus) && is_array($aRelationshipStatus) === true && count($aRelationshipStatus) > 0)
                {
                        $aRelationshipStatus['lang_name'] = _p($aRelationshipStatus['phrase_var_name']);
                } else
                {
                        $aRelationshipStatus = array();
                }

                $iShorten = intval(Phpfox::getParam('profilepopup.profilepopup_length_in_index'));
                $sShowMutualFriend = Phpfox::getParam('profilepopup.show_mutual_friend') ? '1' : '0';

                //      update firstname and lastname
                if (Phpfox::getParam('user.split_full_name') && empty($aUser['first_name']) && empty($aUser['last_name']))
                {
                        preg_match('/(.*) (.*)/', $aUser['full_name'], $aNameMatches);
                        if (isset($aNameMatches[1]) && isset($aNameMatches[2]))
                        {
                                $aUser['first_name'] = $aNameMatches[1];
                                $aUser['last_name'] = $aNameMatches[2];
                        } else
                        {
                                $aUser['first_name'] = $aUser['full_name'];
                        }
                }

                //      integrate with Fox Favorite
                if (Phpfox::isModule('foxfavorite') && Phpfox::isUser())
                {
                        $sFFModule = 'profile';
                        $iFFItemId = $aUser['user_name'];
                        $iFFUserId = $aUser['user_id'];

                        $bFFPass = true;
                        if (!Phpfox::getService('foxfavorite')->isAvailModule($sFFModule)
                                || $iFFUserId == Phpfox::getUserId()
                                || empty($iFFUserId)
                                || (Phpfox::getUserBy('view_id') != 0))
                        {
                                $bFFPass = false;
                        }

                        if ($bFFPass === true)
                        {
                                $bFFIsAlreadyFavorite = Phpfox::getService('foxfavorite')->isAlreadyFavorite($sFFModule, $iFFItemId);
                                $this->template()->assign(array(
                                        'bFFIsAlreadyFavorite' => $bFFIsAlreadyFavorite,
                                        'sFFModule' => $sFFModule,
                                        'iFFItemId' => $iFFItemId
                                        )
                                );
                        }
                }

				// Resume Module
				if($oProfilePopup->canViewResumeByUserID(intval(Phpfox::getUserId()), $aUser['user_id']) == true){
					$aResumeItems = $oProfilePopup->getItemsByModule(1, 'user', 'resume');
					$iResumeLen = count($aResumeItems);
					$oneItemResumeIsDisplay = '0';
					$aResume = $oProfilePopup->getPublishedResumeByUserID($aUser['user_id']);
					$iResumeId = $aResume['resume_id'];
					$aResume = Phpfox::getService("resume.basic")->getBasicInfo($iResumeId);
					
					for ($idx = 0; $idx < $iResumeLen; $idx++){
						if($aResumeItems[$idx]['is_display'] == 1){
							$oneItemResumeIsDisplay = '1';
						}
                        //      language name
                        $aResumeItems[$idx]['lang_name'] = '';
                        if (intval($aResumeItems[$idx]['is_custom_field']) == 1)
                        {
                            if(Phpfox::getLib('locale')->isPhrase($aResumeItems[$idx]['phrase_var_name'])){
                                $aResumeItems[$idx]['lang_name'] = _p($aResumeItems[$idx]['phrase_var_name']);
                            } else {
                                $aResumeItems[$idx]['is_display'] = 0;
                            }                                
                            
                        } else
                        {
                            $aResumeItems[$idx]['lang_name'] = _p('profilepopup.' . $aResumeItems[$idx]['phrase_var_name']);
                        }
					}
					
					$aCurrentWork = Phpfox::getService('resume.experience')->getCurrentWork($iResumeId);
					$aResume['level_name'] = Phpfox::getService('resume.level')->getLevelById($aResume['level_id']);
					$aLatestEducation = Phpfox::getService('resume.education')->getLatestEducation($iResumeId);
					$aCats = Phpfox::getService('resume.category')->getCatNameList($iResumeId);
					$catPlainText = '';
					foreach($aCats as $k => $v){
						if($k == 0){
							$catPlainText .= $v['name']; 							
						} else {
							$catPlainText .= ' | ' . $v['name'];
						}						
					}
					
	                $this->template()->assign(array(
	                        'canViewResume' => '1',
	                        'aResumeItems' => $aResumeItems,
	                        'oneItemResumeIsDisplay' => $oneItemResumeIsDisplay,
	                        'aResume' => $aResume,
	                        'aCurrentWork' => $aCurrentWork,
	                        'aLatestEducation' => $aLatestEducation,
	                        'aCats' => $aCats,
	                        'catPlainText' => $catPlainText,
	                        )
	                );
				} else {
	                $this->template()->assign(array(
	                        'canViewResume' => '0'
	                        )
	                );
				}
				
				//	get cover photo
				if(Phpfox::isModule('photo') && isset($aUser['cover_photo']) && $showCoverPhoto == true)
				{
					$aCoverPhoto = Phpfox::getService('photo')->getCoverPhoto($aUser['cover_photo']);
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

                //      check view profile permission
                $aFriend = $oProfilePopup->getFriendByUserIDAndFriendID(intval(Phpfox::getUserId()), intval($aUser['user_id']));
                if (
                        (Phpfox::getService('user.block')->isBlocked($aUser['user_id'], Phpfox::getUserId()) && !Phpfox::getUserParam('user.can_override_user_privacy'))
                        || (
                        ((Phpfox::isModule('friend') && Phpfox::getParam('friend.friends_only_profile')) )
                        && empty($aUser['is_friend'])
                        && !Phpfox::getUserParam('user.can_override_user_privacy')
                        && $aUser['user_id'] != Phpfox::getUserId()
                        )
                        || (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'profile.view_profile'))
                )
                {
                        $this->template()->assign(array(
                                'iIsCanViewProfile' => 0,
                                'bIsPage' => $bIsPage,
                                'iIsUser' => $iIsUser,
                                'aFriend' => $aFriend,
                                'aUser' => $aUser
                                )
                        );

                        return;
                }

                $bIsMarried = false;
                if ($aRelationshipStatus['lang_name'] == 'Married') {
                    $sRelationship = Phpfox::getService('custom')->getRelationshipPhrase($aUser);
                    $bIsMarried = true;
                }

                $bShowLine = false;
                if ($aUser['gender_name'] || $aUser['birthdate_display'] || $aUser['cf_content'] || $aStatus || (isset($aRelationshipStatus) === true && Phpfox::getParam('user.enable_relationship_status') && isset($aRelationshipStatus['lang_name']) && strlen(trim($aRelationshipStatus['lang_name'])) > 0)) {
                    $bShowLine = true;
                }
				
                $this->template()->assign(array(
                        'bIsPage' => $bIsPage,
                        'iIsUser' => $iIsUser,
                        'iIsCanViewProfile' => 1,
                        'iIsCanViewBasicInfo' => $iIsCanViewBasicInfo,
                        'iIsCanViewProfileInfo' => $iIsCanViewProfileInfo,
                        'iIsCanViewMutualFriends' => $iIsCanViewMutualFriends,
                        'iIsCanViewLocation' => $iIsCanViewLocation,
                        'aUser' => $aUser,
                        'sRelationship' => $sRelationship,
                        'bShowLine' => $bShowLine,
                        'bIsMarried' => $bIsMarried,
                        'aAllItems' => $aAllItems,
                        'aStatus' => $aStatus,
                        'aRelationshipStatus' => $aRelationshipStatus,
                        'aFriend' => $aFriend,
                        'iShorten' => $iShorten,
                        'sShowMutualFriend' => $sShowMutualFriend,
                        'iNumberOfMutualFriend' => $iNumberOfMutualFriend,
                        'iMutualTotal' => $iTotal,
                        'aMutualFriends' => $aMutual,
                        'bEnableCachePopup' => Phpfox::getParam('profilepopup.enable_cache_popup'),
                        'bShowBDay' => $bShowBDayInput,
                        'coverPhotoPosition' => (int)$aUser['cover_photo_top']*0.3865
                        )
                );
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {
                (($sPlugin = Phpfox_Plugin::get('profilepopup.component_block_user_clean')) ? eval($sPlugin) : false);
        }

}


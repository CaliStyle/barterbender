<?php

namespace Apps\Core_Pages\Service;

use Core;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Request;
use Phpfox_File;
use Phpfox_Image;

defined('PHPFOX') or exit('NO DICE!');

class Process extends \Phpfox_Pages_Process
{

    public function updateProfilePictureForThumbnail($iPage, $sNewPath)
    {
        db()->update($this->_sTable,['image_path' => $sNewPath],'page_id = '.$iPage);
    }

    /**
     * @return Facade|object
     */
    public function getFacade()
    {
        return \Phpfox::getService('pages.facade');
    }

    /**
     * @param $iPageId
     * @return bool|int
     */
    public function addPageClaim($iPageId)
    {
        if(empty($iPageId))
        {
            return false;
        }
        return db()->insert(Phpfox::getT('pages_claim'), array('status_id' => '1', 'page_id' => ((int)$iPageId), 'user_id' => Phpfox::getUserId(), 'time_stamp' => PHPFOX_TIME));
    }

    /**
     * @param $iPageId
     * @param null $sPath
     * @return bool
     */
    public function updateUserImageAndPhotoProfileForProcessCrop($iPageId, $sPath = null)
    {
        if(empty($sPath))
        {
            return false;
        }

        $oFile = Phpfox_File::instance();
        $oImage = Phpfox_Image::instance();

        $iGroupUserId = Phpfox::getService('pages')->getUserId($iPageId);

        $sUserImage = $this->database()->select('user_image')
            ->from(Phpfox::getT('user'))
            ->where('user_id = ' . (int) $iGroupUserId)
            ->execute('getSlaveField');

        if (!empty($sUserImage))
        {
            if (file_exists(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '')))
            {
                $oFile->unlink(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, ''));
                foreach(Phpfox::getService('user')->getUserThumbnailSizes() as $iSize)
                {
                    if (file_exists(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '_' . $iSize)))
                    {
                        $oFile->unlink(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '_' . $iSize));
                    }

                    if (file_exists(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '_' . $iSize . '_square')))
                    {
                        $oFile->unlink(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '_' . $iSize . '_square'));
                    }
                }
            }
        }

        $sFileName =  md5($iGroupUserId . PHPFOX_TIME . uniqid()) . '%s.' . substr($sPath, -3);
        $sTo = Phpfox::getParam('core.dir_user') . sprintf($sFileName,'');

        if (file_exists($sTo))
        {
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', filesize($sTo), '-');
            $oFile->unlink($sTo);
        }

        $mReturn = Phpfox_Request::instance()->send($sPath, array(), 'GET');
        $hFile = @fopen($sTo, 'w');
        @fwrite($hFile, $mReturn);
        @fclose($hFile);

        if(filesize($sTo) > 0)
        {
            $bReturn = Phpfox::getLib('cdn')->put($sTo);
        }
        else
        {
            $oFile->unlink($sTo);
            $oFile->copy($sPath, $sTo);
            $bReturn = Phpfox::getLib('cdn')->put($sTo);
        }

        $sTo = Phpfox::getParam('core.dir_user') . sprintf($sFileName,'');

        if (file_exists($sTo)) {
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', filesize($sTo));
        }


        $iServerId = Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID');

        foreach(Phpfox::getService('user')->getUserThumbnailSizes() as $iSize)
        {
            if (Phpfox::getParam('core.keep_non_square_images'))
            {
                $oImage->createThumbnail(Phpfox::getParam('core.dir_user') . sprintf($sFileName, ''), Phpfox::getParam('core.dir_user') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
            }

            $oImage->createThumbnail(Phpfox::getParam('core.dir_user') . sprintf($sFileName, ''), Phpfox::getParam('core.dir_user') . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
        }
        if (Phpfox::isAppActive('Core_Photos')){
            $iMaxWidth = (int) Phpfox::getUserParam('photo.maximum_image_width_keeps_in_server');
            list($width, $height, $type, $attr) = getimagesize(Phpfox::getParam('core.dir_user') . sprintf($sFileName, ''));
            if ($iMaxWidth < $width){
                $oImage->createThumbnail(Phpfox::getParam('core.dir_user') . sprintf($sFileName, ''), Phpfox::getParam('core.dir_user') . sprintf($sFileName, ''), $iMaxWidth, $height);
            }
        }
        $this->database()->update(Phpfox::getT('user'), array('user_image' => $sFileName, 'server_id' => $iServerId), 'user_id = ' . (int) $iGroupUserId);

        if (Phpfox::isAppActive('Core_Photos'))
        {
            $aProfileImage = db()->select('photo_id, destination')
                ->from(Phpfox::getT('photo'))
                ->where('module_id = "pages" AND group_id = '. (int)$iPageId . ' AND is_cover = 1 AND is_profile_photo = 1')
                ->execute('getSlaveRow');
            if(!empty($aProfileImage))
            {
                $sExtension = pathinfo($sTo, PATHINFO_EXTENSION);
                $sNewPhotoPath = md5($aProfileImage['photo_id']) . '%s.' . $sExtension;

                $oFile->unlink(Phpfox::getParam('photo.dir_photo') . sprintf($aProfileImage['destination'], ''));
                @copy($sTo, Phpfox::getParam('photo.dir_photo') . sprintf($sNewPhotoPath, ''));

                //push to cdn
                Phpfox::getLib('cdn')->put(Phpfox::getParam('photo.dir_photo') . sprintf($sNewPhotoPath, ''));

                foreach (Phpfox::getService('photo')->getPhotoPicSizes() as $iSize) {
                    $oFile->unlink(Phpfox::getParam('photo.dir_photo') . sprintf($aProfileImage['destination'], '_' . $iSize));
                    $oImage->createThumbnail(Phpfox::getParam('photo.dir_photo') . sprintf($sNewPhotoPath, ''),
                        Phpfox::getParam('photo.dir_photo') . sprintf($sNewPhotoPath, '_' . $iSize), $iSize, $iSize, true,
                        false);
                }
                db()->update(Phpfox::getT('photo'), array('destination' => $sNewPhotoPath),
                    'photo_id = ' . (int)$aProfileImage['photo_id']);
            }
        }
    }

    public function addWidget($aVals, $iEditId = null)
    {
        $aPage = $this->getFacade()->getItems()->getPage($aVals['page_id']);

        if (!isset($aPage['page_id'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_looking_for'));
        }

        $bCanModerate = $this->getFacade()->getUserParam('can_approve_pages') || $this->getFacade()->getUserParam('can_edit_all_pages') || $this->getFacade()->getUserParam('can_delete_all_pages');

        if (!$this->getFacade()->getItems()->isAdmin($aPage) && !$bCanModerate) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_add_a_widget_to_this_page'));
        }

        if (empty($aVals['title'])) {
            Phpfox_Error::set($this->getFacade()->getPhrase('provide_a_title_for_your_widget'));
        }

        // parse content, remove script
        $aVals['text'] = preg_replace('/<script.*<\/script>/', '', $aVals['text']);

        if (empty($aVals['text'])) {
            Phpfox_Error::set($this->getFacade()->getPhrase('provide_content_for_your_widget'));
        }

        if (!$aVals['is_block']) {
            if (empty($aVals['menu_title'])) {
                Phpfox_Error::set($this->getFacade()->getPhrase('provide_a_menu_title_for_your_widget'));
            }

            if (empty($aVals['url_title'])) {
                Phpfox_Error::set($this->getFacade()->getPhrase('provide_a_url_title_for_your_widget'));
            }
        }

        if (Phpfox::isModule($aVals['url_title'])) {
            Phpfox_Error::set($this->getFacade()->getPhrase('you_cannot_use_this_url_for_your_widget'));
        }

        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        $oFilter = Phpfox::getLib('parse.input');

        if ($iEditId !== null) {
            $sNewTitle = $this->database()->select('url_title')
                ->from(Phpfox::getT('pages_widget'))
                ->where('widget_id = ' . (int)$iEditId)
                ->execute('getSlaveField');
        }

        if (!$aVals['is_block'] && (isset($sNewTitle) && ($sNewTitle != $aVals['url_title']))) {
            $sNewTitle = Phpfox::getLib('parse.input')->prepareTitle('pages', $aVals['url_title'], 'url_title',
                Phpfox::getUserId(), Phpfox::getT('pages_widget'),
                'page_id = ' . (int)$aPage['page_id'] . ' AND url_title LIKE \'%' . $aVals['url_title'] . '%\'');
        }

        //Check duplicate widget title_url
        if (!$aVals['is_block']) {
            if ($iEditId) {
                $sMoreConds = ' AND widget_id !=' . (int)$iEditId;
            } else {
                $sMoreConds = '';
            }
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(':pages_widget')
                ->where('page_id=' . (int)$aPage['page_id'] . ' AND url_title="' . $aVals['url_title'] . '"' . $sMoreConds)
                ->executeField();

            if ($iCnt) {
                return Phpfox_Error::set(_p("The Url title is exist."));
            }
        }
        $aSql = array(
            'page_id' => $aPage['page_id'],
            'title' => $aVals['title'],
            'is_block' => (int)$aVals['is_block'],
            'menu_title' => ($aVals['is_block'] ? null : $aVals['menu_title']),
            'url_title' => ($aVals['is_block'] ? null : (isset($sNewTitle) ? $sNewTitle : $aVals['url_title']))
        );

        if ($iEditId === null) {
            $aSql['time_stamp'] = PHPFOX_TIME;
            $aSql['user_id'] = Phpfox::getUserId();

            $iId = $this->database()->insert(Phpfox::getT('pages_widget'), $aSql);

            $this->database()->insert(Phpfox::getT('pages_widget_text'), array(
                    'widget_id' => $iId,
                    'text' => $oFilter->clean($aVals['text']),
                    'text_parsed' => $oFilter->prepare($aVals['text'])
                )
            );
        } else {
            $this->database()->update(Phpfox::getT('pages_widget'), $aSql, 'widget_id = ' . (int)$iEditId);
            $this->database()->update(Phpfox::getT('pages_widget_text'), array(
                'text' => $oFilter->clean($aVals['text']),
                'text_parsed' => $oFilter->prepare($aVals['text'])
            ), 'widget_id = ' . (int)$iEditId
            );

            $iId = $iEditId;
        }

        return $iId;
    }

    /**
     * Update page
     *
     * @param $iId
     * @param $aVals
     * @param $aPage
     * @return bool
     * @throws \Exception
     */
    public function update($iId, $aVals, $aPage)
    {
        if (!$this->_verify($aVals)) {
            return false;
        }
        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_process_update_0')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }

        $aUser = $this->database()->select('user_id')
            ->from(Phpfox::getT('user'))
            ->where('profile_page_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aUpdate = array(
            'type_id' => (isset($aVals['type_id']) ? (int)$aVals['type_id'] : '0'),
            'category_id' => (isset($aVals['category_id']) ? (int)$aVals['category_id'] : 0),
            'reg_method' => (isset($aVals['reg_method']) ? (int)$aVals['reg_method'] : 0),
            'privacy' => (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0)
        );

        /* Only store the location if the admin has set a google key or ipinfodb key. This input is not always available */
        if (Phpfox::getParam('core.google_api_key') && isset($aVals['location'])) {
            if (isset($aVals['location']['name'])) {
                $aUpdate['location_name'] = $this->preParse()->clean($aVals['location']['name']);
            }
            if (isset($aVals['location']['latlng']) && $aVals['location']['latlng'] != '-43.132123,9.140625') {
                $aMatch = explode(',', $aVals['location']['latlng']);
                if (isset($aMatch[1])) {
                    $aUpdate['location_latitude'] = $aMatch[0];
                    $aUpdate['location_longitude'] = $aMatch[1];
                }
            }
        }

        if (isset($aVals['landing_page'])) {
            $aUpdate['landing_page'] = $aVals['landing_page'];
        }
        if (!empty($aVals['title'])) {
            $aUpdate['title'] = $this->preParse()->clean($aVals['title']);
        }

        // remove old image
        if (!empty($aPage['image_path']) && (!empty($aVals['temp_file']) || !empty($aVals['remove_photo'])) && $this->deleteImage($aPage)) {
            $aUpdate['image_path'] = null;
            $aUpdate['image_server_id'] = 0;

            if(!empty($aVals['remove_photo']))
            {
                $oFile = Phpfox_File::instance();
                $iPageUserId = Phpfox::getService('pages')->getUserId($iId);
                $sUserImage = $this->database()->select('user_image')
                    ->from(Phpfox::getT('user'))
                    ->where('user_id = ' . (int) $iPageUserId)
                    ->execute('getSlaveField');
                if (!empty($sUserImage))
                {
                    if (file_exists(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '')))
                    {
                        $oFile->unlink(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, ''));
                        foreach(Phpfox::getService('user')->getUserThumbnailSizes() as $iSize)
                        {
                            if (file_exists(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '_' . $iSize)))
                            {
                                $oFile->unlink(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '_' . $iSize));
                            }

                            if (file_exists(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '_' . $iSize . '_square')))
                            {
                                $oFile->unlink(Phpfox::getParam('core.dir_user') . sprintf($sUserImage, '_' . $iSize . '_square'));
                            }
                        }
                        $this->database()->update(Phpfox::getT('user'), array('user_image' => null, 'server_id' => 0), 'user_id = ' . (int) $iPageUserId);
                    }
                }
                storage()->del('user/avatar/' . $iPageUserId);
            }
        }

        if (!empty($aVals['temp_file'])) {
            // get image from temp file
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                if (!Phpfox::getService('user.space')->isAllowedToUpload($aPage['user_id'], $aFile['size'])) {
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);

                    return false;
                }
                $aUpdate['image_path'] = $aFile['path'];
                $aUpdate['image_server_id'] = $aFile['server_id'];
                $aUpdate['item_type'] = $this->getFacade()->getItemTypeId();
                Phpfox::getService('user.space')->update($aPage['user_id'], 'pages', $aFile['size']);
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
            // change profile image of page
            define('PHPFOX_PAGES_IS_IN_UPDATE', true);
            $iServerId = Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID');
            $sPath = Phpfox::getParam('pages.dir_image') . sprintf($aFile['path'], '');

            if (!empty($iServerId)) {
                $sPath = Phpfox::getLib('cdn')->getUrl(str_replace(PHPFOX_DIR, '', $sPath), $iServerId);
            }

            Phpfox::getService('user.process')->uploadImage($aUser['user_id'], true, $sPath);

            // add feed after updating page's profile image
            $iPageUserId = Phpfox::getService('pages')->getUserId($iId);
            if (Phpfox::isModule('feed') && $oProfileImage = storage()->get('user/avatar/' . $iPageUserId, null)) {
                Phpfox::getService('feed.process')->callback([
                    'table_prefix' => 'pages_',
                    'module' => 'pages',
                    'add_to_main_feed' => true,
                    'has_content' => true
                ])->add('pages_photo', $oProfileImage->value, 0, 0, $iId, $iPageUserId);
            }
        }

        $this->database()->update($this->_sTable, $aUpdate, 'page_id = ' . (int)$iId);

        $this->database()->update(Phpfox::getT('pages_text'), array(
            'text' => $this->preParse()->clean($aVals['text']),
            'text_parsed' => $this->preParse()->prepare($aVals["text"])
        ), 'page_id = ' . (int)$iId);

        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_process_update_1')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }

        $aCachedEmails = array();

        // Invite to page
        if ((isset($aVals['invite']) && is_array($aVals['invite'])) || (isset($aVals['emails']) && $aVals['emails'])) {
            // get invited friends, emails
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('pages_invite'))
                ->where('page_id = ' . (int)$iId)
                ->execute('getSlaveRows');
            $aInvited = array();
            foreach ($aInvites as $aInvite) {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
            }

            // invite friends
            if (isset($aVals['invite']) && is_array($aVals['invite'])) {
                $sUserIds = '';
                foreach ($aVals['invite'] as $iUserId) {
                    if (!is_numeric($iUserId)) {
                        continue;
                    }
                    $sUserIds .= $iUserId . ',';
                }
                $sUserIds = rtrim($sUserIds, ',');

                $aUsers = $this->database()->select('user_id, email, language_id, full_name')
                    ->from(Phpfox::getT('user'))
                    ->where('user_id IN(' . $sUserIds . ')')
                    ->execute('getSlaveRows');

                $sLink = $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url']);

                list(, $aMembers) = $this->getFacade()->getItems()->getMembers($aPage['page_id']);

                foreach ($aUsers as $aUser) {
                    if (in_array($aUser['user_id'], array_column($aMembers, 'user_id'))) {
                        continue;
                    }

                    if (isset($aCachedEmails[$aUser['email']])) {
                        continue;
                    }

                    if (isset($aInvited['user'][$aUser['user_id']])) {
                        continue;
                    }

                    $sMessage = _p('full_name_invited_you_to_the_page_title', [
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'title' => $aPage['title']
                    ], $aUser['language_id']);
                    $sMessage .= "\n" . _p('to_view_this_page_click_the_link_below_a_href_link_link_a',
                            ['link' => $sLink], $aUser['language_id']) . "\n";

                    // add personal message
                    if (!empty($aVals['personal_message'])) {
                        $sMessage .= _p('full_name_added_the_following_personal_message',
                                ['full_name' => Phpfox::getUserBy('full_name')], $aUser['language_id'])
                            . $aVals['personal_message'];
                    }
                    // send email to user
                    Phpfox::getLib('mail')->to($aUser['user_id'])
                        ->subject(_p('full_name_sent_you_a_page_invitation',
                            array('full_name' => Phpfox::getUserBy('full_name')), $aUser['language_id']))
                        ->message($sMessage)
                        ->translated()
                        ->send();

                    $aCachedEmails[$aUser['email']] = true;

                    // add to table pages_invite
                    $this->database()->insert(Phpfox::getT('pages_invite'), array(
                            'page_id' => $iId,
                            'type_id' => $this->getFacade()->getItemTypeId(),
                            'user_id' => Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp' => PHPFOX_TIME
                        )
                    );
                    // send notification
                    (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add($this->getFacade()->getItemType() . '_invite',
                        $iId, $aUser['user_id']) : null);
                }
            }

            // invite emails
            if (isset($aVals['emails']) && $aVals['emails']) {
                $aEmails = explode(',', $aVals['emails']);
                foreach ($aEmails as $sEmail) {
                    $sEmail = trim($sEmail);
                    if (!Phpfox::getLib('mail')->checkEmail($sEmail)) {
                        continue;
                    }

                    if (isset($aCachedEmails[$sEmail])) {
                        continue;
                    }

                    if (isset($aInvited['email'][$sEmail])) {
                        continue;
                    }

                    $sLink = $this->getFacade()->getItems()->getUrl($iId, $aPage['title'], $aPage['vanity_url']);

                    $sMessage = _p('full_name_invited_you_to_the_title', [
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'title' => $aPage['title'],
                        'link' => $sLink
                    ]);
                    if (!empty($aVals['personal_message'])) {
                        $sMessage .= _p('full_name_added_the_following_personal_message',
                                ['full_name' => Phpfox::getUserBy('full_name')])
                            . $aVals['personal_message'];
                    }
                    $oMail = Phpfox::getLib('mail');
                    if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1) {
                        $oMail->fromEmail(Phpfox::getUserBy('email'))
                            ->fromName(Phpfox::getUserBy('full_name'));
                    }
                    $bSent = $oMail->to($sEmail)
                        ->subject([
                            'full_name_invited_you_to_the_page_title',
                            [
                                'full_name' => Phpfox::getUserBy('full_name'),
                                'title' => $aPage['title']
                            ]
                        ])
                        ->message($sMessage)
                        ->send();

                    if ($bSent) {
                        // cache email for not duplicate invite.
                        $aCachedEmails[$sEmail] = true;

                        $this->database()->insert(Phpfox::getT('pages_invite'), array(
                                'page_id' => $iId,
                                'type_id' => $this->getFacade()->getItemTypeId(),
                                'user_id' => Phpfox::getUserId(),
                                'invited_email' => $sEmail,
                                'time_stamp' => PHPFOX_TIME
                            )
                        );
                    }
                }
            }
            // notification message
            Phpfox::addMessage($this->getFacade()->getPhrase('invitations_sent_out'));
        }

        $aUserCache = array();
        // get old admins
        $aOldAdmins = Phpfox::getService('pages')->getPageAdmins($iId);
        $this->database()->delete(Phpfox::getT('pages_admin'), 'page_id = ' . (int)$iId);
        $aAdmins = Phpfox_Request::instance()->getArray('admins');
        if (count($aAdmins)) {
            foreach ($aAdmins as $iAdmin) {
                if (isset($aUserCache[$iAdmin])) {
                    continue;
                }

                $aUserCache[$iAdmin] = true;
                //Add to member first
                $sType = $this->getFacade()->getItemType();
                //Check is liked
                if (!Phpfox::getService('pages')->isMember($iId, $iAdmin)) {
                    db()->insert(':like', [
                        'type_id' => $sType,
                        'item_id' => (int) $iId,
                        'user_id' => $iAdmin,
                        'time_stamp' => PHPFOX_TIME
                    ]);
                    $this->database()->updateCount('like', 'type_id = \'pages\' AND item_id = ' . (int)$iId . '', 'total_like', 'pages', 'page_id = ' . (int)$iId);
                    $this->cache()->remove('pages_' . $iId . '_members');
                }
                // Notify to new admin for the first time
                if (!in_array($iAdmin, array_column($aOldAdmins, 'user_id'))) {
                    Phpfox::getService('notification.process')->add($this->getFacade()->getItemType() . '_invite_admin',
                        $iId, $iAdmin);
                }
                //Then add to admin
                $this->database()->insert(Phpfox::getT('pages_admin'), array('page_id' => $iId, 'user_id' => $iAdmin));

                $this->cache()->remove('admin_' . $iAdmin . '_pages');
            }
        }
        $this->cache()->remove('pages_' . $iId . '_admins');

        if (isset($aVals['perms'])) {
            $this->database()->delete(Phpfox::getT('pages_perm'), 'page_id = ' . (int)$iId);
            foreach ($aVals['perms'] as $sPermId => $iPermValue) {
                $this->database()->insert(Phpfox::getT('pages_perm'),
                    array('page_id' => (int)$iId, 'var_name' => $sPermId, 'var_value' => (int)$iPermValue));
            }
        }


        $this->database()->update(Phpfox::getT('user'),
            array('full_name' => Phpfox::getLib('parse.input')->clean($aVals['title'], 255)),
            'profile_page_id = ' . (int)$iId);

        return true;
    }

    /**
     * Verify params on update page
     * @param $aVals
     * @return bool
     * @throws \Exception
     */
    private function _verify($aVals)
    {
        if (empty($aVals['title'])) {
            Phpfox_Error::set(_p('page_name_is_empty'));

            return false;
        }

        return true;
    }

    /**
     * Remove admin
     * @param $iPageId
     * @param $iAdminId
     */
    public function removeAdmin($iPageId, $iAdminId)
    {
        db()->delete(':pages_admin', ['page_id' => $iPageId, 'user_id' => $iAdminId]);
        $this->cache()->remove('pages_' . $iPageId . '_admins');
        $this->cache()->remove('admin_' . $iAdminId . '_pages');
    }

    /**
     * Delete category
     * @param $iId
     * @param bool $bIsSub
     * @param bool $bDeleteChildren
     * @return bool
     */
    public function deleteCategory($iId, $bIsSub = false, $bDeleteChildren = false)
    {
        if ($bIsSub) {
            if ($bDeleteChildren) {
                // delete all pages belong to this category
                $aPages = Phpfox::getService('pages')->getItemsByCategory($iId, true, 0, 0 , false, 'delete');
                foreach ($aPages as $aPage) {
                    Phpfox::getService('pages.process')->delete($aPage['page_id']);
                }
            }

            // Delete phrase of category
            $aCategory = $this->database()->select('*')
                ->from(':pages_category')
                ->where('category_id=' . (int)$iId)
                ->execute('getSlaveRow');

            if (isset($aCategory['name']) && Core\Lib::phrase()->isPhrase($aCategory['name'])) {
                Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
            }
            $this->database()->delete(Phpfox::getT('pages_category'), 'category_id = ' . (int)$iId);
        } else {
            if ($bDeleteChildren) {
                // delete all pages belong to this type
                $aPages = Phpfox::getService('pages')->getItemsByCategory($iId, false, 0, 0 , false, 'delete');
                foreach ($aPages as $aPage) {
                    Phpfox::getService('pages.process')->delete($aPage['page_id']);
                }
                // delete all categories belong to this type
                $aCategories = $this->database()->select('category_id')
                    ->from(':pages_category')
                    ->where('type_id=' . (int)$iId)
                    ->executeRows();
                foreach ($aCategories as $aCategory) {
                    $this->deleteCategory($aCategory['category_id'], true, $bDeleteChildren);
                }
            }

            // delete category image
            $this->getFacade()->getType()->deleteImage((int)$iId);

            // Delete phrase of type
            $aType = $this->database()->select('*')
                ->from(':pages_type')
                ->where('type_id=' . (int)$iId)
                ->execute('getSlaveRow');

            if (isset($aType['name']) && Core\Lib::phrase()->isPhrase($aType['name'])) {
                Phpfox::getService('language.phrase.process')->delete($aType['name'], true);
            }

            $this->database()->delete(Phpfox::getT('pages_type'), 'type_id = ' . (int)$iId);
        }

        $this->cache()->removeGroup('pages');

        return true;
    }

    /**
     * Delete profile image
     * @param $aPage
     * @return bool
     */
    public function deleteImage($aPage)
    {
        if (!$aPage['image_path']) {
            return true;
        }

        $aParams = Phpfox::getService('pages')->getUploadPhotoParams();
        $aParams['type'] = 'pages';
        $aParams['path'] = $aPage['image_path'];
        $aParams['user_id'] = $aPage['user_id'];
        $aParams['update_space'] = true;
        $aParams['server_id'] = $aPage['image_server_id'];

        return Phpfox::getService('user.file')->remove($aParams);
    }

    /**
     * Add new page
     * @param $aVals
     * @param bool $bIsApp
     * @return int
     * @throws \Exception
     */
    public function add($aVals, $bIsApp = false)
    {
        $iViewId = ($this->getFacade()->getUserParam('approve_pages') ? '1' : '0');

        // Flood control
        if ($iWaitTime = Phpfox::getUserParam('pages.pages_flood_control')) {
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field' => 'time_stamp', // The time stamp field
                    'table' => Phpfox::getT('pages'), // Database table we plan to check
                    'condition' => 'item_type = 0 AND user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iWaitTime * 60 // Seconds);
                )
            );

            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                return Phpfox_Error::set(Phpfox::getLib('spam')->getWaitTime());
            }
        }

        if (empty($aVals['title'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('page_name_cannot_be_empty'));
        }

        if (defined('PHPFOX_APP_CREATED') || $bIsApp) {
            $iViewId = 0;
        }

        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_process_add_1')) {
            eval($sPlugin);
        }

        $aInsert = [
            'view_id' => $iViewId,
            'type_id' => (isset($aVals['type_id']) ? (int)$aVals['type_id'] : 0),
            'app_id' => (isset($aVals['app_id']) ? (int)$aVals['app_id'] : 0),
            'category_id' => (isset($aVals['category_id']) ? (int)$aVals['category_id'] : 0),
            'user_id' => Phpfox::getUserId(),
            'title' => $this->preParse()->clean($aVals['title'], 255),
            'time_stamp' => PHPFOX_TIME,
            'item_type' => $this->getFacade()->getItemTypeId()
        ];

        $iId = $this->database()->insert($this->_sTable, $aInsert);

        $aInsertText = array('page_id' => $iId);
        if (isset($aVals['info'])) {
            $aInsertText['text'] = $this->preParse()->clean($aVals['info']);
            $aInsertText['text_parsed'] = $this->preParse()->prepare($aVals['info']);
        }
        $this->database()->insert(Phpfox::getT('pages_text'), $aInsertText);

        $sSalt = '';
        for ($i = 0; $i < 3; $i++) {
            $sSalt .= chr(rand(33, 91));
        }

        $sPossible = '23456789bcdfghjkmnpqrstvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $sPassword = '';
        $i = 0;
        while ($i < 10) {
            $sPassword .= substr($sPossible, mt_rand(0, strlen($sPossible) - 1), 1);
            $i++;
        }

        $iUserId = $this->database()->insert(Phpfox::getT('user'), array(
                'profile_page_id' => $iId,
                'user_group_id' => NORMAL_USER_ID,
                'view_id' => '7',
                'full_name' => $this->preParse()->clean($aVals['title']),
                'joined' => PHPFOX_TIME,
                'password' => Phpfox::getLib('hash')->setHash($sPassword, $sSalt),
                'password_salt' => $sSalt
            )
        );

        $aExtras = array(
            'user_id' => $iUserId
        );

        $this->database()->insert(Phpfox::getT('user_activity'), $aExtras);
        $this->database()->insert(Phpfox::getT('user_field'), $aExtras);
        $this->database()->insert(Phpfox::getT('user_space'), $aExtras);
        $this->database()->insert(Phpfox::getT('user_count'), $aExtras);
        $this->setDefaultPermissions($iId);

        if (!$this->getFacade()->getUserParam('approve_pages')) {
            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), $this->getFacade()->getItemType());
        }

        Phpfox::getService('like.process')->add($this->getFacade()->getItemType(), $iId, null, null);

        $this->cache()->remove('admin_' . Phpfox::getUserId() . '_pages');

        return $iId;
    }

    /**
     * @param $iPageId
     * @param $iPhotoId
     * @param bool $bIsAjaxPageUpload
     * @return bool
     * @throws \Exception
     */
    public function setCoverPhoto($iPageId, $iPhotoId, $bIsAjaxPageUpload = false)
    {
        if (!$this->getFacade()->getItems()->isAdmin($iPageId) && !Phpfox::isAdmin() && !$this->getFacade()->getUserParam('can_edit_all_pages')) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('user_is_not_an_admin'));
        }

        if ($bIsAjaxPageUpload == false) {
            // check that this photo belongs to this page
            $iPhotoId = $this->database()->select('photo_id')
                ->from(Phpfox::getT('photo'))
                ->where('module_id = \'' . $this->getFacade()->getItemType() . '\' AND group_id = ' . (int)$iPageId . ' AND photo_id = ' . (int)$iPhotoId)
                ->execute('getSlaveField');
        }

        if (!empty($iPhotoId)) {
            if($bIsAjaxPageUpload == false) {
                $iPhotoId = $this->processSetCoverPhoto($iPageId, $iPhotoId);
                if($iPhotoId === false) {
                    return _p('Cannot set cover photo for this page.');
                }
            }
            $this->database()->update(Phpfox::getT('pages'),
                array('cover_photo_position' => '', 'cover_photo_id' => (int)$iPhotoId), 'page_id = ' . (int)$iPageId);
            // create feed after changing cover
            if(Phpfox::isModule('feed')) {
                Phpfox::getService('feed.process')->callback([
                    'table_prefix' => 'pages_',
                    'module' => 'pages',
                    'add_to_main_feed' => true,
                    'has_content' => true
                ])->add('pages_cover_photo', $iPhotoId, 0, 0, $iPageId, Phpfox::getService('pages')->getUserId($iPageId));
            }
            return true;
        }

        return Phpfox_Error::set($this->getFacade()->getPhrase('the_photo_does_not_belong_to_this_page'));
    }

    /**
     * Clone photo for page's cover photo
     * @param $pageId
     * @param $photoId
     * @return bool
     */
    private function processSetCoverPhoto($pageId, $photoId){
        $photoInfo = Phpfox::getService('photo')->getPhotoItem($photoId);
        $photoPath = Phpfox::getParam('photo.dir_photo'). sprintf($photoInfo['destination'],'');
        if(!file_exists($photoPath) && $photoInfo['server_id'] > 0) {
            $photoPath = Phpfox::getLib('image.helper')->display([
                'server_id' => $photoInfo['server_id'],
                'path' => 'photo.url_photo',
                'file' => $photoInfo['destination'],
                'suffix' => '',
                'return_url' => true
            ]);
        }

        $tempFilePath = Phpfox::getParam('core.dir_cache'). md5($photoId . PHPFOX_TIME . uniqid()). '.' . $photoInfo['extension'];
        file_put_contents($tempFilePath, file_get_contents($photoPath));
        register_shutdown_function(function () use ($tempFilePath) {
            @unlink($tempFilePath);
        });

        if(!file_exists($tempFilePath)) {
            return false;
        }

        $pageUserId = $this->getFacade()->getItems()->getUserId($pageId);
        $insert = [
            'description' => null,
            'type_id' => 0,
            'module_id' => 'pages',
            'group_id' => $pageId,
            'is_cover_photo' => 1,
            'name' => $photoInfo['file_name'],
            'ext' => $photoInfo['extension'],
            'size' => $photoInfo['file_size'],
            'type' => $photoInfo['mime_type'],
        ];
        $photoId = Phpfox::getService('photo.process')->add($pageUserId, $insert);
        if($photoId) {
            $sFileName = Phpfox::getLib('file')->upload($tempFilePath, Phpfox::getParam('photo.dir_photo'), $photoId, true);
            $file = Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '');
            // Get the current image width/height
            $aSize = getimagesize($file);
            // Update the image with the full path to where it is located.
            $update = array(
                'destination' => $sFileName,
                'width' => $aSize[0],
                'height' => $aSize[1],
                'server_id' => \Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID'),
                'allow_download' => 1,
                'view_id' => 0,
            );
            Phpfox::getService('photo.process')->update($pageUserId, $photoId, $update);
            Phpfox::getService('pages.process')->updateCoverPhoto($photoId, $pageId);

            $oImage = Phpfox::getLib('image');
            foreach(Phpfox::getService('photo')->getPhotoPicSizes() as $size)
            {
                $oImage->createThumbnail($file, Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $size), $size, $aSize[1], true, false);
            }
            return $photoId;
        }
        return false;
    }

    /**
     * @param $iId
     * @param bool $bDoCallback
     * @param bool $bForce
     * @return bool
     * @throws \Exception
     */
    public function delete($iId, $bDoCallback = true, $bForce = false)
    {
        $aPage = $this->database()->select('*')
            ->from(Phpfox::getT('pages'))
            ->where('page_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aPage['page_id'])) {
            return $bForce ? false : Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_trying_to_delete'));
        }

        if ($bForce || $aPage['user_id'] == Phpfox::getUserId() || Phpfox::getUserParam('pages.can_delete_all_pages')) {
            $iUser = $this->database()->select('user_id')->from(Phpfox::getT('user'))->where('profile_page_id = ' . (int)$aPage['page_id'] . ' AND view_id = 7')->execute('getSlaveField');

            $this->database()->delete(Phpfox::getT('pages_url'), 'page_id = ' . (int)$aPage['page_id']);
            $this->database()->delete(Phpfox::getT('feed'),
                'type_id = \'' . $this->getFacade()->getItemType() . '_itemLiked\' AND item_id = ' . (int)$aPage['page_id']);

            if (((int)$iUser) > 0 && $bDoCallback === true) {
                Phpfox::massCallback('onDeleteUser', $iUser);
            }
            if ($bDoCallback) {
                Phpfox::massCallback('onDeletePage', $iId, $this->getFacade()->getItemType());
            }

            $this->deleteImage($aPage);

            $this->database()->delete(Phpfox::getT('pages'), 'page_id = ' . $aPage['page_id']);

            if($aPage['view_id'] == 0) {
                Phpfox::getService('user.activity')->update($aPage['user_id'], $this->getFacade()->getItemType(), '-');
            }

            (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete($this->getFacade()->getItemType(), (int)$aPage['page_id'], 0, true) : null);

            //close all sponsorships
            (Phpfox::isAppActive('Core_BetterAds') ? Phpfox::getService('ad.process')->closeSponsorItem('pages', (int)$aPage['page_id']) : null);

            // clear cache on pages
            $this->cache()->remove('pages_' . $iId . '_admins');
            $this->cache()->remove('pages_' . $iId . '_members');
            $this->cache()->remove('pages_' . $iId . '_pending_users');

            if ($aPage['is_sponsor'] == 1) {
                $this->cache()->remove('pages_sponsored');
            }

            // clear cache on admin of page
            $this->cache()->remove('admin_' . $aPage['user_id'] . '_pages');

            return true;
        }

        return Phpfox_Error::set($this->getFacade()->getPhrase('you_are_unable_to_delete_this_page'));
    }

    public function setProfilePicture($sSrcFile, $iPageId = 0)
    {
        if (!$iPageId) {
            $iPageId = Phpfox::getUserBy('profile_page_id');
        }

        if (!$iPageId) {
            return;
        }

        $sSrcExt = pathinfo($sSrcFile, PATHINFO_EXTENSION);
        $sFileName = md5($iPageId . PHPFOX_TIME . uniqid()) . "%s.$sSrcExt";
        $sFilePath = Phpfox::getParam('pages.dir_image') . $sFileName;
        $sOriginalFile = sprintf($sFilePath, '');
        file_put_contents($sOriginalFile, fox_get_contents($sSrcFile));
        // put to cdn
        Phpfox::getLib('cdn')->put($sOriginalFile);
        // generate thumbnails
        $this->generateThumbnails($sFileName);
        // set as current page's profile image
        db()->update(':pages', [
            'image_path' => $sFileName,
            'image_server_id' => \Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
        ], ['page_id' => $iPageId]);
        // update space of user
        Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'pages', filesize($sOriginalFile));
    }

    public function generateThumbnails($sFileName)
    {
        $oImage = \Phpfox_Image::instance();
        $sSrcPath = Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '');

        foreach (Phpfox::getService('pages')->getPhotoPicSizes() as $iSize) {
            if (Phpfox::getParam('core.keep_non_square_images')) {
                $oImage->createThumbnail($sSrcPath,
                    Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
            }
            $oImage->createThumbnail($sSrcPath,
                Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize,
                false);
        }
    }

    public function removeLogo($iPageId = null)
    {
        $aPage = $this->getFacade()->getItems()->getPage($iPageId);
        if (!isset($aPage['page_id'])) {
            return false;
        }

        $aPage['link'] = $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
            $aPage['vanity_url']);

        if (!$this->getFacade()->getItems()->isAdmin($aPage) && !Phpfox::getUserParam('pages.can_edit_all_pages')) {
            return false;
        }

        $this->database()->update(Phpfox::getT('pages'), array('cover_photo_id' => '0', 'cover_photo_position' => null),
            'page_id = ' . (int)$iPageId);

        return $aPage;
    }

    public function deleteWidget($iId)
    {
        $aWidget = $this->database()->select('*')
            ->from(Phpfox::getT('pages_widget'))
            ->where('widget_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aWidget['widget_id'])) {
            return false;
        }

        $aPage = $this->getFacade()->getItems()->getPage($aWidget['page_id']);

        if (!isset($aPage['page_id'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_looking_for'));
        }

        if (!$this->getFacade()->getItems()->isAdmin($aPage) && !Phpfox::isAdmin() && !Phpfox::getUserParam('pages.can_edit_all_pages')) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_delete_this_widget'));
        }

        $this->database()->delete(Phpfox::getT('pages_widget'), 'widget_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('pages_widget_text'), 'widget_id = ' . (int)$iId);

        return true;
    }

    public function feature($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('pages.can_feature_page', true);
        $this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')), 'page_id = ' . (int)$iId);
        $this->cache()->remove('pages_featured');

        return true;
    }

    public function sponsor($iId, $sType)
    {
        if (!Phpfox::getUserParam('pages.can_sponsor_pages') && !Phpfox::getUserParam('pages.can_purchase_sponsor_pages') && !defined('PHPFOX_API_CALLBACK')) {
            return Phpfox_Error::set(_p('hack_attempt'));
        }

        $iType = (int)$sType;
        if ($iType != 0 && $iType != 1) {
            return false;
        }
        db()->update($this->_sTable, array('is_sponsor' => $iType), 'page_id = ' . (int)$iId);
        if ($sPlugin = Phpfox_Plugin::get('pages.service_process_sponsor__end')) {
            eval($sPlugin);
        }
        return true;
    }

    public function reassignOwner($iPageId, $iUserId)
    {
        $aPage = $this->getFacade()->getItems()->getPage($iPageId);
        if (empty($aPage['page_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_page_you_are_looking_for'));
        }
        if ($iUserId == $aPage['user_id']) {
            return Phpfox_Error::set(_p('you_can_not_reassign_for_current_owner'));
        }
        if ($aPage['user_id'] != Phpfox::getUserId() && !Phpfox::isAdmin()) {
            return Phpfox_Error::set(_p('you_do_not_have_permission_to_do_this'));
        }
        db()->update($this->_sTable, ['user_id' => $iUserId],'page_id = '. $iPageId);

        $this->cache()->remove('pages_' . $iPageId . '_admins');
        $this->cache()->remove('admin_' . $aPage['user_id'] . '_pages');
        $this->cache()->remove('admin_' . $iUserId . '_pages');

        //Update activity
        Phpfox::getService('user.activity')->update($iUserId, 'pages');
        Phpfox::getService('user.activity')->update($aPage['user_id'], 'pages', '-');

        Phpfox::getService('notification.process')->add($this->getFacade()->getItemType() . '_reassign_owner',
            $iPageId, $iUserId);
        if (Phpfox::getUserId() != $aPage['user_id']) {
            Phpfox::getService('notification.process')->add($this->getFacade()->getItemType() . '_owner_changed',
                $iPageId, $aPage['user_id']);
        }
        return true;
    }
}

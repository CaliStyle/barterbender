<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Service_Process extends Phpfox_Service
{
    private $_bHasImage = false;
    private $_sDirPetition = "";

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('petition');
        $this->_sDirPetition = Phpfox::getParam('core.dir_pic') . 'petition/';
    }

    private function _verify(&$aVals, $bIsUpdate = false)
    {
        if (isset($_FILES['image'])) {
            foreach ($_FILES['image']['error'] as $iKey => $sError) {
                if ($sError == UPLOAD_ERR_OK) {
                    $aImage = Phpfox::getLib('file')->load('image[' . $iKey . ']', array(
                            'jpg',
                            'gif',
                            'png'
                        )
                    );

                    if ($aImage === false) {
                        return false;
                    }

                    $this->_bHasImage = true;
                }
            }
        }
        return true;
    }

    public function close($iId)
    {
        $iId = rtrim($iId, ',');
        return $this->database()->update(Phpfox::getT('petition'), array('petition_status' => '1'), 'is_approved = 1 AND petition_id IN(' . $iId . ')');
    }

    public function add($aVals)
    {
        (($sPlugin = Phpfox_Plugin::get('petition.service_process__start')) ? eval($sPlugin) : false);
        $oFilter = Phpfox::getLib('parse.input');

        // check if the user entered a forbidden word
        //Phpfox::getService('ban')->checkAutomaticBan($aVals['description'] . ' ' . $aVals['title']. ' ' . $aVals['target']. ' ' . $aVals['short_description']. ' ' . $aVals['petition_goal']);


        // Check if links in titles
        if (!Phpfox::getLib('validator')->check($aVals['title'], array('url'))) {
            return Phpfox_Error::set(Phpfox::getPhrase('petition.we_do_not_allow_links_in_titles'));
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }
        if (!isset($aVals['privacy_sign'])) {
            $aVals['privacy_sign'] = 0;
        }
        $sTitle = $oFilter->clean($aVals['title'], 255);
        $bHasAttachments = false;// (!empty($aVals['attachment']) && Phpfox::getUserParam('petition.can_attach_on_petition'));

        $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_time_month'], $aVals['end_time_day'], $aVals['end_time_year']);
        if ($iEndTime < PHPFOX_TIME) {
            return Phpfox_Error::set(Phpfox::getPhrase('petition.please_edit_petition_end_date_before_update_petition_status'));
        }
        $aInsert = array(
            'user_id' => Phpfox::getUserId(),
            'module_id' => (isset($aVals['module_id']) ? $aVals['module_id'] : 'petition'),
            'item_id' => (isset($aVals['item_id']) ? $aVals['item_id'] : '0'),
            'title' => $sTitle,
            'time_stamp' => PHPFOX_TIME,
            'start_time' => PHPFOX_TIME,
            'end_time' => ($iEndTime < PHPFOX_TIME) ? PHPFOX_TIME + 86400 : $iEndTime,
            'is_approved' => 1,
            'is_send_thank' => (isset($aVals['is_send_thank']) ? $aVals['is_send_thank'] : '1'),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'privacy_sign' => (isset($aVals['privacy_sign']) ? $aVals['privacy_sign'] : '0'),
            'petition_status' => 2,
            'total_attachment' => 0//($bHasAttachments ? Phpfox::getService('attachment')->getCount($aVals['attachment']) : 0)
        );

        if (Phpfox::getUserParam('petition.approve_petitions')) {
            $aInsert['is_approved'] = '0';
        }

        (($sPlugin = Phpfox_Plugin::get('petition.service_process_add_start')) ? eval($sPlugin) : false);

        $iId = $this->database()->insert(Phpfox::getT('petition'), $aInsert);

        $aCallback = (!empty($aVals['module_id']) ? Phpfox::callback('petition.addPetition', $iId) : null);

        (($sPlugin = Phpfox_Plugin::get('petition.service_process_add_end')) ? eval($sPlugin) : false);

        $sTarget = $oFilter->clean($aVals['target'], 255);
        $sPetitionGoal = $oFilter->clean($aVals['petition_goal'], 255);
        $sSignatureGoal = (!empty($aVals['signature_goal']) ? $oFilter->clean($aVals['signature_goal'], 255) : '');

        $sLetter = Phpfox::getParam('petition.target_letter_template');

        $aInsertText = array(
            'petition_id' => $iId,

            /*
            'title'				=>$sTitle,
            'start_time'		=> $aInsert['start_time'],
            'end_time'		      =>$aInsert['end_time'],
            'total_sign'		      =>0,
            */

            'target' => $sTarget,
            'target_email' => $aVals['target_email'],
            'petition_goal' => $sPetitionGoal,
            'signature_goal' => $sSignatureGoal,
            'short_description' => $oFilter->clean($aVals['short_description']),
            'short_description_parsed' => $oFilter->prepare($aVals['short_description']),
            'description' => $oFilter->clean($aVals['description']),
            'description_parsed' => $oFilter->prepare($aVals['description']),
            'letter' => trim($sLetter),
            'letter_parsed' => trim($sLetter),
            'letter_subject' => $sPetitionGoal
        );
        $petition_service = new Petition_Service_Petition();
        //$aInsertText['letter'] = Phpfox::getService('petition')->parseLetter($aInsertText);
        $arr = $aInsertText;
        $arr["title"] = $sTitle;
        $arr["start_time"] = $aInsert['start_time'];
        $arr["end_time"] = $aInsert['end_time'];
        $arr["total_sign"] = 0;

        //$aInsertText['letter'] = $petition_service->parseLetter($aInsertText);

        $aInsertText['letter'] = $petition_service->parseLetter($arr);
        //die("process.class.php");

        $aInsertText['letter_parsed'] = $oFilter->prepare(trim($aInsertText['letter']));

        $this->database()->insert(Phpfox::getT('petition_text'), $aInsertText);

        if ($aInsert['is_approved'] == 1) {
            /**
             * done send feed when dont have social publisher module
             */
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->add('petition', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0), (isset($aVals['item_id']) ? (int)$aVals['item_id'] : 0)) : null);

            // Update user activity
            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'petition');
        }

        if (!empty($aVals['selected_categories'])) {
            Phpfox::getService('petition.category')->addCategoryForPetition($iId, explode(',', rtrim($aVals['selected_categories'], ',')), $aInsert['is_approved'] == 1 ? true : false);
        }

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && Phpfox::getUserParam('petition.can_add_tags_on_petitions') && !empty($aVals['description'])) {
            Phpfox::getService('tag.process')->add('petition', $iId, Phpfox::getUserId(), $aVals['description'], true);
        } else {
            if (Phpfox::getUserParam('petition.can_add_tags_on_petitions') && Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
                Phpfox::getService('tag.process')->add('petition', $iId, Phpfox::getUserId(), $aVals['tag_list']);
            }
        }

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        if ($aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('petition', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }

        // $this->cache()->remove(array('user/' . Phpfox::getUserId(), 'blog_browse'), 'substr');

        (($sPlugin = Phpfox_Plugin::get('petition.service_process__end')) ? eval($sPlugin) : false);
        $this->cache()->remove('petition', 'substr');
        return $iId;
    }

    public function update($iId, $iUserId, $aVals, &$aRow = null)
    {
        (($sPlugin = Phpfox_Plugin::get('petition.service_process_update__start')) ? eval($sPlugin) : false);


        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $oFilter = Phpfox::getLib('parse.input');

        $bHasAttachments = false;//(!empty($aVals['attachment']) && Phpfox::getUserParam('petition.can_attach_on_petition') && $iUserId == Phpfox::getUserId());

        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], $iUserId, $iId);
        }
        $sTitle = $oFilter->clean($aVals['title'], 255);
        $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_time_month'], $aVals['end_time_day'], $aVals['end_time_year']);

        if (isset($aVals['petition_status']) && (int)$aVals['petition_status']) {
            if ($aVals['petition_status'] == 2 && $iEndTime < PHPFOX_TIME) {
                return Phpfox_Error::set(Phpfox::getPhrase('petition.please_edit_petition_end_date_before_update_petition_status'));
            }
        }

        $aUpdate = array(
            'title' => $sTitle,
            'end_time' => $iEndTime,
            'is_approved' => (isset($aVals['is_approved']) ? $aVals['is_approved'] : '0'),
            'is_send_thank' => (isset($aVals['is_send_thank']) ? $aVals['is_send_thank'] : '1'),
            'is_send_online' => (isset($aVals['is_send_online']) ? $aVals['is_send_online'] : '0'),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'privacy_sign' => (isset($aVals['privacy_sign']) ? $aVals['privacy_sign'] : '0'),
            'petition_status' => (isset($aVals['petition_status']) ? (int)$aVals['petition_status'] : '2'),
            'total_attachment' => 0    //($bHasAttachments ? Phpfox::getService('attachment')->getCount($aVals['attachment']) : 0)
        );

        if ($aRow !== null && isset($aVals['petition_status']) && $aRow['petition_status'] > 1) {
            $aUpdate['time_stamp'] = PHPFOX_TIME;
        }

        (($sPlugin = Phpfox_Plugin::get('petition.service_process_update')) ? eval($sPlugin) : false);

        $bIsNotSupported = false;


        // Multi-upload
        if (isset($_FILES['image'])) {
            $oImage = Phpfox::getLib('image');
            $oFile = Phpfox::getLib('file');
            $sInvalid = '';
            $iFileSizes = 0;
            $iUploaded = 0;

            $iMaxUpload = Phpfox::getUserParam('petition.total_photo_upload_limit');
            $aImages = Phpfox::getService('petition')->getImages($iId);

            if (count($aImages) > 0) {
                $iMaxUpload = $iMaxUpload - count($aImages);
            }

            foreach ($_FILES['image']['error'] as $iKey => $sError) {
                if ($iUploaded == $iMaxUpload) {
                    break;
                }
                if ($sError == UPLOAD_ERR_OK) {
                    if ($aImage = $oFile->load('image[' . $iKey . ']', array(
                        'jpg',
                        'gif',
                        'png'
                    ), (Phpfox::getUserParam('petition.max_upload_size_petition') === 0 ? null : (Phpfox::getUserParam('petition.max_upload_size_petition') / 1024))
                    )
                    ) {
                        $iUploaded++;
                        $sFileName = Phpfox::getLib('file')->upload('image[' . $iKey . ']', $this->_sDirPetition, $iId);

                        $iFileSizes += filesize($this->_sDirPetition . sprintf($sFileName, ''));

                        $this->database()->insert(Phpfox::getT('petition_image'), array('petition_id' => $iId, 'image_path' => 'petition/' . $sFileName, 'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')));

                        //$aSizes = array(50, 120, 300);
                        $aSizes = array(50, 120, 300, 500, 1024);
                        foreach ($aSizes as $iSize) {
                            $oImage->createThumbnail($this->_sDirPetition . sprintf($sFileName, ''), $this->_sDirPetition . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
                            $iFileSizes += filesize($this->_sDirPetition . sprintf($sFileName, '_' . $iSize));
                        }
                    } else {
                        if ($sInvalid != '')
                            $sInvalid .= '<li>' . $_FILES['image']['name'][$iKey] . '</li>';
                        else
                            $sInvalid = '<li>' . $_FILES['image']['name'][$iKey] . '</li>';
                    }
                }
            }

            if ($iFileSizes != 0 && $sInvalid == '') {
                // Update user space usage
                Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'petition', $iFileSizes);
                $aUpdate['image_path'] = 'petition/' . $sFileName;
                $aUpdate['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');

                if ($aUpdate['is_approved'] == 1 && !Phpfox::getService('petition')->isPetitionOnFeed($iId) && Phpfox::isModule('socialpublishers')) {
                    /**
                     * send feed and ask if publisher is installed and run
                     */
                    (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('petition', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0), (isset($aVals['item_id']) ? (int)$aVals['item_id'] : 0)) : null);

                    // Update user activity
                    Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'petition');
                }
            }


        }

        $this->database()->update(Phpfox::getT('petition'), $aUpdate, 'petition_id = ' . (int)$iId);

        if (isset($sInvalid) && $sInvalid != '') {
            return Phpfox_Error::set(Phpfox::getPhrase('petition.invalid_files') . '<br/><ul style="margin-left: 20px">' . $sInvalid . '</ul>');
        }

        $sTarget = $oFilter->clean($aVals['target'], 255);
        $sPetitionGoal = $oFilter->clean($aVals['petition_goal'], 255);
        $sSignatureGoal = (!empty($aVals['signature_goal']) ? $oFilter->clean($aVals['signature_goal'], 255) : '');


        $sLetter = (isset($aVals['letter'])) ? $aVals['letter'] : "";//$oFilter->clean($aVals['letter_parsed'])
        $sLetterSubject = (isset($aVals['letter_subject'])) ? $aVals['letter_subject'] : "";

        $aUpdateText = array(
            'target' => $sTarget,
            'target_email' => $aVals['target_email'],
            'petition_goal' => $sPetitionGoal,
            'signature_goal' => $sSignatureGoal,
            'short_description' => $oFilter->clean($aVals['short_description']),
            'short_description_parsed' => $oFilter->prepare($aVals['short_description']),
            'description' => $oFilter->clean($aVals['description']),
            'description_parsed' => $oFilter->prepare($aVals['description']),
            'letter' => $sLetter,
            'letter_parsed' => $oFilter->prepare(trim($sLetter)),
            'letter_subject' => $oFilter->prepare($sLetterSubject)
        );

        $this->sentInvite($iId, $aVals);
        $this->database()->update(Phpfox::getT('petition_text'), $aUpdateText, 'petition_id = ' . (int)$iId);

        //Update category
        //$this->database()->delete(Phpfox::getT('petition_category_data'), "petition_id = " . (int) $iId);

        //$this->database()->insert(Phpfox::getT('petition_category_data'), array('petition_id' => (int)$iId, 'category_id' => (int)$aVals['selected_categories']));
        if (!empty($aVals['selected_categories'])) {
            Phpfox::getService('petition.category')->updateCategoryForPetition($iId, explode(',', rtrim($aVals['selected_categories'], ',')), $aVals['is_approved'] == '1' ? true : false);
        }

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && Phpfox::getUserParam('petition.can_add_tags_on_petitions') && !empty($aVals['description'])) {
            Phpfox::getService('tag.process')->update('petition', $iId, $iUserId, $aVals['description'], true);
        } else {
            if (Phpfox::getUserParam('petition.can_add_tags_on_petitions') && Phpfox::isModule('tag')) {
                if (isset($aVals['tag_list']))
                    Phpfox::getService('tag.process')->update('petition', $iId, $iUserId, (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
            }
        }

        $aPetition = Phpfox::getService('petition')->getPetition($iId);

        if ($aUpdate['is_approved'] == 1) {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('petition', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $iUserId) : null);
        }

        if (Phpfox::isModule('privacy')) {
            if ($aVals['privacy'] == '4') {
                Phpfox::getService('privacy.process')->update('petition', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            } else {
                Phpfox::getService('privacy.process')->delete('petition', $iId);
            }
        }

        // $this->cache()->remove(array('user/' . $iUserId, 'blog_browse'), 'substr');

        (($sPlugin = Phpfox_Plugin::get('petition.service_process_update__end')) ? eval($sPlugin) : false);

        $this->cache()->remove('petition', 'substr');

        if ($bIsNotSupported) {
            return Phpfox_Error::get();
        }
        return $iId;
    }

    public function updateTitle($iId, $iUserId, $sTitle)
    {
        $oFilter = Phpfox::getLib('parse.input');
        $sTitle = $oFilter->clean($sTitle, 255);
        if (!empty($sTitle)) {
            $this->database()->update(Phpfox::getT('petition'), array('title' => $sTitle), 'petition_id = ' . (int)$iId);
            return $iId;
        }
        return 0;
    }

    public function updateInvite($iId, $iSignedId)
    {
        if ((int)$iSignedId) {
            $this->database()->update(Phpfox::getT('petition_invite'), array('signed_id' => (int)$iSignedId), 'invite_id = ' . (int)$iId);
            return $iId;
        }
        return 0;
    }

    public function deleteNews($iId)
    {
        $aNews = $this->database()->select('pn.news_id')
            ->from(Phpfox::getT('petition_news'), 'pn')
            ->join(Phpfox::getT('petition'), 'p', 'p.petition_id = pn.petition_id')
            ->where('pn.news_id = ' . (int)$iId . ' AND p.user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveRow');
        if ($aNews) {
            $this->database()->delete(Phpfox::getT('petition_news'), "news_id = " . (int)$iId);
            return true;
        }
        return false;
    }

    public function postNews($iId, $aVals)
    {
        $oFilter = Phpfox::getLib('parse.input');
        $sHeadline = $oFilter->clean($aVals['news_headline'], 255);
        $sContent = $oFilter->clean($aVals['news_content']);

        if (!empty($sHeadline) && !empty($sContent)) {
            $aInsert = array(
                'petition_id' => $iId,
                'headline' => $sHeadline,
                'link' => $aVals['news_link'],
                'content' => $oFilter->prepare($sContent),
                'time_stamp' => PHPFOX_TIME
            );

            if (isset($aVals['news_id']) && (int)$aVals['news_id'] > 0) {
                $iId = $aVals['news_id'];
                $iId = $this->database()->update(Phpfox::getT('petition_news'), $aInsert, 'news_id = ' . (int)$iId);
            } else {
                $iId = $this->database()->insert(Phpfox::getT('petition_news'), $aInsert);
            }
            return $iId;
        }
        return 0;
    }

    public function sign($iId, $aVals)
    {
        $aPetition = $this->database()->select('petition_id, petition_status, user_id, title, is_send_online, is_send_thank, total_sign')
            ->from($this->_sTable)
            ->where('petition_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if ($aPetition['petition_status'] != 2)
            return false;
        $oFilter = Phpfox::getLib('parse.input');
        $sLocation = $oFilter->clean($aVals['location'], 255);
        $sSignature = $oFilter->clean($aVals['signature'], 255);

        if (!empty($sLocation) && !empty($sSignature)) {
            $aInsert = array(
                'petition_id' => $iId,
                'user_id' => Phpfox::getUserId(),
                'location' => $sLocation,
                'signature' => $sSignature,
                'signature_parse' => $oFilter->prepare($sSignature),
                'time_stamp' => PHPFOX_TIME
            );

            $iId = $this->database()->insert(Phpfox::getT('petition_sign'), $aInsert);

            if ($iId) {
                $this->cache()->remove('petition', 'substr');
                if ($aPetition['is_send_online']) {
                    Phpfox::getService('petition')->sendToTarget($aInsert['petition_id'], $aInsert['user_id']);
                }

                $this->database()->query("
                       UPDATE " . $this->_sTable . "
                       SET total_sign = total_sign + 1
                       WHERE petition_id = " . (int)$aInsert['petition_id']
                );

                $this->cache()->remove('petition_most_signed');

                if ($iInvitedId = $this->getInvitedId($aInsert['petition_id'], Phpfox::getUserId())) {
                    $this->updateInvite($iInvitedId, $iId);
                }
                return array($aPetition['total_sign'] + 1, $aPetition['is_send_thank']);
            }
        }
        return 0;
    }

    public function getInvitedId($iItemId, $iUserid)
    {
        return (int)$this->database()->select('invite_id')
            ->from(Phpfox::getT('petition_invite'))
            ->where('petition_id = ' . (int)$iItemId . ' AND invited_user_id = ' . $iUserid)
            ->execute('getSlaveField');
    }

    public function sentInvite($iId, $aVals)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        $aPetition = $this->database()->select('petition_id, user_id, title')
            ->from($this->_sTable)
            ->where('petition_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (isset($aVals['emails']) || isset($aVals['invite'])) {
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('petition_invite'))
                ->where('petition_id = ' . (int)$iId)
                ->execute('getRows');

            $aInvited = array();
            foreach ($aInvites as $aInvite) {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
            }
        }

        if (isset($aVals['emails'])) {
            // if (strpos($aVals['emails'], ','))
            {
                $aEmails = explode(',', $aVals['emails']);

                $aCachedEmails = array();
                foreach ($aEmails as $sEmail) {
                    $sEmail = trim($sEmail);
                    if (!Phpfox::getLib('mail')->checkEmail($sEmail)) {
                        continue;
                    }

                    if (isset($aInvited['email'][$sEmail])) {
                        continue;
                    }

                    if (isset($aCachedEmails[$sEmail]) && $aCachedEmails[$sEmail] == true) {
                        continue;
                    }

                    $sLink = Phpfox::getLib('url')->permalink('petition', $aPetition['petition_id'], $aPetition['title']);

                    $sMessage = Phpfox::getPhrase('petition.full_name_invited_you_to_the_title', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => $oParseInput->clean($aVals['title'], 255),
                            'link' => $sLink
                        )
                    );

                    if (!empty($aVals['personal_message'])) {
                        $sMessage .= ' ' . Phpfox::getPhrase('petition.full_name_added_the_following_personal_message', array(
                                    'full_name' => Phpfox::getUserBy('full_name')
                                )
                            ) . "\n\n";
                        $sMessage .= $aVals['personal_message'];
                    }
                    $oMail = Phpfox::getLib('mail');
                    if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1) {
                        $oMail->fromEmail(Phpfox::getUserBy('email'))
                            ->fromName(Phpfox::getUserBy('full_name'));
                    }
                    $bSent = $oMail->to($sEmail)
                        ->subject(array('petition.full_name_invited_you_to_the_petition_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean($aVals['title'], 255))))
                        ->message($sMessage)
                        ->send();

                    if ($bSent) {
                        $this->_aInvited[] = array('email' => $sEmail);

                        $aCachedEmails[$sEmail] = true;

                        $this->database()->insert(Phpfox::getT('petition_invite'), array(
                                'petition_id' => $iId,
                                'type_id' => 1,
                                'user_id' => Phpfox::getUserId(),
                                'invited_email' => $sEmail,
                                'time_stamp' => PHPFOX_TIME
                            )
                        );
                    }
                }
            }
        }

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

            foreach ($aUsers as $aUser) {
                if (isset($aCachedEmails[$aUser['email']])) {
                    continue;
                }

                if (isset($aInvited['user'][$aUser['user_id']])) {
                    continue;
                }

                $sLink = Phpfox::getLib('url')->permalink('petition', $aPetition['petition_id'], $aPetition['title']);

                $sMessage = Phpfox::getPhrase('petition.full_name_invited_you_to_the_title', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean($aVals['title'], 255),
                    'link' => $sLink
                ), false, null, $aUser['language_id']);
                if (!empty($aVals['personal_message'])) {
                    $sMessage .= Phpfox::getPhrase('petition.full_name_added_the_following_personal_message', array(
                            'full_name' => Phpfox::getUserBy('full_name')
                        ), false, null, $aUser['language_id']
                        ) . ":\n" . $aVals['personal_message'];
                }
                $bSent = Phpfox::getLib('mail')->to($aUser['user_id'])
                    ->subject(array('petition.full_name_invited_you_to_the_petition_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean($aVals['title'], 255))))
                    ->message($sMessage)
                    ->notification('petition.invite_to_a_petition')
                    ->send();

                if ($bSent) {
                    $this->_aInvited[] = array('user' => $aUser['full_name']);

                    $iInviteId = $this->database()->insert(Phpfox::getT('petition_invite'), array(
                            'petition_id' => $iId,
                            'user_id' => Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp' => PHPFOX_TIME
                        )
                    );

                    (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('petition_invite', $iId, $aUser['user_id']) : null);
                }
            }
        }
    }


    public function updateView($iId)
    {
        $this->database()->query("
			UPDATE " . $this->_sTable . "
			SET total_view = total_view + 1
			WHERE petition_id = " . (int)$iId . "
		");

        return true;
    }

    public function approve($iId)
    {
        Phpfox::getUserParam('petition.can_approve_petitions', true);

        $aPetition = $this->database()->select('p.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('petition'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.petition_id = ' . (int)$iId)
            ->execute('getRow');

        if (!isset($aPetition['petition_id'])) {
            return Phpfox_Error::set(Phpfox::getPhrase('petition.the_petition_you_are_trying_to_approve_is_not_valid'));
        }

        if ($aPetition['is_approved'] == '1') {
            return false;
        }

        $this->database()->update(Phpfox::getT('petition'), array('is_approved' => '1', 'time_stamp' => PHPFOX_TIME), 'petition_id = ' . $aPetition['petition_id']);

        $iCatId = (int)Phpfox::getService('petition.category')->getCategoriesById($iId);

        $this->database()->query("UPDATE " . Phpfox::getT('petition_category') . "
					  SET used = used + 1
					  WHERE category_id = " . $iCatId . "
					");

        if (Phpfox::isModule('feed') && $aPetition['petition_status'] == 2) {
            $aCallback = ($aPetition['module_id'] != 'petition' ? Phpfox::callback('petition.addPetition', $aPetition['petition_id']) : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->add('petition', $iId, $aPetition['privacy'], $aPetition['privacy_comment'], $aPetition['item_id'], $aPetition['user_id']) : null);
        }

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('petition_approved', $aPetition['petition_id'], $aPetition['user_id']);
        }

        if ($aPetition['is_approved'] == '9') {
            $this->database()->updateCounter('user', 'total_spam', 'user_id', $aPetition['user_id'], true);
        }

        Phpfox::getService('user.activity')->update($aPetition['user_id'], 'petition');

        // Send the user an email
        $sLink = Phpfox::getLib('url')->permalink('petition', $aPetition['petition_id'], $aPetition['title']);
        Phpfox::getLib('mail')->to($aPetition['user_id'])
            ->subject(array('petition.your_petition_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
            ->message(array('petition.your_petition_has_been_approved_on_site_title_message', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
            ->notification('petition.petition_is_approved')
            ->send();
        $this->cache()->remove('petition', 'substr');

        (($sPlugin = Phpfox_Plugin::get('petition.service_process_approve_end')) ? eval($sPlugin) : false);
        return true;
    }


    public function feature($iId, $sType = 0)
    {
        Phpfox::getUserParam('petition.can_feature_petition', true);

        if ($this->database()->update($this->_sTable, array('is_featured' => ($sType == '1' ? 1 : 0)), 'is_approved = 1 AND petition_id = ' . (int)$iId)) {
            $this->cache()->remove('petition_featured');
            return true;
        }
        return false;
    }

    //P_Check
    public function directsign($iId, $sType = 0)
    {
        Phpfox::isAdmin(true);

        $this->database()->update($this->_sTable, array('is_directsign' => 0), '1=1');

        $this->database()->update($this->_sTable, array('is_directsign' => ($sType == '1' ? 1 : 0)), 'petition_id = ' . (int)$iId);

        $this->cache()->remove('petition', 'substr');

        return true;
    }

    public function delete($iId)
    {
        (($sPlugin = Phpfox_Plugin::get('petition.service_process_delete__start')) ? eval($sPlugin) : false);
        $aPetition = Phpfox::getService('petition')->getPetitionForEdit($iId);
        if (!isset($aPetition['petition_id'])) {
            return false;
        }

        $iUserId = Phpfox::getService('petition')->hasAccess($iId, 'delete_own_petition', 'delete_user_petition');

        if (!$iUserId) {
            return false;
        }

        $aTemp = $this->database()->select("image_path")->from(Phpfox::getT('petition_image'))->where("petition_id = '$iId'")->execute("getRows");
        $this->database()->delete(Phpfox::getT('petition_image'), "petition_id = '$iId'");
        $aThumbs = array();
        foreach ($aTemp as $aRow) {
            $aThumbs[] = $aRow["image_path"];
        }

        foreach ($aThumbs as $sImagePath) {
            //$aPetition['image_path'] = $sImagePath;
            if (!empty($sImagePath)) {
                $aImages = array(
                    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, ''),
                    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_50'),
                    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_120'),
                    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_300')
                );
                $iFileSizes = 0;

                foreach ($aImages as $sImage) {
                    if (file_exists($sImage)) {
                        $iFileSizes += filesize($sImage);
                        if ($sPlugin = Phpfox_Plugin::get('petition.service_process_delete__pre_unlink')) {
                            return eval($sPlugin);
                        }
                        @unlink($sImage);
                    }
                }

                if ($iFileSizes > 0) {
                    if ($sPlugin = Phpfox_Plugin::get('petition.service_process_delete__pre_space_update')) {
                        return eval($sPlugin);
                    }
                    Phpfox::getService('user.space')->update($aPetition['user_id'], 'petition', $iFileSizes, '-');
                }
            }
        }

        $this->database()->delete(Phpfox::getT('petition'), "petition_id = " . (int)$iId);
        $this->database()->delete(Phpfox::getT('petition_text'), "petition_id = " . (int)$iId);
        $this->database()->delete(Phpfox::getT('petition_track'), 'item_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('petition_sign'), 'petition_id  = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('petition_news'), 'petition_id  = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('petition_invite'), 'petition_id  = ' . (int)$iId);

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('petition', (int)$iId) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_petition', $iId) : null);

        //Delete pages feed
        if ($aPetition['module_id'] == 'pages') {
            $sType = 'petition';

            $aFeeds = $this->database()->select('feed_id, user_id')
                ->from(Phpfox::getT($aPetition['module_id'] . '_feed'))
                ->where('type_id = \'' . $sType . '\' AND item_id = ' . (int)$iId)
                ->execute('getRows');

            foreach ($aFeeds as $aFeed) {
                $this->database()->delete(Phpfox::getT($aPetition['module_id'] . '_feed'), 'feed_id = ' . $aFeed['feed_id']);
            }
        }
        // Update user activity
        Phpfox::getService('user.activity')->update($aPetition['user_id'], 'petition', '-');

        $this->cache()->remove('petition', 'substr');
        $aRows = $this->database()->select('petition_id, category_id')
            ->from(Phpfox::getT('petition_category_data'))
            ->where('petition_id = ' . (int)$iId)
            ->execute('getRows');

        if (count($aRows)) {
            foreach ($aRows as $aRow) {
                $this->database()->delete(Phpfox::getT('petition_category_data'), "petition_id = " . (int)$aRow['petition_id'] . " AND category_id = " . (int)$aRow['category_id']);
                $this->database()->updateCount('petition_category_data', 'category_id = ' . (int)$aRow['category_id'], 'used', 'petition_category', 'category_id = ' . (int)$aRow['category_id']);
            }
        }

        (($sPlugin = Phpfox_Plugin::get('petition.service_process_delete')) ? eval($sPlugin) : false);
        return true;
    }

    public function deleteInline($iId)
    {
        (($sPlugin = Phpfox_Plugin::get('petition.service_process_deleteinline__start')) ? eval($sPlugin) : false);
        if (($iUserId = Phpfox::getService('petition')->hasAccess($iId, 'delete_own_petition', 'delete_user_petition'))) {
            $this->delete($iId);

            (Phpfox::isModule('attachment') ? Phpfox::getService('attachment.process')->deleteForItem($iUserId, $iId, 'petition') : null);
            (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($iUserId, $iId, 'petition') : null);
            (Phpfox::isModule('tag') ? Phpfox::getService('tag.process')->deleteForItem($iUserId, $iId, 'petition') : null);

            // Update user activity
            //Phpfox::getService('user.activity')->update($iUserId, 'petition', '-');
            $this->cache()->remove('petition', 'substr');
            return true;
        }
        (($sPlugin = Phpfox_Plugin::get('petition.service_process_deleteinline__end')) ? eval($sPlugin) : false);
        return false;
    }

    public function setDefault($iImageId)
    {
        $aPetition = $this->database()->select('pimg.image_path, pimg.server_id, p.petition_id, p.user_id')
            ->from(Phpfox::getT('petition_image'), 'pimg')
            ->join($this->_sTable, 'p', 'p.petition_id = pimg.petition_id')
            ->where('pimg.image_id = ' . (int)$iImageId)
            ->execute('getSlaveRow');

        if (!isset($aPetition['user_id'])) {
            return Phpfox_Error::set(Phpfox::getPhrase('petition.unable_to_find_the_image'));
        }

        if (!Phpfox::getService('user.auth')->hasAccess('petition', 'petition_id', $aPetition['petition_id'], 'petition.delete_own_petition', 'petition.delete_user_petition', $aPetition['user_id'])) {
            return Phpfox_Error::set(Phpfox::getPhrase('petition.you_do_not_have_sufficient_permission_to_modify_this_petition'));
        }

        $this->database()->update($this->_sTable, array('image_path' => $aPetition['image_path'], 'server_id' => $aPetition['server_id']), 'petition_id = ' . $aPetition['petition_id']);

        return true;
    }

    public function deleteImage($iImageId)
    {
        $aPetition = $this->database()->select('pimg.image_id, pimg.image_path, pimg.server_id, p.user_id, p.petition_id, p.image_path AS default_image_path')
            ->from(Phpfox::getT('petition_image'), 'pimg')
            ->join($this->_sTable, 'p', 'p.petition_id = pimg.petition_id')
            ->where('pimg.image_id = ' . (int)$iImageId)
            ->execute('getSlaveRow');

        if (!isset($aPetition['user_id'])) {
            return Phpfox_Error::set(Phpfox::getPhrase('petition.unable_to_find_the_image'));
        }

        if (!Phpfox::getService('user.auth')->hasAccess('petition', 'petition_id', $aPetition['petition_id'], 'petition.delete_own_petition', 'petition.delete_user_petition', $aPetition['user_id'])) {
            return Phpfox_Error::set(Phpfox::getPhrase('petition.you_do_not_have_sufficient_permission_to_modify_this_petition'));
        }

        $iFileSizes = 0;
        $aSizes = array('', 50, 120, 300);
        foreach ($aSizes as $iSize) {
            $sImage = Phpfox::getParam('core.dir_pic') . sprintf($aPetition['image_path'], (empty($iSize) ? '' : '_') . $iSize);
            if (file_exists($sImage)) {
                $iFileSizes += filesize($sImage);

                @unlink($sImage);
            }
        }

        $this->database()->delete(Phpfox::getT('petition_image'), 'image_id = ' . $aPetition['image_id']);
        $bIsNewImage = 0;
        if ($aPetition['default_image_path'] == $aPetition['image_path']) {
            $aPetitionImages = Phpfox::getService('petition')->getImages($aPetition['petition_id']);
            if (!empty($aPetitionImages)) {
                $bIsNewImage = $aPetitionImages[0]['image_id'];
            }
            $this->database()->update($this->_sTable, array('image_path' => (empty($aPetitionImages) ? null : $aPetitionImages[0]['image_path']), 'server_id' => (empty($aPetitionImages) ? null : $aPetitionImages[0]['server_id'])), 'petition_id = ' . $aPetition['petition_id']);
        }

        if ($iFileSizes > 0) {
            Phpfox::getService('user.space')->update($aPetition['user_id'], 'petition', $iFileSizes, '-');
        }

        return $bIsNewImage;
    }

    public function updateCounter($iId, $bMinus = false)
    {
        $this->database()->query("
			UPDATE " . $this->_sTable . "
			SET total_comment = total_comment " . ($bMinus ? "-" : "+") . " 1
			WHERE petition_id = " . (int)$iId . "
		");
    }

    public function __call($sMethod, $aArguments)
    {
        if ($sPlugin = Phpfox_Plugin::get('petition.service_process__call')) {
            return eval($sPlugin);
        }

        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

?>

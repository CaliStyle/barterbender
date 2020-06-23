<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Contest_Process extends Phpfox_service
{

    private $_aImageSizes = array(50, 100, 160, 240, 400);

    private $_aCategories = array();


    public function __construct()
    {
        $this->_sTable = Phpfox::getT('contest');
        $this->_sDirContest = Phpfox::getService('contest.contest')->getContestImageDir();
        if (!is_dir($this->_sDirContest)) {
            mkdir($this->_sDirContest);
        }

    }

    /**
     * add new contest
     * @param array $aVals
     * @return int
     */
    public function add($aVals)
    {
        $aInsert = $this->createInsertUpdateArrayFromVal($aVals, Phpfox::getUserId());

        if (!$aInsert) {
            return false;
        }

        $bHasAttachments = (!empty($aVals['attachment']));
        $aInsert['total_attachment'] = 0;

        $iContestId = $this->database()->insert($this->_sTable, $aInsert);

        $this->addCategoriesForContest($iContestId);

        #Email and condition
        $aEmailCondition = array(
            'term_condition' => $aVals['term_condition'],
            'contest_id' => $iContestId
        );
        $this->addEmailCondition($aEmailCondition);

        if ($aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('contest', $iContestId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }

        #If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iContestId);
        }

        Phpfox::getService('contest.mail.process')->sendEmailTo(
            $iTemplateType = Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('create_contest_successfully'),
            $iContestId,
            $aReceivers = Phpfox::getUserId()
        );

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['yn_contest_add_description'])) {
            Phpfox::getService('tag.process')->add('contest', $iContestId, Phpfox::getUserId(), $aVals['yn_contest_add_description'], true);
        }

        $this->checkPublishFormThenPublishAndNavigate($aVals, $iContestId);

        (($sPlugin = Phpfox_Plugin::get('contest.service_contest_process_add_end')) ? eval($sPlugin) : false);
        return $iContestId;
    }

    public function uploadImages()
    {
        $sImageName = md5(PHPFOX_TIME . 'contest') . '%s.jpg';
        $sContestImageDir = Phpfox::getService('contest.contest')->getContestImageDir();
        if (isset($_FILES['image'])) {
            $oImage = Phpfox::getLib('image');
            $oFile = Phpfox::getLib('file');
            $sInvalid = '';
            $iFileSizes = 0;
            $iUploaded = 0;

            // currently we upload only 1 image
            // foreach ($_FILES['image']['error'] as $iKey => $sError) {

            $sError = $_FILES['image']['error'];
            if ($sError == UPLOAD_ERR_OK) {
                if ($aImage = $oFile->load('image', array(
                    'jpg',
                    'gif',
                    'png'
                ), (Phpfox::getUserParam('contest.max_upload_size_contest') === 0 ? null : (Phpfox::getUserParam('contest.max_upload_size_contest') / 1024))
                )
                ) {
                    $sFileName = Phpfox::getLib('file')->upload('image', $sContestImageDir, $sImageName);

                    $iFileSizes += filesize($sContestImageDir . sprintf($sFileName, ''));


                    foreach ($this->_aImageSizes as $iSize) {
                        $oImage->createThumbnail($sContestImageDir . sprintf($sFileName, ''), $sContestImageDir . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
                        $iFileSizes += filesize($sContestImageDir . sprintf($sFileName, '_' . $iSize));
                    }
                } else {
                    if ($sInvalid != '')
                        $sInvalid .= '<li>' . $_FILES['image']['name'] . '</li>';
                    else
                        $sInvalid = '<li>' . $_FILES['image']['name'] . '</li>';
                }
            }

            if (isset($sInvalid) && $sInvalid != '') {
//			   Phpfox_Error::set(_p('fundraising.invalid_files'). '<br/><ul style="margin-left: 20px">'. $sInvalid.'</ul>');
                return false;
            }

            if ($iFileSizes != 0 && $sInvalid == '') {
                // Update user space usage
                // @todo: add this later
//				Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'fundraising', $iFileSizes);

                return $sFileName;
            }

            return true;
        }
    }

    public function payForPublishContest($aVals, $iContestId)
    {
        $oTransaction = Phpfox::getService('contest.transaction');
        $aFees = Phpfox::getService('contest.contest')->getAllFees();
        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);
        $sGateway = 'paypal';
        $sUrl = urlencode(Phpfox::getLib('url')->permalink('contest', $aContest['contest_id'], $aContest['contest_name']));

        $iTotal = 0;

        $bIsPublish = 0;


        if (isset($aVals['publish_fee'])) {
            //handle case publish fee = 0
            if ($aFees['publish'] == 0) {
                // we already handle it in ajax function processPayForPublishContest
            } else {
                $bIsPublish = 1;
                $iTotal += (float)$aFees['publish'];
            }
        }

        $bIsPremium = 0;
        if (isset($aVals['premium_fee'])) {
            $bIsPremium = 1;
            $iTotal += (float)$aFees['premium'];
        }

        $bIsFeature = 0;
        if (isset($aVals['feature_fee'])) {
            $bIsFeature = 1;
            $iTotal += (float)$aFees['feature'];
        }

        $bIsEndingSoon = 0;
        if (isset($aVals['ending_soon_fee'])) {
            $bIsEndingSoon = 1;
            $iTotal += (float)$aFees['ending_soon'];
        }

        if ($iTotal <= 0) {
            //do st about this later
            return array(
                'result' => true,
                'checkout_url' => Phpfox::getService('contest.contest')->getContestUrl($iContestId)
            );
        }


        $aInvoice = array(
            'is_publish' => $bIsPublish,
            'is_premium' => $bIsPremium,
            'is_feature' => $bIsFeature,
            'is_ending_soon' => $bIsEndingSoon
        );


        $aInsert = array(
            'invoice' => serialize($aInvoice),
            'contest_id' => $iContestId,
            'time_stamp' => PHPFOX_TIME,
            'status' => Phpfox::getService('contest.constant')->getTransactionStatusIdByStatusName('initialized'),
            'amount' => $iTotal,
            'user_id' => Phpfox::getUserId()
        );

        $iTransactionId = Phpfox::getService('contest.transaction.process')->add($aInsert);

        if ($iTransactionId > 0) {
            return array(
                'result' => true,
                'checkout_url' => Phpfox::getLib('url')->makeUrl('contest.payment', array('transactionid' => $iTransactionId))
            );
        } else {
            return false;
        }

    }

    public function update($aVals, $iContestId)
    {
        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

        // update email template
        if (isset($aVals['save_email_condition'])) {
            return $this->updateEmailTemplate($aVals['subject'], $aVals['message'], $iContestId);
        }

        //invite friends
        if (isset($aVals['submit_invite'])) {
            return $this->sendInvite($aVals, $iContestId);
        }

        if (isset($aVals['save_settings'])) {
            return $this->updateSettings($aVals, $iContestId);
        }

        $aUpdate = $this->createInsertUpdateArrayFromVal($aVals, $aContest['user_id']);
        if (!$aUpdate) {
            return false;
        }

        $bHasAttachments = (!empty($aVals['attachment']) && $aContest['user_id'] == Phpfox::getUserId());
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], $aContest['user_id'], $iContestId);
        }

        $aUpdate['total_attachment'] = (Phpfox::isModule('attachment') ? Phpfox::getService('attachment')->getCountForItem($iContestId, 'contest') : '0');

        $this->database()->update($this->_sTable, $aUpdate, 'contest_id = ' . $iContestId);

        $this->addCategoriesForContest($iContestId);

        $this->updateTermCondition($aVals['term_condition'], $iContestId);

        if (Phpfox::isModule('privacy')) {
            if ($aVals['privacy'] == '4') {
                Phpfox::getService('privacy.process')->update('contest', $iContestId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            } else {
                Phpfox::getService('privacy.process')->delete('contest', $iContestId);
            }
        }

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['yn_contest_add_description'])) {
            $aContest = $this->database()->select('c.contest_id, c.user_id')
                ->from(Phpfox::getT('contest'), 'c')
                ->where('c.contest_id = ' . $iContestId)
                ->execute('getSlaveRow');

            if (isset($aContest['contest_id'])) {
                Phpfox::getService('tag.process')->update('contest', $iContestId, $aContest['user_id'], $aVals['yn_contest_add_description'], true);
            }
        }

        $this->checkPublishFormThenPublishAndNavigate($aVals, $iContestId);

        return $iContestId;
    }

    public function checkPublishFormThenPublishAndNavigate($aVals, $iContestId)
    {
        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId, $bIsCache = false);

        if (isset($aVals['publish_contest'])) {
            $aFees = Phpfox::getService('contest.contest')->getAllFees();
            if (($aFees['publish'] <= 0 ||
                //denied contest is published so we don't need to charge it again

                $aContest['contest_status'] == Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('denied')
            )
            ) {
                $bResult = Phpfox::getService('contest.contest.process')->publishContest($iContestId);

                if ($bResult) {
                    Phpfox::addMessage(_p('contest.contest_successfully_published'));

                    $bIsShowRegisterPopup = 0;
                    if (Phpfox::getService('contest.permission')->canRegisterService($iContestId, Phpfox::getUserId())) {
                        $bIsShowRegisterPopup = 1;
                    }

                    $sUrl = Phpfox::getLib('url')->permalink('contest', $aContest['contest_id'], $aContest['contest_name'], $bRedirect = false, $sMessage = NULL, $aExtra = array('registerservice' => $bIsShowRegisterPopup));

                    Phpfox::getLib('url')->send($sUrl);

                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function createInsertUpdateArrayFromVal($aVals, $user_id)
    {
        if (!$this->getCategoriesFromForm($aVals)) {
            return false;
        }

        $oFilter = Phpfox::getLib('parse.input');
        $sContestName = $oFilter->clean($aVals['contest_name'], 255);
        $sShortDescription = $oFilter->clean($aVals['short_description'], 160);
        $sShortDescriptionParsed = $oFilter->prepare($aVals['short_description']);

        $iNumberOfMaximumEntry = $aVals['maximum_entry'];
        $iPrivacy = $aVals['privacy'];
        $iPrivacyComment = isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : 0;
        $sAward = $oFilter->clean($aVals['award']);
        $sAwardParsed = $oFilter->prepare($aVals['award']);

        $iType = isset($aVals['contest_type']) ? Phpfox::getService('contest.constant')->getContestTypeIdByTypeName($aVals['contest_type']) : 1;

        if (!$iType) {
            $iType = 1;
        }

        $sMaindescription = $oFilter->clean($aVals['yn_contest_add_description']);
        $sMaindescriptionParsed = $oFilter->prepare($aVals['yn_contest_add_description']);

        $aTime = array();
        $aTimeLine = Phpfox::getService('contest.constant')->getTimeLine();

        foreach ($aTimeLine as $k => $sTimeLine) {
            $aTime[$sTimeLine] = Phpfox::getLib('date')->mktime($aVals[$sTimeLine . '_hour'], $aVals[$sTimeLine . '_minute'], $iStartSubmitSecond = 59, $aVals[$sTimeLine . '_month'], $aVals[$sTimeLine . '_day'], $aVals[$sTimeLine . '_year']);

            // on the interface we have convert into gmt, now we roll back to server time
            $aTime[$sTimeLine] = Phpfox::getService('contest.helper')->convertFromUserTimeZoneToServerTime($aTime[$sTimeLine]);
        }

        //server validation
        if (!$this->_verifyTime($aTime)) {
            return false;
        }

        if (isset($_FILES['image'])) {
            $sImagePath = Phpfox::getService('contest.contest.process')->uploadImages();


            if (!$sImagePath) {
                return Phpfox_Error::set(_p('contest.invalid_image'));
            }
        }

        $aRow = array(
            'contest_name' => $sContestName,
            'short_description' => $sShortDescription,
            'short_description_parsed' => $sShortDescriptionParsed,
            'description' => $sMaindescription,
            'description_parsed' => $sMaindescriptionParsed,
            'time_stamp' => PHPFOX_TIME,
            'privacy' => $iPrivacy,
            'type' => $iType,
            'award_description' => $sAward,
            'award_description_parsed' => $sAwardParsed,
            'privacy_comment' => $iPrivacyComment,
            'number_entry_max' => $iNumberOfMaximumEntry,
            'user_id' => $user_id,
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'number_winning_entry_max' => $aVals['num_winning_entry'],
            'is_auto_approve' => isset($aVals['automatic_approve']) ? 1 : 0,
            'vote_without_join' => isset($aVals['vote_without_join']) ? 1 : 0
        );

        foreach ($aTimeLine as $k => $sTimeLine) {
            $aRow[$sTimeLine] = $aTime[$sTimeLine];
        }

        if (strlen($sImagePath) > 5) {
            $aRow['image_path'] = $sImagePath;
        }


        if (isset($aVals['save_as_draft'])) {
            $aRow['contest_status'] = Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('draft');
        }

        return $aRow;
    }

    private function _verifyTime($aTime)
    {
        if ($aTime['start_time'] < $aTime['begin_time']) {
            return Phpfox_Error::set(_p('contest.the_start_time_of_submitting_must_be_greater_than_or_equal_to_the_start_time_of_contest'));
        }

        if ($aTime['stop_time'] <= $aTime['start_time']) {
            return Phpfox_Error::set(_p('contest.the_end_time_of_submitting_must_be_greater_than_the_start_time_of_it'));
        }

        if ($aTime['stop_time'] <= PHPFOX_TIME) {
            return Phpfox_Error::set(_p('contest.the_end_time_of_submitting_must_be_greater_than_current_time'));
        }

        if ($aTime['stop_vote'] <= $aTime['start_vote']) {
            return Phpfox_Error::set(_p('contest.the_end_time_of_voting_must_be_greater_than_the_start_time_of_it'));
        }

        if ($aTime['stop_vote'] < $aTime['stop_time']) {
            return Phpfox_Error::set(_p('contest.the_end_time_of_voting_must_be_greater_than_or_equal_to_the_end_time_of_submitting'));
        }

        if ($aTime['end_time'] < $aTime['stop_vote']) {
            return Phpfox_Error::set(_p('contest.the_end_time_of_contest_must_be_greater_than_or_equal_to_the_end_time_of_voting'));
        }

        return true;
    }

    //this function will catch all categories submited and store them into $_aCategories for later use
    public function getCategoriesFromForm($aVals)
    {
        if (isset($aVals['category']) && count($aVals['category'])) {

            if (empty($aVals['category'][0])) {
                return Phpfox_Error::set(_p('contest.provide_a_category_this_contest_will_belong_to'));
            } else {
                foreach ($aVals['category'] as $iCategory) {
                    if (empty($iCategory)) {
                        continue;
                    }

                    if (!is_numeric($iCategory)) {
                        continue;
                    }

                    $this->_aCategories[] = $iCategory;
                }
            }
        }

        return true;
    }

    public function addCategoriesForContest($iContestId)
    {

        if (isset($this->_aCategories) && count($this->_aCategories)) {
            $this->database()->delete(Phpfox::getT('contest_category_data'), 'contest_id = ' . (int)$iContestId);

            foreach ($this->_aCategories as $iCategoryId) {
                $this->database()->insert(Phpfox::getT('contest_category_data'), array('contest_id' => $iContestId, 'category_id' => $iCategoryId));
            }
        }
    }

    public function publishContest($iContestId)
    {

        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);


        if (Phpfox::getUserGroupParam($aContest['user_group_id'], 'contest.approve_contests')) {
            $aUpdate = array(
                'contest_status' => Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('pending'),
                'is_published' => 1
            );

            $this->database()->update($this->_sTable, $aUpdate, 'contest_id = ' . $iContestId);
        } else {
            // publish a Contest here
            $aUpdate = array(
                'contest_status' => Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going'),
                'is_published' => 1
            );

            $this->database()->update($this->_sTable, $aUpdate, 'contest_id = ' . $iContestId);

            //we will modify here if supporting pages
            $aCallback = null;

            $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);
            // create feed
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->allowGuest()->callback($aCallback)->add('contest', $iContestId, $aContest['privacy'], (isset($aContest['privacy_comment']) ? (int)$aContest['privacy_comment'] : 0), 0, $aContest['user_id']) : null);

            // Update user activity
            Phpfox::getService('user.activity')->update($aContest['user_id'], 'contest');
        }


        (($sPlugin = Phpfox_Plugin::get('contest.service_contest_process_publishcontest_end')) ? eval($sPlugin) : false);

        return true;
    }

    public function addOrUpdate($aVals)
    {
        $iContestId = 0;

        if ($aVals['yncontest_is_edit']) {
            $aContest = Phpfox::getService('contest.contest')->getContestById($aVals['contest_id']);

            $iContestId = Phpfox::getService('contest.contest.process')->update($aVals, $aVals['contest_id']);

        } else {
            $iContestId = Phpfox::getService('contest.contest.process')->add($aVals);
        }

        return $iContestId;
    }


    public function updatePublishedContest($aVals, $iContestId)
    {
        $oFilter = Phpfox::getLib('parse.input');
        $sShortDescription = $oFilter->clean($aVals['short_description'], 160);
        $sShortDescriptionParsed = $oFilter->prepare($aVals['short_description']);

        $sAward = $oFilter->clean($aVals['award']);

        $sMaindescription = $oFilter->clean($aVals['yn_contest_add_description']);
        $sMaindescriptionParsed = $oFilter->prepare($aVals['yn_contest_add_description']);


        $aRow = array(
            'short_description' => $sShortDescription,
            'short_description_parsed' => $sShortDescriptionParsed,
            'description' => $sMaindescription,
            'description_parsed' => $sMaindescriptionParsed,
            'time_stamp' => PHPFOX_TIME,
            'award_description' => $sAward,
        );


        $this->database()->update($this->_sTable, $aRow, 'contest_id = ' . $iContestId);

        return $iContestId;
    }

    public function addEmailCondition($aVals)
    {
        $aTemplate = Phpfox::getService('contest.mail')->getEmailTemplateByTypeName('thanks_participant');

        $aInsert = array(
            'subject' => $aTemplate['subject'],
            'message' => $aTemplate['content'],
            'term_condition' => Phpfox::getLib('parse.input')->clean($aVals['term_condition']),
            'contest_id' => $aVals['contest_id'],
            'user_id' => Phpfox::getUserId()
        );

        return $this->database()->insert(Phpfox::getT('contest_email_condition'), $aInsert);
    }

    public function updateEmailTemplate($sSubject, $sMessage, $iContestId)
    {
        $aUpdate = array(
            'subject' => Phpfox::getLib('parse.input')->clean($sSubject),
            'message' => Phpfox::getLib('parse.input')->clean($sMessage),
        );

        $this->database()->update(Phpfox::getT('contest_email_condition'), $aUpdate, 'contest_id = ' . $iContestId);

        return true;
    }

    public function updateTermCondition($sTermCondition, $iContestId)
    {
        $aUpdate = array(
            'term_condition' => Phpfox::getLib('parse.input')->clean($sTermCondition)
        );

        $this->database()->update(Phpfox::getT('contest_email_condition'), $aUpdate, 'contest_id = ' . $iContestId);

        return true;
    }

    public function featureContest($iContestId, $iType = 1)
    {
        if ($this->database()->update($this->_sTable, array('is_feature' => ($iType == '1' ? 1 : 0)), 'contest_id = ' . (int)$iContestId)) {

            if ($iType == 1) {
                $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

                $sSubject = _p('contest.your_contest_title_has_been_featured_link',
                    array(
                        'title' => $aContest['contest_name'],
                        'link' => ''
                    ));
                $sMessage = _p('contest.your_contest_title_has_been_featured_link',
                    array(
                        'title' => $aContest['contest_name'],
                        'link' => Phpfox::permalink('contest', $aContest['contest_id'], $aContest['contest_name'])
                    ));

                //here we use fox mailing function
                Phpfox::getLib('mail')->sendToSelf(true)
                    ->to($aContest['user_id'])
                    ->subject($sSubject)
                    ->message($sMessage)
                    ->send();
            }


            return true;
        }
        return false;
    }

    public function premiumContest($iContestId, $iType = 1)
    {
        if ($this->database()->update($this->_sTable, array('is_premium' => ($iType == '1' ? 1 : 0)), 'contest_id = ' . (int)$iContestId)) {
            if ($iType == 1) {
                $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

                $sSubject = _p('contest.your_contest_title_has_been_premiumed_link',
                    array(
                        'title' => $aContest['contest_name'],
                        'link' => ''
                    ));
                $sMessage = _p('contest.your_contest_title_has_been_premiumed_link',
                    array(
                        'title' => $aContest['contest_name'],
                        'link' => Phpfox::permalink('contest', $aContest['contest_id'], $aContest['contest_name'])
                    ));

                //here we use fox mailing function
                Phpfox::getLib('mail')->sendToSelf(true)
                    ->to($aContest['user_id'])
                    ->subject($sSubject)
                    ->message($sMessage)
                    ->send();
            }

            return true;
        }
        return false;
    }

    public function endingSoonContest($iContestId, $iType = 1)
    {
        if ($this->database()->update($this->_sTable, array('is_ending_soon' => ($iType == '1' ? 1 : 0)), 'contest_id = ' . (int)$iContestId)) {
            if ($iType == 1) {
                $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

                $sSubject = _p('contest.contest_ending_soon_email_subject',
                    array(
                        'title' => $aContest['contest_name']
                    ));


                $sSetting = PHpfox::getParam('contest.ending_soon_setting');
                $iDay = Phpfox::getParam('contest.ending_soon_before');
                $iTime = $iDay * 24 * 3600;

                $iStartEndingSoonTime = 0;
                if ($sSetting == 'End of Submission') {
                    $sCriteria = _p('contest.end_of_submission');
                } elseif ($sSetting == 'End of Contest') {
                    $sCriteria = _p('contest.end_of_contest');
                }

                $sMessage = _p('contest.your_contest_title_has_been_set_ending_soon_link',
                    array(
                        'title' => $aContest['contest_name'],
                        'link' => Phpfox::permalink('contest', $aContest['contest_id'], $aContest['contest_name']),
                        'num_of_day' => $iDay,
                        'criteria' => $sCriteria
                    ));

                //here we use fox mailing function
                Phpfox::getLib('mail')->sendToSelf(true)
                    ->to($aContest['user_id'])
                    ->subject($sSubject)
                    ->message($sMessage)
                    ->send();
            }
            return true;
        }
        return false;
    }

    public function activeContest($iContestId, $iType = 1)
    {
        if ($this->database()->update($this->_sTable, array('is_active' => ($iType == '1' ? 1 : 0)), 'contest_id = ' . (int)$iContestId)) {
            if ($iType == 0) {
                $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

                $sSubject = _p('contest.contest_inactive_email_subject',
                    array(
                        'title' => $aContest['contest_name']
                    ));


                $sMessage = _p('contest.your_contest_title_has_been_set_inactive_link',
                    array(
                        'title' => $aContest['contest_name'],
                        'link' => Phpfox::permalink('contest', $aContest['contest_id'], $aContest['contest_name'])
                    ));

                //here we use fox mailing function
                Phpfox::getLib('mail')->sendToSelf(true)
                    ->to($aContest['user_id'])
                    ->subject($sSubject)
                    ->message($sMessage)
                    ->send();

                $aContest = Phpfox::getService('contest.contest')->getContestForCheckingPermission($iContestId);

                (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'contest_notice_inactive', $iItemId = $iContestId, $aContest['user_id']) : null);
            }
            return true;
        }
        return false;
    }

    public function processUserRequest($iTransactionId)
    {
        $aRequest = Phpfox::getService('contest.transaction')->getUserRequestsFromTransaction($iTransactionId);
        if (!$aRequest) {
            return false;
        }

        $iContestId = $aRequest['iContestId'];
        $aService = $aRequest['aService'];

        foreach ($aService as $sService) {
            switch ($sService) {
                case 'publish':
                    Phpfox::getService('contest.contest.process')->publishContest($iContestId);
                    break;
                case 'premium':
                    Phpfox::getService('contest.contest.process')->premiumContest($iContestId);
                    break;
                case 'feature':
                    Phpfox::getService('contest.contest.process')->featureContest($iContestId);
                    break;
                case 'ending_soon':
                    Phpfox::getService('contest.contest.process')->endingSoonContest($iContestId);
                    break;
                default:
                    break;
            }
        }

        (($sPlugin = Phpfox_Plugin::get('contest.service_process_update_user_request__end')) ? eval($sPlugin) : false);
        return true;
    }

    public function sendInvite($aVals, $iContestId)
    {
        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);
        $oParseInput = Phpfox::getLib('parse.input');

        // for testing only
        // $aVals['subject'] = null;
        // $aVals['personal_message'] = null;


        // check is invited, slow performance 
        if (isset($aVals['emails']) || isset($aVals['invite'])) {
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('contest_invite'))
                ->where('item_id = ' . (int)$aContest['contest_id'] . ' AND type_id = ' . Phpfox::getService('contest.constant')->getInviteTypeIdByName('contest'))
                ->execute('getRows');

            $aInvited = array();
            foreach ($aInvites as $aInvite) {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
            }
        }

        if (!empty($aVals['personal_message'])) {
            $sMessage = $aVals['personal_message'];

        } else {
            //in case user leave message box empty
            $sLink = Phpfox::getLib('url')->permalink('contest', $iId = $iContestId, $sTitle = $aContest['contest_name']);
            $sMessage = _p('contest.full_name_invited_you_to_the_title', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean($aContest['contest_name'], 255),
                    'link' => $sLink
                )
            );

        }

        if (!empty($aVals['subject'])) {
            $sSubject = $aVals['subject'];
        } else {
            //in case user leave subject box empty
            $sSubject = _p('contest.full_name_invited_you_to_the_contest_title', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean($aContest['contest_name'], 255),
                )
            );
        }

        if (isset($aVals['contest_entry_id']) && $aVals['contest_entry_id']) {
            $iEntryId = $aVals['contest_entry_id'];
            $aEntry = Phpfox::getService('contest.entry')->getContestEntryById($iEntryId);

            Phpfox::getService('contest.mail.process')->setEntryOwner($aEntry['user_id']);
            Phpfox::getService('contest.mail.process')->setEntry($iEntryId);
        }

        $sSubject = Phpfox::getService('contest.mail.process')->parseTemplate($sSubject, $aContest);
        $sMessage = Phpfox::getService('contest.mail.process')->parseTemplate($sMessage, $aContest);

        $aCustomMesssage = array(
            'subject' => $sSubject,
            'message' => $sMessage
        );


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

//                    if (isset($aInvited['email'][$sEmail]))
//                    {
//                        continue;
//                    }

                    if (isset($aCachedEmails[$sEmail]) && $aCachedEmails[$sEmail] == true) {
                        continue;
                    }

                    $bResult = Phpfox::getService('contest.mail.process')->sendEmailTo($sType = 0, $aContest['contest_id'], $aReceivers = $sEmail, $aCustomMesssage);

                    if ($bResult) {
                        $this->database()->insert(Phpfox::getT('contest_invite'), array(
                                'item_id' => $aContest['contest_id'],
                                'user_id' => Phpfox::getUserId(),
                                'invited_email' => $sEmail,
                                'time_stamp' => PHPFOX_TIME,
                                'type_id' => Phpfox::getService('contest.constant')->getInviteTypeIdByName('contest')
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

                $bResult = Phpfox::getService('contest.mail.process')->sendEmailTo($sType = 0, $aContest['contest_id'], $aReceivers = $aUser['user_id'], $aCustomMesssage);

                if ($bResult) {
                    $iInviteId = $this->database()->insert(Phpfox::getT('contest_invite'), array(
                            'item_id' => $aContest['contest_id'],
                            'user_id' => Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp' => PHPFOX_TIME,
                            'type_id' => Phpfox::getService('contest.constant')->getInviteTypeIdByName('contest')
                        )
                    );
                }


                (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('contest_invited', $aContest['contest_id'], $aUser['user_id']) : null);
            }
        }
        return true;
    }

    public function closeContest($iContestId)
    {
        $aUpdate = array(
            'contest_status' => Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('closed')
        );

        $result = $this->database()->update($this->_sTable, $aUpdate, 'contest_id = ' . $iContestId);

        if ($result) {
            Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('close_contest', $iContestId);
        }

        return $result;
    }

    public function deleteContest($iContestId)
    {
        $this->database()->update(Phpfox::getT('contest'), array(
            'is_deleted' => 1,
        ), 'contest_id = ' . $iContestId);
        return true;

        /*Please do not remove content below.
         * $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

          //delete all image of contest
          Phpfox::getService('contest.contest.process')->deleteAllContestImages($iContestId);

          $this->database()->delete(Phpfox::getT('contest'), "contest_id = " . (int) $iContestId);

        //delete contest anouncement
        $this->database()->delete(Phpfox::getT('contest_announcement'), "contest_id = " . (int) $iContestId);

        $this->database()->delete(Phpfox::getT('contest_category_data'), "contest_id = " . (int) $iContestId);
        $this->database()->delete(Phpfox::getT('contest_email_condition'), "contest_id = " . (int) $iContestId);

        $this->database()->delete(Phpfox::getT('contest_invite'), "item_id = " . (int) $iContestId . ' AND type_id = ' . Phpfox::getService('contest.constant')->getInviteTypeIdByName('contest'));

        $this->database()->delete(Phpfox::getT('contest_participant'), "contest_id = " . (int) $iContestId);

        $this->database()->delete(Phpfox::getT('contest_transaction'), "contest_id = " . (int) $iContestId);

        //@todo: delete entry here
        */
        return true;
    }

    public function deleteAllContestImages($iContestId)
    {

        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);
        if (!$aContest['image_path']) {
            return true;
        }

        $iFileSizes = 0;
        $aSizes = $this->_aImageSizes;
        //delete original image
        $sImage = Phpfox::getParam('core.dir_pic') . 'contest/' . sprintf($aContest['image_path'], '');
        if (file_exists($sImage)) {
            $iFileSizes += filesize($sImage);

            @unlink($sImage);
        }

        foreach ($aSizes as $iSize) {
            $sImage = Phpfox::getParam('core.dir_pic') . 'contest/' . sprintf($aContest['image_path'], (empty($iSize) ? '' : '_') . $iSize);
            if (file_exists($sImage)) {
                $iFileSizes += filesize($sImage);

                @unlink($sImage);
            }
        }

        return true;

    }

    public function approveContest($iContestId)
    {
        $aUpdate = array(
            'contest_status' => Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going'),
            'is_approved' => 1
        );

        $bResult = $this->database()->update($this->_sTable, $aUpdate, 'contest_id = ' . $iContestId);
        if ($bResult) {
            $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);
            // create feed
            $aCallback = null;

            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->allowGuest()->callback($aCallback)->add('contest', $iContestId, $aContest['privacy'], (isset($aContest['privacy_comment']) ? (int)$aContest['privacy_comment'] : 0), 0, $aContest['user_id']) : null);
            Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('approve_contest', $iContestId);
            (($sPlugin = Phpfox_Plugin::get('contest.service_contest_process_approvecontest_end')) ? eval($sPlugin) : false);
        }

        return $bResult;
    }

    public function denyContest($iContestId)
    {
        $aUpdate = array(
            'contest_status' => Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('denied')
        );

        $bResult = $this->database()->update($this->_sTable, $aUpdate, 'contest_id = ' . $iContestId);

        if ($bResult) {
            Phpfox::getService('contest.contest.process')->sendNotificationAndEmail('deny_contest', $iContestId);
        }
        return $bResult;
    }

    public function updateStatusRegisterService($user_id, $contest_id, $type, $value)
    {
        $aUpdates = array();
        if ($type == "is_ending_soon" || $type == '[is_premium' || $type = 'is_feature') {

            $aUpdates[$type] = $value;
            if ($value == 0) {
                $aUpdates['contest_status'] = 5; //close
            }

            $this->database()->update(Phpfox::getT('contest'), $aUpdates, 'user_id = ' . $user_id . ' and contest_id = ' . $contest_id);
        }
    }

    public function viewContest($contest_id, $total_view)
    {
        if ($this->database()->update(Phpfox::getT('contest'), array(
            'total_view' => $total_view + 1
        ), 'contest_id = ' . $contest_id)
        ) {
            return $total_view + 1;
        }
        return $total_view;
    }

    public function updateSettings($aVals, $iContestId)
    {

        $aUpdate = array(
            'number_winning_entry_max' => $aVals['num_winning_entry'],
            'is_auto_approve' => isset($aVals['automatic_approve']) ? 1 : 0
        );

        $this->database()->update(Phpfox::getT('contest'), $aUpdate, 'contest_id = ' . $iContestId);

        return $iContestId;
    }

    public function checkAndUpdateStatusOfContests($aParam = array())
    {

        $sPreCond = 'contest_status = ' . Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going');

        // custome condition used when we want to check only 1 contest
        // this server mererly for performance purpose, update on the whole database is awkward in some cases
        if (isset($aParam['custom_condition'])) {
            $sPreCond .= $aParam['custom_condition'];
        }

        // we find contest start submit
        // can_submit_entry variable used to detect is the contest in proper period to submit entry
        $sCond = $sPreCond . ' AND can_submit_entry = 0 AND  start_time <= ' . PHPFOX_TIME .
            ' AND stop_time >= ' . PHPFOX_TIME;
        $aRows = $this->database()->select('contest_id')
            ->from($this->_sTable)
            ->where($sCond)
            ->execute('getSlaveRows');

        foreach ($aRows as $aRow) {
            Phpfox::getService('contest.contest.process')->startSubmitAContest($aRow['contest_id']);
        }


        // we find contest stop submit
        $sCond = $sPreCond . ' AND can_submit_entry = 1 AND stop_time <= ' . PHPFOX_TIME;
        $aRows = $this->database()->select('contest_id')
            ->from($this->_sTable)
            ->where($sCond)
            ->execute('getSlaveRows');

        foreach ($aRows as $aRow) {
            Phpfox::getService('contest.contest.process')->setEndsubmitEntryContest($aRow['contest_id']);
        }

        // we find contest ended
        $sCond = $sPreCond . ' AND end_time <= ' . PHPFOX_TIME;
        $aRows = $this->database()->select('contest_id')
            ->from($this->_sTable)
            ->where($sCond)
            ->execute('getSlaveRows');
        foreach ($aRows as $aRow) {
            Phpfox::getService('contest.contest.process')->endAContest($aRow['contest_id']);
        }


        return true;
    }

    public function endAContest($iContestId)
    {
        $this->closeContest($iContestId);
    }

    public function startSubmitAContest($iContestId)
    {
        $aUpdate = array(
            'can_submit_entry' => 1
        );

        $this->database()->update(Phpfox::getT('contest'), $aUpdate, 'contest_id = ' . $iContestId);

        return $iContestId;
    }


    public function setEndsubmitEntryContest($iContestId)
    {
        $aUpdate = array(
            'can_submit_entry' => 0
        );

        $this->database()->update(Phpfox::getT('contest'), $aUpdate, 'contest_id = ' . $iContestId);

        return $iContestId;
    }


    public function checkAndUpdateStatusOfAContest($iContestId)
    {
        $sCustomCond = ' AND contest_id = ' . $iContestId . ' AND contest_status = ' . Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('on_going');
        $aParam = array(
            'custom_condition' => $sCustomCond
        );
        Phpfox::getService('contest.contest.process')->checkAndUpdateStatusOfContests($aParam);
    }


    public function sendNotificationAndEmail($sType, $iContestId, $iEntryId = null)
    {

        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

        switch ($sType) {
            case 'close_contest':
                // onwer, follower, participant
                // firstly we get all follower and participant
                $aUsers = Phpfox::getService('contest.participant')->getAllParticipantAndFollowerOfContest($aContest['contest_id']);
                foreach ($aUsers as $aUser) {
                    (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'contest_notice_close', $iItemId = $aContest['contest_id'], $aUser['user_id'], $iSentUserId = $aContest['user_id']) : null);
                }

                //entry owner must be a participant so we do not need to send here
                //then we send email to owner
                Phpfox::getService('contest.mail.process')->sendEmailTo(
                    $iTemplateType = Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('contest_closed'),
                    $aContest['contest_id'],
                    $aReceivers = $aContest['user_id']
                );
                break;


            case 'join_contest':

                $iParticipantId = Phpfox::getService('contest.participant')->getParticipantIdByContestAndUserId($aContest['contest_id'], Phpfox::getUserId());

                // send notification for owner
                (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'contest_notice_join', $iItemId = $iParticipantId, $aContest['user_id'], $iSenderUserId = null) : null);


                // send email for participant
                Phpfox::getService('contest.mail.process')->setParticipant($iParticipantId);

                Phpfox::getService('contest.mail.process')->sendEmailTo(
                    $iTemplateType = Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('thanks_participant'),
                    $aContest['contest_id'],
                    $aReceivers = Phpfox::getUserId()
                );
                break;

            case 'leave_contest':

                $iParticipantId = Phpfox::getService('contest.participant')->getParticipantIdByContestAndUserId($aContest['contest_id'], Phpfox::getUserId());

                // send notification for owner
                (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'contest_notice_leave', $iItemId = $iParticipantId, $aContest['user_id'], $iSenderUserId = null) : null);

                break;
            case 'favorite_contest':

                $iParticipantId = Phpfox::getService('contest.participant')->getParticipantIdByContestAndUserId($aContest['contest_id'], Phpfox::getUserId());

                // send notification for owner
                (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'contest_notice_favorite', $iItemId = $iParticipantId, $aContest['user_id'], $iSenderUserId = null) : null);

                break;
            case 'follow_contest':

                $iParticipantId = Phpfox::getService('contest.participant')->getParticipantIdByContestAndUserId($aContest['contest_id'], Phpfox::getUserId());

                // send notification for owner
                (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'contest_notice_follow', $iItemId = $iParticipantId, $aContest['user_id'], $iSenderUserId = null) : null);

                break;
            case 'approve_entry':
                // firstly we get all follower
                $aUsers = Phpfox::getService('contest.participant')->getListFollowingByContestId($aContest['contest_id']);
                $aEntry = Phpfox::getService('contest.entry')->getContestEntryById($iEntryId);

                if(Phpfox::isModule('notification')) {
                    foreach ($aUsers as $aUser) {
                        if ($aUser['user_id'] != $aEntry['user_id']) {
                            Phpfox::getService('notification.process')->add($sType = 'contest_notice_approveentry',
                                $iItemId = $iEntryId, $aUser['user_id'], $iSentUserId = null);
                        }
                    }
                    if(Phpfox::getUserId() !=  $aEntry['user_id']) {
                        Phpfox::getService('notification.process')->add('contest_notice_approveentry',
                            $iEntryId, $aEntry['user_id'], null);
                    }
                }


                $sSubject = _p('contest.full_name_submitted_an_entry_title_link',
                    array(
                        'full_name' => $aEntry['full_name'],
                        'title' => $aEntry['title'],
                        'link' => ''
                    ));
                $sMessage = _p('contest.full_name_submitted_an_entry_title_link',
                    array(
                        'full_name' => $aEntry['full_name'],
                        'title' => $aEntry['title'],
                        'link' => Phpfox::permalink('contest', $aEntry['contest_id'], $aEntry['contest_name']) . 'entry_' . $aEntry['entry_id'] . '/'
                    ));

                //here we use fox mailing function
                Phpfox::getLib('mail')->sendToSelf(true)
                    ->to($aContest['user_id'])
                    ->subject($sSubject)
                    ->message($sMessage)
                    ->send();


                //send email to participant
                $iParticipantId = Phpfox::getService('contest.participant')->getParticipantIdByContestAndUserId($aContest['contest_id'], $aEntry['user_id']);
                // set participant
                Phpfox::getService('contest.mail.process')->setParticipant($iParticipantId);

                // send thanks submiting message to entry owner
                Phpfox::getService('contest.mail.process')->sendEmailTo(
                    $iTemplateType = Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('thanks_for_submitting_entry'),
                    $aContest['contest_id'],
                    $aReceivers = $aEntry['user_id']
                );
                break;
            case 'deny_entry':
                $aEntry = Phpfox::getService('contest.entry')->getContestEntryById($iEntryId);
                // set entry & owner
                Phpfox::getService('contest.mail.process')->setEntry($iEntryId);
                Phpfox::getService('contest.mail.process')->setEntryOwner($aEntry['user_id']);

                // send thanks submiting message to entry owner
                (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add('contest_notice_denyentry',
                    $iEntryId, $aEntry['user_id'], null) : null);
                Phpfox::getService('contest.mail.process')->sendEmailTo(
                    $iTemplateType = Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('entry_denied'),
                    $aContest['contest_id'],
                    $aReceivers = $aEntry['user_id']
                );
                break;
            case 'approve_contest':

                Phpfox::getService('contest.mail.process')->sendEmailTo(
                    $iTemplateType = Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('contest_approved'),
                    $aContest['contest_id'],
                    $aReceivers = $aContest['user_id']
                );
                (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add('contest_notice_approvecontest', $aContest['contest_id'], $aContest['user_id'], Phpfox::getUserId()) : null);
                break;
            case 'deny_contest':

                Phpfox::getService('contest.mail.process')->sendEmailTo(
                    $iTemplateType = Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('contest_denied'),
                    $aContest['contest_id'],
                    $aReceivers = $aContest['user_id']
                );
                (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add('contest_notice_denycontest', $aContest['contest_id'], $aContest['user_id'], Phpfox::getUserId()) : null);
                break;

            case 'inform_winning_entry':
                $aParticipants = Phpfox::getService('contest.participant')->getListParticipantByContestId($aContest['contest_id']);
                $aReceivers = array();

                // @todo: increase performance
                foreach ($aParticipants as $aParticipant) {
                    $aReceivers[] = $aParticipant['user_id'];
                }

                Phpfox::getService('contest.mail.process')->sendEmailTo(
                    $iTemplateType = Phpfox::getService('contest.constant')->getEmailTemplateTypeIdByTypeName('inform_winning_entries'),
                    $aContest['contest_id'],
                    $aReceivers
                );

                $aUsers = Phpfox::getService('contest.participant')->getListFollowingByContestId($aContest['contest_id']);
                foreach ($aUsers as $aUser) {
                    (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->add($sType = 'contest_notice_winningentry', $iItemId = $aContest['contest_id'], $aUser['user_id'], $iSentUserId = $aContest['user_id']) : null);
                }


                break;
            default:

                break;
        }
    }

    public function addUserClose($iContestId, $iUserId)
    {
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }

        return $this->database()->update($this->_sTable, array('closed_by' => (int)$iUserId), 'contest_id = ' . (int)$iContestId);
    }
}

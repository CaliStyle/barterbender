<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Karaoke
 * @version        3.01
 */
class Resume_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function manageOrdering()
    {
        $aVals = $this->get('val');
        Phpfox::getService('resume.level.process')->updateOrdering($aVals['ordering']);
    }

    /**
     * Update level title through quick edir js form
     */

    public function deleteField()
    {
        if (Phpfox::getService('resume.custom.process')->delete($this->get('id'))) {
            $this->call('$(\'#js_field_' . $this->get('id') . '\').parents(\'li:first\').remove();');
        }
    }

    public function showInProfileInfo()
    {

        $iResumeId = $this->get('iResumeId', 0);
        $iShowInProfile = $this->get('iShowInProfile', 0);
        $sCheckboxId = $this->get('sCheckboxId');

        $aShowInProfile = Phpfox::getLib("database")
            ->select('resume_id')
            ->from(Phpfox::getT('resume_basicinfo'))
            ->where('is_show_in_profile = 1 AND user_id = ' . Phpfox::getUserId())
            ->execute('getRows');
        foreach ($aShowInProfile as $Resume) {
            $this->call("$('#resume_id_" . $Resume['resume_id'] . "').attr('checked',false);");
        }
        if (Phpfox::getService('resume.basic.process')->updateShowInProfileInfo($iResumeId, $iShowInProfile)) {


            if ($iShowInProfile == 1) {
                $this->alert(_p('resume.resume_has_been_published_in_your_profile'), _p('resume.resume'));
            } else {
                $this->alert(_p('resume.resume_has_been_unpublished_in_your_profile'), _p('resume.resume'));
            }
        }

    }

    public function toggleActiveField()
    {
        if (Phpfox::getService('resume.custom.process')->toggleActivity($this->get('id'))) {
            $this->call('$Core.custom.toggleFieldActivity(' . $this->get('id') . ')');
        }
    }

    public function updateLevelTitle()
    {
        Phpfox::getUserParam('admincp.has_admin_access', true);

        // Check singer name is empty or not
        if (Phpfox::getLib('parse.format')->isEmpty($this->get('quick_edit_input'))) {
            $iLevelId = (int)$this->get('level_id');
            $sLevelName = Phpfox::getService("resume.level")->getLevelById($iLevelId);
            $this->alert(_p('resume.please_enter_level_title'));
            $this->html('#' . $this->get('id'), '<a href="#?type=input&amp;id=js_resume_level_edit_title' . $this->get('level_id') . '&amp;content=js_resume_level' . $this->get('level_id') . '&amp;call=resume.updateLevelTitle&amp;level_id=' . $this->get('level_id') . '" class="quickEdit" id="js_resume_level' . $this->get('level_id') . '">' . Phpfox::getLib('parse.input')->clean($sLevelName) . '</a>')
                ->call('$Core.loadInit();');
            return false;
        }

        $aVals = array('title' => trim($this->get('quick_edit_input')));
        if (Phpfox::getService('resume.level.process')->update($this->get('level_id'), $aVals)) {
            $this->html('#' . $this->get('id'), '<a href="#?type=input&amp;id=js_resume_level_edit_title' . $this->get('level_id') . '&amp;content=js_resume_level' . $this->get('level_id') . '&amp;call=resume.updateLevelTitle&amp;level_id=' . $this->get('level_id') . '" class="quickEdit" id="js_resume_level' . $this->get('level_id') . '">' . Phpfox::getLib('locale')->convert($aVals['title']) . '</a>')
                ->call('$Core.loadInit();');
        }
    }

    /**
     *  Delete level in manage level page in admin control pannel
     */
    public function deleteLevel()
    {
        Phpfox::getUserParam('admincp.has_admin_access', true);
        Phpfox::getService('resume.level.process')->delete($this->get('id'));
        $this->call("window.location = window.location;");
    }


    /**
     * Delete experience in edit resume
     */
    public function delete_experience()
    {
        $exp_id = $this->get('exp_id');
        Phpfox::getService("resume.experience.process")->deleteExperience($exp_id);
        $this->call("$('#experience_" . $exp_id . "').remove();");
    }

    /**
     * Delete school from Education
     */
    public function delete_education()
    {
        $exp_id = $this->get('exp_id');
        Phpfox::getService("resume.education.process")->deleteEducation($exp_id);
        $this->call("$('#education_" . $exp_id . "').remove();");
    }

    /**
     * Delete certification from Certification
     */
    public function delete_certification()
    {
        $exp_id = $this->get('exp_id');
        Phpfox::getService("resume.certification.process")->deleteCertification($exp_id);
        $this->call("$('#certification_" . $exp_id . "').remove();");
    }

    public function delete_language()
    {
        $exp_id = $this->get('exp_id');
        Phpfox::getService("resume.language.process")->deleteLanguage($exp_id);
        $this->call("$('#language_" . $exp_id . "').remove();");
    }

    public function delete_publication()
    {
        $exp_id = $this->get('exp_id');
        Phpfox::getService("resume.publication.process")->deletePublication($exp_id);
        $this->call("$('#publication_" . $exp_id . "').remove();");
    }

    /**
     *  Delete resume in manage resume page in admin control pannel
     */
    public function deleteResume()
    {
        Phpfox::getUserParam('admincp.has_admin_access', true);
        Phpfox::getService('resume.process')->delete($this->get('id'));
        $this->call("window.location = window.location;");
    }

    /**
     *  Delete resume in manage resume page in admin control pannel
     * @see Resume_Service_Process
     */
    public function approveResume()
    {
        $iId = $this->get('id');
        Phpfox::getUserParam('admincp.has_admin_access', true);

        $aResume = Phpfox::getService('resume.basic')->getQuick($iId);
        if (!isset($aResume['user_id'])) {
            $this->alert(_p("resume.resume_is_not_valid"));
            return;
        }

        $iTotalPublishedResume = Phpfox::getService('resume.basic')->getTotalPublishedResumes($aResume['user_id']);

        $aResumeUser = Phpfox::getService('user')->getUser($aResume['user_id']);
        $iLimitResume = Phpfox::getUserGroupParam($aResumeUser['user_group_id'], 'resume.limit_maximum_resume_active');

        if ($iLimitResume > 0 && $iTotalPublishedResume >= $iLimitResume) {
            Phpfox::getService('resume.process')->approve($iId);
            Phpfox::getService('resume.process')->setPrivate($iId);

            $this->alert(_p("resume.public_resumes_of_this_user_has_been_reached_limit"));
            $this->call("$(\"#private_resume_{$iId}\").show();");
            $this->call("$(\"#approve_select_resume_{$iId}\").hide();");
            return;
        }

        Phpfox::getService('resume.process')->approve($iId);
        $this->call("$(\"#approved_resume_{$iId}\").show();");
        $this->call("$(\"#approve_select_resume_{$iId}\").hide();");
    }

    /**
     *  Delete resume in manage resume page in admin control pannel
     */
    public function denyResume()
    {
        $iId = $this->get('id');
        Phpfox::getUserParam('admincp.has_admin_access', true);
        Phpfox::getService('resume.process')->deny($iId);
        $this->call("$(\"#denied_resume_{$iId}\").show();");
        $this->call("$(\"#approve_select_resume_{$iId}\").hide();");
    }

    public function approveResumeFrontEnd()
    {
        $iId = $this->get('id');
        Phpfox::getUserParam('resume.can_approve_resumes', true);

        $aResume = Phpfox::getService('resume.basic')->getQuick($iId);
        if (!isset($aResume['user_id'])) {
            $this->alert(_p("resume.resume_is_not_valid"));
            return;
        }

        $iTotalPublishedResume = Phpfox::getService('resume.basic')->getTotalPublishedResumes($aResume['user_id']);

        $aResumeUser = Phpfox::getService('user')->getUser($aResume['user_id']);
        $iLimitResume = Phpfox::getUserGroupParam($aResumeUser['user_group_id'], 'resume.limit_maximum_resume_active');

        if ($iLimitResume > 0 && $iTotalPublishedResume >= $iLimitResume) {
            Phpfox::getService('resume.process')->approve($iId);
            Phpfox::getService('resume.process')->setPrivate($iId);

            $this->alert(_p("resume.public_resumes_of_this_user_has_been_reached_limit"));
            $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
            return;
        }

        Phpfox::getService('resume.process')->approve($iId);
        $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
    }

    /**
     *  Delete resume in manage resume page in admin control pannel
     */
    public function denyResumeFrontEnd()
    {
        $iId = $this->get('id');
        Phpfox::getUserParam('resume.can_approve_resumes', true);
        Phpfox::getService('resume.process')->deny($iId);
        $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
    }

    /**
     * Delete a account from admin
     */
    public function deleteAccount()
    {
        $account_id = $this->get('account_id');
        Phpfox::getService("resume.account.process")->deleteAccount($account_id);
        $this->call("$('#resume_view_" . $account_id . "').hide();");
    }

    public function delete_service()
    {
        $account_id = $this->get('account_id');
        Phpfox::getService("resume.account.process")->deleteAccount($account_id);
        $this->call("window.location.href='" . Phpfox::getLib("url")->makeUrl('resume.account') . "'");
    }

    public function setApproveView()
    {
        // Get account id and recording status
        $iId = $this->get('id');
        $aAccount = Phpfox::getService('resume.account')->getAccountById($iId);

        $sStatus = $this->get('status');

        $oSetting = Phpfox::getService("resume.setting");

        // Get global setting
        $iWhoViewedMMeGroupId = (int)$oSetting->getUserGroupId($aAccount['user_id'], 1);

        $iViewAllResumeGroupId = (int)$oSetting->getUserGroupId($aAccount['user_id'], 2);

        if ($sStatus == 'yes') {
            // Set approve
            Phpfox::getService("resume.account.process")->updateApprove($aAccount['account_id'], "is_employer", 1);

            $user_group_id = $iViewAllResumeGroupId;

            Phpfox::getService("resume.account")->updateUserGroup($aAccount['user_id'], $user_group_id);

            //Add Notification
            Phpfox::getService('notification.process')->add('resume_view_approve', $aAccount['account_id'], $aAccount['user_id']);

            $this->call("$(\"#resume_view_{$iId}\").find(\".type_1\").find(\".yes_button\").show();");
            $this->call("$(\"#resume_view_{$iId}\").find(\".type_1\").find(\".no_button\").hide();");
        } else {
            // Set approve
            Phpfox::getService("resume.account.process")->updateApprove($aAccount['account_id'], "is_employer", 0);

            $user_group_id = 2;

            Phpfox::getService("resume.account")->updateUserGroup($aAccount['user_id'], $user_group_id);

            // Add Notification
            Phpfox::getService('notification.process')->add('resume_view_unapprove', $aAccount['account_id'], $aAccount['user_id']);

            $this->call("$(\"#resume_view_{$iId}\").find(\".type_1\").find(\".yes_button\").hide();");
            $this->call("$(\"#resume_view_{$iId}\").find(\".type_1\").find(\".no_button\").show();");
        }
    }

    public function setApproveWhoView()
    {
        // Get account id and recording status
        $iId = $this->get('id');
        $aAccount = Phpfox::getService('resume.account')->getAccountById($iId);

        $sStatus = $this->get('status');

        $oSetting = Phpfox::getService("resume.setting");

        // Get global setting
        $iWhoViewedMMeGroupId = (int)$oSetting->getUserGroupId($aAccount['user_id'], 1);

        $iViewAllResumeGroupId = (int)$oSetting->getUserGroupId($aAccount['user_id'], 2);

        $user_group_id = $iViewAllResumeGroupId;

        if ($sStatus == 'yes') {
            // Set approve
            Phpfox::getService("resume.account.process")->updateApprove($aAccount['account_id'], "is_employee", 1);

            $user_group_id = $iWhoViewedMMeGroupId;

            Phpfox::getService("resume.account")->updateUserGroup($aAccount['user_id'], $user_group_id);

            //Add Notification
            Phpfox::getService('notification.process')->add('resume_whoview_approve', $aAccount['account_id'], $aAccount['user_id']);

            $this->call("$(\"#resume_view_{$iId}\").find(\".type_2\").find(\".yes_button\").show();");
            $this->call("$(\"#resume_view_{$iId}\").find(\".type_2\").find(\".no_button\").hide();");
        } else {
            // Set approve
            Phpfox::getService("resume.account.process")->updateApprove($aAccount['account_id'], "is_employee", 0);

            $user_group_id = 2;
            Phpfox::getService("resume.account")->updateUserGroup($aAccount['user_id'], $user_group_id);


            //Add Notification
            Phpfox::getService('notification.process')->add('resume_whoview_unapprove', $aAccount['account_id'], $aAccount['user_id']);

            $this->call("$(\"#resume_view_{$iId}\").find(\".type_2\").find(\".yes_button\").hide();");
            $this->call("$(\"#resume_view_{$iId}\").find(\".type_2\").find(\".no_button\").show();");
        }
    }

    /**
     * Moderate recording method on my recording page
     */
    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'delete':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('resume.process')->delete($iId);
                    $this->slideUp('#js_item_m_resume_' . $iId);
                }
                $sMessage = _p('resume.resume_s_successfully_deleted');
                break;
        }

        $this->alert($sMessage, 'Moderation', 300, 150, true);
        $this->hide('.moderation_process');

    }

    /**
     * Add favorite
     */
    public function addFavorite()
    {
        $iId = $this->get('id');
        Phpfox::getBlock('resume.add-favorite', array('iId' => $iId));
    }

    /**
     * Delete Favorite
     */
    public function deleteFavorite()
    {
        $iItemId = $this->get('id');
        $sType = $this->get('type');
        $iFavoriteId = phpfox::getLib('database')->select('favorite_id')
            ->from(phpfox::getT('resume_favorite'))
            ->where('resume_id = ' . $iItemId . ' and user_id =' . phpfox::getUserId())
            ->execute('getSlaveField');

        if ($iFavoriteId) {
            Phpfox::getService('resume.process')->deleteFavorite($iFavoriteId);
            if ($sType == 'my')
                $this->call("location.reload();");
        }
    }

    /**
     * Add Note
     */
    public function addNote()
    {
        $iId = $this->get('id');
        $this->setTitle(_p('resume.add_note'));
        Phpfox::getBlock('resume.add-note', array('iId' => $iId));
    }

    /**
     * Delete note
     */
    public function deleteNote()
    {
        $iId = $this->get('id');
        $iViewId = Phpfox::getLib('database')->select('view_id')
            ->from(Phpfox::getT('resume_viewme'))
            ->where('resume_id = ' . $iId . ' AND user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveField');
        if ($iViewId) {
            Phpfox::getLib('database')->update(Phpfox::getT('resume_viewme'), array('note' => ""), 'view_id = ' . $iViewId);
            $this->call("$(\"#note_resume_{$iId}\").hide();");
        }
    }

    /**
     * Popup at Who viewed me
     */
    public function register()
    {
        $this->setTitle(_p('resume.who_s_view_me'));
        $aAccount = Phpfox::getService("resume.account")->getAccount();
        if ($aAccount && $aAccount['view_resume'] != 1) {
            echo _p('resume.your_request_has_been_sent_please_wait_approve_from_administrator');
        } else {
            Phpfox::getBlock('resume.registerpopup');
        }
    }

    /**
     * Send message at who viewed me
     */
    public function sendMessagePupUp()
    {

        $user_id = $this->get('user_id');
        $type = $this->get('type');
        $resume_id = $this->get('resume_id');
        $this->setTitle(_p('resume.send_email'));
        Phpfox::getBlock('resume.sendmessage', array(
            'user_id' => $user_id,
            'type' => $type,
            'resume_id' => $resume_id
        ));
    }

    public function sendMessage()
    {
        $aVals = $this->get('val');
        $sError = '';
        if (empty($aVals['title'])) {
            $sError .= _p('resume.title_is_not_allowed_empty') . '<br/>';
        }
        if (empty($aVals['message'])) {
            $sError .= _p('resume.message_is_not_allowed_empty') . '<br/>';
        }
        //Success
        if (empty($sError)) {
            phpfox::getService('resume.process')->SendMessage($aVals);
            if (!empty($aVals['resume_id'])) {
                Phpfox::getService('resume.viewme')->updateMessageCount($aVals['resume_id']);
            }
            $sJs = 'tb_remove();';
            $this->call($sJs);
        } else {
            //fail
            $this->call('$(".error_message").html("' . $sError . '").show();');
            $sDisableJs = "$('#btnSend').removeClass('disabled').removeAttr('disabled');";
            $sJs = 'setTimeout("' . $sDisableJs . '", 1800);';
            $this->call($sJs);
        }
    }

    /**
     * Can't import resume
     */
    public function alertimport()
    {
        $totalallow = Phpfox::getUserParam("resume.maximum_resumes");
        $this->setTitle(_p('resume.maximum_resumes'));
        echo _p('resume.each_users_only_can_create_maximum_limit_resume', array('limit' => $totalallow));
    }

    public function Popupwaitingapprove()
    {
        $this->setTitle(_p('resume.register_now'));
        echo _p('resume.you_have_successfully_registered_it_is_pending_for_approving');
    }

    public function getChildren()
    {
        Phpfox::getBlock('resume.country-child', array('authorized_country_child_value' => $this->get('country_iso'), 'authorized_country_child_id' => $this->get('country_child_id')));

        $this->remove('#js_cache_country_iso')->html('#authorized_js_country_child_id', $this->getContent(false));
    }

    /**
     * Send message at who viewed me
     */
    public function registerViewResume()
    {
        Phpfox::isUser(true);
        $this->setTitle(_p('resume.view_resume'));
        $aAccount = Phpfox::getService("resume.account")->getAccount();
        if ($aAccount && $aAccount['view_resume'] >= 1) {
            echo _p('resume.your_request_has_been_sent_please_wait_approve_from_administrator');
        } else {
            Phpfox::getBlock('resume.register-view-popup');
        }
    }

    public function upgradeAccount()
    {
        $view = $this->get('view');

        $aVals = array();
        $aAccount = Phpfox::getService("resume.account")->getAccount();
        if (!$aAccount) {
            $aVals['view_resume'] = $view;
            $iId = Phpfox::getService("resume.account.process")->add($aVals);
        } else {

            $aVals['account_id'] = $aAccount['account_id'];
            if ($view == 0) {

                if ($aAccount['is_employer'] == 1 || $aAccount['view_resume'] == 1) {
                    $aVals['view_resume'] = 2;
                } else
                    $aVals['view_resume'] = 1;

            } else {
                if ($aAccount['is_employee'] == 1 || $aAccount['view_resume'] == 0) {
                    $aVals['view_resume'] = 2;
                } else
                    $aVals['view_resume'] = 1;
            }

            Phpfox::getService("resume.account.process")->update($aVals);
        }
        $this->alert(_p('resume.your_request_has_been_sent_successfully'));
    }

    public function editNote()
    {
        // Get Params
        $iResumeId = (int)$this->get('resume_id');
        $iUserId = (int)$this->get('user_id');

        // Get User Object
        $aUser = Phpfox::getService('user')->getUser($iUserId);
        $sFullName = "No Name";

        if ($aUser) {
            $sFullName = $aUser['full_name'];
        }

        $this->setTitle(_p('resume.your_note_in_resume_of_full_name', array('full_name' => $sFullName)));
        Phpfox::getBlock('resume.edit-note', array('iResumeId' => $iResumeId));
    }

    public function updateNote()
    {
        // Get params
        $iResumeId = (int)$this->get('resume_id');
        $sNote = $this->get('text');
        $sNote = substr($sNote, 0, 500);

        $sRedirectLink = Phpfox::getLib("url")->makeUrl('resume', array('view' => 'noted'));

        Phpfox::getService('resume.viewme')->updateNote($iResumeId, $sNote);
        $this->call("tb_remove();window.location.href='{$sRedirectLink}'");
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'resume_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove();
    }

    public function updateActivity()
    {
        if (Phpfox::getService('resume.category.process')->updateActivity($this->get('id'), $this->get('active'), $this->get('sub'))) {

        }
    }
    public function deleteOption()
    {
        $id = $this->get('id');

        if (Phpfox::getService('resume.custom.process')->deleteOption($id))
        {
            $this->remove('#js_current_value_'.$id);
        }
        else
        {

        }
    }
}
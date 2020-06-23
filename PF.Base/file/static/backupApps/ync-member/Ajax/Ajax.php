<?php

/**
 * [PHPFOX_HEADER]
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        PhuongNV
 * @package        yn_member
 */

namespace Apps\YNC_Member\Ajax;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Ajax;
use Core_Service_Process;
use User_Service_Auth;
use Feed_Service_Process;
use Phpfox_Image;
use Core;
use Phpfox_Error;

class Ajax extends Phpfox_Ajax
{
    public function editcustomfieldgroup()
    {
        $iCustomFieldGroupId = (int)$this->get('id');

        Phpfox::getBlock('ynmember.editcustomfield', array('iCustomFieldGroupId' => $iCustomFieldGroupId));

        $this->html('#site_content', $this->getContent(false));
    }

    public function AdminAddCustomFieldBackEnd()
    {
        $iGroupId = $this->get('iGroupId');

        Phpfox::getComponent('ynmember.admincp.customfield.addfield', array('iGroupId' => $iGroupId), 'controller');
    }

    public function addField()
    {
        $aVals = $this->get('val');

        list($iFieldId, $aOptions) = Phpfox::getService('ynmember.custom.process')->add($aVals);

        if (!empty($iFieldId)) {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->set('id', $aVals['groupid']);
            $this->editcustomfieldgroup();
            $this->call("js_box_remove($('.js_box_close'));");
        } else {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->alert(_p('please_input_name_of_field'));
        }
    }

    public function updateField()
    {
        $aVals = $this->get('val');

        if (Phpfox::getService('ynmember.custom.process')->update($aVals['id'], $aVals)) {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->set('id', $aVals['groupid']);
            $this->editcustomfieldgroup();
            $this->call("js_box_remove($('.js_box_close'));");
        } else {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->alert(_p('please_input_name_of_field'));
        }
    }

    public function deleteField()
    {
        $id = $this->get('id');
        if (Phpfox::getService('ynmember.custom.process')->delete($id)) {
            $this->remove('#js_custom_field_' . $id);
        }
    }

    public function deleteOption()
    {
        $id = $this->get('id');

        if (Phpfox::getService('ynmember.custom.process')->deleteOption($id)) {
            $aFields = Phpfox::getService('ynmember.custom')->getCustomField();
            $this->remove('#js_current_value_' . $id);
        } else {
            $this->alert(_p('could_not_delete'));
        }
    }

    public function toggleActiveGroup()
    {
        if (Phpfox::getService('ynmember.custom.group')->toggleActivity($this->get('id'))) {
            $this->call('$Core.ynmember.toggleGroupActivity(' . $this->get('id') . ')');
        }
    }

    public function AdminAddCustomFieldGroup()
    {
        if ($aVals = $this->get('val')) {
            if ($this->__isValid($aVals)) {

                if (isset($aVals['group_id'])) {
                    if (Phpfox::getService('ynmember.custom.group')->updateGroup($aVals['group_id'], $aVals)) {
                        Phpfox::getBlock('ynmember.editcustomfield', array('iCustomFieldGroupId' => $aVals['group_id']));

                        $this->html('#site_content', $this->getContent(false));
                        $this->html('#ajax-response-custom', "<div class='message'>" . _p('custom_field_groups_successfully_updated') . "</div>");
                        $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');},2000);');
                    }
                } else {
                    if ($iGroupId = Phpfox::getService('ynmember.custom.group')->addGroup($aVals)) {
                        Phpfox::getBlock('ynmember.editcustomfield', array('iCustomFieldGroupId' => $iGroupId));

                        $this->html('#site_content', $this->getContent(false));
                        $this->html('#ajax-response-custom', "<div class='message'>" . _p('custom_field_groups_successfully_added') . "</div>");
                        $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');},2000);');
                    }
                }
            }
        }
    }

    public function AdminDeleteCustomFieldGroup()
    {
        $iCustomFieldGroupId = (int)$this->get('id');
        if (Phpfox::getService('ynmember.custom.group')->deleteGroup($iCustomFieldGroupId)) {
            $this->html('#ajax-response-custom', "<div class='message'>" . _p('custom_fields_successfully_deleted') . "</div>");
            $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');$(\'.toolbar-top a:eq(5) a\').trigger(\'click\')},2000);');
        }
    }

    public function AdminDeleteCustomField()
    {
        $iCustomFieldDelete = $this->get('iFieldId');
        $iCustomFieldGroupId = $this->get('iGroupId');
        if (!isset($iCustomFieldGroupId) && empty($iCustomFieldGroupId)) return;
        if (Phpfox::getService('ynmember.custom.process')->delete($iCustomFieldDelete)) {
            Phpfox::getBlock('ynmember.editcustomfield', array('iCustomFieldGroupId' => $iCustomFieldGroupId));

            $this->html('#site_content', $this->getContent(false));
        }
    }

    public function AdminUpdateOrderCustomField()
    {
        if (($aFieldOrders = $this->get('field')) && Phpfox::getService('ynmember.custom.process')->updateOrder($aFieldOrders)) {
            $this->html('#ajax-response-custom', "<div class='message'>" . _p('Custom fields successfully updated') . "</div>");

        }

        if (($aGroupOrders = $this->get('group')) && Phpfox::getService('ynmember.custom.group')->updateOrder($aGroupOrders)) {
            $this->html('#ajax-response-custom', "<div class='message'>" . _p('Custom fields successfully updated') . "</div>");

        }
    }

    public function __isValid($aVals)
    {
        $emptyGroupName = false;
        $group_name = $aVals['group_name'];
        foreach ($group_name as $keygroup_name => $valuegroup_name) {
            if (is_array($valuegroup_name)) {
                foreach ($valuegroup_name as $keyvaluegroup_name => $valuevaluegroup_name) {
                    if (strlen(trim($valuevaluegroup_name)) == 0) {
                        $emptyGroupName = true;
                        break;
                    }
                }
            } else {
                if (strlen(trim($valuegroup_name)) == 0) {
                    $emptyGroupName = true;
                    break;
                }
            }
        }
        if ($emptyGroupName) {
            Phpfox_Error::set(_p('group_name_cannot_be_empty'));
            return false;
        }

        return true;
    }

    public function filterAdminFilterMember()
    {
        $aSearch = $this->get('search');
        Phpfox::getComponent('ynmember.admincp.managemembers', ['search' => $aSearch], 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function filterAdminFilterReview()
    {
        $aSearch = $this->get('search');
        Phpfox::getComponent('ynmember.admincp.managereviews', ['search' => $aSearch], 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function changePageManageMembers()
    {
        $aPage = $this->get('page');
        $aSearch = [
            'full_name' => $this->get('full_name'),
            'email' => $this->get('email'),
            'user_group_id' => $this->get('user_group_id'),
            'is_featured' => $this->get('is_featured'),
        ];
        Phpfox::getComponent('ynmember.admincp.managemembers', ['page' => $aPage, 'search' => $aSearch], 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function changePageManageReviews()
    {
        $aPage = $this->get('page');
        $aSearch = [
            'review_by' => $this->get('review_by'),
            'review_for' => $this->get('review_for'),
            'title' => $this->get('title'),
            'from' => $this->get('from'),
            'to' => $this->get('to'),
        ];

        Phpfox::getComponent('ynmember.admincp.managereviews', ['page' => $aPage, 'search' => $aSearch], 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function updateModInAdmin()
    {
        $iUserId = (int)$this->get('iUserId');
        $bIsMod = (int)$this->get('iIsMod');
        $bNewMod = (int)!$this->get('iIsMod');
        if ($bIsMod == 1 || $bIsMod == 0) {
            if ($bIsMod == 0 && (Phpfox::getService('ynmember.process')->setMod($iUserId, $bIsMod))) {
                $this->call("ynmember.clearMod($iUserId);");
                $this->html('#ynmember_member_update_mod_' . $iUserId, '<div class="js_item_is_active"><a href="javascript:void(0)" class="js_item_active_link" onclick="ynmember.updateMod(this);" data-user_id="' . $iUserId . '" data-is_mod="' . $bNewMod . '"></a></div>');
                return true;
            } elseif ($bIsMod == 1 && (Phpfox::getService('ynmember.process')->setMod($iUserId, $bIsMod))) {
                $this->html('#ynmember_member_update_mod_' . $iUserId, '<div class="js_item_is_not_active"><a href="javascript:void(0)" class="js_item_active_link" onclick="ynmember.updateMod(this);" data-user_id="' . $iUserId . '" data-is_mod="' . $bNewMod . '"></a></div>');
                return true;
            }
        }// else potential hack attempt

        $this->alert(_p('an_error_occurred_and_this_operation_was_not_completed')); // potential hack attempt

        return false;
    }

    public function updateFeaturedInAdmin()
    {
        $iUserId = (int)$this->get('iUserId');
        $bFeature = (int)$this->get('iIsFeatured');
        $bNewFeature = (int)!$this->get('iIsFeatured');
        if ($bFeature == 1 || $bFeature == 0) {
            if ($bFeature == 0 && (Phpfox::getService('user.featured.process')->feature($iUserId))) // trying to feature
            {
                $this->html('#ynmember_member_update_featured_' . $iUserId, '<div class="js_item_is_active"><a href="javascript:void(0)" class="js_item_active_link"  onclick="$Core.ajaxMessage();ynmember.updateFeatured(this);" data-user_id="' . $iUserId . '" data-is_featured="' . $bNewFeature . '"></a></div>');
            } elseif ($bFeature == 1 && (Phpfox::getService('user.featured.process')->unfeature($iUserId))) {
                $this->html('#ynmember_member_update_featured_' . $iUserId, '<div class="js_item_is_not_active"><a href="javascript:void(0)" class="js_item_active_link"  onclick="$Core.ajaxMessage();ynmember.updateFeatured(this);" data-user_id="' . $iUserId . '" data-is_featured="' . $bNewFeature . '"></a></div>');

            }
            Phpfox::getLib('cache')->remove();

            return true;
        }

        $this->alert(_p('an_error_occurred_and_this_operation_was_not_completed'));

        return false;
    }

    public function featureMember()
    {

        $iUserId = (int)$this->get('user_id');
        $bFeature = (int)$this->get('featured');
        $bResult = false;
        if ($bFeature == 1 || $bFeature == 0) {
            if ($bFeature) {
                $bResult = Phpfox::getService('user.featured.process')->unfeature($iUserId);
            } else {
                $bResult = Phpfox::getService('user.featured.process')->feature($iUserId); // trying to feature
            }
        }

        if ($bResult) {
            Phpfox::getLib('cache')->remove();

            $aUser = Phpfox::getService('user')->get($iUserId);
            Phpfox::getService('ynmember.member')->processUser($aUser);

            $this->template()
                ->assign(['aUser' => $aUser])
                ->getTemplate('ynmember.block.entry_link_action');
            $this->replaceWith(".ynmember_link_action_$iUserId", $this->getContent(false));
            $this->call('$Core.reloadPage();');
        } else {
            $this->alert(_p('an_error_occurred_and_this_operation_was_not_completed'));
        }

        return false;
    }

    public function submitReview()
    {
        $iUserId = $this->get('user_id');
        if (!Phpfox::getService('ynmember.review')->canWriteReview($iUserId) && !user('ynmember_edit_review_others')) {
            return Phpfox_Error::set(_p("You don't have permission to do this action."));
        }
        if (($aVals = $this->get('val'))) {
            $aValidateCustomField = $this->_verifyCustomForm($aVals);
            if ($aValidateCustomField['error']) {
                $this->html('#js_ajax_compose_error_message', '<div class="error_message">' . $aValidateCustomField['message'] . '</div>');
                return null;
            }
            $aVals['user_id'] = $iUserId;

            if (isset($aVals['submit'])) {
                $iReviewId = Phpfox::getService('ynmember.review.process')->add($aVals);
            } else if (isset($aVals['update'])) {
                $iReviewId = (int)$aVals['review_id'];
                Phpfox::getService('ynmember.review.process')->update($aVals);
                if (isset($aVals['custom']) && count($aVals['custom']) > 0) {
                    Phpfox::getService('ynmember.custom.process')->deleteValue($iReviewId);
                }
            }
            if (isset($aVals['custom']) && count($aVals['custom']) > 0) {
                Phpfox::getService('ynmember.custom.process')->addValue($aVals['custom'], $iReviewId);
            }

            $this->call('$(\'#\' + tb_get_active()).find(\'.js_box_content:first\').html(\'<div class="message">' . str_replace("'", "\\'", _p('Your review has been submitted successfully.')) . '</div>\'); setTimeout(\'tb_remove();\', 1500); $Core.reloadPage();');
        }
    }

    public function submitPlace()
    {
        if ($aVals = $this->get('val')) {
            $bIsEdit = isset($aVals['place_id']);
            $oFilter = Phpfox::getLib('parse.input');

            if (!isset($aVals['current'])) {
                $aVals['current'] = 0;
            }

            if (empty($aVals['location_title'])) {
                $aVals['location_title'] = $oFilter->clean($aVals['location_address'], 255);
            }

            if (isset($aVals['submit'])) {
                $iPlaceId = Phpfox::getService('ynmember.place.process')->add($aVals);
            } else if (isset($aVals['update'])) {
                $iPlaceId = Phpfox::getService('ynmember.place.process')->update($aVals);
            }
        }

        $aPlace = Phpfox::getService('ynmember.place')->getForEdit($iPlaceId);

        $this->template()->assign([
            'aPlace' => $aPlace
        ])->getTemplate('ynmember.block.entry_profile_place');

        $this->call('$(\'#\' + tb_get_active()).find(\'.js_box_content:first\').html(\'<div class="message">' . str_replace("'", "\\'", _p('Your place has been saved successfully.')) . '</div>\'); setTimeout(\'tb_remove();$Core.loadInit();\', 1000);');

        if ($bIsEdit) {
            $this->replaceWith('#ynmember_place_' . $iPlaceId, $this->getContent(false));
        } else {
            $this->append('#ynmember_place_' . $aPlace['type'], $this->getContent(false));
        }
    }

    public function deletePlace()
    {
        $iPlaceId = $this->get('place_id');

        Phpfox::getService('ynmember.place.process')->delete($iPlaceId);

        $this->slideUp('#ynmember_place_' . $iPlaceId);
    }

    public function deleteReview()
    {
        $iReviewId = $this->get('review_id');
        $aReview = Phpfox::getService('ynmember.review')->getForEdit($iReviewId);
        $bAllow = (Phpfox::getUserId() == $aReview['user_id']) ? user('ynmember_delete_review_self') : user('ynmember_delete_review_others');
        if (!$bAllow) {
            return Phpfox_Error::set(_p("You don't have permission to do this action."));
        }
        Phpfox::getService('ynmember.review.process')->delete($iReviewId);
        $this->call("window.location.href = window.location.href");
    }

    public function updateFriendship()
    {
        $sAction = $this->get('action');
        $iId = $this->get('id');
        $iViewerId = Phpfox::getUserId();
        $iUserId = $this->get('user_id');

        if (!Phpfox::isModule('friend') || !$sAction || !$iViewerId || (($sAction != 'delete') && !$iId))
            return;

//        $aUser = Phpfox::getService('user')->get($iUserId, true);
        $sMessage = '';
        $bResult = false;

        switch ($sAction) {
            case 'cancel':
                $bResult = Phpfox::getService('friend.request.process')->delete($iId, $iViewerId);
//                $sMessage = _p('Friend request to {{ user_full_name }} has been cancelled.', ['user_full_name' => $aUser['full_name']]);
                $sMessage = _p('Friend request has been cancelled!');
                break;
            case 'delete':
                $bResult = Phpfox::getService('friend.process')->delete($iUserId, false);
//                $sMessage = _p('You are no longer friends with {{ user_full_name }}.', ['user_full_name' => $aUser['full_name']]);
                $sMessage = _p('You are no longer friends!');
                break;
            case 'confirm':
                $bResult = Phpfox::getService('friend.process')->add($iViewerId, $iUserId);
                $sMessage = _p('The request has been accepted successfully!');
                break;
            case 'deny':
                $bResult = Phpfox::getService('friend.process')->deny($iViewerId, $iUserId);
                $sMessage = _p('The request has been denied successfully!');
                break;
        }

        if ($bResult) {
            $aUser = Phpfox::getService('user')->get($iUserId, true);

            $this->template()
                ->assign(['aUser' => $aUser])
                ->getTemplate('ynmember.block.entry_link_friendship');
            $this->replaceWith(".ynmember_link_friendship_$iUserId", $this->getContent(false));

            $this->template()
                ->assign(['aUser' => $aUser])
                ->getTemplate('ynmember.block.entry_link_friendship_new');
            $this->replaceWith(".ynmember_link_friendship_new_$iUserId", $this->getContent(false));
        } else {
            $sMessage = _p('An error has occurred!');
        }

        $this->outputMsg($sMessage);
    }

    public function shareMember()
    {
        Phpfox::getUserParam('ynmember_share_member', true);
        $iUserId = $this->get('user_id', 0);
        $aUser = Phpfox::getService('user')->get($iUserId, true);
        if (empty($aUser) || empty($aUser['user_id']))
            return;
        if (Phpfox::isModule('feed')) {
            Phpfox::getService('feed.process')->add('ynmember', $iUserId);
        }

        $sMessage = _p('Successfully share member.');
        $this->outputMsg($sMessage);
    }

    // get notification
    public function followMember()
    {
        Phpfox::getUserParam('ynmember_follow_member', true);
        $iItemId = (int)$this->get('user_id');

        $sMessage = '';

        if (!Phpfox::getService('ynmember.member')->isFollowingMember(Phpfox::getUserId(), $iItemId)) {
            $bResult = Phpfox::getService('ynmember.process')->addFollow($iItemId);
            $sMessage = _p('Successfully getting notification from this member.');
        } else {
            $bResult = Phpfox::getService('ynmember.process')->removeFollow($iItemId);
            $sMessage = _p('You are no longer getting notification from this member.');
        }

        if ($bResult) {
            $aUser = Phpfox::getService('user')->get($iItemId, true);
            Phpfox::getService('ynmember.member')->processUser($aUser);
            $this->template()
                ->assign(['aUser' => $aUser])
                ->getTemplate('ynmember.block.entry_link_action');
            $this->replaceWith(".ynmember_link_action_$iItemId", $this->getContent(false));
        } else {
            $sMessage = _p('An error has occurred!');
        }

        $this->outputMsg($sMessage);
    }

    public function sendBirthdayWish()
    {
        $aVals = $this->get('val');

        list($success, $message) = Phpfox::getService('ynmember.process')->addBirthdayWish($aVals);

        if ($success) {
            $iUserId = $aVals['user_id'];
            $aUser = Phpfox::getService('user')->get($aVals['user_id']);
            $aUser['new_age'] = Phpfox::getService('user')->age($aUser['birthday']);
            Phpfox::getService('ynmember.member')->processBirthdayWish($aUser);
            $this->template()
                ->assign(['aUser' => $aUser])
                ->getTemplate('ynmember.block.entry_birthday_upcoming_popup');
            $this->replaceWith("#ynmember_birthday_wish_$iUserId", $this->getContent(false));
            $this->attr("form#ynmember_birthday_wish_form_". $iUserId ." .js_ynmember_birthday_wish", 'disabled', true);
            $this->remove("#ynmember_send_bw_btn_$iUserId");
        }

    }

    public function voteReview()
    {
        Phpfox::isUser();
        $iReviewId = $this->get('review_id');
        $iPositive = $this->get('positive');

        $aRow = Phpfox::getService('ynmember.review')->getReview($iReviewId);
        if (empty($aRow['review_id'])) {
            return null;
        }

        $aVals = [
            'review_id' => $iReviewId,
            'positive' => $iPositive ? 1 : 0,
        ];

        Phpfox::getService('ynmember.review.process')->vote($aVals);

        $aRow = Phpfox::getService('ynmember.review')->getReview($iReviewId);

        $this->template()->assign([
            'aReview' => $aRow
        ])->getTemplate('ynmember.block.entry_review_useful');

        $this->replaceWith(".ynmember_review_useful_$iReviewId", $this->getContent(false));
    }

    public function actionMultiSelectReview()
    {
        $aVals = $this->get('review_row');
        if (!count($aVals)) {
            $this->alert(_p('No Reviews Selected'));
            return false;
        }
        $oProcess = Phpfox::getService('ynmember.review.process');
        foreach ($aVals as $key => $reviewId) {
            $oProcess->delete($reviewId);
        }
        $this->alert(_p('Reviews successfully deleted.'));
        $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.toolbar-top a:eq(3) a\').trigger(\'click\')},2000);');
    }

    public function deleteReviewInAdmin()
    {
        $iId = (int)$this->get('review_id');
        if ($iId) {
            Phpfox::getService('ynmember.review.process')->delete($iId);
            $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.toolbar-top a:eq(3) a\').trigger(\'click\')},2000);');
        }
    }

    public function outputMsg($sMessage = '')
    {
//        $this->call("$('#core_js_messages').html(''); $('#core_js_messages').message('$sMessage', 'valid').slideDown('slow').fadeOut(4000);");
        if ($sMessage) {
//            $this->alert($sMessage);
            $this->call($this->alert($sMessage, _p('Notice'), 300, 1000, true, true));
        } else {
            $this->alert(_p('an_error_occurred_and_this_operation_was_not_completed'));
        }
    }

    private function _verifyCustomForm($aVals)
    {
        $aFieldValues = isset($aVals['custom']) ? $aVals['custom'] : array();

        $aFields = Phpfox::getService('ynmember.custom')->getCustomField();

        foreach ($aFields as $k => $aField) {
            if ($aField['is_required'] && $aField['group_id'] && $aField['is_active'] && empty($aFieldValues[$aField['field_id']])) {
                return [
                    'error' => 1,
                    'message' => _p('custom_field_string_is_required', ['string' => Phpfox::getPhrase($aField['phrase_var_name'])])
                ];
            }
        }

        return [
            'error' => 0,
        ];
    }
}
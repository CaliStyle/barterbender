<?php
/**
 * Created by PhpStorm.
 * User: davidnguyen
 * Date: 7/26/16
 * Time: 10:36 AM
 */

namespace Apps\YouNet_UltimateVideos\Ajax;


use Phpfox;
use Phpfox_Ajax;
use Phpfox_Url;

/**
 * Class Ajax
 *
 * @package Apps\YouNet_UltimateVideos\Ajax
 */
class Ajax extends Phpfox_Ajax
{
    public function sponsor_video()
    {
        Phpfox::isUser(true);

        $videoId = $this->get('iVideoId');
        $sponsor = (int)$this->get('iValue');

        if($sponsor) {
            Phpfox::getUserParam('ultimatevideo.can_sponsor_video', true);
        }

        if(Phpfox::getService('ultimatevideo.process')->sponsor($videoId, $sponsor)) {
            if($sponsor) {
                $video = Phpfox::getService('ultimatevideo')->getSimpleVideo($videoId);
                $sModule = _p('ultimatevideo_video_sponsor');
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'ultimatevideo',
                    'section' => 'video',
                    'item_id' => $videoId,
                    'name' => _p('default_campaign_custom_name', ['module' => $sModule, 'name' => html_entity_decode($video['title'])])
                ));
            }
            else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('ultimatevideo_video', $videoId);
            }
            $this->alert($sponsor ? _p('video_successfully_sponsored') : _p('video_successfully_unsponsored'), null,
                300, 150, true);
            $this->call('$("#js_' . ($sponsor ? 'unsponsor' : 'sponsor') . '_' . $videoId . '").removeClass("hide");');
            $this->call('$("#js_' . ($sponsor ? 'sponsor' : 'unsponsor') . '_' . $videoId . '").addClass("hide");');
        }

    }

    public function editcategory()
    {
        $iCategoryId = (int)$this->get('id');
        Phpfox::getBlock('ultimatevideo.editcategory', array('iCategoryId' => $iCategoryId));

        $this->html('#site_content', $this->getContent(false));
    }

    public function editcustomfieldgroup()
    {
        $iCustomFieldGroupId = (int)$this->get('id');

        Phpfox::getBlock('ultimatevideo.editcustomfield', array('iCustomFieldGroupId' => $iCustomFieldGroupId));

        $this->html('#site_content', $this->getContent(false));
    }

    public function AdminAddCustomFieldBackEnd()
    {
        $iGroupId = $this->get('iGroupId');

        Phpfox::getComponent('ultimatevideo.admincp.customfield-addfield', array('iGroupId' => $iGroupId), 'controller');
    }

    public function addField()
    {
        $aVals = $this->get('val');

        list($iFieldId, $aOptions) = Phpfox::getService('ultimatevideo.custom.process')->add($aVals);
        if (!empty($iFieldId)) {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
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
        if (Phpfox::getService('ultimatevideo.custom.process')->update($aVals['id'], $aVals)) {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
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
        if (Phpfox::getService('ultimatevideo.custom.process')->delete($id)) {
            $this->remove('#js_custom_field_' . $id);
        }
    }

    public function deleteOption()
    {
        $id = $this->get('id');

        if (Phpfox::getService('ultimatevideo.custom.process')->deleteOption($id)) {
            $aFields = Phpfox::getService('ultimatevideo.custom')->getCustomField();
            $this->remove('#js_current_value_' . $id);
        } else {
            $this->alert(_p('could_not_delete'));
        }
    }

    public function toggleActiveGroup()
    {
        if (Phpfox::getService('ultimatevideo.custom.group')->toggleActivity($this->get('id'))) {
            $this->call('$Core.ultimatevideo.toggleGroupActivity(' . $this->get('id') . ')');
        }
    }

    public function AdminAddCustomFieldGroup()
    {
        if ($aVals = $this->get('val')) {
            if ($this->__isValid($aVals)) {

                if (isset($aVals['group_id'])) {
                    if (Phpfox::getService('ultimatevideo.custom.group')->updateGroup($aVals['group_id'], $aVals)) {
                        Phpfox::getBlock('ultimatevideo.editcustomfield', array('iCustomFieldGroupId' => $aVals['group_id']));

                        $this->html('#site_content', $this->getContent(false));
                        $this->html('#ajax-response-custom', "<div class='message'>" . _p('custom_field_groups_successfully_updated') . "</div>");
                        $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');},2000);');
                    }
                } else {
                    if ($iGroupId = Phpfox::getService('ultimatevideo.custom.group')->addGroup($aVals)) {
                        Phpfox::getBlock('ultimatevideo.editcustomfield', array('iCustomFieldGroupId' => $iGroupId));

                        $this->html('#site_content', $this->getContent(false));
                        $this->html('#ajax-response-custom', "<div class='message'>" . _p('custom_field_groups_successfully_added') . "</div>");
                        $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');},2000);');
                    }
                }
            }
        }

    }

    public function AdminAddCategory()
    {
        if ($aVals = $this->get('val')) {
            if (isset($aVals['category_id'])) {
                if (Phpfox::getService('ultimatevideo.category.process')->update($aVals['category_id'], $aVals)) {
                    $this->html('#ajax-response-custom', "<div class='message'>" . _p('category_successfully_updated') . "</div>");
                    $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');$(\'.apps_menu ul li:eq(2) a\').trigger(\'click\')},2000);');
                }
            } else {
                if (Phpfox::getService('ultimatevideo.category.process')->add($aVals)) {
                    $this->html('#ajax-response-custom', "<div class='message'>" . _p('category_successfully_added') . "</div>");
                    $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');$(\'.apps_menu ul li:eq(2) a\').trigger(\'click\')},2000);');
                }
            }
        }
    }

    public function AdminDeleteCategory()
    {
        $iCategoryId = (int)$this->get('id');
        if (Phpfox::getService('ultimatevideo.category.process')->delete($iCategoryId)) {
            $this->html('#ajax-response-custom', "<div class='message'>" . _p('category_successfully_deleted') . "</div>");
            $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');$(\'.apps_menu ul li:eq(2) a\').trigger(\'click\')},2000);');
            $this->call('setTimeout(function(){ location.reload(); },1000);');
        }
    }

    public function AdminUpdateOrderCategory()
    {
        if ($aOrder = $this->get('order')) {
            if (Phpfox::getService('ultimatevideo.category.process')->updateOrder($aOrder)) {
                $this->html('#ajax-response-custom', "<div class='message'>" . _p('category_order_successfully_updated') . "</div>");
            }
        }
    }

    public function AdminUpdateOrderCustomField()
    {
        if (($aFieldOrders = $this->get('field')) && Phpfox::getService('ultimatevideo.custom.process')->updateOrder($aFieldOrders)) {
            $this->html('#ajax-response-custom', "<div class='message'>" . _p('custom_fields_successfully_updated') . "</div>");

        }

        if (($aGroupOrders = $this->get('group')) && Phpfox::getService('ultimatevideo.custom.group')->updateOrder($aGroupOrders)) {
            $this->html('#ajax-response-custom', "<div class='message'>" . _p('custom_fields_successfully_updated') . "</div>");

        }
    }

    public function AdminDeleteCustomFieldGroup()
    {
        $iCustomFieldGroupId = (int)$this->get('id');
        if (Phpfox::getService('ultimatevideo.custom.group')->deleteGroup($iCustomFieldGroupId)) {
            $this->html('#ajax-response-custom', "<div class='message'>" . _p('custom_fields_successfully_deleted') . "</div>");
            $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');$(\'.apps_menu ul li:eq(5) a\').trigger(\'click\')},2000);');
            $this->call('setTimeout(function(){ location.reload(); },1000);');
        }
    }

    public function AdminDeleteCustomField()
    {
        $iCustomFieldDelete = $this->get('iFieldId');
        $iCustomFieldGroupId = $this->get('iGroupId');
        if (!isset($iCustomFieldGroupId) && empty($iCustomFieldGroupId)) return;
        if (Phpfox::getService('ultimatevideo.custom.process')->delete($iCustomFieldDelete)) {
            Phpfox::getBlock('ultimatevideo.editcustomfield', array('iCustomFieldGroupId' => $iCustomFieldGroupId));

            $this->html('#site_content', $this->getContent(false));
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
            \Phpfox_Error::set(_p('group_name_cannot_be_empty'));
            return false;
        }

        return true;
    }

    public function changeCustomFieldByCategory()
    {
        $oRequest = Phpfox::getLib('request');
        $iCategoryId = $oRequest->get('iCategoryId');
        $aCustomFields = Phpfox::getService('ultimatevideo')->getCustomFieldByCategoryId($iCategoryId);

        $keyCustomField = array();


        $aCustomData = array();
        if ($this->get('iVideoId')) {
            $aCustomDataTemp = Phpfox::getService('ultimatevideo.custom')->getCustomFieldByVideoId($this->get('iVideoId'));

            if (count($aCustomFields)) {
                foreach ($aCustomFields as $aField) {
                    foreach ($aCustomDataTemp as $aFieldValue) {
                        if ($aField['field_id'] == $aFieldValue['field_id']) {
                            $aCustomData[] = $aFieldValue;
                        }
                    }
                }
            }

        }

        if (count($aCustomData)) {
            $aCustomFields = $aCustomData;
        }

        Phpfox::getBlock('ultimatevideo.custom.form', array(
            'aCustomFields' => $aCustomFields,
        ));
        // FAILURE/SUCCESS
        echo json_encode(array(
            'status' => 'SUCCESS',
            'content' => $this->getContent(false)
        ));
    }

    public function validationUrl()
    {
        $sUrl = $this->get('url');
        $sUrl = trim($sUrl);
        if (empty($sUrl)) {
            echo json_encode(array(
                'status' => "FAIL",
            ));
            exit;
        }
        if (substr($sUrl, 0, 7) != 'http://' && substr($sUrl, 0, 8) != 'https://') {
            echo json_encode(array(
                'status' => "FAIL",
                'error_message' => _p('please_provide_a_valid_url')
            ));
            exit;
        }

        if (preg_match('/dailymotion/', $sUrl) && substr($sUrl, 0, 8) == 'https://') {
            $sUrl = str_replace('https', 'http', $sUrl);
        }

        if ($parsed = Phpfox::getService('link')->getLink($sUrl)) {
            if (empty($parsed['embed_code'])) {
                echo json_encode(array(
                    'status' => "FAIL",
                    'error_message' => _p('unable_to_load_a_video_to_embed')
                ));
                exit;
            }
            $embed_code = str_replace('http://player.vimeo.com/', 'https://player.vimeo.com/', $parsed['embed_code']);
            // check fb

            $description = str_replace("<br />", "\r\n", $parsed['description']);
            echo json_encode(array(
                'status' => "SUCCESS",
                'title' => $parsed['title'],
                'description' => $description,
                'embed_code' => $embed_code,
                'default_image' => $parsed['default_image'],
                'duration' => $parsed['duration']
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => "FAIL",
                'error_message' => _p('we_could_not_find_a_video_there_please_check_the_url_and_try_again')
            ));
        }
        exit;
    }

    public function validationUrlLegacy()
    {
        $url = $this->get('url');
        $code = $this->get('code');
        $type = $this->get('type');
        if ($type == "VideoURL") {
            if (empty($url)) {
                echo json_encode(array(
                    'status' => "FAIL",
                    'error_message' => _p('we_could_not_find_a_video_there_please_check_the_url_and_try_again')
                ));
            } else {
                echo json_encode(array(
                    'status' => "SUCCESS",
                    'title' => "",
                    'description' => ""
                ));
            }
            die;
        }
        $adapter = Phpfox::getService('ultimatevideo')->getClass($type);
        if ($type == "Facebook") {
            $adapter->setParams(array('link' => $url));
        } elseif ($type != "Dailymotion") {
            $adapter->setParams(array('code' => $code));
        } else {
            $adapter->setParams(array('link' => $code, 'code' => $code));
        }
        $valid = ($adapter->isValid()) ? true : false;
        if ($adapter->fetchLink()) {
            $title = strip_tags($adapter->getVideoTitle());
            $description = $adapter->getVideoDescription();
            $description = str_replace("<br />", "\r\n", $description);
            echo json_encode(array(
                'status' => "SUCCESS",
                'title' => $title,
                'description' => $description
            ));
        } else {
            echo json_encode(array(
                'status' => "FAIL",
                'error_message' => _p('we_could_not_find_a_video_there_please_check_the_url_and_try_again')
            ));
        }
    }

    public function updateFeaturedInAdmin()
    {
        // Get Params
        $iVideoId = (int)$this->get('iVideoId');
        $iIsFeatured = (int)$this->get('iIsFeatured');
        $iIsFeatured = (int)!$iIsFeatured;

        $oProcess = Phpfox::getService('ultimatevideo.process');
        if ($iVideoId) {
            $sResult = $oProcess->featureVideo($iVideoId, $iIsFeatured);
        }
        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_video'));
            return false;
        }
        if ($iIsFeatured) {
            $this->html('#ynuv_video_update_featured_' . $iVideoId, '<div class="js_item_is_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="$Core.ajaxMessage();ultimatevideo.updateFeatured(' . $iVideoId . ',' . $iIsFeatured . ');"></a></div>');
        } else {
            $this->html('#ynuv_video_update_featured_' . $iVideoId, '<div class="js_item_is_not_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="$Core.ajaxMessage();ultimatevideo.updateFeatured(' . $iVideoId . ',' . $iIsFeatured . ');"></a></div>');
        }

        return true;
    }

    public function updateApprovedInAdmin()
    {
        // Get Params
        $iVideoId = (int)$this->get('iVideoId');
        $iIsApproved = (int)$this->get('iIsApproved');
        $iIsApproved = (int)!$iIsApproved;

        $oProcess = Phpfox::getService('ultimatevideo.process');
        if ($iVideoId) {
            $sResult = $oProcess->approvedVideo($iVideoId, $iIsApproved);
        }
        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_approve_this_video'));
            return false;
        }
        if ($iIsApproved) {
            $this->html('#ynuv_video_update_approve_' . $iVideoId, '<div class="js_item_is_active"><a href="#?call=ultimatevideo.updateApprovedInAdmin&amp;iVideoId=' . $iVideoId . '&amp;iIsApproved=' . $iIsApproved . '" class="js_item_active_link" ></a></div>');
        } else {
            $this->html('#ynuv_video_update_approve_' . $iVideoId, '<div class="js_item_is_not_active"><a href="#?call=ultimatevideo.updateApprovedInAdmin&amp;iVideoId=' . $iVideoId . '&amp;iIsApproved=' . $iIsApproved . '" class="js_item_active_link" ></a></div>');
        }
        $this->call('$Core.loadInit();');
        return true;
    }

    public function deleteVideoInAdmin()
    {
        $iVideoId = (int)$this->get('iVideoId');
        if ($iVideoId) {
            if (Phpfox::getService('ultimatevideo.process')->deleteVideo($iVideoId)) {
                $this->alert(_p('video_successfully_deleted'));
                $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.apps_menu ul li:eq(7) a\').trigger(\'click\')},2000);');
                $this->call('$("#ynuv_video_row_' . $iVideoId . '").slideToggle();');
            } else {
                $this->alert(_p('you_do_not_have_permission_to_delete_this_video'));
            }
        }
    }

    public function actionMultiSelectVideo()
    {
        $aVals = $this->get('video_row');
        $aType = $this->get('val');
        if (!count($aVals)) {
            $this->alert(_p('no_videos_selected'));
            return false;
        }
        $oProcess = Phpfox::getService('ultimatevideo.process');
        if ($aType['selected']) {
            switch ($aType['selected']) {
                case '1':
                    $success = false;
                    foreach ($aVals as $key => $videoID) {
                        $sResult = $oProcess->deleteVideo($videoID);
                        if (!$sResult) {
                            $success = false;
                            $this->alert(_p('you_do_not_have_permission_to_detele_videos'));
                            continue;
                        } else {
                            $success = true;
                            $this->call('$("#ynuv_video_row_' . $videoID . '").slideToggle();');
                        }
                    }
                    if ($success) {

                        $this->alert(_p('videos_successfully_deleted'));
                        $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.apps_menu ul li:eq(7) a\').trigger(\'click\')},2000);');
                    } else {
                        $this->alert(_p('delete_failed'));
                    }
                    break;
                case '2':
                    foreach ($aVals as $key => $videoID) {
                        $sResult = $oProcess->approvedVideo($videoID, 1);
                        if (!$sResult) {
                            $this->alert(_p('you_do_not_have_permission_to_approve_videos'));
                            continue;
                        } else {
                            $this->html('#ynuv_video_update_approve_' . $videoID, '<div class="js_item_is_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="ultimatevideo.updateApproved(' . $videoID . ',1);"></a></div>');
                        }
                    }
                    break;
                case '3':
                    foreach ($aVals as $key => $videoID) {
                        $sResult = $oProcess->approvedVideo($videoID, 0);
                        if (!$sResult) {
                            $this->alert(_p('you_do_not_have_permission_to_unapprove_videos'));
                            continue;
                        } else {
                            $this->html('#ynuv_video_update_approve_' . $videoID, '<div class="js_item_is_not_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="ultimatevideo.updateApproved(' . $videoID . ',0);"></a></div>');
                        }
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }

        return null;
    }

    public function filterAdminFilterVideo()
    {
        $aSearch = $this->get('search');
        Phpfox::getComponent('ultimatevideo.admincp.managevideos', array('search' => $aSearch), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function changePageManageVideo()
    {
        $aPage = $this->get('page');
        $aParam = $this->getAll();
        $aSearch = array(
            'title' => $this->get('title'),
            'owner' => $this->get('owner'),
            'category' => $this->get('category'),
            'source' => $this->get('source'),
            'feature' => $this->get('feature'),
            'approve' => $this->get('approve')
        );
        Phpfox::getComponent('ultimatevideo.admincp.managevideos', array('page' => $aPage, 'search' => $aSearch), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function delete_video()
    {
        $iVideoId = (int)$this->get('iVideoId');
        $isDetail = $this->get('isDetail');

        if ($iVideoId) {
            if (Phpfox::getService('ultimatevideo.process')->deleteVideo($iVideoId)) {
                $this->alert(_p('videos_successfully_deleted'));
                if ($isDetail == 'false') {
                    $this->call('setTimeout(function(){window.location.href=window.location.href;},2000);');
                } else {
                    $sUrl = \Phpfox_Url::instance()->makeUrl('ultimatevideo');
                    $this->call('setTimeout(function(){window.location.href="' . $sUrl . '";},2000);');
                }
            } else {
                $this->alert(_p('you_do_not_have_permission_to_delete_this_video'));

            }
        }
    }

    public function approve_video()
    {
        // Get Params
        $iVideoId = (int)$this->get('iVideoId');

        $oProcess = Phpfox::getService('ultimatevideo.process');
        $sResult = null;

        if ($iVideoId) {
            $sResult = $oProcess->approvedVideo($iVideoId, 1);
        }

        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_approve_this_video'));
        } else {
            $this->alert(_p('video_successfully_approved'));
            $this->call('setTimeout(function(){window.location.href=window.location.href;},2000);');
        }

    }

    public function featured_video()
    {
        // Get Params
        $iVideoId = (int)$this->get('iVideoId');

        $oProcess = Phpfox::getService('ultimatevideo.process');
        $sResult = null;

        if ($iVideoId) {
            $sResult = $oProcess->featureVideo($iVideoId, 1);
        }

        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_video'));
        } else {
            $this->call('$(".ynuv_feature_video_' . $iVideoId . '").data("cmd","unfeatured_video").html("<i class=\"fa fa-diamond\"></i>' .
                _p(' Un-Feature') . '");');
            $this->call('$(".ynuv_feature_video_icon_' . $iVideoId . '").css("display","inline-flex");');
        }

    }

    public function unfeatured_video()
    {
        // Get Params
        $iVideoId = (int)$this->get('iVideoId');

        $oProcess = Phpfox::getService('ultimatevideo.process');
        $sResult = null;

        if ($iVideoId) {
            $sResult = $oProcess->featureVideo($iVideoId, 0);
        }

        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_video'));
        } else {
            $this->call('$(".ynuv_feature_video_' . $iVideoId . '").data("cmd","featured_video").html("<i class=\"fa fa-diamond\"></i>' .
                _p(' Feature') . '");');
            $this->call('$(".ynuv_feature_video_icon_' . $iVideoId . '").css("display","none");');
        }


    }

    public function favorite_video()
    {
        $iVideoId = (int)$this->get('iVideoId');

        Phpfox::getService('ultimatevideo.favorite')->add(Phpfox::getUserId(), $iVideoId);


        $this->call('$(".ynuv_favorite_video_' . $iVideoId . '").data("cmd","unfavorite_video").html("<i class=\"ico ico-star\"></i>' .
            _p(' Un-Favorite') . '")');

    }

    public function unfavorite_video()
    {
        $iVideoId = (int)$this->get('iVideoId');

        Phpfox::getService('ultimatevideo.favorite')->delete(Phpfox::getUserId(), $iVideoId);

        $this->call('if(window.location.href.match("view=favorite") != null || window.location.href.match("view_favorite") != null){setTimeout(function(){window.location.href=window.location.href},2000);}');
        $this->call('$(".ynuv_favorite_video_' . $iVideoId . '").data("cmd","favorite_video").html("<i class=\"ico ico-star-o\"></i>' .
            _p(' Favorite') . '")');

    }

    public function watchlater_video()
    {
        $iVideoId = (int)$this->get('iVideoId');

        Phpfox::getService('ultimatevideo.watchlater')->add(Phpfox::getUserId(), $iVideoId);

        $this->call('$(".ynuv_watchlater_video_' . $iVideoId . '").data("cmd","unwatchlater_video").html("<i class=\"ico ico-clock\"></i>' .
            _p(' Un-Watch Later') . '")');
    }

    public function unwatchlater_video()
    {
        $iVideoId = (int)$this->get('iVideoId');

        Phpfox::getService('ultimatevideo.watchlater')->delete(Phpfox::getUserId(), $iVideoId);

        $this->call('if(window.location.href.match("view=later") != null || window.location.href.match("view_later") != null){setTimeout(function(){window.location.href=window.location.href},2000);}');
        $this->call('$(".ynuv_watchlater_video_' . $iVideoId . '").data("cmd","watchlater_video").html("<i class=\"ico ico-clock-o\"></i>' .
            _p(' Watch Later') . '")');
    }

    public function delete_video_history()
    {
        $iVideoId = (int)$this->get('iVideoId');
        if ($iVideoId) {
            if (Phpfox::getService('ultimatevideo.history')->deleteVideo(Phpfox::getUserId(), $iVideoId)) {
                $this->alert(_p('video_successfully_deleted_from_your_history'));
                $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
            } else {
                $this->alert(_p('delete_history_fail_please_try_again'));
            }
        }
    }

    public function delete_playlist_history()
    {
        $iPlaylistId = (int)$this->get('iVideoId');
        if ($iPlaylistId) {
            if (Phpfox::getService('ultimatevideo.history')->deletePlaylist(Phpfox::getUserId(), $iPlaylistId)) {
                $this->alert(_p('playlist_successfully_removed_from_your_history'));
                $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
            } else {
                $this->alert(_p('delete_history_fail_please_try_again'));
            }
        }
    }

    public function video_clear_all()
    {
        $sView = $this->get('sView');
        if ($sView == 'history') {
            Phpfox::getService('ultimatevideo.history')->deleteAllHistory(0);
            $message = _p('videos_in_history_are_removed_successfully');
        } elseif ($sView == 'favorite') {
            Phpfox::getService('ultimatevideo.favorite')->deleteAllFavorite();
            $message = _p('videos_in_favorite_are_removed_successfully');
        } elseif ($sView == 'later') {
            Phpfox::getService('ultimatevideo.watchlater')->deleteAllWatchlater();
            $message = _p('videos_in_watch_later_are_removed_successfully');
        }
        $this->alert($message);
        $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
    }

    public function playlist_clear_all()
    {
        Phpfox::getService('ultimatevideo.history')->deleteAllHistory(1);
        $this->alert(_p('playlists_in_history_are_removed_successfully'));
        $this->call('{setTimeout(function(){window.location.href=window.location.href},2000);}');
    }

    public function showPopupCustomGroup()
    {
        $iCategoryId = $this->get('category_id');
        Phpfox::getBlock('ultimatevideo.popup_customfield_category', array('category_id' => $iCategoryId));
    }

    public function updateFeaturedPlaylistInAdmin()
    {
        // Get Params
        $iPlaylistId = (int)$this->get('iPlaylistId');
        $iIsFeatured = (int)$this->get('iIsFeatured');
        $iIsFeatured = (int)!$iIsFeatured;

        $oProcess = Phpfox::getService('ultimatevideo.playlist.process');
        if ($iPlaylistId) {
            $sResult = $oProcess->feature($iPlaylistId, $iIsFeatured);
        }
        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_playlist'));
            return false;
        }
        if ($iIsFeatured) {
            $this->html('#ynuv_playlist_update_featured_' . $iPlaylistId, '<div class="js_item_is_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="ultimatevideo_playlist.updateFeatured(' . $iPlaylistId . ',' . $iIsFeatured . ');"></a></div>');
        } else {
            $this->html('#ynuv_playlist_update_featured_' . $iPlaylistId, '<div class="js_item_is_not_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="ultimatevideo_playlist.updateFeatured(' . $iPlaylistId . ',' . $iIsFeatured . ');"></a></div>');
        }

        return true;
    }

    public function updateApprovedPlaylistInAdmin()
    {
        // Get Params
        $iPlaylistId = (int)$this->get('iPlaylistId');
        $iIsApproved = (int)$this->get('iIsApproved');
        $iIsApproved = (int)!$iIsApproved;

        $oProcess = Phpfox::getService('ultimatevideo.playlist.process');
        if ($iPlaylistId) {
            $sResult = $oProcess->approved($iPlaylistId, $iIsApproved);
        }
        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_approve_this_playlist'));
            return false;
        }
        if ($iIsApproved) {
            $this->html('#ynuv_playlist_update_approve_' . $iPlaylistId, '<div class="js_item_is_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="ultimatevideo_playlist.updateApproved(' . $iPlaylistId . ',' . $iIsApproved . ');"></a></div>');
        } else {
            $this->html('#ynuv_playlist_update_approve_' . $iPlaylistId, '<div class="js_item_is_not_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="ultimatevideo_playlist.updateApproved(' . $iPlaylistId . ',' . $iIsApproved . ');"></a></div>');
        }

        return true;
    }

    public function deletePlaylistInAdmin()
    {
        $iPlaylistId = (int)$this->get('iPlaylistId');
        if ($iPlaylistId) {
            if (Phpfox::getService('ultimatevideo.playlist.process')->delete($iPlaylistId)) {
                $this->alert(_p('playlist_successfully_deleted'));
                $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.apps_menu ul li:eq(8) a\').trigger(\'click\')},2000);');
                $this->call('$("#ynuv_playlist_row_' . $iPlaylistId . '").slideToggle();');
            } else {
                $this->alert(_p('you_do_not_have_permission_to_delete_this_playlist'));
            }
        }
    }

    public function filterAdminFilterPlaylist()
    {
        $aSearch = $this->get('search');
        Phpfox::getComponent('ultimatevideo.admincp.manageplaylists', array('search' => $aSearch), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function actionMultiSelectPlaylist()
    {
        $aVals = $this->get('playlist_row');
        $aType = $this->get('val');
        if (!count($aVals)) {
            $this->alert(_p('no_playlists_selected'));
            return false;
        }
        $oProcess = Phpfox::getService('ultimatevideo.playlist.process');
        if ($aType['selected']) {
            switch ($aType['selected']) {
                case '1':
                    $success = false;
                    foreach ($aVals as $key => $playlistID) {
                        $sResult = $oProcess->delete($playlistID);
                        if (!$sResult) {
                            $success = false;
                            $this->alert(_p('you_do_not_have_permission_to_detele_playlists'));
                            continue;
                        } else {
                            $success = true;
                            $this->call('$("#ynuv_playlist_row_' . $playlistID . '").slideToggle();');
                        }
                    }
                    if ($success) {

                        $this->alert(_p('playlists_successfully_deleted'));
                        $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.apps_menu ul li:eq(8) a\').trigger(\'click\')},2000);');
                    } else {
                        $this->alert(_p('delete_failed'));
                    }
                    break;
                case '2':
                    foreach ($aVals as $key => $playlistID) {
                        $sResult = $oProcess->approved($playlistID, 1);
                        if (!$sResult) {
                            $this->alert(_p('you_do_not_have_permission_to_approve_playlists'));
                            continue;
                        } else {
                            $this->html('#ynuv_playlist_update_approve_' . $playlistID, '<div class="js_item_is_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="ultimatevideo_playlist.updateApproved(' . $playlistID . ',1);"></a></div>');
                        }
                    }
                    break;
                case '3':
                    foreach ($aVals as $key => $playlistID) {
                        $sResult = $oProcess->approved($playlistID, 0);
                        if (!$sResult) {
                            $this->alert(_p('you_do_not_have_permission_to_unapprove_playlists'));
                            continue;
                        } else {
                            $this->html('#ynuv_playlist_update_approve_' . $playlistID, '<div class="js_item_is_not_active"><a href="javascript:void(0);" class="js_item_active_link"  onclick="ultimatevideo_playlist.updateApproved(' . $playlistID . ',0);"></a></div>');
                        }
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }

        return null;
    }

    public function rate_video()
    {
        Phpfox::isUser(true);
        $iVideoId = (int)$this->get('iVideoId');
        $iRating = (int)$this->get('iValue');

        if ($iVideoId) {
            if (Phpfox::getService('ultimatevideo.rating')->add(Phpfox::getUserId(), $iVideoId, $iRating)) {
                $aVideo = Phpfox::getService('ultimatevideo')->getVideo($iVideoId);
                $total_rate_text = $aVideo['total_rating'] == 1 ? _p('rate') : _p('rates');
                $this->call('UltimateVideo.update_video_rating(' . $iRating . ',' . $aVideo['rating'] . ',' . $aVideo['total_rating'] . ',"' . $total_rate_text . '");');
            } else {
                $this->alert(_p('rating_fail_please_try_again'));
            }
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'approve':
                user('ynuv_can_approve_video', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.process')->approvedVideo($iId, 1);
                }
                $sMessage = _p('videos_successfully_approved');
                break;
            case 'delete':
                user('ynuv_can_delete_video_of_other_user', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.process')->deleteVideo($iId);
                }
                $sMessage = _p('videos_successfully_deleted');
                break;
            case 'feature':
                user('ynuv_can_feature_video', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.process')->featureVideo($iId, 1);
                }
                $sMessage = _p('videos_successfully_featured');
                break;
            case 'unfeature':
                user('ynuv_can_feature_video', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.process')->featureVideo($iId, 0);
                }
                $sMessage = _p('videos_successfully_un_featured');
                break;
            case 'unfavorite':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.favorite')->delete(Phpfox::getUserId(), $iId);
                }
                $sMessage = _p('videos_successfully_un_favorite');
                break;
            case 'unwatched':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.watchlater')->delete(Phpfox::getUserId(), $iId);
                }
                $sMessage = _p('videos_successfully_remove_from_watch_later_list');
                break;
            case 'history':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.history')->deleteVideo(Phpfox::getUserId(), $iId);
                }
                $sMessage = _p('videos_successfully_remove_from_history');
                break;
        }

        $this->alert($sMessage, 'Moderation', 300, 150, true);
        $this->hide('.moderation_process');
        $this->call('setTimeout(function(){ window.location.href = window.location.href; },3000);');
    }

    public function changePageManagePlaylist()
    {
        $aPage = $this->get('page');
        $aParam = $this->getAll();
        $aSearch = array(
            'title' => $this->get('title'),
            'owner' => $this->get('owner'),
            'category' => $this->get('category'),
            'feature' => $this->get('feature'),
            'approve' => $this->get('approve')
        );
        Phpfox::getComponent('ultimatevideo.admincp.manageplaylists', array('page' => $aPage, 'search' => $aSearch), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function getAllPlaylistOfUser()
    {
        Phpfox::getBlock('ultimatevideo.user_playlist_checklist');
        $eleId = $this->get('eleId');
        $this->html('#' . $eleId, $this->getContent(false));
        $this->call('if (!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )){$(".ynuv_quick_list_playlist_wrapper").mCustomScrollbar({theme: "minimal-dark",mouseWheel: {preventDefault: true}}).addClass(\'dont-unbind-children\');}');
        $this->call('ynuv_dropdown_scrollto($(' . $eleId . ').closest(".dropdown"));');
    }

    public function updateQuickAddVideoToPlaylist()
    {

        $isChecked = $this->get('isChecked');
        $iVideoId = $this->get('iVideoId');
        $iPlaylistId = $this->get('iPlaylistId');
        $iContainerId = $this->get('iContainerId');
        if (!$iVideoId || !$iPlaylistId) {
            $this->alert(_p("Can't add video to playlist"));
            return;
        }
        $aPlaylist = Phpfox::getService('ultimatevideo.playlist')->getPlaylistById($iPlaylistId);

        if ($isChecked) {
            $result = Phpfox::getService('ultimatevideo.playlist.process')->addVideo($iVideoId, $iPlaylistId);

            if (!$result) {
                $message = _p('can_not_add_videos_to_this_playlist');
                $this->call('$(".ynuv_error_add_to_playlist_' . $iVideoId . '").css("display","flex");');
                $this->html('.ynuv_error_add_to_playlist_' . $iVideoId, $message);
                $this->call('$("#' . $iContainerId . '").find(".checkbox > label[data-playlist=\'' . $iPlaylistId . '\'] input").removeAttr("checked");');
                $this->call('setTimeout(function(){$(".ynuv_error_add_to_playlist_' . $iVideoId . '").hide()},3000);');
            }
        } else {
            $result = Phpfox::getService('ultimatevideo.playlist.process')->removeVideo($iVideoId, $iPlaylistId);
            if (!$result) {
                $message = _p('remove_failed_this_video_is_not_belong_to_this_playlist');
                $this->call('$(".ynuv_error_add_to_playlist_' . $iVideoId . '").css("display","flex");');
                $this->html('.ynuv_error_add_to_playlist_' . $iVideoId, $message);
                $this->call('setTimeout(function(){$(".ynuv_error_add_to_playlist_' . $iVideoId . '").hide()},3000);');
            }
        }
    }

    public function addPlaylistOnAction()
    {
        $iVideoId = $this->get('iVideoId');
        $iContainerId = $this->get('iContainerId');
        $iPrivacy = $this->get('iPrivacy', 0);
        $aVals = array(
            'title' => $this->get('sTitle'),
            'privacy' => $iPrivacy
        );
        if (strlen(trim($aVals['title'])) == 0) {
            $this->alert(_p('please_input_the_playlist_title'));
        } else {
            if (!user('ynuv_can_add_playlist', 0)) {
                $message = _p('you_dont_have_permission_to_add_new_playlist');
                $this->call('$(".ynuv_error_add_to_playlist_' . $iVideoId . '").show();');
                $this->html('.ynuv_error_add_to_playlist_' . $iVideoId, $message);
                $this->call('setTimeout(function(){$(".ynuv_error_add_to_playlist_' . $iVideoId . '").hide()},3000);');
                return;
            }
            if ($iPlaylistId = Phpfox::getService('ultimatevideo.playlist.process')->add($aVals, true, $iVideoId)) {
                $aPlaylist = Phpfox::getService('ultimatevideo.playlist')->getPlaylistById($iPlaylistId);
                $result = Phpfox::getService('ultimatevideo.playlist.process')->addVideo($iVideoId, $iPlaylistId);
                if ($result) {
                    $message = _p('added_successfully');
                    $this->call('$(".ynuv_noti_add_to_playlist_' . $iVideoId . '").css("display","flex");');
                    $this->html('.ynuv_noti_add_to_playlist_' . $iVideoId, $message);
                    $this->call('setTimeout(function(){$(".ynuv_noti_add_to_playlist_' . $iVideoId . '").hide()},3000);');
                } else {
                    $message = _p('can_not_add_videos_to_new_playlist');
                    $this->call('$(".ynuv_error_add_to_playlist_' . $iVideoId . '").css("display","flex");');
                    $this->html('.ynuv_error_add_to_playlist_' . $iVideoId, $message);
                    $this->call('setTimeout(function(){$(".ynuv_error_add_to_playlist_' . $iVideoId . '").hide()},3000);');
                }

                Phpfox::getBlock('ultimatevideo.user_playlist_checklist');
                $this->html('#' . $iContainerId, $this->getContent(false));
            }
        }

    }

    public function featured_playlist()
    {
        // Get Params
        $iPlaylistId = (int)$this->get('iVideoId');

        $oProcess = Phpfox::getService('ultimatevideo.playlist.process');
        $sResult = null;

        if ($iPlaylistId) {
            $sResult = $oProcess->feature($iPlaylistId, 1);
        }

        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_playlist'));
        } else {
            $this->call('$(".ynuv_feature_playlist_' . $iPlaylistId . '").data("cmd","unfeatured_playlist").html("<i class=\"fa fa-diamond\"></i>' .
                _p(' Un-Feature') . '");');
            $this->call('$(".ynuv_feature_playlist_icon_' . $iPlaylistId . '").css("display","inline-flex");');
        }

    }

    public function unfeatured_playlist()
    {
        // Get Params
        $iPlaylistId = (int)$this->get('iVideoId');

        $oProcess = Phpfox::getService('ultimatevideo.playlist.process');
        $sResult = null;

        if ($iPlaylistId) {
            $sResult = $oProcess->feature($iPlaylistId, 0);
        }

        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_playlist'));
        } else {
            $this->call('$(".ynuv_feature_playlist_' . $iPlaylistId . '").data("cmd","featured_playlist").html("<i class=\"fa fa-diamond\"></i>' .
                _p(' Feature') . '");');
            $this->call('$(".ynuv_feature_playlist_icon_' . $iPlaylistId . '").css("display","none");');
        }


    }

    public function approve_playlist()
    {
        // Get Params
        $iPlaylistId = (int)$this->get('iVideoId');

        $oProcess = Phpfox::getService('ultimatevideo.playlist.process');
        $sResult = null;

        if ($iPlaylistId) {
            $sResult = $oProcess->approved($iPlaylistId, 1);
        }

        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_approve_this_playlist'));
        } else {
            $this->alert(_p('playlist_successfully_approved'));
            $this->call('window.location.href=window.location.href');
        }

    }

    public function delete_playlist()
    {
        $iPlaylistId = (int)$this->get('iPlaylistId');
        $isDetail = $this->get('isDetail');
        if ($iPlaylistId) {
            if (Phpfox::getService('ultimatevideo.playlist.process')->delete($iPlaylistId)) {
                $this->alert(_p('playlist_successfully_deleted'));
                if ($isDetail == 'false') {
                    $this->call('setTimeout(function(){window.location.href=window.location.href;},2000);');
                } else {
                    $sUrl = \Phpfox_Url::instance()->makeUrl('ultimatevideo.playlist');
                    $this->call('setTimeout(function(){window.location.href="' . $sUrl . '";},2000);');
                }
            } else {
                $this->alert(_p('you_do_not_have_permission_to_delete_this_playlist'));
            }
        }
    }

    public function playlist_moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'approve':
                user('ynuv_can_approve_playlist', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.playlist.process')->approved($iId, 1);
                }
                $sMessage = _p('playlists_successfully_approved');
                break;
            case 'delete':
                user('ynuv_can_delete_playlist_of_other_user', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.playlist.process')->delete($iId);
                }
                $sMessage = _p('playlists_successfully_deleted');
                break;
            case 'feature':
                user('ynuv_can_feature_playlist', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.playlist.process')->feature($iId, 1);
                }
                $sMessage = _p('playlists_successfully_featured');
                break;
            case 'unfeature':
                user('ynuv_can_feature_playlist', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.playlist.process')->feature($iId, 0);
                }
                $sMessage = _p('playlists_successfully_un_featured');
                break;
            case 'history':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('ultimatevideo.history')->deletePlaylist(Phpfox::getUserId(), $iId);
                }
                $sMessage = _p('playlists_successfully_remove_from_history');
                break;
        }

        $this->alert($sMessage, 'Moderation', 300, 150, true);
        $this->hide('.moderation_process');
        $this->call('setTimeout(function(){ window.location.href = window.location.href; },3000);');
    }

    public function changePageVideosInPlaylist()
    {
        $iPage = $this->get('page');
        $iPlaylistId = $this->get('playlist');
        $iModeView = $this->get('mode');
        Phpfox::getBlock('ultimatevideo.playlist_detail_mode_listing', array('page' => $iPage, 'playlist_id' => $iPlaylistId, 'current_mode' => $iModeView));
        $this->html('#ultimatevide_playlist_mode_listing', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'ynultimatevideo_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('ynultimatevideo');
    }

    public function updateActivity()
    {
        Phpfox::getService('ultimatevideo.category.process')->updateActivity($this->get('id'), $this->get('active'), $this->get('sub'));
    }

    public function updateHot()
    {
        Phpfox::getService('ultimatevideo.category.process')->updateHot($this->get('id'), $this->get('active'), $this->get('sub'));
    }

    public function getsubcategory()
    {
        $iSub = $this->get('id');
        Phpfox::getComponent('ultimatevideo.admincp.category', array('sub' => $iSub), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function loadFormAddOnFeed()
    {
        return Phpfox::getLib('template')->getBuiltFile('ultimatevideo.block.share_on_feed');
    }

    public function uploadVideoOnFeed()
    {
        $aVals = $this->get('val');
    }

    public function rate_list()
    {
        $this->error(false);
        Phpfox::getBlock('ultimatevideo.rate_list');

        $iTotalRates = Phpfox::getService('ultimatevideo.rating')->getRates($this->get('video_id'), true);

        $sTitle = $iTotalRates == 1 ? _p('1_rate_for_this_video') : _p('number_rates_for_this_video', ['number' => $iTotalRates]);
        $this->setTitle($sTitle);
    }
}
<?php


namespace Apps\YouNet_UltimateVideos\Controller;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');


class AddController extends Phpfox_Component
{
    public function process()
    {
        if (!setting('ynuv_app_enabled')) {
            return Phpfox::getLib('module')->setController('error.404');
        }
        Phpfox::isUser(true);
        \Core\Route\Controller::$name = '';
        $bIsEdit = false;
        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);
        $sError = null;


        if (!$sError && $iEditId = $this->_checkIsInEditVideo()) {
            $bIsEdit = true;
            $aVideo = Phpfox::getService('ultimatevideo')->getVideoForEdit($iEditId);
            $sModule = $aVideo['module_id'];
            $iItem = $aVideo['item_id'];
            if (!$aVideo) {
                $sError = _p('unable_to_find_the_video_you_are_looking_for');
            }
            if (Phpfox::getUserId() == $aVideo['user_id'] && !user('ynuv_can_edit_own_video') && !user('ynuv_can_edit_video_of_other_user')) {
                $sError = _p('you_do_not_have_permission_to_edit_your_video');
            }
            if (Phpfox::getUserId() != $aVideo['user_id'] && !user('ynuv_can_edit_video_of_other_user')) {
                $sError = _p('you_do_not_have_permission_to_edit_video_add_by_other_user');
            }
            if (Phpfox::isModule('tag')) {

                $aVideo['tag_list'] = '';

                $aTags = Phpfox::getService('tag')->getTagsById('ynultimatevideo', $aVideo['video_id']);

                if (isset($aTags[$iEditId])) {
                    foreach ($aTags[$iEditId] as $aTag) {
                        $aVideo['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $aVideo['tag_list'] = trim(trim($aVideo['tag_list'], ','));
                }
            }

            if (!empty($aVideo['image_path'])) {
                $aVideo['current_image'] = $aVideo['image_server_id'] == -1 ? (Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') . $aVideo['image_path']) : Phpfox::getLib('image.helper')->display(
                    array(
                        'server_id' => $aVideo['image_server_id'],
                        'path' => 'core.url_pic',
                        'file' => $aVideo['image_path'],
                        'suffix' => '_500',
                        'return_url' => true
                    )
                );
            }
            $this->setParam('aSelectedCategories', array($aVideo['category_id']));
            $this->template()->assign(array(
                'aForms' => $aVideo,
                'iMaxFileSize' => (user('ynuv_max_file_size_photos_upload') == 0) ? null : Phpfox::getLib('phpfox.file')->filesize((user('ynuv_max_file_size_photos_upload') / 1024) * 1048576),
            ));
        }

        if (!empty($sModule) && !empty($iItem)) {
            $this->template()->assign(array(
                'sModule' => $sModule,
                'iItem' => $iItem
            ));
        }

        if (!empty($sModule) && ($sModule == "pages" || $sModule == "groups") && Phpfox::hasCallback($sModule, 'getItem')) {
            $aCallback = Phpfox::callback($sModule . '.getItem', $iItem);
            $bCheckParentPrivacy = true;
            if (Phpfox::hasCallback($sModule, 'checkPermission')) {
                $bCheckParentPrivacy = Phpfox::callback($sModule . '.checkPermission', $iItem, 'ultimatevideo.share_videos');
            }

            if (!$bCheckParentPrivacy) {
                $sError = _p('unable_to_view_this_item_due_to_privacy_settings');
            }
        }

        if (!$sError && Phpfox::getService('ultimatevideo')->countVideoOfUserId(Phpfox::getUserId()) >= user('ynuv_how_many_video_user_can_add') && !$bIsEdit) {
            $sError = _p('you_have_reached_your_creating_video_limit_please_contact_administrator');
        }
        if (!$sError && !user('ynuv_can_upload_video') && !$bIsEdit) {
            $sError = _p('you_do_not_have_permission_to_upload_a_video_please_contact_administrator');
        }
        $bIsSpam = false;
        if (!$sError && user('ynuv_time_before_share_other_video', 0) != 0 && !$bIsEdit) {
            $iFlood = user('ynuv_time_before_share_other_video', 0);
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field' => 'time_stamp', // The time stamp field
                    'table' => Phpfox::getT('ynultimatevideo_videos'), // Database table we plan to check
                    'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                )
            );

            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                $bIsSpam = true;

                \Phpfox_Error::set(_p('uploading_video_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
            }
        }
        $aValidationParam = $this->_getValidationParams();
        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
        $isPass = true;
        $uploadByZencoder = false;
        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ynuv_add_video_form',
                'aParams' => $aValidationParam
            )
        );

        if (!$sError && $this->_checkIfSubmittingAForm()) {

            $aVals = $this->request()->getArray('val');

            $aValidationParam = $this->_getValidationParams();

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'ynuv_add_video_form',
                    'aParams' => $aValidationParam
                )
            );

            if ((user('ynuv_time_before_share_other_video', 0) != 0) && !$bIsEdit) {
                $iFlood = user('ynuv_time_before_share_other_video', 0);
                $aFlood = array(
                    'action' => 'last_post', // The SPAM action
                    'params' => array(
                        'field' => 'time_stamp', // The time stamp field
                        'table' => Phpfox::getT('ynultimatevideo_videos'), // Database table we plan to check
                        'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                        'time_stamp' => $iFlood * 60 // Seconds);
                    )
                );

                // actually check if flooding
                if (Phpfox::getLib('spam')->check($aFlood)) {

                    \Phpfox_Error::set(_p('uploading_video_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                }
            }
            if ($this->_verifyCustomForm($aVals) && $oValid->isValid($aVals)) {
                if (\Phpfox_Error::isPassed()) {
                    \Phpfox_Error::reset();
                    if ($iEditedId = $this->_checkIsInEditVideo()) {
                        if ($iVideoId = Phpfox::getService('ultimatevideo.process')->update($aVals, $iEditedId)) {
                            $this->url()->send('ultimatevideo.add', array('id' => $iVideoId), _p('your_video_successfully_updated'));
                        }
                    } else {
                        //integrate with business and contest
                        $aVals['callback_module'] = $this->request()->get('module', false);
                        $aVals['callback_item_id'] = $this->request()->getInt('item', false);

                        if (empty($aVals['video_type']) || $aVals['video_type'] == "upload") {
                            $aVals['video_source'] = 'Uploaded';
                            if (empty($aVals['video_path']) && empty($aVals['encoding_id'])) {
                                $isPass = false;
                                \Phpfox_Error::set(_p('no_files_found_or_file_is_not_valid_please_try_again'));
                            } else {
                                $methodUpload = Phpfox::getParam('ultimatevideo.ynuv_video_method_upload');
                                if($methodUpload == 0 && !empty($aVals['video_path'])) {
                                    $aVals['video_code'] = substr($aVals['video_path'], strpos($_FILES['video_path'], '/') + 1);
                                }
                                elseif($methodUpload == 1 && !empty($aVals['encoding_id'])) {
                                    $storageData = [
                                        'privacy' => (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
                                        'privacy_list' => json_encode(isset($aVals['privacy_list']) ? $aVals['privacy_list'] : []),
                                        'callback_module' => (isset($aVals['callback_module']) ? $aVals['callback_module'] : ''),
                                        'callback_item_id' => (isset($aVals['callback_item_id']) ? (int)$aVals['callback_item_id'] : 0),
                                        'parent_user_id' => isset($aVals['parent_user_id']) ? $aVals['parent_user_id'] : 0,
                                        'title' => isset($aVals['title']) ? $aVals['title'] : '',
                                        'text' => '',
                                        'description' => isset($aVals['status_info']) ? $aVals['status_info'] : (isset($aVals['description']) ? $aVals['description'] : ''),
                                        'updated_info' => 1,
                                        'feed_values' => json_encode($aVals),
                                        'tagged_friends' => isset($aVals['tagged_friends']) ? $aVals['tagged_friends'] : null,
                                        'location_name' => (!empty($aVals['location']['name'])) ? Phpfox::getLib('parse.input')->clean($aVals['location']['name']) : '',
                                        'video_embed' => $aVals['video_embed'],
                                        'video_source' => 'Uploaded',
                                        'is_approved' => !Phpfox::getUserParam('ultimatevideo.ynuv_should_be_approve_before_display_video'),
                                        'allow_upload_channel' => isset($aVals['allow_upload_channel']) ? $aVals['allow_upload_channel'] : 0,
                                        'category' => json_encode(isset($aVals['category']) ? $aVals['category'] : []),
                                        'tag_list' => isset($aVals['tag_list']) ? $aVals['tag_list'] : ''
                                    ];

                                    $storageData['location_name'] = (!empty($aVals['location']['name'])) ? Phpfox::getLib('parse.input')->clean($aVals['location']['name']) : null;
                                    if ((!empty($aVals['location']['latlng']))) {
                                        $aMatch = explode(',', $aVals['location']['latlng']);
                                        $aMatch['latitude'] = floatval($aMatch[0]);
                                        $aMatch['longitude'] = floatval($aMatch[1]);
                                        $storageData['location_latlng'] = [
                                            'latitude' => $aMatch['latitude'],
                                            'longitude' => $aMatch['longitude']
                                        ];
                                    } else {
                                        $storageData['location_latlng'] = '';
                                    }

                                    storage()->update('ynuv_video_' . $aVals['encoding_id'], $storageData);
                                    $uploadByZencoder = true;
                                }
                            }
                        }
                        if ($isPass) {
                            if(!$uploadByZencoder) {
                                $iVideoId = Phpfox::getService('ultimatevideo.process')->add($aVals);
                            }
                            if ($uploadByZencoder || !empty($iVideoId)) {
                                if ($aVals['video_source'] == "Uploaded") {
                                    if (isset($aVals['allow_upload_channel'])) {
                                        $this->url()->send('ultimatevideo.oauth2', $iVideoId, null);
                                    } else {
                                        $this->url()->send('ultimatevideo', ['view' => 'my'], _p('your_video_successfully_added'));
                                    }
                                } else {
                                    $this->url()->send('ultimatevideo', $iVideoId, _p('your_video_successfully_added'));
                                }
                            }
                        }
                    }
                }
            }
        }
        $aCallback = false;
        if (!$sError && $sModule !== false && $iItem !== false && Phpfox::hasCallback($sModule, 'getVideoDetails')) {
            if ($aCallback = Phpfox::callback($sModule . '.getVideoDetails', array('item_id' => $iItem))) {
                $this->template()
                    ->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home'])
                    ->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
                if ($sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($iItem, '')) {
                    $sError = _p('unable_to_view_this_item_due_to_privacy_settings');
                }
            }
        }

        if ($sError) {
            $this->template()
                ->setBreadCrumb(_p('ultimate_videos'), Phpfox::permalink('ultimatevideo', null, false));
        } else {
            $this->template()->setBreadCrumb(_p('ultimate_videos'), Phpfox::permalink('ultimatevideo', null, false))
                ->setBreadCrumb((!$bIsEdit) ? _p('share_a_video') : _p('edit_video'), Phpfox::permalink('ultimatevideo.add', ($bIsEdit) ? 'id_' . $iEditId : null, null, false))
                ->setTitle((!$bIsEdit) ? _p('share_a_video') : _p('edit_video'))
                ->setBreadcrumb(($bIsEdit ? _p('edit_video') . ': ' . $aVideo['title'] : _p('add_new_video')), ($bIsEdit ? $this->url()->makeUrl('ultimatevideo.add', array('id' => $aVideo['video_id'])) : $this->url()->makeUrl('ultimatevideo.add')), true)
                ->setEditor(array('wysiwyg' => true))
                ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'progress.js' => 'static_script',
                    'jscript/jquery.validate.js' => 'app_YouNet_UltimateVideos',
                ));
            $this->template()->assign(array(
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'sCategories' => Phpfox::getService('ultimatevideo.category')->get(),
                'corePath' => $corePath,
                'bIsEdit' => $bIsEdit,
            ));
        }

        $this->template()->setHeader('cache', array(
            '<script type="text/javascript">
                            var isInitAddVideo = false;
                            $Behavior.ultimatevideoAddVideoOnLoad = function() {
                                if(isInitAddVideo == false){
                                    ultimatevideoAddVideoInit = window.setInterval(function(){
                                        if(typeof ultimatevideo == \'undefined\'){
                                        }else{
                                            if(isInitAddVideo == false){
                                                isInitAddVideo = true;
                                                ultimatevideo.ultimatevideoAddVideo();
                                                window.clearInterval(ultimatevideoAddVideoInit);
                                            }
                                            else
                                            {
                                                window.clearInterval(ultimatevideoAddVideoInit);
                                            }
                                        }
                                    },200);
                                }
                            }
                        </script>'
        ));

        if (!$sError && $bIsEdit) {
            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">
                    var isInitAddVideoCategory = false;
                    $Behavior.ultimatevideoEditCategory = function() {
                        var aCategories = JSON.parse(\'' . $aVideo['categories'] . '\');
                        var categorySection;

                        for (var i = 0; i < aCategories.length; i++) {
                            
                            categorySection = $(\'#ynuv_section_category\');
                            $(categorySection).find(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true);
                            $(categorySection).find(\'#js_mp_holder_\' + aCategories[i]).show();
                        }
                        if(isInitAddVideoCategory == false){
                            ultimatevideoAddVideoCategoryInit = window.setInterval(function(){
                                            if(typeof ultimatevideo == \'undefined\'){
                                            }else{
                                                if(isInitAddVideoCategory == false){
                                                    isInitAddVideoCategory = true;
                                                    ultimatevideo.changeCustomFieldByCategory(aCategories[aCategories.length-1]);
                                                    window.clearInterval(ultimatevideoAddVideoCategoryInit);
                                                }
                                                else
                                                {
                                                    window.clearInterval(ultimatevideoAddVideoCategoryInit);
                                                }
                                            }
                            },200);
                        }
                    }
                </script>'
            ));
            $this->template()->buildPageMenu('js_ultimatevideo_block',
                [],
                array(
                    'link' => $this->url()->permalink('ultimatevideo', $aVideo['video_id'], $aVideo['title']),
                    'phrase' => _p('view_this_video')
                )
            );
        }
        if (!$bIsEdit) {
            $aMenus = array(
                'upload' => _p('upload_a_video'),
                'url' => _p('from_url')
            );
            $this->template()->buildPageMenu('js_ultimatevideo_block',
                [],
                array(
                    'link' => $this->url()->permalink('ultimatevideo', $aVideo['video_id'], $aVideo['title']),
                    'phrase' => _p('view_this_video')
                )
            );
        }
        $this->template()->assign('bNoAttachaFile', true);
        if (!$sError && Phpfox::isModule('attachment')) {
            $this->setParam(array('attachment_share' => array(
                    'type' => 'ultimatevideo',
                    'id' => 'ynuv_add_video_form',
                    'edit_id' => ($bIsEdit ? $this->request()->getInt('id') : 0),
                    'inline' => false
                )
                )
            );
        }

        $this->template()->assign([
            'sError' => $sError,
            'bIsSpam' => $bIsSpam,
        ]);

        return null;
    }

    /**
     * check validator for form
     * @by : hainm
     * @param array $aVals
     * @return array
     */
    private function _getValidationParams($aVals = array())
    {
        if (!$this->_checkIsInEditVideo()) {
            $aParam = array(
                'title' => array(
                    'def' => 'required',
                    'title' => _p('video_title_cannot_be_empty'),
                ));
            if (!empty($aVals['video_type']) && $aVals['video_type'] == 'url') {

                $aParam['video_link'] = array(
                    'def' => 'required',
                    'title' => _p('video_url_cannot_be_empty'),
                );
                $aParam['video_code'] = array(
                    'def' => 'required',
                    'title' => _p('invalid_video_url_please_try_again'),
                );
            }
        } else {
            $aParam = array(
                'title' => array(
                    'def' => 'required',
                    'title' => _p('video_title_cannot_be_empty'),
                ),
            );
        }

        return $aParam;
    }

    private function _checkIfSubmittingAForm()
    {
        if ($this->request()->getArray('val')) {
            return true;
        } else {
            return false;
        }
    }

    private function _checkIsInEditVideo()
    {
        if ($this->request()->getInt('id')) {
            $iEditedVideoId = $this->request()->getInt('id');
            return $iEditedVideoId;
        } else {
            return false;
        }
    }

    private function _verifyCustomForm($aVals)
    {
        if (isset($aVals['custom'])) {
            $aFieldValues = $aVals['custom'];

            $aFields = Phpfox::getService('ultimatevideo.custom')->getCustomField();

            foreach ($aFields as $k => $aField) {
                if ($aField['is_required'] && isset($aFieldValues[$aField['field_id']]) && empty($aFieldValues[$aField['field_id']])) {
                    return \Phpfox_Error::set(_p('custom_field_is_required') . _p($aField['phrase_var_name']));
                }
            }

        }
        return true;

    }

    private function _getFileType($aFile)
    {
        $aExts = preg_split("/[\/\\.]/", $aFile['name']);
        $iCnt = count($aExts) - 1;
        return strtolower($aExts[$iCnt]);
    }
}
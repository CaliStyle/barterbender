<?php


namespace Apps\YouNet_UltimateVideos\Controller;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');


class AddPlaylistController extends \Phpfox_Component
{
    public function process()
    {
        if (!setting('ynuv_app_enabled')) {
            return Phpfox::getLib('module')->setController('error.404');
        }
        Phpfox::isUser(true);
        \Core\Route\Controller::$name = '';
        $bIsEdit = false;
        $sError = null;
        if ($iEditId = $this->_checkIsInEditPlaylist()) {
            $bIsEdit = true;
            $aPlaylist = Phpfox::getService('ultimatevideo.playlist')->getForEdit($iEditId);

            if (!$aPlaylist) {
                $sError = _p('unable_to_find_the_playlist_you_are_looking_for');
            }
            if (!user('ynuv_can_edit_playlist_of_other_user') && !user('ynuv_can_edit_own_playlists') && Phpfox::getUserId() == $aPlaylist['user_id']) {
                $sError = _p('you_do_not_have_permission_to_edit_your_playlist');
            }
            if (!user('ynuv_can_edit_playlist_of_other_user') && Phpfox::getUserId() != $aPlaylist['user_id']) {
                $sError = _p('you_do_not_have_permission_to_edit_playlist_add_by_other_user');
            }
            $aVideos = Phpfox::getService('ultimatevideo.playlist')->getVideosManage($iEditId);

            foreach($aVideos as $key => $video) {
                if($video['image_server_id'] == -1) {
                    $aVideos[$key]['image_path'] = Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') . $video['image_path'];
                }
            }

            if (!empty($aPlaylist['image_path'])) {
                $aPlaylist['current_image'] = Phpfox::getLib('image.helper')->display(
                    array(
                        'server_id' => $aPlaylist['image_server_id'],
                        'path' => 'core.url_pic',
                        'file' => $aPlaylist['image_path'],
                        'suffix' => '_120',
                        'return_url' => true
                    )
                );
            }
            $this->setParam('aSelectedCategories', array($aPlaylist['category_id']));
            $this->template()->assign(array(
                'aVideos' => $aVideos,
                'aForms' => $aPlaylist,
                'iMaxFileSize' => (user('ynuv_max_file_size_photos_upload') == 0) ? null : Phpfox::getLib('phpfox.file')->filesize((user('ynuv_max_file_size_photos_upload') / 1024) * 1048576),
            ));
        }
        $aValidationParam = $this->_getValidationParams();
        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ynuv_add_playlist_form',
                'aParams' => $aValidationParam
            )
        );
        if (!$sError && !user('ynuv_can_add_playlist') && !$bIsEdit) {
            $sError = _p('you_do_not_have_permission_to_add_a_playlist_please_contact_administrator');
        }
        // Manage videos
        $aOrder = $this->request()->getArray('order');
        $sRemovedVideo = $this->request()->get('removed');
        if (!$sError && ($iEditedId = $this->_checkIsInEditPlaylist()) && (!empty($aOrder) || !empty($sRemovedVideo))) {
            if (!empty($aOrder)) {
                Phpfox::getService('ultimatevideo.playlist.process')->updateOrder($aOrder, $iEditId);
            }
            if (!empty($sRemovedVideo)) {
                $aRemoved = explode(',', $sRemovedVideo);
                foreach ($aRemoved as $key => $iVideoId) {
                    Phpfox::getService('ultimatevideo.playlist.process')->removeVideo($iVideoId, $iEditedId);
                }
            }

            $this->url()->send('ultimatevideo.addplaylist', array('id' => $iEditedId, 'tab' => 'video'), _p('videos_list_successfully_updated'));
        }
        if (!$sError && $this->_checkIfSubmittingAForm()) {
            $aVals = $this->request()->getArray('val');

            $aValidationParam = $this->_getValidationParams();

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'ynuv_add_playlist_form',
                    'aParams' => $aValidationParam
                )
            );

            if ($oValid->isValid($aVals)) {
                if ((!empty($aVals['temp_file']) || !empty($aVals['remove_logo'])) && $iEditedId = $this->_checkIsInEditPlaylist()) {
                    Phpfox::getService('ultimatevideo.playlist.process')->processuploadImage($aVals, $iEditedId);
                }
                if (\Phpfox_Error::isPassed()) {
                    if ($iEditedId = $this->_checkIsInEditPlaylist()) {
                        if ($iPlaylistId = Phpfox::getService('ultimatevideo.playlist.process')->update($aVals, $iEditedId)) {
                            $this->url()->send('ultimatevideo.addplaylist', array('id' => $iPlaylistId), _p('your_playlist_successfully_updated'));
                        }
                    } else {

                        if ($iPlaylistId = Phpfox::getService('ultimatevideo.playlist.process')->add($aVals)) {
                            $this->url()->send('ultimatevideo.playlist', $iPlaylistId, _p('your_playlist_successfully_added'));
                        }
                    }
                }
            }
        }

        $this->template()->setBreadCrumb(_p('ultimate_videos'), Phpfox::permalink('ultimatevideo.playlist', null, false))->setBreadCrumb((!$bIsEdit) ? _p('add_new_playlist') : _p('manage_playlist'), Phpfox::permalink('ultimatevideo.addplaylist', ($bIsEdit) ? 'id_' . $iEditId : null, null, false))
            ->setBreadcrumb(($bIsEdit ? _p('manage_playlist') . ': ' . $aPlaylist['title'] : _p('add_new_playlist')), ($bIsEdit ? $this->url()->makeUrl('ultimatevideo.addplaylist', array('id' => $aPlaylist['playlist_id'])) : $this->url()->makeUrl('ultimatevideo.addplaylist')), true)
            ->setEditor(array('wysiwyg' => true))
            ->setTitle((!$bIsEdit) ? _p('create_a_new_playlist') : _p('manage_playlist'))
            ->setEditor(array('wysiwyg' => true))
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script'
            ));
        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
        $this->template()->assign(array(
            'sCreateJs' => $oValid->createJS(),
            'sGetJsForm' => $oValid->getJsForm(),
            'sCategories' => Phpfox::getService('ultimatevideo.category')->get(),
            'corePath' => $corePath,
            'sModule' => '',
            'bIsEdit' => $bIsEdit
        ));
        $this->template()->setHeader('cache', array(
            '<script type="text/javascript">
                            var isInitAddPlaylist = false;
                            $Behavior.ultimatevideoAddPlaylistOnLoad = function() {
                                if(isInitAddPlaylist == false){
                                    setTimeout(function(){
                                        if(typeof ultimatevideo_playlist == \'undefined\'){
                                        }else{
                                            isInitAddPlaylist = true;
                                            ultimatevideo_playlist.ultimatevideoAddPlaylist();

                                        }
                                    },250);
                                }
                            }
                        </script>'
        ));
        if (!$sError && $bIsEdit) {
            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">
                    $Behavior.ultimatevideoEditCategory = function() {
                        var aCategories = JSON.parse(\'' . $aPlaylist['categories'] . '\');
                        var categorySection;

                        for (var i = 0; i < aCategories.length; i++) {
                            
                            categorySection = $(\'#ynuv_section_category\');
                            $(categorySection).find(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true);
                            $(categorySection).find(\'#js_mp_holder_\' + aCategories[i]).show();
                        }

                    }
                </script>'
            ));
            $aMenus = array(
                'detail' => _p('playlist_info'),
                'video' => _p('manage_videos'),
            );

            $this->template()->buildPageMenu('js_ultimatevideo_playlist_block',
                $aMenus,
                array(
                    'link' => $this->url()->permalink('ultimatevideo.playlist', $aPlaylist['playlist_id'], $aPlaylist['title']),
                    'phrase' => _p('view_this_playlist')
                )
            );
        }
        $this->template()->assign('bNoAttachaFile', true);
        if (!$sError && Phpfox::isModule('attachment')) {
            $this->setParam(array('attachment_share' => array(
                    'type' => 'ultimatevideo',
                    'id' => 'ynuv_add_playlist_form',
                    'edit_id' => ($bIsEdit ? $this->request()->getInt('id') : 0),
                    'inline' => false
                )
                )
            );
        }
        $this->template()->assign([
            'sError' => $sError
        ]);

        return null;
    }

    private function _checkIfSubmittingAForm()
    {
        if ($this->request()->getArray('val')) {
            return true;
        } else {
            return false;
        }
    }

    private function _checkIsInEditPlaylist()
    {
        if ($this->request()->getInt('id')) {
            $iEditedPlaylistId = $this->request()->getInt('id');
            return $iEditedPlaylistId;
        } else {
            return false;
        }
    }

    private function _getValidationParams($aVals = array())
    {
        $aParam = array(
            'title' => array(
                'def' => 'required',
                'title' => _p('playlist_name_cannot_be_empty'),
            )
        );

        return $aParam;
    }
}
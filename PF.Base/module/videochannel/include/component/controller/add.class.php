<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Add extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        $this->_save();
        $this->display();
    }
    private function display()
    {
        $sLayout = '';
        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);
        if ($sModule !== false && $iItem !== false)
        {
            $sLayout = 'display_in_page';
        }
        switch ($sLayout)
        {
            case 'display_in_page':
                $this->_displayInPage();
                break;

            case 'display_normal':
            default:
                $this->_displayNormal();
                break;
        }
    }

    private function _displayInPage()
    {
        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);
        $aInPage = Phpfox::getService('videochannel')->getIsInPageModule($sModule, $iItem, $this->request()->get('val'));
        $bCanAddChannelInPage = false;
        if (isset($aInPage['module_id']))
        {
            $sModule = $aInPage['module_id'];
            $iItem = $aInPage['item_id'];
            if ((Phpfox::getService('pages')->hasPerm($aInPage['item_id'], 'videochannel.share_videos') && Phpfox::getUserParam('videochannel.can_upload_video_on_page', true)) || Phpfox::isAdmin())
            {
                $bCanAddChannelInPage = true;
            }
            else
            {
                $this->url()->send('videochannel');
            }
        }
        else
        {
            Phpfox::getUserParam('videochannel.can_upload_videos', true);
        }
        $aCallback = false;
        if ($sModule !== false && $iItem !== false && Phpfox::hasCallback($sModule, 'getVideoDetails'))
        {
            if (($aCallback = Phpfox::callback($sModule . '.getVideoDetails', array('item_id' => $iItem))))
            {
                $this->template()
                        ->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home'])
                        ->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
                if ($sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($iItem, 'videochannel.share_videos'))
                {
                    return Phpfox_Error::display(_p('videochannel.unable_to_view_this_item_due_to_privacy_settings'));
                }
            }
        }
        // If post value, check module and item.
        if (($aVals = $this->request()->get('val')))
        {
            $sModule = (isset($aVals['module']) ? $aVals['module'] : false);
            $iItem =  (isset($aVals['item']) ? $aVals['item'] : false);
        }
        
        $sMethod = 'simple';
        $sMethodUrl = str_replace(array('method_simple/', 'method_massuploader/'), '', $this->url()->getFullUrl()) . 'method_' . ($sMethod == 'simple' ? 'massuploader' : 'simple') . '/';
        if ($sMethod == 'massuploader')
        {
            $this->_setMassUploader();
        }
        $this->_buildPageMenu();
        $this->template()
                ->setTitle(_p('videochannel.upload_share_a_video'))
                ->setBreadcrumb(_p('videochannel.videochannel'), ($aCallback === false ? $this->url()->makeUrl('videochannel') : $aCallback['url_home'] . "videochannel"))
                ->setBreadcrumb(_p('videochannel.upload_share_a_video'), ($aCallback === false ? $this->url()->makeUrl('videochannel.add') : $this->url()->makeUrl('videochannel.add', array('module' => $sModule, 'item' => $iItem))), true);
        // Post link.
        $strPostLink = $this->url()->makeUrl('videochannel.add.module_' . $this->request()->get('module', false) . '.item_' . $this->request()->get('item', false));
        $this->template()
                ->assign(array(
                    'sModule' => $this->request()->get('module', false),
                    'iItem' => $this->request()->get('item', false),
                    'sMethod' => $sMethod,
                    'sMethodUrl' => $sMethodUrl,
                    'strPostLink' => $strPostLink
                ))
                ->setHeader('cache', array(
            'upload.js' => 'module_videochannel',
            'videochannel.js' => 'module_videochannel'
        ));
    }

    private function _displayNormal()
    {
        Phpfox::getUserParam('videochannel.can_upload_videos', true);

        $sMethod = 'simple';
        $sMethodUrl = str_replace(array('method_simple/', 'method_massuploader/'), '', $this->url()->getFullUrl()) . 'method_' . ($sMethod == 'simple' ? 'massuploader' : 'simple') . '/';
        if ($sMethod == 'massuploader')
        {
            $this->_setMassUploader();
        }
        $this->_buildPageMenu();
        $this->template()
                ->setTitle(_p('videochannel.upload_share_a_video'))
                ->setBreadcrumb(_p('videochannel.videochannel'), $this->url()->makeUrl('videochannel'))
                ->setBreadcrumb(_p('videochannel.upload_share_a_video'), $this->url()->makeUrl('videochannel.add'), true);
        // Post link.
        $strPostLink = $this->url()->makeUrl('videochannel.add.url');
        $this->template()->assign(array(
            'sModule' => false,
            'iItem' => false,
            'sMethod' => $sMethod,
            'sMethodUrl' => $sMethodUrl,
            'strPostLink' => $strPostLink
        ))->setHeader('cache', array(
            'upload.js' => 'module_videochannel',
            'videochannel.js' => 'module_videochannel'
        ));
    }

    private function _setFFMPEG()
    {
        $sFile = '';
        if (false)
        {
            $sFile = _p('videochannel.file_upload');
            $sErrorFFMPEG = "";
            $aVals = array(
                'ffmpeg_path' => Phpfox::getParam('videochannel.ffmpeg_path'),
                'mencoder_path' => Phpfox::getParam('videochannel.mencoder_path')
            );
            if (!($mReturn = Phpfox::getService('videochannel')->requirementCheck($aVals)))
            {
                $sSiteTitle = Phpfox::getParam('core.site_title');
                $sErrorFFMPEG = _p('videochannel.must_set_the_path_to_ffmpeg_for_sitename_before_uploading_of_videos', array('sSiteName' => $sSiteTitle));
                $this->template()->assign(array('sErrorFFMPEG' => $sErrorFFMPEG));
            }
        }
        return $sFile;
    }

    private function _setMassUploader()
    {
        $iMaxFileSize = (Phpfox::getUserParam('videochannel.video_file_size_limit') === 0 ? null : ((Phpfox::getUserParam('videochannel.video_file_size_limit') / 1) * 1048576));
        if (Phpfox::isModule('photo'))
        {
            $this->template()->setPhrase(array('photo.you_can_upload_a_jpg_gif_or_png_file'));
        }
        $arPhrase = array(
            'core.name',
            'core.status',
            'core.in_queue',
            'core.upload_failed_your_file_size_is_larger_then_our_limit_file_size'
        );

        $this->template()->setPhrase($arPhrase)
                ->setHeader(array(
            'massuploader/swfupload.js' => 'static_script',
            'massuploader/upload.js' => 'static_script',
            '<script type="text/javascript">
                $oSWF_settings = {
                    object_holder: function(){
                        return \'swf_video_upload_button_holder\';
                    },

                    div_holder: function(){
                        return \'swf_video_upload_button\';
                    },

                    get_settings: function(){
                        swfu.setUploadURL("' . $this->url()->makeUrl('videochannel.frame') . '");
                        swfu.setFileSizeLimit("' . $iMaxFileSize . ' B");
                        swfu.setFileUploadLimit(1);
                        swfu.setFileQueueLimit(1);
                        swfu.customSettings.flash_user_id = ' . Phpfox::getUserId() . ';
                        swfu.customSettings.sHash = "' . Phpfox::getService('core')->getHashForUpload() . '";
                        swfu.setFileTypes("*.mpg; *.mpeg; *.wmv; *.avi; *.mov; *.flv","Video files (mpg, mpeg, wmv, avi, mov or flv)");
                        swfu.atFileQueue = function(){
                            $(\'#js_upload_actual_inner_form\').slideUp();

                            $(\'#js_video_form :input\').each(function(iKey, oObject){									
                                swfu.addPostParam($(oObject).attr(\'name\'), $(oObject).val());
                            });
                        }
                    }
                }
            </script>',
                )
        );
    }

    private function _buildPageMenu()
    {
        $aMenus = array();
        $sFile = $this->_setFFMPEG();
        if ($sFile != '')
        {
            $aMenus['file'] = $sFile;
        }
        //$aMenus['url'] = _p('videochannel.paste_url');
        $this->template()->buildPageMenu('js_upload_video', $aMenus);
    }

    /**
     * Save function includes: Add and Edit.
     */
    private function _save()
    {
        if (($aVals = $this->request()->get('val')))
        {
            if (($iFlood = Phpfox::getUserParam('videochannel.flood_control_videos')) !== 0)
            {
                $aFlood = array(
                    'action' => 'last_post', // The SPAM action
                    'params' => array(
                        'field' => 'time_stamp', // The time stamp field
                        'table' => Phpfox::getT('channel_video'), // Database table we plan to check
                        'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                        'time_stamp' => $iFlood * 60 // Seconds);	
                    )
                );
                // actually check if flooding
                if (Phpfox::getLib('spam')->check($aFlood))
                {
                    Phpfox_Error::set(_p('videochannel.you_are_sharing_a_video_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                }
            }
            if (Phpfox_Error::isPassed())
            {
                if (Phpfox::getService('videochannel.grab')->get($aVals['url']))
                {
                    // Add video.
                    
                    if ($iId = Phpfox::getService('videochannel.process')->addShareVideo($aVals))
                    {
                        $aVideo = Phpfox::getService('videochannel')->getForEdit($iId);
                        if (Phpfox::getService('videochannel.grab')->hasImage())
                        {
                            if (isset($aVals['module']) && isset($aVals['item']) && Phpfox::hasCallback($aVals['module'], 'uploadVideo'))
                            {
                                $aCallback = Phpfox::callback($aVals['module'] . '.uploadVideo', $aVals['item']);
                                if ($aCallback !== false)
                                {
                                    $this->url()->send($aCallback['url_home'], array('videochannel', $sTitle), _p('videochannel.video_successfully_added'));
                                }
                            }
                            $this->url()->permalink('videochannel', $aVideo['video_id'], $aVideo['title'], true, _p('videochannel.video_successfully_added'));
                        }
                        else
                        {
                            $this->url()->send('videochannel.edit.photo', array('id' => $aVideo['video_id']), _p('videochannel.video_successfull_added_however_you_will_have_to_manually_upload_a_photo_for_it'));
                        }
                    }
                }
            }
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_add_clean')) ? eval($sPlugin) : false);
    }

}

?>

<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Edit extends Phpfox_Component
{

    /**
     *
     * @var Videochannel_Service_Videochannel 
     */
    public $oSerVideoChannel;

    /**
     *
     * @var Phpfox_Template 
     */
    public $oLibTemplate;

    /**
     *
     * @var Phpfox_Url 
     */
    public $oLibUrl;

    /**
     *
     * @var Videochannel_Service_Category_Category 
     */
    public $oSerVideoChannelCategory;

    public function __construct($aParams)
    {
        parent::__construct($aParams);

        $this->oSerVideoChannel = Phpfox::getService('videochannel');

        $this->oLibTemplate = Phpfox::getLib('template');

        $this->oLibUrl = Phpfox::getLib('url');

        $this->oSerVideoChannelCategory = Phpfox::getService('videochannel.category');
    }

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        $bIsEdit = false;
        $sStep = $this->request()->get('step', false);
        $sAction = $this->request()->get('req3', false);
        // Call the service.
        $oServerVideoChannel = Phpfox::getService('videochannel');
        $oServiceCategory = Phpfox::getService('videochannel.category');
        // Get the callback.
        $aCallback = false;
        if (($iId = $this->request()->getInt('id')))
        {
            if (($aVideo = $oServerVideoChannel->getForEdit($iId)))
            {
                $bIsEdit = true;
                if (!empty($aVideo['image_path'])) {
                    $aVideo['current_image'] = Phpfox::getLib('image.helper')->display(
                        array(
                            'server_id' => $aVideo['image_server_id'],
                            'path' => 'core.url_pic',
                            'file' => $aVideo['image_path'],
                            'suffix' => '_120',
                            'return_url' => true
                        )
                    );
                }
            }
        }
        if ($bIsEdit === false)
        {
            return Phpfox_Error::display(_p('videochannel.unable_to_edit_this_video'));
        }
        // Update video.
        if (($aVals = $this->request()->getArray('val')))
        {
            if (count($aVals) == 1 && isset($aVals['keyword']))
            {
                // Do nothing.
            }
            else
            {
                if (($mReturn = Phpfox::getService('videochannel.process')->update($aVideo['video_id'], $aVals)))
                {
                    if (isset($aVideo['is_featured']) && $aVideo['is_featured'] == 1)
                    {
                        Phpfox::getLib('cache')->remove('videochannel_featured');
                    }
                    if (isset($aVals['actions']))
                    {
                        $this->url()->send('videochannel.edit.' . $aVals['action'], array('id' => $aVideo['video_id']), _p('videochannel.video_successfully_updated'));
                    }
                    else
                    {
                        $this->url()->permalink('videochannel', $aVideo['video_id'], $aVideo['title'], true, _p('videochannel.video_successfully_updated'));
                    }
                }
                $sStep = (isset($aVals['step']) ? $aVals['step'] : '');
                $sAction = (isset($aVals['action']) ? $aVals['action'] : '');
            }
        }
        if ($aVideo['module_id'] != 'videochannel' && Phpfox::hasCallback($aVideo['module_id'], 'uploadVideo'))
        {
            $aCallback = Phpfox::callback($aVideo['module_id'] . '.uploadVideo', $aVideo['item_id']);
        }
        $sVideoMessage = '';
        if (($sVideoMessage = Phpfox::getLib('session')->get('video_add_message')))
        {
            Phpfox::getLib('session')->remove('video_add_message');
        }
        $this->template()
                ->buildPageMenu('js_video_block', array(
            'detail' => _p('videochannel.video_details'),
            'photo' => _p('videochannel.photo')
                ), array(
            'link' => $this->url()->permalink('videochannel', $aVideo['video_id'], $aVideo['title']),
            'phrase' => _p('videochannel.view_this_video')
                )
        );
        $bIsInChannel = false;
        $bIsInChannel = Phpfox::getService('videochannel.process')->isInChannel($iId);
        $iMaxFileSize = (Phpfox::getUserParam('videochannel.max_size_for_video_photos') === 0 ? null : ((Phpfox::getUserParam('videochannel.max_size_for_video_photos') / 1024) * 1048576));
        $this->template()->setTitle(_p('videochannel.editing_video') . ': ' . $aVideo['title']);
        // Set breadcrumb for page module.
        if (!empty($aVideo['module_id']) && $aVideo['module_id'] != 'videochannel')
        {
            if ($aCallback = Phpfox::callback($aVideo['module_id'] . '.getVideoDetails', $aVideo))
            {
                // Set for root page and user page.
                $this->template()
                        ->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home'])
                        ->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
                if ($aVideo['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aCallback['item_id'], 'videochannel.view_browse_videos'))
                {
                    return Phpfox_Error::display(_p('videochannel.unable_to_view_this_item_due_to_privacy_settings'));
                }
            }
        }
        // Set for Video Channel page.
        $this->template()
                ->setBreadcrumb(_p('videochannel.videochannel'), ($aCallback === false ? $this->url()->makeUrl('videochannel') : $aCallback['url_home'] . 'videochannel'))
                ->setBreadcrumb(_p('videochannel.editing_video') . ': ' . $aVideo['title'], $this->oLibUrl->makeUrl('videochannel.edit', array('id' => $iId)), true)
                ->setPhrase(array('core.select_a_file_to_upload'))
                ->setHeader(array(
            'videochannel.js' => 'module_videochannel',
            'edit.js' => 'module_videochannel',
            'progress.js' => 'static_script',
            '<script type="text/javascript">$Behavior.videoProgressBarSettings = function(){ oProgressBar = {holder: \'#js_video_block_photo_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: 1, total: 1, frame_id: \'js_upload_frame\', file_id: \'image\'}; $Core.progressBarInit();}</script>'
                )
        );
        // Set default category id.
        $iCategoryId = 0;
        // Fix bug: Ajax Mode.
        // Get categories by video id.
        $aCategories = $oServiceCategory->getCategoriesByVideoId($iId);
        // A video will has a parent category and maybe have a child category. 
        // If it only has a parent, just get parent, otherwise get child.
        if (count($aCategories) == 1)
        {
            // Get parent category id.
            $iCategoryId = isset($aCategories[0]['category_id']) ? $aCategories[0]['category_id'] : 0;
        }
        else
        {
            // Get child category id.
            foreach ($aCategories as $aCategory)
            {
                // Is a child.
                if ($aCategory['parent_id'] != 0)
                {
                    // Get the child category id. Because max count is 2.
                    $iCategoryId = isset($aCategory['category_id']) ? $aCategory['category_id'] : 0;
                }
            }
        }

        $this->template()->assign(array(
            'aCallback' => $aCallback,
            'bIsInChannel' => $bIsInChannel,
            'sStep' => $sStep,
            'sVideoMessage' => $sVideoMessage,
            'sAction' => ($sAction ? $sAction : 'detail'),
            'sCategories' => $oServiceCategory->getSelectBox(0,1),
            'aForms' => $aVideo,
            'iMaxFileSize' => $iMaxFileSize,
            'sOnClickDeleteImage' => "if (confirm('" . _p('videochannel.are_you_sure') . "')) { $('#js_submit_upload_image').show(); $('#js_video_upload_image').show(); $('#js_video_current_image').remove(); $.ajaxCall('videochannel.deleteImage', 'id=" . $aVideo['video_id'] . "'); } return false;",
            'iMaxFileSize_filesize' => Phpfox::getLib('phpfox.file')->filesize($iMaxFileSize)
                )
        );
        $this->template()->setHeader('cache', array(
            '<script type="text/javascript">
                $Behavior.EditCategory = function() {
                    var aCategories = JSON.parse("[' . $aVideo['categories'] .']");
                    var categorySection;

                    for (var i = 0; i < aCategories.length; i++) {
                        categorySection = $(\'#videochannel_section_category\');
                        $(categorySection).find(\'.js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true);
                        $(categorySection).find(\'#js_mp_holder_\' + aCategories[i]).show();
                    }
                }
            </script>'
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_edit_clean')) ? eval($sPlugin) : false);
    }

}

?>

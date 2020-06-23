<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Cover_Photos extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('directory.helper')->buildMenu();
        if ($iEditedBusinessId = $this->request()->getInt('id')) {
            $this->setParam('iBusinessId', $iEditedBusinessId);
        }

        if (empty($iEditedBusinessId)) {
            $this->url()->send('directory');
        }

        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iEditedBusinessId);

        // check permission
        if (!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'], $iEditedBusinessId) ||
            !Phpfox::getService('directory.permission')->canManageCoverPhotosDashBoard($iEditedBusinessId)
        ) {
            $this->url()->send('subscribe');
        }
        $sModule = $aBusiness['module_id'];
        $iItemId = $aBusiness['item_id'];
        if (!empty($sModule) && $sModule != 'directory' && !empty($iItemId)) {
            if (Phpfox::hasCallback($sModule, 'getItem')) {
                $aCallback = Phpfox::callback($sModule . '.getItem', $iItemId);
                if ($aCallback === false) {
                    return Phpfox_Error::display(_p('Cannot find the parent item.'));
                }
            }
        }
        if ($aVals = $this->request()->getArray('val')) {
            $aResult = array();
            if (isset($aVals['order_photo'])) {
                $aResult = Phpfox::getService('directory.process')->updateCoverPhotos($aVals);
            }

            $this->url()->send("directory.cover-photos", array('id' => $iEditedBusinessId),
                _p('directory.updated_cover_photos_successfully'));
        }

        $aImages = Phpfox::getService('directory')->getImages($iEditedBusinessId);
        $iMaxFileSize = Phpfox::getParam('directory.max_upload_size_photos');
        $iUploaded = count($aImages);
        $iMaxUpload = $aBusiness['package_max_cover_photo'] - $iUploaded;
        $this->template()
            ->setEditor()
            ->setPhrase(array())
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'share.js' => 'module_attachment',
                'country.js' => 'module_core',
                '<script type="text/javascript">$Behavior.directoryProgressBarSettings = function(){ if ($Core.exists(\'#js_directory_block_cover_photos_holder\')) { oProgressBar = {holder: \'#js_directory_block_cover_photos_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: ' . $iMaxUpload . ', total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\'}; $Core.progressBarInit(); } }</script>',
                'jquery/ui.js' => 'static_script',
            ));

        $this->template()->assign(array(
            'aImages' => $aImages
        ));

        $this->template()->assign(array(
            'aParamsUpload' => array(
                'id' => $iEditedBusinessId,
                'total_image' => count($aImages),
                'total_image_limit' => $iMaxUpload + count($aImages),
                'remain_upload' => $iMaxUpload,
            ),
            'sMainImage' => $aBusiness['logo_path'],
            'iTotalImage' => count($aImages),
            'iMaxUpload' => $iMaxUpload,
            'iMaxFileSize' => $iMaxFileSize,
            'iBusinessid' => $iEditedBusinessId,
            'sCorePath' => Phpfox::getParam('core.path')
        ));
        $this->template()->setBreadcrumb(_p('directory.cover_photos'),
            $this->url()->permalink('directory.edit', 'id_' . $iEditedBusinessId));

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();


    }

    public function clean()
    {

    }

}

?>
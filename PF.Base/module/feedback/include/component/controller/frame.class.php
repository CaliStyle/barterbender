<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class FeedBack_Component_Controller_Frame extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() {
        $iId = $this->request()->getInt('id');

        $sAttach = Phpfox::getService('feedback')->getTotalAttachment($iId);
        if ($sAttach['total_attachment'] >= Phpfox::getUserParam('feedback.define_how_many_pictures_can_be_uploaded_per_feedback')) {
            echo json_encode([
                'errors' => [_p('feedback.you_have_reach_limit_uploaded_pictures_for_this_feedback')]
            ]);
            exit;
        }
        // Make sure the user group is actually allowed to upload an image
        if (!Phpfox::getUserParam('feedback.can_upload_pictures')) {
            echo json_encode([
                'errors' => [_p('You do not have permission to upload photos for feedback')]
            ]);
            exit;
        }

        $aFeedBack = Phpfox::getService('feedback')->getFeedBackDetailById($iId);
        if (!$aFeedBack) {
            echo json_encode([
                'errors' => [_p('The feedback you are looking for either does not exist or has been removed')]
            ]);
            exit;
        }
        if (!is_dir(Phpfox::getParam('core.dir_pic') . 'feedback')) {
            if (!@mkdir(Phpfox::getParam('core.dir_pic') . 'feedback', 0777, 1)) {

            }
        }
        $aParams = Phpfox::getService('feedback')->getUploadParams(['id' => $iId]);
        $aParams['user_id'] = $aFeedBack['user_id'];
        $aParams['type'] = 'feedback';

        $aImage = Phpfox::getService('user.file')->load('file', $aParams);
        if (!$aImage) {
            echo json_encode([
                'errors' => [_p('cannot_find_the_uploaded_photo_please_try_again')]
            ]);
            exit;
        }

        if (!empty($aImage['error'])) {
            echo json_encode([
                'errors' => [$aImage['error']]
            ]);
            exit;
        }
        $aFile = Phpfox::getService('user.file')->upload('file', $aParams, true);
        if (empty($aFile) || !empty($aFile['error'])) {
            if (empty($aFile)) {
                echo json_encode([
                    'errors' => [_p('cannot_find_the_uploaded_file_please_try_again')]
                ]);
                exit;
            }

            if (!empty($aFile['error'])) {
                echo json_encode([
                    'errors' => [$aFile['error']]
                ]);
                exit;
            }
        }

        $iImageId = Phpfox::getService('feedback.process')->uploadPicture([
            'file_name' => basename($aFile['name']),
            'picture_path' => sprintf($aFile['name'], ''),
            'thumb_url' => sprintf($aFile['name'], '_70'),
            'filesize' => 300,
            'feedback_id' => $iId

        ]);
        Phpfox::getService('feedback.process')->updateTotalPicture($iId);

        echo json_encode([
            'id' => $iImageId,
            'aFile' => $aFile,
            'aImage' => $aImage
        ]);
        exit;
    }



    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('feedback.component_controller_frame_clean')) ? eval($sPlugin) : false);
    }
}

?>
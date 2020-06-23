<?php
defined('PHPFOX') or exit('NO DICE!');

class Fevent_Component_Controller_Frame_Upload extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iId = $_REQUEST['id'];
        $aEvent = Phpfox::getService('fevent')->getForEdit($iId);
        if (!$aEvent) {
            echo json_encode([
                'errors' => [_p('the_event_you_are_looking_for_either_does_not_exist_or_has_been_removed')]
            ]);
            exit;
        }
        $aParams = Phpfox::getService('fevent')->getUploadParams(['id' => $iId]);
        $aParams['user_id'] = $aEvent['user_id'];
        $aParams['type'] = 'fevent';

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
        $iImageId = db()->insert(Phpfox::getT('fevent_image'), array(
            'event_id' => $iId,
            'image_path' => $aFile['name'],
            'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
        ));
        if (empty($aEvent['image_path']) && $iImageId) {
            Phpfox::getService('fevent.process')->setDefault($iImageId);
        }
        echo json_encode([
            'id' => $iImageId,
        ]);
        exit;
    }
}
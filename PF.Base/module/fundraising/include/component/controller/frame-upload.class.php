<?php
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Frame_Upload extends Phpfox_Component
{
    public function process()
    {
        $iId = $_REQUEST['id'];
        $aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iId);
        if (!$aCampaign) {
            echo json_encode([
                'errors' => [_p('the_campaign_you_are_looking_for_either_does_not_exist_or_has_been_removed')]
            ]);
            exit;
        }

        // Check callback to get params
        $sType = $_REQUEST['type'];
        if (!Phpfox::hasCallback($sType, 'getUploadParams')) {
            echo json_encode([
                'errors' => [_p('Do not has necessary callback')]
            ]);
            exit;
        }
        $aParams = Phpfox::callback($sType . '.getUploadParams');
        $aParams['user_id'] = $aCampaign['user_id'];
        $aParams['type'] = $sType;
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

        // Add image
        $iImageId = db()->insert(Phpfox::getT('fundraising_image'), array(
            'campaign_id' => $iId,
            'image_path' => 'fundraising' . PHPFOX_DS . $aFile['name'],
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'ordering' => 0,
        ));

        if (empty($aCampaign['image_path']) && $iImageId) {
            Phpfox::getService('fundraising.process')->setDefault($iImageId);
        }

        echo json_encode([
            'id' => $iImageId,
        ]);
        exit;
    }
}

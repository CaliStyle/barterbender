<?php
defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Frame_Upload extends Phpfox_Component
{
    public function process()
    {
        $iId = $_REQUEST['id'];
        $aListing = Phpfox::getService('ecommerce')->getProductForEdit($iId);
        if (!$aListing) {
            echo json_encode([
                'errors' => [_p('the_listing_you_are_looking_for_either_does_not_exist_or_has_been_removed')]
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
        $aParams['user_id'] = $aListing['user_id'];
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

        $sDirImage = Phpfox::getParam('core.dir_pic') . 'ynecommerce' . PHPFOX_DS;
        $sLogoPath = 'ynecommerce' . PHPFOX_DS . $aFile['name'];
        list($width, $height, $type, $attr) = getimagesize($sDirImage . sprintf($aFile['name'], ''));

        // Add image
        $iImageId = db()->insert(Phpfox::getT('ecommerce_product_image'), array(
            'product_id' => $iId,
            'image_path' => $sLogoPath,
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'ordering' => 0,
            'is_profile' => 0,
            'file_size' => $aFile['size'],
            'extension' => pathinfo($sDirImage . sprintf($aFile['name'], ''), PATHINFO_EXTENSION),
            'width' => $width,
            'height' => $height,
        ));

        if (empty($aListing['logo_path']) && $iImageId) {
            db()->update(Phpfox::getT('ecommerce_product'),
                array(
                    'logo_path' => $sLogoPath,
                    'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
                ),
                'product_id = ' . $iId);
        }
        echo json_encode([
            'id' => $iImageId,
        ]);
        exit;
    }
}

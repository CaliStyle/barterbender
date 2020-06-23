<?php
namespace Apps\YNC_Blogs\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_File;
use Phpfox_Request;

defined('PHPFOX') or exit('NO DICE!');

class FrameUploadController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $sType = 'ynblog';
        if (!Phpfox::hasCallback($sType, 'getUploadParams')) {
            return false;
        }

        $aParams = [
            'list_type' => [],
            'max_size' => null,
            'upload_dir' => Phpfox::getParam('core.dir_pic'),
            'thumbnail_sizes' => [],
            'user_id' => Phpfox::getUserId(),
            'type' => $sType,
            'param_name' => 'file',
            'field_name' => 'temp_file'

        ];

        $aParams = array_merge($aParams, Phpfox::callback($sType . '.getUploadParams'));
        $aParams['update_space'] = false;

        $aFile = Phpfox::getService('ynblog.blog')->upload($aParams['param_name'], $aParams);

        if (!$aFile) {
            echo json_encode([
                'type' => $sType,
                'error' => _p('upload_fail_please_try_again_later')
            ]);
            exit;
        }

        if (!empty($aFile['error'])) {
            echo json_encode([
                'type' => $sType,
                'error' => $aFile['error']
            ]);
            exit;
        }

        $iId = phpFox::getService('core.temp-file')->add([
            'type' => $sType,
            'size' => $aFile['size'],
            'path' => $aFile['name'],
            'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
        ]);

        echo json_encode([
            'file' => $iId,
            'type' => $sType,
            'field_name' => $aParams['field_name']
        ]);
        exit;
    }
}
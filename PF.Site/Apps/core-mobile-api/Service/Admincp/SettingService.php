<?php

namespace Apps\Core_MobileApi\Service\Admincp;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Error;
use Phpfox_File;
use Phpfox_Service;

class SettingService extends Phpfox_Service
{

    public function __construct()
    {

    }

    public function getAppLogo()
    {
        if ($aLogo = storage()->get('mobile-api/logo')) {
            $bIsDefault = false;
            $sLogo = Phpfox::getLib('image.helper')->display([
                'file'       => 'mobile/' . $aLogo->value->path,
                'server_id'  => $aLogo->value->server_id,
                'path'       => 'core.url_pic',
                'return_url' => true
            ]);
        } else {
            //Get site logo
            $bIsDefault = true;
            $sLogo = flavor()->active->logo_url();
        }
        return [$sLogo, $bIsDefault];
    }

    public function updateLogo()
    {
        $oFile = Phpfox_File::instance();
        $sPicStorage = Phpfox::getParam('core.dir_pic') . 'mobile/';
        if (isset($_FILES['logo']['name']) && ($_FILES['logo']['name'] != '')) {
            $aIcon = $oFile->load('logo', ['jpg', 'png']);
            if (!Phpfox_Error::isPassed()) {
                return false;
            }
            if ($aIcon !== false) {
                $sLogoName = $oFile->upload('logo', $sPicStorage, 'logo');
                $aUpdate = [
                    'path'      => $sLogoName,
                    'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
                ];
                //Remove Old Logo
                $this->deleteLogo();
                storage()->set('mobile-api/logo', $aUpdate);
            }
        }
        return true;
    }

    public function deleteLogo()
    {
        $aLogo = storage()->get('mobile-api/logo');
        if (!empty($aLogo)) {
            $sLogo = Phpfox::getParam('core.dir_pic') . 'mobile/' . sprintf($aLogo->value->path, '');
            Phpfox_File::instance()->unlink($sLogo);
        } else {
            return false;
        }
        storage()->del('mobile-api/logo');
        return true;
    }
}
<?php

namespace Apps\P_AdvEvent\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Cache;

class BirthdayPhotoController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $oImage = Phpfox::getLib('image');
        $imageDir = Phpfox::getParam('event.dir_image');
        $imageName = md5(PHPFOX_TIME . 'fevent') . '%s.jpg';
        $size = 1024;

        if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
            $aImage = Phpfox::getLib('file')->load('file', array('jpg', 'gif', 'png'), $size);
            if ($aImage) {
                $sFileName = Phpfox::getLib('file')->upload('file', $imageDir, $imageName);

                $oImage->createThumbnail(Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                        ''), Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                        '_' . $size), $size, $size);

                Phpfox::getService('fevent')->updateSetting('fevent_birthday_photo_image_path', $sFileName);
                Phpfox::getService('fevent')->updateSetting('fevent_birthday_photo_server_id', Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'));
                Phpfox_Cache::instance()->remove('fevent_birthday_background_image');
            }
        }

        $imagePathSetting = Phpfox::getService('fevent')->getSetting('fevent_birthday_photo_image_path', '');
        $serverIdSetting = Phpfox::getService('fevent')->getSetting('fevent_birthday_photo_server_id', 0);

        if (!empty($imagePathSetting['default_value'])) {
            $imagePath = $imagePathSetting['default_value'];
            $serverId = $serverIdSetting['default_value'];
            $currentImageUrl = Phpfox::getLib('image.helper')->display(
                array(
                    'server_id' => $serverId,
                    'path' => 'event.url_image',
                    'file' => $imagePath,
                    'suffix' => '',
                    'return_url' => true
                ));
        } else {
            $currentImageUrl = Phpfox::getParam('core.path_actual')
                . 'PF.Site' . PHPFOX_DS
                . 'Apps' . PHPFOX_DS
                . 'p-advevent' . PHPFOX_DS
                . 'assets' . PHPFOX_DS
                . 'image' . PHPFOX_DS
                . 'bg-default-birthday.png';
        }

        $this->template()->setTitle(_p('admin_menu_birthday_block_photo'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_fevent"), $this->url()->makeUrl('admincp.app', ['id' => 'P_AdvEvent']))
            ->setBreadcrumb(_p('admin_menu_birthday_block_photo'), $this->url()->makeUrl('admincp.fevent.birthdayphoto'))
            ->setPhrase(array(
                'fevent.view',
                'fevent.hide',
            ))
            ->assign(array(
                'currentImageUrl' => $currentImageUrl
            ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}
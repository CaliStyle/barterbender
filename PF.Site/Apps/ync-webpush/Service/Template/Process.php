<?php
namespace Apps\YNC_WebPush\Service\Template;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Error;
use Phpfox_File;
use Phpfox_Image;
use Phpfox_Service;

/**
 * Class Process
 * @package Apps\YNC_WebPush\Service\Template
 */
class Process extends Phpfox_Service
{
    static $_aIconSize = ['50', '100'];
    static $_aPhotoSize = ['100', '200', '400'];

    /**
     * Template constructor.
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncwebpush_template');
    }

    /**
     * @param $aVals
     * @param bool $bUpdate
     * @return bool|int|mixed
     */
    public function add($aVals, $bUpdate = false, $aValExtra = [])
    {
        $oFile = Phpfox_File::instance();
        $oFilter = Phpfox::getLib('parse.input');
        $oImage = Phpfox_Image::instance();
        $sPicStorage = Phpfox::getParam('core.dir_pic') . 'yncwebpush/';
        if (!is_array($aVals)) {
            return Phpfox_Error::set($aVals);
        }
        $aItem = [];
        if ($bUpdate) {
            if (empty($aVals['template_id'])) {
                return Phpfox_Error::set(_p('failed_can_not_find_editing_template'));
            } else {
                $aItem = Phpfox::getService('yncwebpush.template')->getForEdit($aVals['template_id']);
                if (!$aItem) {
                    return Phpfox_Error::set(_p('failed_can_not_find_editing_template'));
                }
            }
        }

        if (!empty($aVals['redirect_url'])) {
            $sReg = '/(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\-\.@:%_\+~#=]+)+((\.[a-zA-Z])*)(\/([0-9A-Za-z-\-\.@:%_\+~#=\?])*)*/';
            if (!preg_match($sReg, $aVals['redirect_url'])) {
                return Phpfox_Error::set(_p('please_add_a_valid_redirect_url'));
            }
        }
        if (!empty($aValExtra['icon_path'])) {
            $sIconName = str_replace('yncwebpush/', '', $aValExtra['icon_path']);
        } elseif (isset($_FILES['icon']['name']) && ($_FILES['icon']['name'] != '')) {
            $aIcon = $oFile->load('icon', array('jpg', 'gif', 'png'));
            if (!Phpfox_Error::isPassed()) {
                return false;
            }
            if ($aIcon !== false) {
                $sIconName = $oFile->upload('icon', $sPicStorage, 'icon');
                foreach (self::$_aIconSize as $size) {
                    $oImage->createThumbnail($sPicStorage . sprintf($sIconName, ''),
                        $sPicStorage . sprintf($sIconName, '_' . $size), $size, $size);
                }
            }
        }
        if (!empty($aValExtra['photo_path'])) {
            $sPhotoName = str_replace('yncwebpush/', '', $aValExtra['photo_path']);
        } elseif (isset($_FILES['photo']['name']) && ($_FILES['photo']['name'] != '')) {
            $aPhoto = $oFile->load('photo', array('jpg', 'gif', 'png'));
            if (!Phpfox_Error::isPassed()) {
                return false;
            }
            if ($aPhoto !== false) {
                $sPhotoName = $oFile->upload('icon', $sPicStorage, 'icon');
                foreach (self::$_aPhotoSize as $size) {
                    $oImage->createThumbnail($sPicStorage . sprintf($sPhotoName, ''),
                        $sPicStorage . sprintf($sPhotoName, '_' . $size), $size, $size);
                }
            }
        }
        $aInsert = [
            'template_name' => $oFilter->clean($aVals['template_name']),
            'title' => $oFilter->clean($aVals['title']),
            'message' => !empty($aVals['message']) ? $oFilter->clean($aVals['message']) : '',
            'redirect_url' => !empty($aVals['redirect_url']) ? $aVals['redirect_url'] : '',
        ];
        if (!$bUpdate) {
            $aExtra = [
                'icon_path' => !empty($sIconName) ? 'yncwebpush/' . $sIconName : '',
                'photo_path' => !empty($sPhotoName) ? 'yncwebpush/' . $sPhotoName : '',
                'icon_server_id' => !empty($sIconName) ? Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID') : 0,
                'photo_server_id' => !empty($sPhotoName) ? Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID') : 0,
                'time_stamp' => PHPFOX_TIME,
                'used' => !empty($aVals['used']) ? $aVals['used'] : 0
            ];
            $iId = db()->insert($this->_sTable, array_merge($aInsert, $aExtra));
        } else {
            $iId = $aVals['template_id'];
            if (!empty($sIconName)) {
                if (!$aItem['used']) {
                    $this->deleteImage($aItem, 'icon');
                }
                $aInsert['icon_path'] = 'yncwebpush/' . $sIconName;
                $aInsert['icon_server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
            }
            if (!empty($sPhotoName)) {
                if (!$aItem['used']) {
                    $this->deleteImage($aItem, 'photo');
                }
                $aInsert['photo_path'] = 'yncwebpush/' . $sPhotoName;
                $aInsert['photo_server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
            }
            db()->update($this->_sTable, $aInsert, 'template_id =' . (int)$iId);
        }
        $this->cache()->removeGroup('yncwebpush_template');
        return $iId;
    }

    /**
     * @param $aItem
     * @param $sType
     * @return bool
     */
    public function deleteImage($aItem, $sType)
    {
        if (!in_array($sType, ['icon', 'photo'])) {
            return false;
        }
        if ($sType == 'icon') {
            $aSizes = self::$_aIconSize;
            $sImagePath = $aItem['icon_path'];
            $iServerId = $aItem['icon_server_id'];
        } else {
            $aSizes = self::$_aPhotoSize;
            $sImagePath = $aItem['photo_path'];
            $iServerId = $aItem['photo_server_id'];
        }
        $oFile = Phpfox_File::instance();
        if ($sImagePath) {
            if (file_exists(Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, ''))) {
                $oFile->unlink(Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, ''));
            }
            foreach (array_merge([''], $aSizes) as $size) {
                $sPrefix = (empty($size) ? '' : '_') . $size;
                $sPath = Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, $sPrefix);
                if (file_exists($sPath)) {
                    $oFile->unlink($sPath);
                }
                $sUrl = Phpfox::getLib('cdn')->getUrl($sPath);
                if ($iServerId > 0 && Phpfox::getParam('core.allow_cdn')) {
                    Phpfox::getLib('cdn')->remove($sUrl);
                }
            }
        }
        return true;
    }

    public function deleteTemplate($iId)
    {
        if (!$iId) {
            return false;
        }
        $aTemplate = Phpfox::getService('yncwebpush.template')->getForEdit($iId);
        if (!$aTemplate) {
            return Phpfox_Error::set(_p('failed_can_not_find_deleting_template'));
        }
        //Remove icon/photo
        if (!$aTemplate['used']) {
            if (!empty($aTemplate['icon_path'])) {
                $this->deleteImage($aTemplate, 'icon');
            }
            if (!empty($aTemplate['photo_path'])) {
                $this->deleteImage($aTemplate, 'photo');
            }
        }
        db()->delete($this->_sTable, 'template_id =' . (int)$iId);
        $this->cache()->removeGroup('yncwebpush_template');
        return true;
    }

}
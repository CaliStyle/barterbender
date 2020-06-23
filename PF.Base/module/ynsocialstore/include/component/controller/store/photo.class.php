<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/7/16
 * Time: 6:29 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Store_Photo extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);

        if (isset($_SERVER['HTTP_X_FILE_NAME'])) {
            define('PHPFOX_HTML5_PHOTO_UPLOAD', true);
        }
        $sField = 'logo_path';
        $iStoreId = $this->request()->getInt('store_id');
        $bIsCover = $this->request()->get('is_cover_photo');
        if (isset($bIsCover) && $bIsCover) {
            $sField = 'cover_path';
        }

        $aImage = Phpfox_File::instance()->load('image', array('jpg', 'gif', 'png'));

        if (isset($aImage['name']) && !empty($aImage['name'])) {

            if (($aImage = Phpfox::getService('ynsocialstore.process')->upload($iStoreId, $sField, 'store','image',($bIsCover ? true : false))) !== false) {
                if (isset($_SERVER['HTTP_X_FILE_NAME'])) {
                    return [
                        'redirect' => $this->url()->makeUrl('ynsocialstore.store.'.$iStoreId)
                    ];
                }

                $this->url()->send('ynsocialstore.store.'.$iStoreId);
            }
        }
    }
}
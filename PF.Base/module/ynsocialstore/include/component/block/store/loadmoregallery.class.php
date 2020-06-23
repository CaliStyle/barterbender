<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/19/16
 * Time: 6:17 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_LoadMoreGallery extends Phpfox_Component
{
    public function process()
    {
        $sType = $this->request()->get('type');
        $page = (int)$this->request()->get('page') + 1;
        $iPageSizePhoto = 6;
        $iPageSizeAlbum = 8;

        if ($sType == 'albums') {
            $iStoreId = $this->request()->getInt('store_id', 0);
            if ($iStoreId) {
                list($iCount,$aItems) = Phpfox::getService('photo.album')->get('pa.group_id = '.$iStoreId.' AND pa.user_id = '.Phpfox::getUserId().' AND pa.module_id = \'ynsocialstore\'', null, $page, $iPageSizeAlbum);
                if (!isset($iCount)) $iCount = count($aItems);
            }
        } else {
            $iStoreId = $this->request()->getInt('store_id', 0);
            $iAlbumId = $this->request()->getInt('album_id', 0);
            $sAlbumName = $this->request()->get('albumName', _p('united_album'));

            if (!$iAlbumId) {
                $sAlbumName = _p('all_photos');
                list($iCount,$aItems) = Phpfox::getService('photo')->get('p.group_id = '.$this->request()->get('store_id', 0).' AND p.user_id = '.Phpfox::getUserId().' AND p.module_id = \'ynsocialstore\'', null, $page, $iPageSizePhoto);
            } else {
                if ($iStoreId && $iAlbumId) {
                    list($iCount,$aItems) = Phpfox::getService('photo')->get('p.group_id = '.$this->request()->get('store_id', 0).' AND p.album_id = '.$this->request()->get('album_id',0).' AND p.user_id = '.Phpfox::getUserId().' AND p.module_id = \'ynsocialstore\'', null, $page, $iPageSizePhoto);
                }
            }
        }
        $iCount = count($aItems);

        $this->template()->assign(array(
            'type' => $sType,
            'page' => $page,
            'iAlbumId' => isset($iAlbumId) ? $iAlbumId : 0,
            'sAlbumName' => isset($sAlbumName) ? $sAlbumName : _p('united_album'),
            'sStoreId' => $this->request()->get('store_id'),
            'aItems' => isset($aItems) ? $aItems : array(),
            'iCount' => isset($iCount) ? $iCount : 0,
        ));
    }
}
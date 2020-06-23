<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/11/16
 * Time: 9:57 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_DetailPhotos extends Phpfox_Component
{
    public function process()
    {
        $bSpecialMenu = !PHPFOX_IS_AJAX;
        $this->template()->assign(array(
                'bSpecialMenu' => $bSpecialMenu,
            )
        );

        //TODO check permission
        $aStore = $this->getParam('aStore');

        if (empty($aStore)) {
            return false;
        }

        $req6 = $this->request()->get('req6');

        if($req6 == 'albums'){
            $bShowPhotos = false;
        } else {
            $bShowPhotos = true;
        }

        $sItemUrl = $this->url()->makeUrl('current');

        if ($bShowPhotos) {
            $aSort = array(
                'latest' => array('photo.photo_id', _p('photo.latest')),
                'most-viewed' => array('photo.total_view', _p('photo.most_viewed')),
                'most-talked' => array('photo.total_comment', _p('photo.most_discussed'))
            );
            $aBrowseParams = array(
                'module_id' => Phpfox::isModule('advancedphoto') ? 'advancedphoto' : 'photo',
                'alias' => 'photo',
                'field' => 'photo_id',
                'table' => Phpfox::getT('photo'),
                'hide_view' => array('pending', 'my')
            );

            $aSearchParam = array(
                'type' => 'photo',
                'field' => 'photo_id',
                'search_tool' => array(
                    'table_alias' => 'photo',
                    'search' => array(
                        'action' => $sItemUrl,
                        'default_value' => _p('photo.search_photos'),
                        'name' => 'search',
                        'field' => 'photo.title'
                    ),
                    'sort' => $aSort,
                    'show' => array(20, 40, 60)
                )
            );

        } else {
            $aSort = array(
                'latest' => array('pa.time_stamp', _p('photo.latest')),
                'most-talked' => array('pa.total_comment', _p('photo.most_discussed'))
            );
            $aBrowseParams = array(
                'module_id' => Phpfox::isModule('advancedphoto') ? 'advancedphoto.album' : 'photo.album',
                'alias' => 'pa',
                'field' => 'album_id',
                'table' => Phpfox::getT('photo_album'),
                'hide_view' => array('pending', 'myalbums')
            );

            $aSearchParam = array(
                'type' => 'photo.album',
                'field' => 'pa.album_id',
                'search_tool' => array(
                    'table_alias' => 'pa',
                    'search' => array(
                        'action' => $sItemUrl,
                        'default_value' => _p('photo.search_photo_albums'),
                        'name' => 'search',
                        'field' => 'pa.name'
                    ),
                    'sort' => $aSort,
                    'show' => array(9, 12, 15)
                )
            );
        }
        $bCanDeletePhoto = ($aStore['user_id'] == Phpfox::getUserId()) || Phpfox::isAdmin();
        $aModerationMenu = [];
        if($bCanDeletePhoto){
            $aModerationMenu[] = [
                'phrase' => _p('core.delete'),
                'action' => $bShowPhotos ? 'deletePhoto' : 'deleteAlbum'
            ];
        }

        $this->setParam('global_moderation', [
            'name' => 'ynsocialstore',
            'ajax' => 'ynsocialstore.moderation',
            'menu' => $aModerationMenu
        ]);

        $sModuleId = Phpfox::getService('ynsocialstore.helper')->getModuleIdPhoto();
        $sController = $sModuleId . '.add';

        $this->search()->set($aSearchParam);

        if ($bShowPhotos) {
            $this->search()->setCondition("AND photo.module_id = 'ynsocialstore' AND photo.group_id = ".$aStore['store_id']);
        } else {
            $this->search()->setCondition("AND pa.module_id = 'ynsocialstore' AND pa.group_id = ".$aStore['store_id']);
        }
        $this->search()->browse()->params($aBrowseParams)->execute();
        $aItems = $this->search()->browse()->getRows();

        PhpFox::getLib('pager')->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->getCount()
        ));

        $sLinkPhotos = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aStore['store_id'], $aStore['name']) . "photos/";
        $sLinkAlbums = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aStore['store_id'], $aStore['name']) . "photos/albums/";

        //TODO Check permission for adding photo

        $this->template()
           ->assign(array(
                'bCanAddPhotoInStore' => ($aStore['user_id'] == Phpfox::getUserId()),
                'iStoreId' => $aStore['store_id'],
                'sUrlAddPhoto' => Phpfox::getLib('url')->makeUrl($sController, array()) . 'module_ynsocialstore/item_'.$aStore['store_id'].'/',
                'aItems' => !empty($aItems) ? $aItems : array(),
                'sLinkPhotos' => $sLinkPhotos,
                'sLinkAlbums' => $sLinkAlbums,
                'bShowPhotos' => $bShowPhotos,
                'sHeader' => '',
                'iPage' => $this->search()->getPage(),
                'iUserId' => $aStore['user_id'],
                'bCanDeletePhoto' => $bCanDeletePhoto
            )
        );

        return 'block';
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_block_store_detailphotos')) ? eval($sPlugin) : false);
    }
}
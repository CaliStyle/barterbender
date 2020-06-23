<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/10/16
 * Time: 10:39 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_Detailcover extends Phpfox_Component
{
    public function process()
    {
        if (null === ($iStoreId = $this->request()->getInt('req3'))) {
            return false;
        }

        $aStore = $this->getParam('aStore');

        if (empty($aStore) || $aStore['theme_id'] == 2) {
            return false;
        }

        $aStore['time_stamp'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'),$aStore['time_stamp']);
        $aStore['is_favorite'] = Phpfox::getService('ynsocialstore.favourite')->isFavorite(Phpfox::getUserId(),$aStore['store_id']);
        $aStore['is_following'] = Phpfox::getService('ynsocialstore.following')->isFollowing(Phpfox::getUserId(),$aStore['store_id']);
        $aStore['bookmark_url'] = Phpfox::permalink('ynsocialstore.store', $aStore['store_id'], $aStore['name']);
        // Get Image
        if (!empty($aStore['logo_path'])) {
            $aStore['image'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aStore['server_id'],
                'path' => 'core.url_pic',
                'file' => 'ynsocialstore' . PHPFOX_DS .$aStore['logo_path'],
                'suffix' => '_480',
                'return_url' => true
            ));
            $size_img = @getimagesize($aStore['image']);
            if (!empty($size_img)) {
                $og_image_width = $size_img[0];
                $og_image_height = $size_img[1];
            } else {
                $og_image_width = 640;
                $og_image_height = 442;
            }
            $this->template()
                ->setMeta('og:image', $aStore['image'])
                ->setMeta('og:image:width', $og_image_width)
                ->setMeta('og:image:height', $og_image_height);
        }

        $this->template()->assign(array(
            'aItem' => $aStore,
            'sUrl'=> Phpfox::permalink('ynsocialstore.store.embed', $iStoreId, ''),
            'sHeader' => '',
        ));

        return 'block';
    }
}
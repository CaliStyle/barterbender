<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/10/16
 * Time: 10:39 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_Detailcover_2 extends Phpfox_Component
{
    public function process()
    {
        if (null === ($iStoreId = $this->request()->getInt('req3'))) {
            return false;
        }

        $aStore = $this->getParam('aStore');

        if (empty($aStore) || $aStore['theme_id'] == 1) {
            return false;
        }
        $sStoreName = Phpfox::getLib('parse.input')->cleanTitle($aStore['name']);
        $sDetailPage = $this->request()->get('req5');
        $prefixDetail = "ynsocialstore.store.$iStoreId.$sStoreName";
        $aProfileMenu = array(
            array(
                'sMenu' => 'home',
                'sPhrase' => "<i class='ico ico-home'></i>"._p('home'),
                'sLink' => "$prefixDetail",
                'sClass' => '',
            ),
            array(
                'sMenu' => 'products',
                'sPhrase' => "<i class='ico ico-cubes-o'></i>"._p('products').(($aStore['total_products']) ? "<span id='total_products_store_$iStoreId'>".$aStore['total_products']."</span>": ""),
                'sLink' => "$prefixDetail.products",
                'sClass' => '',
            ),
            array(
                'sMenu' => 'activities',
                'sPhrase' => "<i class='ico ico-flag-rectangle'></i>"._p('activities'),
                'sLink' => "$prefixDetail.activities",
                'sClass' => '',
            ),
            array(
                'sMenu' => 'photos',
                'sPhrase' => "<i class='ico ico-file-photo'></i>"._p('photos'),
                'sLink' => "$prefixDetail.photos",
                'sClass' => 'flag-mb',
            ),
            array(
                'sMenu' => 'reviews',
                'sPhrase' => "<i class='ico ico-check-circle'></i>"._p('reviews').(($aStore['total_review']) ? "<span id='total_review_store_$iStoreId'>".$aStore['total_review']."</span>" : ""),
                'sLink' => "$prefixDetail.reviews",
                'sClass' => 'flag-mb',
            ),
            array(
                'sMenu' => 'aboutus',
                'sPhrase' => "<i class='ico ico-businessman'></i>"._p('about_us'),
                'sLink' => "$prefixDetail.aboutus",
                'sClass' => 'flag-mb',
            ),
            array(
                'sMenu' => 'shipandpayment',
                'sPhrase' => "<i class='ico ico-truck'></i>"._p('shipping_and_payment'),
                'sLink' => "$prefixDetail.shipandpayment",
                'sClass' => 'flag-tl',
            ),
            array(
                'sMenu' => 'policy',
                'sPhrase' => "<i class='ico ico-newspaper-alt-o'></i>"._p('return_policy'),
                'sLink' => "$prefixDetail.policy",
                'sClass' => 'flag-tl',
            ),
            array(
                'sMenu' => 'buyerprotection',
                'sPhrase' => "<i class='ico ico-shield'></i>"._p('buyer_protection'),
                'sLink' => "$prefixDetail.buyerprotection",
                'sClass' => 'flag-fs',
            ),
            array(
                'sMenu' => 'faqs',
                'sPhrase' => "<i class='ico ico-question-circle'></i>"._p('faqs'),
                'sLink' => "$prefixDetail.faqs",
                'sClass' => 'flag-fs',
            )
        );

        $sUrlAboutUs = $this->url()->makeUrl('ynsocialstore.store', [$aStore['store_id'], Phpfox::getLib('parse.input')->cleanTitle($aStore['name']), 'aboutus'], true);
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
            'aProfileMenu' => $aProfileMenu,
            'sDetailPage' => !empty($sDetailPage) ? $sDetailPage : 'home',
            'sUrlAboutUs' => $sUrlAboutUs,
        ));

        return 'block';
    }

    public function getStoreDetails($aItem)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id']))
        {
            return false;
        }

        Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('pages.pages'),
            'breadcrumb_home' => \Phpfox_Url::instance()->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'ynsocialstore/',
            'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }
}
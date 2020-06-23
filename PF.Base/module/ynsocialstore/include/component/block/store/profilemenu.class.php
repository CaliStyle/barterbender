<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/7/16
 * Time: 3:54 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_Profilemenu extends Phpfox_Component
{
    public function process()
    {
        $iStoreId = $this->request()->getInt('req3');
        $aStore = $this->getParam('aStore');

        if (empty($aStore) || $aStore['theme_id'] == 2) {
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
                'sClass' => '',
            ),
            array(
                'sMenu' => 'reviews',
                'sPhrase' => "<i class='ico ico-check-circle'></i>"._p('reviews').(($aStore['total_review']) ? "<span id='total_review_store_$iStoreId'>".$aStore['total_review']."</span>" : ""),
                'sLink' => "$prefixDetail.reviews",
                'sClass' => '',
            ),
            array(
                'sMenu' => 'aboutus',
                'sPhrase' => "<i class='ico ico-businessman'></i>"._p('about_us'),
                'sLink' => "$prefixDetail.aboutus",
                'sClass' => '',
            ),
            array(
                'sMenu' => 'shipandpayment',
                'sPhrase' => "<i class='ico ico-truck'></i>"._p('shipping_and_payment'),
                'sLink' => "$prefixDetail.shipandpayment",
                'sClass' => '',
            ),
            array(
                'sMenu' => 'policy',
                'sPhrase' => "<i class='ico ico-newspaper-alt-o'></i>"._p('return_policy'),
                'sLink' => "$prefixDetail.policy",
                'sClass' => '',
            ),
            array(
                'sMenu' => 'buyerprotection',
                'sPhrase' => "<i class='ico ico-shield'></i>"._p('buyer_protection'),
                'sLink' => "$prefixDetail.buyerprotection",
                'sClass' => '',
            ),
            array(
                'sMenu' => 'faqs',
                'sPhrase' => "<i class='ico ico-question-circle'></i>"._p('faqs'),
                'sLink' => "$prefixDetail.faqs",
                'sClass' => '',
            )
        );

        $this->template()->assign(array(
            'aProfileMenu' => $aProfileMenu,
            'sDetailPage' => !empty($sDetailPage) ? $sDetailPage : 'home',
            'sHeader' => _p('profile_menu'),
        ));

        return 'block';
    }
}
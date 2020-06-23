<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 5:49 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Product_Detail extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        $this->template()
        ->setHeader('cache', array(
           'owl.carousel.min.js' => 'module_ynsocialstore',
           'owl.carousel.css' => 'module_ynsocialstore',
           'easyzoom.js' => 'module_ynsocialstore',
               )
        );

        $sError = '';

        if (!$this->request()->getInt('req3'))
        {
            return Phpfox_Module::instance()->setController('error.404');
        }

        $iProductId = $this->request()->getInt('req3');
        if($aVal = $this->request()->getArray('val'))
        {
            if($iId = Phpfox::getService('ynsocialstore.product.process')->addProductSubscriber($aVal,$iProductId))
            {
                $this->url()->send('current',_p('Your email submitted successfully'));
            }
        }
        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductForDetailById($iProductId);
        if (empty($aProduct)) {
           $sError = _p('unable_to_find_the_product_you_are_looking_for');
        } else {
            if (!Phpfox::getUserParam('ynsocialstore.can_view_product') || (!Phpfox::isAdmin() && Phpfox::getUserId() != $aProduct['user_id'] && (in_array($aProduct['product_status'], array('denied', 'pending')) || $aProduct['store_status'] == 'expired' ))) {
                $sError = _p('you_do_not_have_permission_to_view_this_product');
            }
        }
        if (empty($sError) && Phpfox::isModule('privacy'))
        {
            Phpfox::getService('privacy')->check('ynsocialstore_product', $aProduct['product_id'], $aProduct['user_id'], $aProduct['privacy'], $aProduct['is_friend']);
            Phpfox::getService('privacy')->check('ynsocialstore_store', $aProduct['item_id'], $aProduct['user_id'], $aProduct['store_privacy'], $aProduct['is_friend']);
        }
        if (empty($sError) && $aProduct['user_id'] != Phpfox::getUserId())
        {
            Phpfox::getService('ynsocialstore.product.process')->updateTotalView($aProduct['product_id']);
        }

        $aDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sDefaultSymbol = Phpfox::getService('core.currency')->getSymbol($aDefaultCurrency);

        $aProduct['is_wishlist'] = Phpfox::getService('ynsocialstore.product.wishlist')->isWishlist(Phpfox::getUserId(),$aProduct['product_id']);
        if(!empty($sError)){
            $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
                    ->assign(['sError' => $sError]);
            return false;
        }

        // Get Image for Facebook
        if (!empty($aProduct['logo_path'])) {
            $aProduct['image'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aProduct['server_id'],
                'path' => 'core.url_pic',
                'file' => $aProduct['logo_path'],
                'suffix' => '_400',
                'return_url' => true
            ));
            $size_img = @getimagesize($aProduct['image']);
            if (!empty($size_img)) {
                $og_image_width = $size_img[0];
                $og_image_height = $size_img[1];
            } else {
                $og_image_width = 640;
                $og_image_height = 442;
            }
            $this->template()
                ->setMeta('og:image', $aProduct['image'])
                ->setMeta('og:image:width', $og_image_width)
                ->setMeta('og:image:height', $og_image_height);
        }

        $this->setParam(array(
                    'aProduct' => $aProduct,
                    'iProductId' => $aProduct['product_id'],
                    'sDefaultSymbol' => $sDefaultSymbol,
                    'aDefaultCurrency' => $aDefaultCurrency,
                        ));
        $this->setParam('aFeed', array(
               'comment_type_id' => 'ynsocialstore_product',
               'privacy' => $aProduct['privacy'],
               'comment_privacy' => $aProduct['privacy'],
               'like_type_id' => 'ynsocialstore_product',
               'feed_is_liked' => isset($aProduct['is_liked']) ? $aProduct['is_liked'] : false,
               'feed_is_friend' => $aProduct['is_friend'],
               'item_id' => $aProduct['product_id'],
               'user_id' => $aProduct['user_id'],
               'total_comment' => $aProduct['total_comment'],
               'feed_type'=>'ynsocialstore_product',
               'total_like' => $aProduct['total_like'],
               'feed_link' => $aProduct['bookmark_url'],
               'feed_title' => $aProduct['name'],
               'feed_display' => 'view',
               'feed_total_like' => $aProduct['total_like'],
               'report_module' => 'ynsocialstore_product',
               'report_phrase' => _p('Report this product'),
               'time_stamp' => $aProduct['product_creation_datetime'],
           )
        );

        Phpfox::getService('ynsocialstore.helper')->buildMenu();

        $this->template()->setTitle($aProduct['name'])
            ->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadCrumb($aProduct['store_name'], $this->url()->permalink('ynsocialstore.store', $aProduct['store_id'], $aProduct['store_name']));

        $sListCategories = Phpfox::getService('ecommerce.category')->getCategoryIds($aProduct['product_id'], 'ynsocialstore_product');

        $aCategories = Phpfox::getService('ynsocialstore.product')->getCategoriesByList($sListCategories);

        foreach ($aCategories as $aCategory)
        {
            $this->template()->setBreadCrumb($aCategory['title'], $this->url()->permalink('ynsocialstore.store', $aProduct['store_id'], $aProduct['store_name']).'products/category_'.$aCategory['category_id'], true);
        }

        $bIsReviewTab = $this->request()->get('tab','') == 'reviews' ? true : false;

        $this->template()->setPhrase(array(
            'ynsocialstore.are_you_sure',
            'ynsocialstore.yes',
            'ynsocialstore.no',
            'ynsocialstore.confirm_feature_product_unlimited',
            'ynsocialstore.are_you_sure_want_to_delete_this_product_this_action_cannot_be_reverted',
            'ynsocialstore.are_you_sure_want_to_delete_these_products_this_action_cannot_be_reverted'
        ));
        $this->setParam('sError',$sError);
        $this->template()->assign(array(
            'aItem' => $aProduct,
            'sError' => $sError,
            'iPage' => $this->search()->getPage(),
            'bIsReviewTab' => $bIsReviewTab,
            'bIsDetail' => true,
            'sUrl'=> Phpfox::permalink('ynsocialstore.product.embed', $iProductId, '')
        ));
        /*========== SET COOKIE FOR RECENTLY VIEWED BLOCK ========= */
        Phpfox::getService('ynsocialstore.product.process')->setCookieRecentViewProduct($iProductId);


    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_product_detail_clean')) ? eval($sPlugin) : false);
    }
}
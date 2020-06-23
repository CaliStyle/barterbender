<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/31/16
 * Time: 9:06 AM
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Controller_Manage_Photos extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'));

        if ($iProductId = $this->request()->getInt('id')) {
            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductForManageAttr($iProductId);
            $this->template()->buildPageMenu('js_ynsocialstore_products_block', [], [
                'link' => Phpfox::permalink('social-store.product', $aProduct['product_id'], null),
                'phrase' => _p('ynsocialstore_view_product_detail')
            ]);
        } else {
            $this->url()->send('ynsocialstore');
        }

        if (empty($aProduct)) {
            return Phpfox_Error::display(_p('unable_to_find_the_product_you_are_looking_for'));
        }

        // Check if user has permission edit their own products
        if (!Phpfox::getService('ynsocialstore.permission')->canEditProduct(false, $aProduct['user_id'])) {
            return Phpfox_Error::display(_p('you_do_not_have_permission_to_edit_this_product'));
        }

        if ($aVals = $this->request()->getArray('val')) {
            if (isset($aVals['order_photo'])) {
                Phpfox::getService('ecommerce.process')->updateOrderCoverPhotos($aVals);
            }

            $this->url()->send("ynsocialstore.manage-photos", array('id' => $iProductId),
                _p('updated_cover_photos_successfully'));
        }

        //GetSetting
        $aSettings = Phpfox::getService('ynsocialstore.package')->getPackageByStoreId($aProduct['store_id']);

        if (!empty($aSettings['max_photo_per_product'])) {
            $settingCoverMax = $aSettings['max_photo_per_product'];
        } else {
            $settingCoverMax = 8;
        }

        $settingCoverMaxSize = Phpfox::getUserParam('ecommerce.max_size_for_icons');

        $aImages = Phpfox::getService('ynsocialstore.product')->getImages($aProduct['product_id']);

        //Set first image for product when it has no logo before
        if (empty($aProduct['logo_path']) && count($aImages) && !empty($aImages[0]['image_id'])) {
            Phpfox::getService('ynsocialstore.product')->setMainProductPhoto($aProduct['product_id'],
                $aImages[0]['image_id']);
            $aProduct['logo_path'] = $aImages[0]['image_path'];
        }

        $iMaxFileSize = $settingCoverMaxSize;
        $iUploaded = count($aImages);
        $iMaxUpload = $settingCoverMax - $iUploaded;

        $this->template()
            ->setEditor()
            ->setPhrase(array())
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'share.js' => 'module_attachment',
                'country.js' => 'module_core',
                '<script type="text/javascript">$Behavior.ynsocialstoreProgressBarSettings = function(){ if ($Core.exists(\'#js_ynsocialstore_block_cover_photos_holder\')) { oProgressBar = {holder: \'#js_ynsocialstore_block_cover_photos_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: ' . $iMaxUpload . ', total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\'}; $Core.progressBarInit(); } }</script>',
                'jquery/ui.js' => 'static_script',
            ));

        $this->template()->assign(array(
            'aImages' => $aImages
        ));

        $this->setParam('iProductId', $aProduct['product_id']);

        $this->template()->assign(array(
            'iMaxUpload' => $iMaxUpload,
            'iMaxFileSize' => $iMaxFileSize,
            'iProductId' => $aProduct['product_id'],
            'sProductName' => $aProduct['name'],
            'iOwnerId' => $aProduct['user_id'],
            'sCorePath' => Phpfox::getParam('core.path'),
            'sMainImage' => $aProduct['logo_path'],
        ));

        $this->template()->
        assign([
            'aParamsUpload' => array(
                'id' => $aProduct['product_id'],
                'total_image' => count($aImages),
                'total_image_limit' => $iMaxUpload + count($aImages),
                'remain_upload' => $iMaxUpload,
            ),
            'iTotalImage' => count($aImages),
            'iTotalImageLimit' => $iMaxUpload + count($aImages),
            'iRemainUpload' => $iMaxUpload,
            'iMaxFileSize' => $iMaxFileSize,
        ]);

        $this->template()
            ->setBreadCrumb(_p('seller_section'), $this->url()->makeUrl('ynsocialstore.statistic'))
            ->setBreadCrumb(_p('my_stores'), $this->url()->makeUrl('ynsocialstore.manage-store'))
            ->setBreadcrumb($aProduct['store_name'],
                $this->url()->permalink('ynsocialstore.store', $aProduct['store_id'], $aProduct['store_name']))
            ->setBreadcrumb(_p('manage_photos'),
                $this->url()->permalink('ynsocialstore.manage-photos', 'id_' . $aProduct['product_id']), true);

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        return true;
    }
}
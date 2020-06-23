<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Cover_Photos extends Phpfox_Component
{
    private $_sModule = false;
    private $_iItem = false;
    private function _checkIsInPageAndPagePermission() {
        //will check later

        if ($this->_sModule !== false && $this->_iItem !== false) {

            switch ($this->_sModule) {
                case 'pages':
                    $this->_aCallback = Phpfox::callback('auction.getAuctionsDetails', array('item_id' => $this->_iItem));
                    break;

                default:
                    $this->_aCallback = Phpfox::callback($this->_sModule . '.getAuctionsDetails', array('item_id' => $this->_iItem));
                    break;
            }

            if ($this->_aCallback) {
                $this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'], $this->_aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home'])
                                ->setBreadCrumb(_p('auction'),$this->_aCallback['url_home'].'auction/');
                if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem, 'auction.share_auctions')) {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings '));
                }
            }
        }
    }

    public function process()
    {
        Phpfox::getService('auction.helper')->buildMenu();
        $iEditedAuctionId = 0;
        if ($this->request()->getInt('id')) {

            $iProductId = $this->request()->getInt('id');
            $aAuction = Phpfox::getService('auction')->getQuickAuctionByProductId($iProductId);
            $iEditedAuctionId = $aAuction['auction_id'];
            $this->setParam('iAuctionId', $aAuction['auction_id']);
        }

        if (!(int)$iEditedAuctionId) {
            $this->url()->send('auction');
        }
        if ($aAuction['module_id'] != 'auction') {
            $this->_sModule = $aAuction['module_id'];
            $this->_iItem = $aAuction['item_id'];
        }
        $this->_checkIsInPageAndPagePermission();
        if ($aVals = $this->request()->getArray('val')) {
            $aResult = array();
            if (isset($aVals['submit_photo'])) {
                $aResult = Phpfox::getService('ecommerce.process')->updateCoverPhotos($aVals, 'auction');
            }

            if (isset($aResult['error']) && $aResult['error'] && count($_FILES)) {
                $this->url()->send("auction.cover-photos", array('id' => $iProductId), $aResult['message']);
            } else {
                $this->url()->send("auction.cover-photos", array('id' => $iProductId), _p('updated_cover_photos_successfully'));

            }
        }

        //get global settings
        $aGlobalSetting = Phpfox::getService('auction')->getGlobalSetting();

        //get number cover
        if (isset($aGlobalSetting['actual_setting']['max_number_cover_photos'])) {
            $settingCoverMax = $aGlobalSetting['actual_setting']['max_number_cover_photos'];
        } else {
            $settingCoverMax = 8;
        }

        //get size cover
        if (isset($aGlobalSetting['actual_setting']['max_upload_size_cover_photos'])) {
            $settingCoverMaxSize = $aGlobalSetting['actual_setting']['max_upload_size_cover_photos'];
        } else {
            $settingCoverMaxSize = 500;
        }

        $aImages = Phpfox::getService('ecommerce')->getImages($aAuction['product_id']);
        $iMaxFileSize = $settingCoverMaxSize;
        $iUploaded = count($aImages);
        $iMaxUpload = $settingCoverMax - $iUploaded;
        if($this->_sModule == 'auction' || $this->_sModule == false)
        {
            $this->template()
                ->setBreadcrumb(_p('auction'),$this->url()->makeUrl('auction'));
        }
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
                '<script type="text/javascript">$Behavior.auctionProgressBarSettings = function(){ if ($Core.exists(\'#js_auction_block_cover_photos_holder\')) { oProgressBar = {holder: \'#js_auction_block_cover_photos_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: ' . $iMaxUpload . ', total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\'}; $Core.progressBarInit(); } }</script>',
                'jquery/ui.js' => 'static_script',
            ));

        $this->template()->assign(array(
            'aImages' => $aImages
        ));

        $this->template()->assign(array(
            'iMaxUpload' => $iMaxUpload,
            'iMaxFileSize' => $iMaxFileSize,
            'iAuctionId' => $iEditedAuctionId,
            'iProductId' => $aAuction['product_id'],
            'iOwnerId' => $aAuction['user_id'],
            'aAuction' => $aAuction,
            'sCorePath' => Phpfox::getParam('core.path'),
            'sMainImage' => $aAuction['logo_path'],
        ));

        $this->template()->
        assign([
            'aParamsUpload' => array(
                'id' => $aAuction['product_id'],
                'total_image' => count($aImages),
                'total_image_limit' => $iMaxUpload + count($aImages),
                'remain_upload' => $iMaxUpload,
                'file_size' => $iMaxFileSize
            ),
            'iTotalImage' => count($aImages),
            'iTotalImageLimit' => $iMaxUpload + count($aImages),
            'iRemainUpload' => $iMaxUpload,
            'iMaxFileSize' => $iMaxFileSize,
        ]);


        $this->template()->setBreadcrumb($aAuction['name'], $this->url()->permalink('auction.detail', $aAuction['product_id']));
        $this->template()->setBreadcrumb(_p('cover_photos'), $this->url()->permalink('auction.cover-photos', 'id_' . $aAuction['product_id']), true);

        Phpfox::getService('auction.helper')->loadauctionJsCss();


    }

    public function clean()
    {

    }

}

?>
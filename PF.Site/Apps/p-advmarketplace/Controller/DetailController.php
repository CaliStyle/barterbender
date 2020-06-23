<?php

namespace Apps\P_AdvMarketplace\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class DetailController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getUserParam('advancedmarketplace.can_access_advancedmarketplace', true);

        define('PHPFOX_ADVANCEDMARKETPLACE_DETAIL', true);

        if (!($iListingId = $this->request()->getInt('req3'))) {
            $this->url()->send('advancedmarketplace');
        }

        if (!($aListing = Phpfox::getService('advancedmarketplace')->getListing($iListingId, Phpfox::getUserId(), true))) {
            return Phpfox_Error::display(_p('advancedmarketplace.the_listing_you_are_looking_for_either_does_not_exist_or_has_been_removed'));
        }

        if ($aListing['post_status'] == 2) {
            if ($aListing['user_id'] == phpfox::getUserId() || phpfox::getUserParam('advancedmarketplace.can_view_draft_listings')) {

            } else {
                return Phpfox_Error::display(_p('advancedmarketplace.the_listing_you_are_looking_for_either_does_not_exist_or_has_been_removed'));
            }
        }

        $sExchangeRate = '';
        if ($aListing['currency_id'] != Phpfox::getService('core.currency')->getDefault()) {
            if (($sAmount = Phpfox::getService('core.currency')->getXrate($aListing['currency_id'],
                $aListing['price']))) {
                $sExchangeRate .= ' (' . Phpfox::getService('core.currency')->getCurrency($sAmount) . ')';
            }
        }

        $sListingPrice = ($aListing['price'] == '0.00' ? _p('advancedmarketplace.free') : Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id'])) . $sExchangeRate;
        $sTagType = 'advancedmarketplace';

        $this->setParam('aListing', $aListing);

        $aListing['images'] = Phpfox::getService('advancedmarketplace')->getImages($iListingId);

        if (Phpfox::isUser() && $aListing['invite_id'] && !$aListing['visited_id'] && $aListing['user_id'] != Phpfox::getUserId()) {
            Phpfox::getService('advancedmarketplace.process')->setVisit($aListing['listing_id'], Phpfox::getUserId());
        }

        $aListing['location_parsed'] = trim((!empty($aListing['location']) ? $aListing['location'] . ' - ' : '') . (!empty($aListing['address']) ? $aListing['address'] . ' - ' : '') . (!empty($aListing['city']) ? $aListing['city'] : ''),' - ');

        if (Phpfox::isUser() && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->delete('comment_advancedmarketplace',
                $this->request()->getInt('req3'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('advancedmarketplace_like',
                $this->request()->getInt('req3'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('advancedmarketplace_follow', $aListing['listing_id'],
                Phpfox::getUserId());
        }

        if (Phpfox::isModule('notification') && $aListing['user_id'] == Phpfox::getUserId()) {
            Phpfox::getService('notification.process')->delete('advancedmarketplace_approved', $aListing['listing_id'],
                Phpfox::getUserId());

        }

        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('advancedmarketplace', $aListing['listing_id'], $aListing['user_id'],
                $aListing['privacy'], $aListing['is_friend']);
        }

        $aFollower = phpfox::getLib('database')->select('*')->from(phpfox::getT('advancedmarketplace_follow'))->where('user_id = ' . $aListing['user_id'] . ' and  user_follow_id = ' . phpfox::getUserId())->execute('getSlaveRow');
        $bFollow = 1;
        if (!empty($aFollower)) {
            $bFollow = 0;
        }

        if ($aListing['view_id'] == '1') {
            $aPendingItem = [
                'message' => _p('advancedmarketplace.listing_is_pending_approval'),
                'actions' => []
            ];
            if (Phpfox::getUserParam('advancedmarketplace.can_approve_listings')) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('advancedmarketplace.approve'),
                    'action' => '$.ajaxCall(\'advancedmarketplace.approve\', \'inline=false&amp;listing_id=' . $aListing['listing_id'] . '\', \'POST\')'
                ];
            }
            if (Phpfox::getUserParam('advancedmarketplace.can_edit_other_listing') || Phpfox::getUserParam('advancedmarketplace.can_edit_own_listing')) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $this->url()->makeUrl('advancedmarketplace.add', ['id' => $aListing['listing_id']]),
                ];
            }
            if (Phpfox::getUserParam('advancedmarketplace.can_delete_own_listing') || Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings')) {
                $aPendingItem['actions']['delete'] = [
                    'is_confirm' => true,
                    'confirm_message' => _p('are_you_sure'),
                    'label' => _p('delete'),
                    'action' => $this->url()->makeUrl('advancedmarketplace', ['delete' => $aListing['listing_id']]),
                ];
            }

            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }

        $this->setParam('aRatingCallback', array(
            'type' => 'user',
            'default_rating' => $aListing['total_score'],
            'item_id' => $aListing['user_id'],
            'stars' => range(1, 10)
        ));

        $this->setParam('aFeed', array(
            'comment_type_id' => 'advancedmarketplace',
            'privacy' => $aListing['privacy'],
            'comment_privacy' => $aListing['privacy_comment'],
            'like_type_id' => 'advancedmarketplace',
            'feed_is_liked' => $aListing['is_liked'],
            'feed_is_friend' => $aListing['is_friend'],
            'item_id' => $aListing['listing_id'],
            'user_id' => $aListing['user_id'],
            'total_comment' => $aListing['total_comment'],
            'total_like' => $aListing['total_like'],
            'feed_link' => $this->url()->permalink('advancedmarketplace.detail', $aListing['listing_id'],
                $aListing['title']),
            'feed_title' => $aListing['title'],
            'feed_display' => 'view',
            'feed_total_like' => $aListing['total_like'],
            'report_module' => 'advancedmarketplace',
            'report_phrase' => _p('advancedmarketplace.report_this_listing_lowercase')
        ));

        $this->template()->setTitle($aListing['title'])
            ->setBreadcrumb($aListing['title'],
                $this->url()->permalink('advancedmarketplace.detail', $aListing['listing_id'], $aListing['title']),
                true)
            ->setMeta('description', $aListing['description'])
            ->setMeta('description', Phpfox::getParam('advancedmarketplace.advmarketplace_meta_description'))
            ->setMeta('keywords', $this->template()->getKeywords($aListing['title'] . $aListing['description']))
            ->setMeta('keywords', Phpfox::getParam('advancedmarketplace.advmarketplace_meta_keywords'))
            ->setMeta('og:image', Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aListing['server_id'],
                    'path' => 'advancedmarketplace.url_pic',
                    'file' => $aListing['image_path'],
                    'suffix' => '_400',
                    'return_url' => true
                )
            )
            )
            ->setHeader('cache', array(
                'jquery/plugin/star/jquery.rating.js' => 'static_script',
                'jquery.rating.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'pager.css' => 'style_css',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'jscript/detail.js' => 'app_p-advmarketplace',
                'feed.js' => 'module_feed',
                'jscript/jquery.easing.min.js' => 'app_p-advmarketplace',
                'masterslider.min.js' => 'module_core',
                'masterslider.css' => 'module_core'
            ))
            ->setEditor(array(
                'load' => 'simple'
            ))
            ->assign(array(
                'aListing' => $aListing,
                'sListingPrice' => $sListingPrice,
                'aImages' => Phpfox::getService('advancedmarketplace')->getImages($aListing['listing_id']),
                'iFollower' => phpfox::getUserId(),
                'bFollow' => $bFollow,
                'core_path' => Phpfox::getParam('core.path_file'),

            ));

        $bIsUserProfile = false;
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsUserProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            $bIsUserProfile = true;
            $aUser = $this->getParam('aUser');
        }
        if (isset($aListing['module_id']) && $aListing['module_id'] != 'advancedmarketplace') {
            if (in_array($aListing['module_id'], ['pages', 'groups'])
                && Phpfox::isModule($aListing['module_id'])
                && $aCallback = $this->getListingDetails($aListing)
            ) {
                $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home'])
                    ->setBreadCrumb(_p('marketplace'), $aCallback['url_home'] . 'advancedmarketplace/');
            }
        } else {
            $this->template()->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                    'advancedmarketplace.') : $this->url()->makeUrl('advancedmarketplace.')));
        }

        if (Phpfox::isModule('rate')) {
            $this->template()->setPhrase(array('rate.thanks_for_rating'))->setHeader(array(
                'rate.js' => 'module_rate',
                '<script type="text/javascript">$Behavior.rateMarketplaceUser = function() {$Core.rate.init({display: true}); }</script>',
                'view.js' => 'module_advancedmarketplace'
            ));
        }
        $iPage = 0;
        $iSize = 2;

        list($iCount, $aRating) = Phpfox::getService("advancedmarketplace")->frontend_getListingReview($iListingId,
            $iSize, $iPage);


        $iRatingCount = Phpfox::getLib("database")
            ->select("count(*)")
            ->from(Phpfox::getT("advancedmarketplace_rate"))
            ->where(sprintf("listing_id = %d", $iListingId))
            ->execute("getSlaveField");

        $fAVGRating = Phpfox::getLib("database")
            ->select("AVG(rating)")
            ->from(Phpfox::getT("advancedmarketplace_rate"))
            ->where(sprintf("listing_id = %d", $aListing['listing_id']))
            ->execute("getSlaveField");


        $rating = (int)$fAVGRating / 2;

        $aTitleLabel = [
            'type_id' => 'ync-type-id'
        ];

        if ($aListing['is_featured']) {
            Phpfox::getLib('module')->appendPageClass('item-featured');
        }
        if ($aListing['is_sponsor']) {
            Phpfox::getLib('module')->appendPageClass('item-sponsor');
        }

        Phpfox::getLib('module')->appendPageClass('p-detail-page');
        $aFilterMenu = Phpfox::getService('advancedmarketplace')->getSectionMenu();
        $this->template()->buildSectionMenu('advancedmarketplace', $aFilterMenu);

        // Increment the view counter
        $bUpdateCounter = false;
        if (Phpfox::isModule('track')) {
            if (!$aListing['is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('advancedmarketplace', $aListing['listing_id']);
            } else {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('advancedmarketplace', $aListing['listing_id']);
                } else {
                    Phpfox::getService('track.process')->update('advancedmarketplace', $aListing['listing_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }

        if ($bUpdateCounter) {
            Phpfox::getService("advancedmarketplace.process")->updateViewCounter($iListingId);
            $aListing['total_view'] += 1;
        }

        if ($aListing['is_featured']) {
            $aTitleLabel['label']['featured'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'diamond'

            ];
        }
        if ($aListing['is_sponsor']) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'sponsor'

            ];
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;
        $iCatId = $aListing["category"]["category_id"];
        $aCustomFields = Phpfox::getService("advancedmarketplace.customfield.advancedmarketplace")->frontend_loadCustomFields($iCatId, $iListingId);

        $this->template()->setPhrase(['view_more', 'advancedmarketplace_add_to_wishlist', 'added_to_wish_list_replacement'])
            ->assign(array(
                "aRating" => $aRating,
                "iRatingCount" => $iRatingCount,
                "iCount" => $iCount,
                "iPage" => $iPage,
                "iSize" => $iSize,
                'rating' => $rating,
                'aCustomFields' => $aCustomFields,
                'aTitleLabel' => $aTitleLabel,
                'sUrl' => Phpfox::permalink('advancedmarketplace.embed', $aListing['listing_id'], $aListing['title']),
                'sTagType' => $sTagType,
                'findAddressUrl' => (Phpfox::getParam('core.force_https_secure_pages') ? 'https://' : 'http://') . 'maps.google.com/?q=' . urlencode($aListing['location_parsed'])
            ));

        $this->template()->assign('bNotShowActionButton', true);

        Phpfox::getService("advancedmarketplace.process")->updateRecentView($iListingId);

        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_view_process_end')) ? eval($sPlugin) : false);
    }

    public function getListingDetails($aItem)
    {
        Phpfox::getService($aItem['module_id'])->setIsInPage();

        $aRow = Phpfox::getService($aItem['module_id'])->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService($aItem['module_id'])->setMode();

        $sLink = Phpfox::getService($aItem['module_id'])->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => $aItem['module_id'] == 'pages' ? _p('pages.pages') : _p('groups'),
            'breadcrumb_home' => \Phpfox_Url::instance()->makeUrl($aItem['module_id']),
            'module_id' => $aItem['module_id'],
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'advancedmarketplace/',
            'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }
}

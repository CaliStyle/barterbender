<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 *
 */
class Coupon_Component_Controller_Detail extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser();
        // get callback for pages here
        $aCallback = $this->getParam('aCallback', false);

        // Check view permission
        Phpfox::getUserParam("coupon.can_view_coupon", true);
        $iViewerId = Phpfox::getUserId();

        // Check if form invite is submit here
        if ($this->request()->getArray('val')) {
            $aVals = $this->request()->getArray('val');
        }

        if ($this->request()->getInt('id')) {
            return Phpfox::getLib('module')->setController('error.404');
        }

        // Variables
        $oCoupon = Phpfox::getService('coupon');

        // Get related coupon
        $aCouponId = $this->request()->getInt('req3');
        $bCanEdit = false;
        $bCanDelete = false;
        $bCanAction = false;

        $aCoupon = $oCoupon->callback($aCallback)->getCouponById($aCouponId);
        $aCustomFields = Phpfox::getService('coupon.custom')->getByCouponId($aCouponId);
        if (!$aCoupon) {
            $this->template()->assign(array(
                'aCoupon' => $aCoupon
            ));
            return Phpfox_Error::set(_p('coupon_not_found'));
        }

        $bCanClaim = $oCoupon->canClaimACoupon($aCoupon);
        $bNeedApproved = ($aCoupon['is_approved'] == 0 && $aCoupon['is_draft'] == 0 && $aCoupon['status'] == $oCoupon->getStatusCode('pending'));
        $bCanPublish = ($aCoupon['user_id'] == Phpfox::getUserId() && $aCoupon['status'] != $oCoupon->getStatusCode('pending'));

        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('coupon', $aCoupon['coupon_id'], $aCoupon['user_id'],
                $aCoupon['privacy'], $aCoupon['is_friend']);
        }

//        if (Phpfox::isModule('pages') && !Phpfox::getService('pages')->hasPerm($aCoupon['item_id'],
//                'coupon.view_browse_coupons')) {
//            return Phpfox_Error::display('Unable to view this item due to privacy settings.');
//        }

        if ($aCoupon['user_id'] != $iViewerId) {
            Phpfox::getService('coupon.process')->updateTotalView($aCoupon['coupon_id']);
        }

        // Manage Permission
        if ($iViewerId != $aCoupon['user_id']) {
            if ($aCoupon['is_draft']) {
                $bCanEdit = Phpfox::getUserParam("coupon.can_edit_other_user_coupon");
                $bCanDelete = Phpfox::getUserParam("coupon.can_delete_other_user_coupon");
            }
        } else {
            if ($aCoupon['is_draft']) {
                $bCanEdit = Phpfox::getUserParam("coupon.can_edit_own_coupon");
                $bCanDelete = Phpfox::getUserParam("coupon.can_delete_own_coupon");
            }
        }
        if (Phpfox::isAdmin() || $iViewerId == $aCoupon['user_id']) {
            $bCanAction = true;
        }

        if (!empty($aCoupon['gmap']) && Phpfox::getLib('parse.format')->isSerialized($aCoupon['gmap'])) {
            $aGmap = unserialize($aCoupon['gmap']);
            $aCoupon['latitude'] = $aGmap['latitude'];
            $aCoupon['longitude'] = $aGmap['longitude'];
        }
        if (!isset($aCoupon['coupon_id']) || $aCoupon['is_removed']) {
            return Phpfox_Error::display(_p('coupon_not_found'));
        }

        // Draft view permission
        if ($aCoupon['is_draft'] && !Phpfox::isAdmin() && $aCoupon['user_id'] != $iViewerId) {
            $this->url()->send('subscribe');
        }

        // Begin invite friend after get this detail campaign
        if (isset($aVals['submit_invite'])) {
            Phpfox::getService('coupon.process')->inviteFriends($aVals, $aCoupon);
        }


        $oCoupon = phpFox::getService('coupon');

        $bCanPause = ((Phpfox::getUserParam('coupon.can_pause_own_coupon') && Phpfox::getUserId() == $aCoupon['user_id']) || Phpfox::isAdmin()) &&
            (in_array($aCoupon['status'], array(
                $oCoupon->getStatusCode('running'),
                $oCoupon->getStatusCode('upcoming'),
                $oCoupon->getStatusCode('endingsoon')
            )));
        $bCanResume = ((Phpfox::getUserParam('coupon.can_resume_own_coupon') && Phpfox::getUserId() == $aCoupon['user_id']) || Phpfox::isAdmin()) &&
            (in_array($aCoupon['status'], array(
                $oCoupon->getStatusCode('pause')
            )));

        $bCanClose = ((Phpfox::getUserParam('coupon.can_close_own_coupon') && Phpfox::getUserId() == $aCoupon['user_id']) || Phpfox::isAdmin()) &&
            (in_array($aCoupon['status'], array(
                $oCoupon->getStatusCode('running'),
                $oCoupon->getStatusCode('upcoming'),
                $oCoupon->getStatusCode('pause'),
                $oCoupon->getStatusCode('endingsoon'),
                $oCoupon->getStatusCode('pending')
            )));

        $aCoupon['bookmark_url'] = $sLink = Phpfox::permalink('coupon.detail', $aCoupon['coupon_id'],
            $aCoupon['title']);

        // Generate feed comment
        $this->setParam('aFeed', array(
                'comment_type_id' => 'coupon',
                'privacy' => $aCoupon['privacy'],
                'comment_privacy' => $aCoupon['privacy_comment'],
                'like_type_id' => 'coupon',
                'feed_is_liked' => isset($aCoupon['is_liked']) ? $aCoupon['is_liked'] : false,
                'feed_is_friend' => $aCoupon['is_friend'],
                'item_id' => $aCoupon['coupon_id'],
                'user_id' => $aCoupon['user_id'],
                'total_comment' => $aCoupon['total_comment'],
                'total_like' => $aCoupon['total_like'],
                'feed_link' => $aCoupon['bookmark_url'],
                'feed_title' => $aCoupon['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aCoupon['total_like'],
                'report_module' => 'coupon',
                'report_phrase' => _p('report_this_coupon'),
                'time_stamp' => $aCoupon['time_stamp']
            )
        );

        // Set title, meta, param, breadcrumb, header and variables
        $this->setParam('aCoupon', $aCoupon);

        $this->template()->setTitle($aCoupon['title']);

        // if ($aCoupon['module_id'] != 'coupon' && ($aCallback = Phpfox::callback('coupon.getCouponsDetails', array('item_id' => $aCoupon['item_id'])))) {
        if ($aCoupon['module_id'] != 'coupon') {
            if (Phpfox::isModule('pages')) {
                ($aCallback = Phpfox::callback('coupon.getCouponsDetails', array('item_id' => $aCoupon['item_id'])));
            } elseif (Phpfox::isModule('groups')) {
                $aCallback = Phpfox::callback('coupon.getCouponsGroupDetails', array('item_id' => $aCoupon['item_id']));
            } else {
                $aCallback = Phpfox::callback($aCoupon['module_id'] . '.getCouponsDetails',
                    array('item_id' => $aCoupon['item_id']));
            }
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
        }
        $this->template()->setBreadCrumb(_p('coupon'),
            $aCoupon['module_id'] == 'coupon' ? $this->url()->makeUrl('coupon') : $aCallback['url_home_pages'])
            ->setBreadCrumb((Core\Lib::phrase()->isPhrase($aCoupon['category'])) ? _p($aCoupon['category']) : Phpfox_Locale::instance()->convert($aCoupon['category']),
                $this->url()->permalink('coupon.category', $aCoupon['category_id'], $aCoupon['category']))
            ->setBreadCrumb('', '', true);

        $this->template()->setMeta('description', Phpfox::getParam('coupon.coupon_meta_description'))
            ->setMeta('description', $aCoupon['title'] . '.')
            ->setMeta('description', $aCoupon['description'] . '.')
            ->setMeta('keywords', $this->template()->getKeywords($aCoupon['title']))
            ->setMeta('keywords', Phpfox::getParam('coupon.coupon_meta_keywords'))
            ->setMeta('og:url', $sLink)
            ->setMeta('og:image', Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aCoupon['server_id'],
                'path' => 'core.url_pic',
                'file' => $aCoupon['image_path'],
                'suffix' => '_200_square',
                'return_url' => true
            )));

        $this->template()->setHeader(array(
            'detail.css' => 'module_coupon',
            'yncoupon.js' => 'module_coupon'
        ));

        $this->template()->setHeader('cache', array(
            'quick_edit.js' => 'static_script',
            'switch_menu.js' => 'static_script',
            'comment.css' => 'style_css',
            'feed.js' => 'module_feed',
            'jquery.rating.css' => 'style_css',
            'jquery/plugin/star/jquery.rating.js' => 'static_script',
            'jquery/plugin/jquery.highlightFade.js' => 'static_script',
            'jquery/plugin/jquery.scrollTo.js' => 'static_script',
        ));

        // Claim Percent
        $iPercent = 100;
        if ($aCoupon['quantity'] > 0) {
            $iPercent = $aCoupon['total_claim'] / $aCoupon['quantity'] * 100;
        }

        // Claims Remain
        $sRemain = _p("unlimited_remain");
        if ($aCoupon['quantity'] > 0) {
            $sRemain = $aCoupon['quantity'] - $aCoupon['total_claim'];
            if ($sRemain < 0) {
                $sRemain = 0;
            }

            $sRemain = $sRemain . " " . _p("claims_remain");
        }

        // Remain Time
        $sRemainTime = Phpfox::getService('coupon')->convertTimeToCountdownString($aCoupon['end_time']);

        $bCanFollow = true;
        if ($aCoupon['user_id'] == $iViewerId) {
            $bCanFollow = false;
        }
        $bIsFavorited = $oCoupon->isFavorited($aCoupon['coupon_id']);
        $this->template()->assign(array(
            'aCoupon' => $aCoupon,
            'bCanAction' => $bCanAction,
            'bCanDelete' => $bCanDelete,
            'bCanEdit' => $bCanEdit,
            'bCanClose' => $bCanClose,
            'bCanPause' => $bCanPause,
            'bCanResume' => $bCanResume,
            'bCanClaim' => $bCanClaim,
            'bCanFollow' => $bCanFollow,
            'bNeedApproved' => $bNeedApproved,
            'bCanPublish' => $bCanPublish,
            'aFields' => $aCustomFields,
            'iPercent' => $iPercent,
            'sRemain' => $sRemain,
            'sRemainTime' => $sRemainTime,
            'bIsFavorited' => $bIsFavorited,
            'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png"
        ));

        // Set page phrase for jscript call
        $this->template()->setPhrase(array(
            'coupon.you_must_agree_with_the_terms_and_conditions_before_getting_code'
        ));

        $this->_buildSubsectionMenu();
    }

    private function _buildSubsectionMenu()
    {
        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
            $aFilterMenu = array(
                _p('all_coupons') => ''
            );

            if (Phpfox::isUser()) {
                $aFilterMenu[_p('my_coupons')] = 'my';
            }

            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')) {
                $aFilterMenu[_p('friend_s_coupons')] = 'friend';
            }

            if (Phpfox::isAdmin()) {
                $iTotalPending = Phpfox::getService('coupon')->getTotalPending();
                if ($iTotalPending) {
                    $aFilterMenu[_p('pending_coupon') . '<span class="pending count-item">' . $iTotalPending . '</span>'] = 'pending';
                }
            }

            $aFilterMenu[] = true;
            if (Phpfox::isUser()) {
                $aFilterMenu[_p('my_claim_coupons')] = 'my_claims';
                $aFilterMenu[_p('my_favorite_coupon')] = 'favorite';
                $aFilterMenu[_p('my_following_coupon')] = 'following';
            }

            $aFilterMenu[_p('featured_coupon')] = 'featured';
            $aFilterMenu[_p('upcoming_coupon')] = 'upcoming';
            $aFilterMenu[_p('ending_soon_coupon')] = 'endingsoon';
            $aFilterMenu[_p('faq_s')] = 'faq';

            Phpfox::getLib('template')->buildSectionMenu('coupon', $aFilterMenu);
        }
    }
}

?>
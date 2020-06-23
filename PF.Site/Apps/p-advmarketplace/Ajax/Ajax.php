<?php

namespace Apps\P_AdvMarketplace\Ajax;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Ajax;

class Ajax extends Phpfox_Ajax
{
    public function detailPayment()
    {
        Phpfox::isUser(true);
        $this->setTitle(_p('advancedmarketplace_purchase_confirmation'));
        Phpfox::getBlock('advancedmarketplace.detail-payment', [
            'invoice_id' => $this->get('invoice_id')
        ]);
    }

    public function confirmPurchase()
    {
        Phpfox::isUser(true);
        Phpfox::getBlock('advancedmarketplace.purchase-popup', [
            'listing_id' => $this->get('listing_id'),
            'invoice_id' => $this->get('invoice_id')
        ]);
    }

    public function deleteReview()
    {
        Phpfox::isUser(true);
        $rateId = $this->get('rate_id');
        $listingId = $this->get('listing_id');
        if (empty($rateId) || empty($listingId)) {
            return false;
        }
        if (Phpfox::getService('advancedmarketplace.rate.process')->deleteReview($rateId)) {
            $listing = Phpfox::getService('advancedmarketplace')->getListing($listingId);
            Phpfox::getService('advancedmarketplace.rate')->getReviewPermission($listing);
            $this->call('$("#js_reviewer_' . $rateId . '").remove();');
            \Phpfox_Template::instance()->assign(array(
                'aListing' => $listing
            ))->getTemplate('advancedmarketplace.block.review-entry');
            $content = $this->getContent(false);
            $this->html('#js_review_entry_block', $content);
            $this->call('if(!$(".js_reviewer_item").length && $(".js_pager_view_more_link").length){$(".js_pager_view_more_link").remove();}');
            $this->call('setTimeout(function(){$Core.loadInit();},100);');
        } else {
            $this->alert(_p('advancedmarketplace_you_can_not_delete_this_review_because_of_denied_permission'), _p('error'));
        }
    }

    public function submitReview()
    {
        Phpfox::isUser(true);
        if (!Phpfox::getUserParam('advancedmarketplace.can_post_a_review')) {
            return $this->alert(_p('advancedmarketplace_you_can_not_review_listing_because_of_denied_permission'), _p('error'));
        }
        $vals = $this->get('val');
        if (empty($vals['rating'])) {
            $this->call('$("#js_advancedmarketplace_form_rating").find(".js_submit_review_btn:first").prop("disabled", false);');
            return $this->alert(_p('advancedmarketplace_you_need_to_rate_before_submitting_your_review'), _p('error'));
        }
        if ($reviewId = Phpfox::getService('advancedmarketplace.rate.process')->addReview($vals)) {
            $listing = Phpfox::getService('advancedmarketplace')->getListing($vals['listing_id']);
            $review = Phpfox::getService('advancedmarketplace.rate')->getReview($reviewId, true);
            Phpfox::getService('advancedmarketplace.rate')->getReviewPermission($listing);
            \Phpfox_Template::instance()->assign(array(
                'aListing' => $listing
            ))->getTemplate('advancedmarketplace.block.review-entry');
            $content = $this->getContent(false);

            \Phpfox_Template::instance()->assign(array(
                'reviewer' => $review,
                'listing_id' => $vals['listing_id']
            ))->getTemplate('advancedmarketplace.block.reviewer-entry');
            $reviewerContent = $this->getContent(false);

            $this->html('#js_review_entry_block', $content);
            $this->prepend('.js_reviewer_listing', $reviewerContent);
            $this->call('setTimeout(function(){$Core.loadInit();},100);');
        }
    }

    public function addWishlist()
    {
        Phpfox::isUser(true);
        $listingId = $this->get('listing_id');
        $wishlist = $this->get('wishlist');
        if (Phpfox::getService('advancedmarketplace.process')->processWishlist($listingId, Phpfox::getUserId(), $wishlist)) {
            $params = [
                'parent' => !empty($this->get('detail')) ? '.js_detail_action_list' : ('.js_listing_item_' . $listingId),
                'wishlist' => $wishlist ? 0 : 1,
            ];
            if (!empty($this->get('detail')) || !empty($this->get('feed'))) {
                $params['change_text'] = true;
                $params['advancedmarketplace_add_to_wishlist'] = _p('advancedmarketplace_add_to_wishlist');
                $params['advancedmarketplace_remove_from_wishlist'] = _p('added_to_wish_list_replacement');
            }
            $this->call('appAdvMarketplace.processAfterAddWishlist(' . json_encode($params) . ');');
            if ($this->get('wishlist_page')) {
                $this->call('$(".js_listing_item_' . $listingId . '").remove();');
            }
        }
    }

    public function delete()
    {
        if (Phpfox::getService('advancedmarketplace.process')->delete($this->get('id'))) {
            $this->call('$(\'#js_mp_item_holder_' . $this->get('id') . '\').html(\'<div class="message" style="margin:0;">' . _p('advancedmarketplace.successfully_deleted_listing') . '</div>\').fadeOut(5000);');
        }
    }

    public function setDefault()
    {
        Phpfox::getService('advancedmarketplace.process')->setDefault($this->get('id'));
    }

    public function deleteImage()
    {
        $iId = $this->get('id');
        if (Phpfox::getService('advancedmarketplace.process')->deleteImage($iId)) {
            $this->call('$("#js_photo_holder_' . $this->get('id') . '").fadeOut("slow");');
        }
        $iTotalImage = Phpfox::getService('advancedmarketplace')->countImages($iId);
        $iTotalImageLimit = Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit');
        if ($iTotalImage < $iTotalImageLimit) {
            $this->call('$("#js_p_advmarketplace_add_more_photo_btn").show();');
        }
    }

    public function listInvites()
    {
        Phpfox::getBlock('advancedmarketplace.invite-list');

        $this->html('#js_mp_item_holder', $this->getContent(false));
    }

    public function feature()
    {
        //Consider case in block feature : delete item and reload page or delete parent div if no item any more
        $aListing = Phpfox::getService('advancedmarketplace')->getListing($this->get('listing_id'));
        $pageAdmin = $this->get('admin_page');
        $type = $this->get('type');
        if ($aListing["post_status"] == 2) {
            return false;
        }
        if (Phpfox::getService('advancedmarketplace.process')->feature($this->get('listing_id'), $type)) {
            if ($pageAdmin) {
                $this->val('#is_selected_active_' . $this->get('listing_id'), !empty($type) ? 1 : 0);
                $this->call('setTimeout(function(){ location.reload(); },1000);');
            } else {
                if ($type) {
                    $this->addClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_featured');
                } else {
                    $this->removeClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_featured');
                }
                \Phpfox_Template::instance()->assign([
                    'aListing' => [
                        'view_id' => $aListing['view_id'],
                        'is_featured' => $type,
                        'is_sponsor' => $aListing['is_sponsor']
                    ]
                ])->getTemplate('advancedmarketplace.block.status-icon-entry');
                $statusIconContent = $this->getContent(false);
                $this->html('.js_status_icon_item_' . $aListing['listing_id'], $statusIconContent);
            }
        }

        return true;
    }

    public function sponsor()
    {
        $aListing = Phpfox::getService('advancedmarketplace')->getListing($this->get('listing_id'));
        if ($aListing["post_status"] == 2) {
            // $this->alert(_p('advancedmarketplace.listing_successfully_featured'), _p('advancedmarketplace.feature'), 300, 150, true);
            return false;
        }
        if (Phpfox::getService('advancedmarketplace.process')->sponsor($this->get('listing_id'), $this->get('type'))) {
            if ($this->get('type') == '1') {
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'advancedmarketplace',
                    'item_id' => $this->get('listing_id'),
                    'name' => _p('advancedmarketplace_sponsor_title', array('sListingTitle' => $aListing['title']))
                ));
                // listing was sponsored
                $sHtml = '<a href="#" title="' . _p('advancedmarketplace.unsponsor_this_listing') . '" onclick="$(\'#js_sponsor_phrase_' . $this->get('listing_id') . '\').hide(); $.ajaxCall(\'advancedmarketplace.sponsor\', \'listing_id=' . $this->get('listing_id') . '&amp;type=0\', \'GET\'); return false;">' . _p('advancedmarketplace.unsponsor_this_listing') . '</a>';
                $this->call('setTimeout(function(){ location.reload(); },1000);');
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('advancedmarketplace', $this->get('listing_id'));
                $sHtml = '<a href="#" title="' . _p('advancedmarketplace.unsponsor_this_listing') . '" onclick="$(\'#js_sponsor_phrase_' . $this->get('listing_id') . '\').show(); $.ajaxCall(\'advancedmarketplace.sponsor\', \'listing_id=' . $this->get('listing_id') . '&amp;type=1\', \'GET\'); return false;">' . _p('advancedmarketplace.sponsor_this_listing') . '</a>';
                $this->call('setTimeout(function(){ location.reload(); },1000);');
            }
            $this->html('#js_sponsor_' . $this->get('listing_id'),
                $sHtml)->alert($this->get('type') == '1' ? _p('advancedmarketplace.listing_successfully_sponsored') : _p('advancedmarketplace.listing_successfully_un_sponsored'));
            if ($this->get('type') == '1') {
                $this->addClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_sponsored');
            } else {
                $this->removeClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_sponsored');
            }
        }

        return true;
    }

    public function approve()
    {
        Phpfox::getUserParam('advancedmarketplace.can_approve_listings', true);
        $listingId = $this->get('listing_id');
        if (Phpfox::getService('advancedmarketplace.process')->approve($listingId)) {
            $this->alert(_p('advancedmarketplace.listing_has_been_approved'),
                _p('advancedmarketplace.listing_approved'), 300, 100, true);
            $this->hide('#js_item_bar_approve_image');
            $this->hide('.js_moderation_off');
            $this->show('.js_moderation_on');
            if(!empty($this->get('no_reload'))) {
                if($this->get('no_delete')) {
                    $this->call('$(".js_listing_item_'. $listingId .'").find(".sticky-pending-icon").remove();');
                    $this->call('$("#js_approved_'. $listingId .'").remove();');
                }
                else {
                    $this->call('$(".js_listing_item_' . $listingId .'").remove();');
                }
            }
            else {
                $this->call('setTimeout(function(){ location.reload(); },1000);');
            }
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'approve':
                Phpfox::getUserParam('advancedmarketplace.can_approve_listings', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('advancedmarketplace.process')->approve($iId);
                    $this->call('$("#js_mp_item_holder_' . $iId . '").prev().remove();');
                    $this->remove('#js_mp_item_holder_' . $iId);
                }
                $this->updateCount();
                $sMessage = _p('advancedmarketplace.listing_s_successfully_approved');
                break;
            case 'delete':
                Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('advancedmarketplace.process')->delete($iId);
                    $this->call('$("#js_mp_item_holder_' . $iId . '").prev().remove();');
                    $this->slideUp('#js_mp_item_holder_' . $iId);
                }
                $sMessage = _p('advancedmarketplace.listing_s_successfully_deleted');
                break;
            case 'feature':
                Phpfox::getUserParam('advancedmarketplace.can_feature_listings', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('advancedmarketplace.process')->feature($iId, 1);
                    $this->addClass('#js_mp_item_holder_' . $iId, 'row_featured');
                }
                $sMessage = _p('advancedmarketplace.listing_s_successfully_featured');
                break;
            case 'un-feature':
                Phpfox::getUserParam('advancedmarketplace.can_feature_listings', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('advancedmarketplace.process')->feature($iId, 0);
                    $this->removeClass('#js_mp_item_holder_' . $iId, 'row_featured');
                }
                $sMessage = _p('advancedmarketplace.listing_s_successfully_un_featured');
                break;
        }

        if (!empty($sMessage)) {
            $this->alert($sMessage, 'Moderation', 300, 150, true);
        }
        $this->hide('.moderation_process');
        $this->call('setTimeout(function(){ location.reload(); },2000);');
    }

    public function sponsorHelp()
    {
        Phpfox::getBlock('advancedmarketplace.sponsorhelp');

    }

    public function ratePopup()
    {
        $iPage = $this->get('page');
        // echo $iPage;exit;
        phpfox::isUser(true);
        Phpfox::getBlock('advancedmarketplace.rate', array(
            "iId" => $this->get('id'),
            "page" => $iPage
        ));
    }

    public function todaylistingPopup()
    {
        Phpfox::getBlock('advancedmarketplace.admincp.todaylisting', array(
            "iId" => $this->get('id')
        ));
    }

    public function massUploadProcess()
    {
        $this->call("$(\".error_message\").remove();");

        $iEditId = $this->get('iEditId');
        $sInviteLink = $this->get('sInviteLink');

        $aListing = Phpfox::getService('advancedmarketplace')->getListing($iEditId);

        if (!Phpfox::getUserParam('advancedmarketplace.listing_approve') && ($aListing['post_status'] == 1) && !Phpfox::getService('advancedmarketplace')->isListingOnFeed($iEditId)) {

            $aCallback = ($this->get('callback_module') ? Phpfox::callback($this->get('callback_module') . '.addList',
                $this->get('callback_item_id')) : null);

            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->add('advancedmarketplace',
                $iEditId, $aListing['privacy'],
                (isset($aListing['privacy_comment']) ? (int)$aListing['privacy_comment'] : 0)) : null);

            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'advancedmarketplace');
        }

        if ($sInviteLink) {
            $this->call('window.location.href = "' . $sInviteLink . '";');
        }
    }

    public function toggleActiveGroup()
    {
        if (Phpfox::getService('advancedmarketplace.custom.process')->toggleGroupActivity($this->get('id'))) {
            $this->call('$Core.custom.toggleGroupActivity(' . $this->get('id') . ')');
        }
    }

    public function toggleActiveField()
    {
        if (Phpfox::getService('advancedmarketplace.custom.process')->toggleFieldActivity($this->get('id'))) {
            $this->call('$Core.custom.toggleFieldActivity(' . $this->get('id') . ')');
        }
    }

    public function featureSelected()
    {
        $iType = $this->get('type');
        $iListingId = $this->get('listing_id');
        if (isset($iType) && $iType == 1) {
            /*$iType = 0;
            Phpfox::getService('advancedmarketplace.process')->feature($this->get('listing_id'), $iType);
            $this->call("$('#js_listing_is_feature_".$iListingId."').show();");
            $this->call("$('#is_selected_active_".$iListingId."').val(".$iType.");");*/
        } else {
            $iType = 1;
            Phpfox::getService('advancedmarketplace.process')->feature($this->get('listing_id'), $iType);
            $this->call("$('#js_listing_is_un_feature_" . $iListingId . "').show();");
            $this->call("$('#is_selected_active_" . $iListingId . "').val(" . $iType . ");");
        }
        $this->call('_bh.pop();');
        $this->alert(_p('advancedmarketplace.listing_successfully_featured'),
            _p('advancedmarketplace.feature'), 300, 150, true);

    }

    public function unfeatureSelected()
    {
        $iType = $this->get('type');
        $iListingId = $this->get('listing_id');
        if (isset($iType) && $iType == 1) {
            $iType = 0;
            Phpfox::getService('advancedmarketplace.process')->feature($this->get('listing_id'), $iType);
            $this->call("$('#js_listing_is_feature_" . $iListingId . "').show();");
            $this->call("$('#is_selected_active_" . $iListingId . "').val(" . $iType . ");");
        }
        $this->call('_bh.pop();');
        $this->alert(_p('advancedmarketplace.listing_successfully_un_featured'),
            _p('advancedmarketplace.unfeature'), 300, 150, true);
    }

    public function advMarketRating()
    {
        $rating = $this->get('rating');
        $iListingId = $rating["listing_id"];
        $iRate = isset($rating['star']) ? $rating['star'] : 0;
        $sComment = $rating['comment'];
        if (!$iRate && !$sComment) {
            $this->alert(_p("advancedmarketplace.cannot_add_rating_to_a_review_created_without_a_rating"));

            return false;
        }
        $bCanRate = Phpfox::getService("advancedmarketplace.process")->rate($iListingId, $iRate, $sComment);
        if ($bCanRate !== false) {

            // reload ajx...

            $iPage = $this->get('page');
            $iPage = isset($iPage) ? $iPage : 1;
            $iSize = 2;
            // var_dump($iPage);
            List($iCount, $aRating) = Phpfox::getService("advancedmarketplace")->frontend_getListingReview($iListingId,
                ($iSize * $iPage), 0);
            $aParams = array(
                "aListing" => Phpfox::getService("advancedmarketplace")->getListing($iListingId),
                "aRating" => $aRating,
                "iCount" => $iCount,
                "iPage" => $iPage,
                "iSize" => $iSize
            );
            Phpfox::getBlock('advancedmarketplace.review', $aParams);
            // $this->html('#yn_listingrating .content', $this->getContent(false));
            $this->call(sprintf("$(\"#yn_listingrating\").html(\"%s\");",
                str_replace("\"", "\\\"", $this->getContent(false))));
            $this->call('$Behavior.advancedmarketplaceRating();');
            $this->call('$Behavior.advmarket_ratingJS();');
            $this->call('$(".review-count").html(' . $iCount . ');');
            $this->call('$Behavior.globalInit();');
            $this->alert(_p('advancedmarketplace.thank_for_your_rating'));
            $this->call('setTimeout(function(){ location.reload(); },1000);');

        }

        return true;
    }

    public function advMarketTodayListing()
    {
        $bCheck = $this->get('check');
        $iListingId = $this->get('id');
        $aDate = $this->get('todaylistingitem');
        if ($bCheck == 'true') {
            Phpfox::getService("advancedmarketplace.process")->todaylisting((int)$iListingId, $aDate);
            $this->alert(_p('advancedmarketplace.today_listing_added_successfully'));
        } else {
            $this->alert(_p('choose_day_to_set_as_today_listing'));
            $this->call('setTimeout(function(){tb_remove();},4000);');
        }
    }

    public function deleteField()
    {

        if (Phpfox::getService('advancedmarketplace.custom.process')->deleteField($this->get('id'))) {

            $this->call('$(\'#js_field_' . $this->get('id') . '\').parents(\'li:first\').remove();');
        }
    }

    public function sponsorSelected()
    {
        $iType = $this->get('type');
        $iListingId = $this->get('listing_id');
        if (isset($iType) && $iType == 1) {
            /*$iType = 0;
            Phpfox::getService('advancedmarketplace.process')->sponsor($this->get('listing_id'), $iType);
            $this->call("$('#js_listing_is_sponsor_".$iListingId."').show();");
            $this->call("$('#is_selected_active_".$iListingId."').val(".$iType.");");*/
        } else {
            $iType = 1;
            Phpfox::getService('advancedmarketplace.process')->sponsor($this->get('listing_id'), $iType);
            $this->call("$('#js_listing_is_un_sponsor_" . $iListingId . "').show();");
            $this->call("$('#is_selected_active_" . $iListingId . "').val(" . $iType . ");");
        }
        $this->call('_ch.pop();');

    }

    public function follow()
    {
        phpfox::isUser(true);
        if (phpfox::isUser()) {
            $bType = $this->get('type');
            $iUserId = $this->get('user_id');
            $iFollower = $this->get('user_follow_id');

            if ($bType == 'follow') {
                Phpfox::getService('advancedmarketplace.process')->addFollow($iUserId, $iFollower);
                $sType = 'unfollow';
            } else {
                Phpfox::getService('advancedmarketplace.process')->removeFollow($iUserId, $iFollower);
                $sType = 'follow';
            }
            if ($sType == 'unfollow') {
                $this->html('.js_seller_action_' . $iUserId .' .js_follow_action',
                    '<a onclick="$(this).addClass(\'disabled\').prop(\'disabled\', true); follow(\'unfollow\','. $iUserId .','. $iFollower .'); return false;" type="button" class="btn btn-default btn-xs mr-1">'. _p('advancedmarketplace.unfollow') .'</a>');
            } else {
                $this->html('.js_seller_action_' . $iUserId .' .js_follow_action',
                    '<a onclick="$(this).addClass(\'disabled\').prop(\'disabled\', true); follow(\'follow\','. $iUserId .','. $iFollower .'); return false;" type="button" class="btn btn-primary btn-xs mr-1">'. _p('advancedmarketplace.follow') .'</a>');
            }
        }

    }

    // nhanlt
    public function listingdetail()
    {
        Phpfox::getBlock('advancedmarketplace.listingdetail', array(
            "aListing" => Phpfox::getService("advancedmarketplace")->getListing($this->get('lid'))
        ));
        $this->call(sprintf("$(\"#yn_advmarket_wrapper\").html($(\"%s\"));",
            str_replace("\"", "\\\"", $this->getContent(false))));
    }

    //nhanlt
    public function review()
    {
        $iPage = $this->get('page');
        $iPage = isset($iPage) ? ($iPage) : 0;
        $iSize = 2;

        List($iCount, $aRating) = Phpfox::getService("advancedmarketplace")->frontend_getListingReview($this->get('lid'),
            $iSize, $iPage);
        Phpfox::getBlock('advancedmarketplace.review', array(
            "aListing" => Phpfox::getService("advancedmarketplace")->getListing($this->get('lid')),
            "aRating" => $aRating,
            "iCount" => $iCount,
            "iPage" => $iPage,
            "iSize" => $iSize
        ));
        $this->call(sprintf("$(\"#yn_advmarket_wrapper\").html($(\"%s\"));",
            str_replace("\"", "\\\"", $this->getContent(false))));
    }

    //nhanlt
    public function reviewpaging()
    {
        $iPage = $this->get('page', 0);
        $iSize = 2;

        List($iCount, $aRating) = Phpfox::getService("advancedmarketplace")->frontend_getListingReview($this->get('lid'),
            $iSize * $iPage, 0);
        $aParams = array(
            "aListing" => Phpfox::getService("advancedmarketplace")->getListing($this->get('lid')),
            "aRating" => $aRating,
            "iCount" => $iCount,
            "iPage" => $iPage,
            "iSize" => $iSize
        );
        Phpfox::getBlock('advancedmarketplace.review', $aParams);
        // $this->html('#yn_listingrating .content', $this->getContent(false));
        $this->call(sprintf("$(\"#yn_listingrating\").html(\"%s\");",
            str_replace("\"", "\\\"", $this->getContent(false))));
        $this->call('$Behavior.advmarket_ratingJS();');
        $this->call('$Behavior.advancedmarketplaceRating();');
        $this->call('$(".ssbt").removeClass("ssbt");');
    }

    //nhanlt
    public function showmanagecustomfieldpopup()
    {
        $aParams = array(
            "lid" => $this->get("lid")
        );
        // remove cache for "fresh phrase"...
        Phpfox::getLib('cache')->remove();
        Phpfox::getBlock('advancedmarketplace.admincp.managecustomfield', $aParams);
    }

    public function addCustomFieldGroup()
    {
        $sText = _p('default_custom_field_group_name');
        $iListingId = $this->get("lid");

        $sKeyVar = Phpfox::getService('advancedmarketplace.customfield.process')->addDefaultCustomFieldGroup($iListingId, $sText);

        $aParams = array(
            "sKeyVar" => $sKeyVar,
            "sText" => $sText,
            "is_active" => "1",
        );
        Phpfox::getBlock('advancedmarketplace.admincp.customfieldgroup', $aParams);
        $this->call(sprintf("processCustomGroupSample(\"%s\");", str_replace("\"", "\\\"", $this->getContent(false))));
    }

    public function editCustomFieldGroup()
    {
        $sCusfGroupId = $this->get("cusfgroupid");
        $sValue = $this->get("value");
        Phpfox::getService("advancedmarketplace.customfield.process")->updateCustomFieldName($sCusfGroupId, $sValue);
        $this->call(sprintf("$(\".ajxloader\").hide();$(\".ref_%s\").removeClass(\"changed\");", $sCusfGroupId));
        $this->call("$.ajaxCall(\"advancedmarketplace.loadCustomFields\", \"cusfgroupid=\" + \"$sCusfGroupId\");");
    }

    public function deleteCustomFieldGroup()
    {
        $sCusfGroupId = $this->get("cusfgroupid");
        Phpfox::getService("advancedmarketplace.customfield.process")->deleteCustomFieldGroup($sCusfGroupId);
        $this->call(sprintf("$(\"li.pref_%s\").remove();", $sCusfGroupId));
        $this->call(sprintf("$(\".ajxloader\").hide();"));
    }

    public function loadCustomFields()
    {
        Phpfox::getLib('cache')->remove();
        $sCusfGroupId = $this->get("cusfgroupid");
        $aCustomFields = Phpfox::getService("advancedmarketplace.customfield.advancedmarketplace")->loadCustomFields($sCusfGroupId);
        $aParams = array(
            "aCustomFields" => $aCustomFields,
            "sKeyVar" => $sCusfGroupId,
        );
        Phpfox::getBlock('advancedmarketplace.admincp.groupcustomfields', $aParams);
        $this->call(sprintf("$(\".ajxloader\").hide();"));
        $this->call(sprintf("processCustomGroupFieldSample(\"%s\");",
            str_replace("\"", "\\\"", $this->getContent(false))));
        $this->call(sprintf("$(\".ajxloader\").hide();"));
        if (count($aCustomFields) <= 0) {
            $this->call(sprintf("$(\".yn_jh_saveall\").hide();"));
        }
    }

    public function addCustomField()
    {
        Phpfox::getLib('cache')->remove();
        $sText = _p("advancedmarketplace.default_custom_field_name");
        $sCusfGroupId = $this->get("cusfgroupid");
        $aCustomFields = Phpfox::getService("advancedmarketplace.customfield.process")->addCustomFields($sCusfGroupId,
            $sText);
        $aParams = array(
            "aCellCustomFields" => $aCustomFields,
            "sKeyVarCell" => $sCusfGroupId,
            "isAdd" => true
        );
        Phpfox::getBlock('advancedmarketplace.admincp.customfieldcell', $aParams);
        $this->call(sprintf("processCustomFieldSample(\"%s\");", str_replace("\"", "\\\"", $this->getContent(false))));
        $this->call(sprintf("$(\".ajxloader\").hide();"));
    }

    public function addOption()
    {
        Phpfox::getLib('cache')->remove();
        $sText = _p("advancedmarketplace.default_custom_field_option_name");
        $iCusfieldId = $this->get("cusfieldid");
        $sFieldType = $this->get("field_type");
        $sKeyVar = Phpfox::getService("advancedmarketplace.customfield.process")->addCustomFieldOption($iCusfieldId,
            $sFieldType, $sText);
        $aParams = array(
            "iCusfieldId" => $iCusfieldId,
            "sTextOption" => $sText,
            "sKeyVarOption" => "advancedmarketplace." . $sKeyVar,
        );
        Phpfox::getBlock('advancedmarketplace.admincp.customfieldoption', $aParams);
        $this->call(sprintf("processCustomFieldOptionSample(\"%s\", $iCusfieldId);",
            str_replace("\"", "\\\"", $this->getContent(false))));
        $this->call(sprintf("$(\".ajxloader\").hide();"));
    }

    public function saveAllCustomField()
    {
        $aCustomFields = $this->get("customfield");
        Phpfox::getService("advancedmarketplace.customfield.process")->updateMultiCustomFields($aCustomFields);
        $this->call(sprintf("$(\".ajxloader\").hide();"));

        $this->call('$("#yn_jh_groupcustomfields").append("<br/><div class=\"msgdiv\">' . _p('all_custom_field_has_been_saved_successfully') . '</div>");');
        $this->call('setTimeout(function(){$(\'.msgdiv\').fadeOut(500, function(){$(\'.msgdiv\').remove();});}, 600);');
    }

    public function setSwitchOnOffCustomFieldGroup()
    {
        $sCusfGroupId = $this->get("cusfgroupid");
        $sState = Phpfox::getService("advancedmarketplace.customfield.process")->setSwitchOnOffCustomFieldGroup($sCusfGroupId);
        $this->call(sprintf("$(\".ajxloader\").hide();"));
        $this->call(sprintf("processSwitchFieldGroupStatus(\"%s\", \"%s\");", $sCusfGroupId, $sState));
    }

    public function frontend_loadCustomFields()
    {
        Phpfox::getLib('cache')->remove();
        $iCatId = $this->get("catid");
        $iListingId = $this->get("lid");
        $aCustomFields = Phpfox::getService("advancedmarketplace.customfield.advancedmarketplace")->frontend_loadCustomFields($iCatId,
            $iListingId);
        $cfInfors = Phpfox::getService("advancedmarketplace")->backend_getcustomfieldinfos();
        $aParams = array(
            "aCustomFields" => $aCustomFields,
            "cfInfors" => $cfInfors,
        );
        Phpfox::getBlock('advancedmarketplace.frontend.customfield', $aParams);
        $this->html("#advmarketplace_js_customfield_form", str_replace("\\n", "", $this->getContent(false)));
    }

    public function updateCustomGroupOrder()
    {
        $aCustomGroupVars = $this->get("customfieldgroup");
        Phpfox::getService("advancedmarketplace.customfield.process")->updateCustomGroupOrder($aCustomGroupVars);
    }

    public function deleteCategoryImage()
    {
        $iId = $this->get('category_id');
        Phpfox::getService('advancedmarketplace.category.process')->deleteCategoryImage($iId);
        $this->call('$(".category-image").remove();');
        $this->softNotice(_p('delete_category_image_successfully'));
    }

    public function removeCustomField()
    {
        $sCustomFieldAlias = $this->get("cusfieldalias");
        Phpfox::getService("advancedmarketplace.customfield.process")->deleteCustomField($sCustomFieldAlias);
    }

    public function deleteTodayListings()
    {
        $aTodayListingIds = $this->get("deleteitem");
        foreach ($aTodayListingIds as $aTodayListingId) {
            phpfox::getService('advancedmarketplace.process')->deleteTodayListing($aTodayListingId);
        }
        $this->call("window.location = window.location;");
    }

    public function gmap()
    {
        Phpfox::getBlock('advancedmarketplace.gmap');
    }

    public function reloadGmap()
    {
        $sLocation = $this->get('location');
        $sCity = $this->get('city');
        $sRadius = (int)$this->get('radius');

        if ($sLocation == "Location...") {
            $sLocation = "";
        }
        if ($sCity != "" && $sCity != "City...") {
            $sLocation = $sLocation . " , " . $sCity;
        }

        list($aCoordinates,) = Phpfox::getService('advancedmarketplace.process')->address2coordinates($sLocation);
        $radius = 0;
        if (is_int($sRadius)) {
            $radius = $sRadius;
        }

        $sIds = $this->get('ids');


        $sIds = trim($sIds, ',');
        $aIds = explode(',', $sIds);

        foreach ($aIds as $iKey => $sId) {
            $aIds[$iKey] = (int)$sId;
        }

        $aIds[0] = 1;
        $aListings = Phpfox::getService('advancedmarketplace')->getListingsByIds($aIds);

        $sJson = json_encode($aListings);

        $this->call('panGmapTo(' . $aCoordinates[1] . ',' . $aCoordinates[0] . ',' . $radius . ',' . $sJson . ');'); // lat, lng
    }

    public function getListingsForGmap()
    {
        $sIds = 1;
        $sIds = trim($sIds, ',');
        $aIds = explode(',', $sIds);
        foreach ($aIds as $iKey => $sId) {
            $aIds[$iKey] = (int)$sId;
        }

        $aListings = Phpfox::getService('advancedmarketplace')->getListingsByIds($aIds);

        $sJson = json_encode($aListings);
        $this->call('displayMarkers("' . str_replace('"', '\\"', $sJson) . '");');
    }

    public function updateActivity()
    {
        Phpfox::getService('advancedmarketplace.category.process')->updateActivity($this->get('id'),
            $this->get('active'));
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'advancedmarketplace_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );
    }

    public function toggleUploadSection()
    {
        $bShowUpload = $this->get('show_upload');
        $isCreating = $this->get('is_creating');
        $iId = $this->get('id');
        $aListing = Phpfox::getService('advancedmarketplace')->getForEdit($iId);
        if (!$iId) {
            return false;
        }
        $iTotalImage = Phpfox::getService('advancedmarketplace')->countImages($iId);
        $iTotalImageLimit = Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit');
        if ($bShowUpload) {
            $this->template()->
            assign([
                'aForms' => $aListing,
                'iTotalImage' => $iTotalImage,
                'iTotalImageLimit' => $iTotalImageLimit,
                'iRemainUpload' => $iTotalImageLimit - $iTotalImage,
                'iListingId' => $iId,
                'isCreating' => $isCreating,
                'aParamsUpload' => array('id' => $iId),
                'iMaxFileSize' => (Phpfox::getUserParam('advancedmarketplace.max_upload_size_listing') === 0 ? '' : (Phpfox::getUserParam('marketplace.max_upload_size_listing'))),
            ])->getTemplate('advancedmarketplace.block.upload-photo');
            $this->call('$(\'#js-p-advmarketplace-photos-container\').html(\'' . $this->getContent() . '\');');
            if ($isCreating) {
                $this->call('$Core.advancedmarketplace.toggleCreatingUploadMorePhotos();');
            }
            $this->call('$Core.loadInit();');
        } else {
            $this->template()->
            assign([
                'isCreating' => $isCreating,
                'iTotalImage' => $iTotalImage,
                'iTotalImageLimit' => $iTotalImageLimit,
                'iRemainUpload' => $iTotalImageLimit - $iTotalImage,
            ]);
            Phpfox::getBlock('advancedmarketplace.photo', [
                'aListing' => $aListing
            ]);
            $this->call('$(\'#js-p-advmarketplace-photos-container\').html(\'' . $this->getContent() . '\');');
            if ($isCreating) {
                $this->call('$Core.advancedmarketplace.toggleCreatingBackToManagePhotos();');
            }
            $this->call('$Core.loadInit();');
        }

        return true;
    }

    public function loadAjaxDetailMapView()
    {
        $iListingId = $this->get('iListingId');

        $aListingLocation = Phpfox::getService('advancedmarketplace')->getListing($iListingId);

        echo json_encode(array(
            'status' => 'SUCCESS',
            'sCorePath' => Phpfox::getParam('core.path'),
            'data' => $aListingLocation
        ));
    }
}

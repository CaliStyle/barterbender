<?php

namespace Apps\P_AdvMarketplaceAPI\Api\Security;

use Apps\Core_MobileApi\Adapter\Setting\SettingInterface;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Apps\Core_MobileApi\Api\Security\AccessControl;
use Apps\Core_MobileApi\Api\Security\UserInterface;
use Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceResource;
use Apps\Core_MobileApi\Api\Resource\UserResource;

class
MarketplaceAccessControl extends AccessControl
{
    const EDIT = "edit";
    const MANAGE_PHOTO = "manage_photo";
    const ADD = "add";
    const INVITE = "invite";
    const COMMENT = "comment";
    const VIEW_EXPIRED = "view_expired";
    const BUY_NOW = "buy_now";
    const REOPEN = "reopen";

    const FEATURE = "feature";
    const APPROVE = "approve";
    const SPONSOR = "sponsor";
    const SPONSOR_IN_FEED = "sponsor_in_feed";
    const PURCHASE_SPONSOR = "purchase_sponsor";
    const WISHLIST = "wishlist";

    protected $supports;

    public function __construct(SettingInterface $setting, UserInterface $context)
    {
        parent::__construct($setting, $context);
        $this->supports = $this->mergePermissions([self::ADD, self::VIEW, self::EDIT, self::COMMENT, self::VIEW_EXPIRED, self::REOPEN,
            self::FEATURE, self::APPROVE, self::SPONSOR, self::SPONSOR_IN_FEED, self::PURCHASE_SPONSOR, self::INVITE, self::MANAGE_PHOTO, self::BUY_NOW, self::WISHLIST]);
    }

    public function isGranted($permission, ResourceBase $resource = null)
    {
        if (in_array($permission, [self::IS_AUTHENTICATED, self::SYSTEM_ADMIN])) {
            return parent::isGranted($permission);
        }
        if (in_array($permission, [self::LIKE, self::SHARE, self::REPORT])) {
            return parent::isGranted($permission, $resource);
        }
        if (!parent::isGranted($permission, $resource)) {
            return false;
        }

        $isOwner = false;
        // Item Owner always able to do any permission
        if ($resource instanceof ResourceBase) {
            if ($this->userContext->compareWith($resource->getAuthor())) {
                $isOwner = true;
            }
        }

        /** @var MarketplaceResource $resource */
        $granted = false;

        switch ($permission) {
            case self::VIEW:
                $granted = $this->isGrantedSetting('advancedmarketplace.can_access_advancedmarketplace');
                break;
            case self::ADD:
                $granted = $this->isGrantedSetting('advancedmarketplace.can_create_listing');
                break;
            case self::EDIT:
                $granted = ($this->isGrantedSetting('advancedmarketplace.can_edit_other_listing')
                    || ($this->isGrantedSetting('advancedmarketplace.can_edit_own_listing') && $isOwner));
                break;
            case self::MANAGE_PHOTO:
                $granted = ($this->isGrantedSetting('advancedmarketplace.can_edit_other_listing')
                    || ($this->isGrantedSetting('advancedmarketplace.can_edit_own_listing') && $isOwner));
                break;
            case self::DELETE:
                $granted = ($this->isGrantedSetting('advancedmarketplace.can_delete_other_listings')
                    || ($this->isGrantedSetting('advancedmarketplace.can_delete_own_listing') && $isOwner));
                break;
            case self::COMMENT:
                $granted = $this->isGrantedSetting(['advancedmarketplace.can_access_advancedmarketplace', 'advancedmarketplace.can_post_comment_on_listing']);
                break;
            case self::SPONSOR:
                $granted = $this->isGrantedSetting('advancedmarketplace.can_sponsor_advancedmarketplace') && \Phpfox::isAppActive('Core_BetterAds') && !$resource->is_draft && $resource->view_id == 0;
                break;
            case self::PURCHASE_SPONSOR:
                if(\Phpfox::isAppActive('Core_BetterAds')) {
                    $isNotPendingAd = \Phpfox::getService('advancedmarketplace')->canPurchaseSponsorItem($resource->id);
                    $granted = $this->setting->getUserSetting('advancedmarketplace.can_purchase_sponsor') && $isOwner && $resource->view_id == 1 && $isNotPendingAd;
                }
                break;
            case self::APPROVE:
                $granted = $this->isGrantedSetting('advancedmarketplace.can_approve_listings') && (!$resource || $resource->getIsPending());
                break;
            case self::FEATURE:
                $granted = $this->isGrantedSetting('advancedmarketplace.can_feature_listings') && !$resource->is_draft && $resource->view_id == 0;
                break;
            case self::INVITE:
                $granted = ($this->isGrantedSetting('advancedmarketplace.can_edit_other_listing')
                        || ($this->isGrantedSetting('advancedmarketplace.can_edit_own_listing') && $isOwner))
                    && $resource && !$resource->view_id;
                break;
            case self::VIEW_EXPIRED:
                $granted = $this->isGrantedSetting('advancedmarketplace.can_view_expired');
                break;
            case self::BUY_NOW:
                /** @var MarketplaceResource $resource */
                $granted = $resource && $resource->is_sell && $resource->view_id != 2 && $resource->price != 'free' && !$isOwner;
                break;
            case self::REOPEN:
                $granted = ($this->isGrantedSetting('advancedmarketplace.can_reopen_own_expired_listing') && $isOwner) || $this->isGrantedSetting('advancedmarketplace.can_reopen_expired_listings');
                break;
            case self::WISHLIST:
                $granted = $this->isGrantedSetting('advancedmarketplace.can_access_advancedmarketplace') && $resource && $resource->view_id == 0 && !$resource->getIsDraft();
        }

        // Check Pages/Group permission
        if ($granted && $this->appContext) {
            switch ($permission) {
                case self::VIEW:
                    $granted = $this->appContext->hasPermission('advancedmarketplace.can_access_advancedmarketplace');
                    break;
                case self::ADD:
                    $granted = ($this->appContext->hasPermission('advancedmarketplace.can_access_advancedmarketplace')
                        && $this->appContext->hasPermission('advancedmarketplace.share_advancedmarketplace'));
                    break;
            }
        }

        return $granted;
    }

}
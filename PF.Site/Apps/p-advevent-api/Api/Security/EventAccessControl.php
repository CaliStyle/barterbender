<?php

namespace Apps\P_AdvEventAPI\Api\Security;

use Apps\Core_MobileApi\Adapter\Setting\SettingInterface;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Apps\Core_MobileApi\Api\Security\AccessControl;
use Apps\Core_MobileApi\Api\Security\UserInterface;
use Apps\Core_MobileApi\Api\Resource\UserResource;

class EventAccessControl extends AccessControl
{
    const EDIT = "edit";
    const MANAGE_PHOTO = "manage_photo";
    const ADD = "add";
    const INVITE = "invite";
    const MASS_EMAIL = "mass_email";
    const COMMENT = "comment";

    const FEATURE = "feature";
    const APPROVE = "approve";
    const SPONSOR = "sponsor";
    const SPONSOR_IN_FEED = "sponsor_in_feed";
    const PURCHASE_SPONSOR = "purchase_sponsor";

    protected $supports;

    public function __construct(SettingInterface $setting, UserInterface $context)
    {
        parent::__construct($setting, $context);
        $this->supports = $this->mergePermissions([self::ADD, self::EDIT, self::MANAGE_PHOTO, self::COMMENT,
            self::FEATURE, self::APPROVE, self::SPONSOR, self::SPONSOR_IN_FEED, self::PURCHASE_SPONSOR, self::INVITE, self::MASS_EMAIL]);
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

        $granted = false;

        switch ($permission) {
            case self::VIEW:
                $granted = $this->isGrantedSetting('fevent.can_access_event');
                break;
            case self::ADD:
                $granted = $this->isGrantedSetting('fevent.can_create_event');
                break;
            case self::EDIT:
            case self::MANAGE_PHOTO:
                $granted = ($this->isGrantedSetting('fevent.can_edit_other_event')
                    || ($this->isGrantedSetting('fevent.can_edit_own_event') && $isOwner));
                break;
            case self::DELETE:
                $granted = ($this->isGrantedSetting('fevent.can_delete_other_event')
                    || ($this->isGrantedSetting('fevent.can_delete_own_event') && $isOwner));
                break;
            case self::COMMENT:
                $granted = $this->isGrantedSetting(['fevent.can_access_event', 'fevent.can_post_comment_on_event']);
                break;
            case self::SPONSOR:
                $granted = $this->isGrantedSetting('fevent.can_sponsor_fevent') && \Phpfox::isAppActive('Core_BetterAds');
                break;
            case self::PURCHASE_SPONSOR:
                if (\Phpfox::isAppActive('Core_BetterAds')) {
                    $isNotPendingAd = \Phpfox::getService('fevent.helper')->canPurchaseSponsorItem($resource->id);
                    $granted = $this->setting->getUserSetting('fevent.can_purchase_sponsor') && $isOwner && $resource->view_id == 1 && $isNotPendingAd;
                }
                break;
            case self::APPROVE:
                $granted = $this->isGrantedSetting('fevent.can_approve_events') && (!$resource || $resource->getIsPending());
                break;
            case self::FEATURE:
                $granted = $this->isGrantedSetting('fevent.can_feature_events') && !$resource->is_draft;
                break;
            case self::INVITE:
                $granted = ($this->isGrantedSetting('fevent.can_edit_other_event')
                        || ($this->isGrantedSetting('fevent.can_edit_own_event') && $isOwner))
                    && (!$resource || !$resource->view_id);
                break;
            case self::MASS_EMAIL:
                $granted = $this->isGrantedSetting('fevent.can_mass_mail_own_members') && $isOwner;
                break;
        }

        // Check Pages/Group permission
        if ($granted && $this->appContext) {
            switch ($permission) {
                case self::VIEW:
                    $granted = $this->appContext->hasPermission('fevent.view_browse_events');
                    break;
                case self::ADD:
                    $granted = ($this->appContext->hasPermission('fevent.view_browse_events')
                        && $this->appContext->hasPermission('fevent.share_events'));
                    break;
            }
        }

        return $granted;
    }

}
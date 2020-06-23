<?php

namespace Apps\Core_MobileApi\Api\Security\Event;

use Apps\Core_MobileApi\Adapter\Setting\SettingInterface;
use Apps\Core_MobileApi\Api\Resource\EventResource;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Apps\Core_MobileApi\Api\Security\AccessControl;
use Apps\Core_MobileApi\Api\Security\UserInterface;


class EventAccessControl extends AccessControl
{
    const EDIT = "edit";
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

        $this->supports = $this->mergePermissions([
            self::ADD, self::EDIT, self::COMMENT,
            self::FEATURE, self::APPROVE, self::SPONSOR, self::SPONSOR_IN_FEED, self::PURCHASE_SPONSOR, self::INVITE, self::MASS_EMAIL
        ]);
    }

    /**
     * @inheritdoc
     *
     * @param $resource EventResource
     */
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
                $granted = $this->isGrantedSetting('event.can_access_event');
                break;
            case self::ADD:
                $granted = $this->isGrantedSetting('event.can_create_event');
                break;
            case self::EDIT:
                $granted = ($this->isGrantedSetting('event.can_edit_other_event')
                    || ($this->isGrantedSetting('event.can_edit_own_event') && $isOwner));
                break;
            case self::DELETE:
                $granted = ($this->isGrantedSetting('event.can_delete_other_event')
                    || ($this->isGrantedSetting('event.can_delete_own_event') && $isOwner) || ($this->appContext && $this->appContext->isAdmin($this->userContext->getId())));
                break;
            case self::DELETE_OWN:
                $granted = $this->isGrantedSetting('event.can_delete_own_event');
                break;
            case self::COMMENT:
                $granted = $this->isGrantedSetting(['event.can_access_event', 'event.can_post_comment_on_event']) && (!$resource || !$resource->getIsPending());
                break;
            case self::SPONSOR:
                $granted = $this->isGrantedSetting('event.can_sponsor_event') && \Phpfox::isAppActive('Core_BetterAds');
                break;
            case self::PURCHASE_SPONSOR:
                $granted = $this->isGrantedSetting('event.can_purchase_sponsor');
                break;
            case self::APPROVE:
                $granted = $this->isGrantedSetting('event.can_approve_events') && (!$resource || $resource->getIsPending());
                break;
            case self::FEATURE:
                $granted = $this->isGrantedSetting('event.can_feature_events');
                break;
            case self::INVITE:
                $granted = ($this->isGrantedSetting('event.can_edit_other_event')
                        || ($this->isGrantedSetting('event.can_edit_own_event') && $isOwner))
                    && (!$resource || !$resource->view_id);
                break;
            case self::MASS_EMAIL:
                $granted = $this->isGrantedSetting('event.can_mass_mail_own_members') && $isOwner;
                break;
        }

        // Check Pages/Group permission
        if ($granted && $this->appContext) {
            switch ($permission) {
                case self::VIEW:
                    $granted = $this->appContext->hasPermission('event.view_browse_events');
                    break;
                case self::ADD:
                    $granted = ($this->appContext->hasPermission('event.view_browse_events')
                        && $this->appContext->hasPermission('event.share_events'));
                    break;
            }
        }

        return $granted;
    }

}
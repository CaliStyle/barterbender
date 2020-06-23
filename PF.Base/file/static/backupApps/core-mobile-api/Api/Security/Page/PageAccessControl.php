<?php

namespace Apps\Core_MobileApi\Api\Security\Page;

use Apps\Core_MobileApi\Adapter\Setting\SettingInterface;
use Apps\Core_MobileApi\Api\Resource\PageResource;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Apps\Core_MobileApi\Api\Security\AccessControl;
use Apps\Core_MobileApi\Api\Security\UserInterface;
use Phpfox;


class PageAccessControl extends AccessControl
{
    const EDIT = "edit";
    const ADD = "add";
    const APPROVE = 'approve';
    const FEATURE = 'feature';
    const SPONSOR = 'sponsor';
    const CLAIM = 'claim';
    const ADD_COVER = "add_cover";
    const REMOVE_COVER = "remove_cover";
    const VIEW_PUBLISH_DATE = "view_publish_date";
    const POST_AS_ADMIN = "post_as_admin";

    protected $supports;

    public function __construct(SettingInterface $setting, UserInterface $context)
    {
        parent::__construct($setting, $context);

        $this->supports = $this->mergePermissions([
            self::ADD, self::DELETE, self::EDIT, self::VIEW_PUBLISH_DATE,
            self::VIEW, self::APPROVE, self::CLAIM, self::FEATURE, self::SPONSOR, self::ADD_COVER, self::REMOVE_COVER, self::POST_AS_ADMIN
        ]);
    }

    /**
     * @inheritdoc
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
        /** @var $resource PageResource */
        if ($resource instanceof ResourceBase) {
            if ($this->userContext->compareWith($resource->getAuthor())) {
                $isOwner = true;
            }
        }
        $granted = false;
        switch ($permission) {
            case self::VIEW:
                $granted = $this->isGrantedSetting('pages.can_view_browse_pages');
                break;
            case self::ADD:
                $granted = $this->isGrantedSetting('pages.can_add_new_pages');
                break;
            case self::EDIT:
                $granted = $this->isGrantedSetting('pages.can_edit_all_pages') || ($resource && \Phpfox::getService('pages')->isAdmin($resource->id));
                break;
            case self::DELETE:
                $granted = $this->isGrantedSetting('pages.can_delete_all_pages') || $isOwner;
                break;
            case self::DELETE_OWN:
                $granted = true;
                break;
            case self::APPROVE:
                $granted = $this->isGrantedSetting('pages.can_approve_pages') && (!$resource || $resource->getIsPending());
                break;
            case self::CLAIM:
                $granted = $this->isGrantedSetting('pages.can_claim_page') && (!$resource || !$resource->claim_id);
                break;
            case self::FEATURE:
                $granted = $this->isGrantedSetting('pages.can_feature_page') && (!$resource || !$resource->getIsPending());
                break;
            case self::SPONSOR:
                $granted = $this->isGrantedSetting('pages.can_sponsor_pages') && (!$resource || !$resource->getIsPending()) && \Phpfox::isAppActive('Core_BetterAds');
                break;
            case self::ADD_COVER:
                $granted = $this->isGrantedSetting('pages.can_add_cover_photo_pages');
                break;
            case self::REMOVE_COVER:
                $granted = $resource && $this->isGrantedSetting('pages.can_add_cover_photo_pages') && $resource->cover_photo_id;
                break;
            case self::VIEW_PUBLISH_DATE:
                $granted = $resource && Phpfox::getService("pages")->hasPerm($resource->getId(), 'pages.view_publish_date');
                break;
            case self::POST_AS_ADMIN:
                $granted = $resource && $resource->getIsAdmin() && Phpfox::getUserBy('profile_page_id') != $resource->getId();
                break;
        }

        return $granted;
    }

}
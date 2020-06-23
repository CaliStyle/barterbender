<?php
/**
 * Created by PhpStorm.
 * User: pro
 * Date: 4/6/18
 * Time: 3:56 PM
 */

namespace Apps\Core_MobileApi\Api\Security\Comment;


use Apps\Core_MobileApi\Adapter\Setting\SettingInterface;
use Apps\Core_MobileApi\Adapter\Utility\ArrayUtility;
use Apps\Core_MobileApi\Api\Resource\CommentResource;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Apps\Core_MobileApi\Api\Security\AccessControl;
use Apps\Core_MobileApi\Api\Security\UserInterface;
use Apps\Core_MobileApi\Service\NameResource;

class CommentAccessControl extends AccessControl
{
    const ADD = "add";
    const REPLY = "reply";
    const EDIT = "edit";
    const HIDE = "hide";

    public function __construct(SettingInterface $setting, UserInterface $userContext)
    {
        parent::__construct($setting, $userContext);
        ArrayUtility::append($this->supports, [self::ADD, self::REPLY, self::EDIT, self::HIDE]);
    }

    /**
     * @param                                   $permission
     * @param CommentResource|ResourceBase|null $resource
     *
     * @return bool|mixed
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
        $granted = false;
        switch ($permission) {
            case self::VIEW:
                if ($resource) {
                    $this->setParameters([
                        'item_type' => $resource->getItemType(),
                        'item_id'   => $resource->getItemId()
                    ]);
                }
                $granted = $this->hasAccessCommentVar(true);
                break;
            case self::REPLY:
                $granted = $this->hasAccessReplyComment();
                break;
            case self::ADD:
                $granted = $this->hasAccessAddComment();
                break;
            case self::EDIT:
                $granted = $resource && $this->hasAccessEditComment($resource);
                break;
            case self::DELETE:
                $granted = $resource && $this->hasAccessDeleteComment($resource);
                break;
            case self::HIDE:
                $granted = $resource && !$this->userContext->compareWith($resource->getAuthor());
                break;
        }

        return $granted;
    }

    private function hasAccessCommentVar($isView = false)
    {
        $isGrant = true;
        if (!$isView && !$this->parameters['item_type'] != 'app' && \Phpfox::hasCallback($this->parameters['item_type'], 'getAjaxCommentVar')) {
            $sVar = \Phpfox::callback($this->parameters['item_type'] . '.getAjaxCommentVar');
            if ($sVar !== null) {
                $isGrant = $this->isGrantedSetting($sVar);
            }
        }
        //check permission to view parent item
        if (!empty($this->parameters['item_type']) && !empty($this->parameters['item_id'])) {
            if (\Phpfox::hasCallback($this->parameters['item_type'], 'canViewItem')) {
                if (!\Phpfox::callback($this->parameters['item_type'] . '.canViewItem', $this->parameters['item_id'])) {
                    $isGrant = false;
                }
            } else {
                $itemType = $this->parameters['item_type'];
                if ($itemType == 'v') {
                    $itemType = 'video';
                }
                $item = NameResource::instance()->getPermissionByResourceName(str_replace('_', '-', $itemType), $this->parameters['item_id']);
                if ($item !== null && !$item) {
                    $isGrant = false;
                }
            }
        }
        return $isGrant;
    }

    private function hasAccessAddComment()
    {
        if (!$this->hasAccessCommentVar()) {
            return false;
        }
        return $this->isGrantedSetting(['comment.can_post_comments', 'feed.can_post_comment_on_feed']);
    }

    private function hasAccessReplyComment()
    {
        if (!$this->hasAccessCommentVar()) {
            return false;
        }
        return $this->isGrantedSetting(['comment.can_post_comments', 'feed.can_post_comment_on_feed']) && \Phpfox::getParam('comment.comment_is_threaded');
    }

    /**
     * @param ResourceBase $resource
     *
     * @return mixed
     */
    private function hasAccessEditComment(ResourceBase $resource)
    {
        $allow = \Phpfox::getService('comment')->hasAccess($resource->getId(), 'edit_own_comment', 'edit_user_comment');
        return !!$allow;
    }

    /**
     * @param CommentResource $resource
     *
     * @return mixed
     */
    private function hasAccessDeleteComment(CommentResource $resource)
    {
        $allow = \Phpfox::getService('comment')->hasAccess($resource->getId(), 'delete_own_comment', 'delete_user_comment') || $resource->getCanDelete();
        return !!$allow;
    }

}
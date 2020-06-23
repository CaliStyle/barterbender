<?php


namespace Apps\Core_MobileApi\Api\Resource;

use Apps\Core_MobileApi\Adapter\MobileApp\Screen;
use Apps\Core_MobileApi\Api\Resource\Object\Image;

class PageMemberResource extends UserResource
{
    const RESOURCE_NAME = "page-member";
    public $resource_name = self::RESOURCE_NAME;

    protected $idFieldName = "user_id";


    public $full_name;
    public $avatar;
    public $is_featured;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function getFullName()
    {
        $this->full_name = isset($this->rawData['full_name']) ? $this->parse->cleanOutput($this->rawData['full_name']) : '';
        return $this->full_name;
    }

    public function getAvatar()
    {
        $image = Image::createFrom([
            'user' => $this->rawData,
        ], ["50_square"]);

        if ($image == null) {
            return $this->getDefaultImage(false, parent::RESOURCE_NAME);
        }
        return (!$this->isDetailView() ? (!empty($image->sizes['50_square']) ? $image->sizes['50_square'] : $this->getDefaultImage(false, parent::RESOURCE_NAME)) : $image->image_url);

    }

    public function getShortFields()
    {
        return ['resource_name', 'id', 'full_name', 'avatar', 'is_featured', 'statistic', 'is_owner', 'friendship', 'friend_id', 'extra'];
    }

    /**
     * Get detail url
     * @return string
     */
    public function getLink()
    {
        return null;
    }

    public function getMobileSettings($params = [])
    {
        $l = $this->getLocalization();
        return self::createSettingForResource([
            'schema'          => [
                'ref' => 'item_member',
            ],
            'resource_name'   => $this->getResourceName(),
            'urls.base'       => 'mobile/page-member',
            'search_input'    => false,
            'list_view'       => [
                'item_view' => 'page_member',
            ],
            'fab_buttons'     => false,
            'can_add'         => false,
            'membership_menu' => [
                ['value' => 'user/unfriend', 'label' => $l->translate('unfriend'), 'style' => 'danger', 'show' => 'friendship==1', 'acl' => 'can_view_remove_friend_link'],
                ['value' => 'user/add_friend_request', 'label' => $l->translate('add_friend'), 'show' => 'friendship==0'],
                ['value' => 'user/accept_friend_request', 'label' => $l->translate('accept_friend_request'), 'show' => 'friendship==2'],
                ['value' => 'user/cancel_friend_request', 'label' => $l->translate('cancel_request'), 'show' => 'friendship==3', 'style' => 'danger'],
                ['value' => Screen::ACTION_CHAT_WITH, 'label' => $l->translate('send_message'), 'show' => 'friendship==1'],
            ],
            'action_menu'     => [
                ['value' => Screen::ACTION_CHAT_WITH, 'label' => $l->translate('send_message'), 'show' => 'friendship==1'],
                ['value' => 'user/add_friend_request', 'label' => $l->translate('add_friend'), 'show' => 'friendship==0'],
                ['value' => 'user/accept_friend_request', 'label' => $l->translate('accept_friend_request'), 'show' => 'friendship==2'],
                ['value' => 'user/cancel_friend_request', 'label' => $l->translate('cancel_request'), 'show' => 'friendship==3', 'style' => 'danger'],
                ['value' => 'user/unfriend', 'label' => $l->translate('unfriend'), 'style' => 'danger', 'show' => 'friendship==1', 'acl' => 'can_view_remove_friend_link'],
            ],
        ]);
    }

    public function getIsFeatured()
    {
        if ($this->is_featured === null) {
            $this->is_featured = \Phpfox::getService('user')->isFeatured($this->getId());
        }
        return (bool)$this->is_featured;
    }
}
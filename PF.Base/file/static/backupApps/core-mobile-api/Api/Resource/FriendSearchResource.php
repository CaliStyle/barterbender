<?php

namespace Apps\Core_MobileApi\Api\Resource;


/**
 * Class FriendSearchResource
 *
 * This resource using for quick map . DO NOT REMOVE.
 *
 * @package Apps\Core_MobileApi\Api\Resource
 */
class FriendSearchResource extends ResourceBase
{
    const RESOURCE_NAME = "friend_search";
    const MINIMAL_FORMAT = "mini";

    public $resource_name = self::RESOURCE_NAME;


    public function getMobileSettings($params = [])
    {
        return self::createSettingForResource([
            'resource_name' => $this->getResourceName(),
            'urls.base'     => 'mobile/friend/search',
            'search_input'  => false,
            'list_view'     => [
                'item_view' => 'page_admin',
            ],
            'fab_buttons'   => false,
            'can_add'       => false,
        ]);
    }

}
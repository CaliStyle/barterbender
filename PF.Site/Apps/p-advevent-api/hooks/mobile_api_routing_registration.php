<?php


if (Phpfox::isModule('fevent')) {
    /**
     * Define RestAPI services
     */
    $this->apiNames['mobile.fevent_api'] = \Apps\P_AdvEventAPI\Service\EventApi::class;
    $this->apiNames['mobile.fevent_category_api'] = \Apps\P_AdvEventAPI\Service\EventCategoryApi::class;
    $this->apiNames['mobile.fevent_invite_api'] = \Apps\P_AdvEventAPI\Service\EventInviteApi::class;
    $this->apiNames['mobile.fevent_photo_api'] = \Apps\P_AdvEventAPI\Service\EventPhotoApi::class;
    $this->apiNames['mobile.fevent_admin_api'] = \Apps\P_AdvEventAPI\Service\EventAdminApi::class;
    $this->specialModules['fevent'] = 'fevent';

    $this->objectResources['fevent'] = \Apps\P_AdvEventAPI\Api\Resource\EventResource::class;
    /**
     * Register Resource Name, This help auto generate routing for the resource
     * Note: resource name must be mapped correctly to resource api
     */
    $this->resourceNames[\Apps\P_AdvEventAPI\Api\Resource\EventResource::RESOURCE_NAME] = 'mobile.fevent_api';
    $this->resourceNames[\Apps\P_AdvEventAPI\Api\Resource\EventCategoryResource::RESOURCE_NAME] = 'mobile.fevent_category_api';
    $this->resourceNames[\Apps\P_AdvEventAPI\Api\Resource\EventInviteResource::RESOURCE_NAME] = 'mobile.fevent_invite_api';
    $this->resourceNames[\Apps\P_AdvEventAPI\Api\Resource\EventPhotoResource::RESOURCE_NAME] = 'mobile.fevent_photo_api';
    $this->resourceNames[\Apps\P_AdvEventAPI\Api\Resource\EventAdminResource::RESOURCE_NAME] = 'mobile.fevent_admin_api';

    Phpfox::getService('mobile.helper.feedPresentation')
        ->addEmbedTypes('fevent_comment', \Apps\Core_MobileApi\Api\Resource\FeedEmbed\StatusComment::class);
}

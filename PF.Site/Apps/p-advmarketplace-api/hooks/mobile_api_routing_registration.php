<?php


if (Phpfox::isModule('advancedmarketplace')) {
    /**
     * Define RestAPI services
     */
    $this->apiNames['mobile.advmarketplaceapi_api'] = \Apps\P_AdvMarketplaceAPI\Service\MarketplaceApi::class;
    $this->apiNames['mobile.advmarketplaceapi_category_api'] = \Apps\P_AdvMarketplaceAPI\Service\MarketplaceCategoryApi::class;
    $this->apiNames['mobile.advmarketplaceapi_photo_api'] = \Apps\P_AdvMarketplaceAPI\Service\MarketplacePhotoApi::class;
    $this->apiNames['mobile.advmarketplaceapi_invite_api'] = \Apps\P_AdvMarketplaceAPI\Service\MarketplaceInviteApi::class;
    $this->specialModules['advmarketplaceapi'] = 'advancedmarketplace';

    $this->objectResources['advancedmarketplace'] = \Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceResource::class;
    /**
     * Register Resource Name, This help auto generate routing for the resource
     * Note: resource name must be mapped correctly to resource api
     */
    $this->resourceNames[\Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceResource::RESOURCE_NAME] = 'mobile.advmarketplaceapi_api';
    $this->resourceNames[\Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceCategoryResource::RESOURCE_NAME] = 'mobile.advmarketplaceapi_category_api';
    $this->resourceNames[\Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplacePhotoResource::RESOURCE_NAME] = 'mobile.advmarketplaceapi_photo_api';
    $this->resourceNames[\Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceInviteResource::RESOURCE_NAME] = 'mobile.advmarketplaceapi_invite_api';
}

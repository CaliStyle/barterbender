<?php

namespace Apps\P_AdvMarketplaceAPI;

\Phpfox::getLib('module')
    ->addServiceNames([
        'mobile.advmarketplaceapi_api' => Service\MarketplaceApi::class,
        'mobile.advmarketplaceapi_category_api' => Service\MarketplaceCategoryApi::class,
        'mobile.advmarketplaceapi_photo_api' => Service\MarketplacePhotoApi::class,
        'mobile.advmarketplaceapi_invite_api' => Service\MarketplaceInviteApi::class
    ]);

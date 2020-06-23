<?php

namespace Apps\P_AdvEventAPI;

\Phpfox::getLib('module')
    ->addServiceNames([
        'mobile.fevent_api' => Service\EventApi::class,
        'mobile.fevent_category_api' => Service\EventCategoryApi::class,
        'mobile.fevent_photo_api' => Service\EventPhotoApi::class,
        'mobile.fevent_invite_api' => Service\EventInviteApi::class,
        'mobile.fevent_admin_api' => Service\EventAdminApi::class,
    ]);

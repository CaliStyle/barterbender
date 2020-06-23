<?php
$aValidation = [
    'number_ads_shown_per_view_on_feed' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of Ads Shown On Feed" must be greater than or equal to 0'),
    ],
    'number_user_view_to_display_ad' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of User Views Before Display Ads on Feed" must be greater than or equal to 0'),
    ],
    'position_of_feed_ad' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Position of Feed Ads on Feed List When It displays" must be greater than or equal to 0'),
    ],
    'maxium_number_of_html_ads' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum number of HTML ads shown on block 3" must be greater than or equal to 0'),
    ],
    'maxium_number_of_banner_ads' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum Number of Banner Ads on Each Block" must be greater than or equal to 0'),
    ],
    'html_ad_ajax_refresh_time' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"HTML Ad Refresh Time (Seconds)" must be greater than or equal to 0'),
    ],
    'number_user_view_to_display_html_ads' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of User Views Before Display HTML Ads" must be greater than or equal to 0'),
    ],
    'number_user_view_to_display_banner_ads' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of User Views Before Display Banner Ads" must be greater than or equal to 0'),
    ],
    'number_of_ad_updated_by_cron' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of Ad Updated by Cron" must be greater than or equal to 0'),
    ],
    'pay_later_request_expired_time' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Pay Later Request Expire Time (Days)" must be greater than 0'),
    ],
];


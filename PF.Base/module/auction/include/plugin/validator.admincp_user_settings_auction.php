<?php

$aValidation = [
    'photo_max_upload_size' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max file size for photos upload" must be greater than or equal to 0'),
    ],
    'how_much_is_user_worth_for_auction_publishing' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Fee for publishing an auction listing" must be greater than or equal to 0'),
    ],
    'how_much_is_user_worth_for_auction_featured' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Fee to feature an auction listing" must be greater than or equal to 0'),
    ],
    'points_auction' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Points for each auction this user created" must be greater than or equal to 0'),
    ],
    'minute_to_control_how_long_user_can_create_a_auction' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Minute to control how long user can create a auction" must be greater than or equal to 0'),
    ],
    'commission_for_admin_when_user_buy_auction' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Commission for admin when user buy auctio" must be greater than or equal to 0'),
    ],
];

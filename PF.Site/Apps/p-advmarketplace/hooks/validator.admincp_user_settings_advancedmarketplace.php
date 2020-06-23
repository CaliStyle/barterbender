<?php

$aValidation = [
    'max_upload_size_listing' => [
        'def' => 'int:required',
        'min' => '0',
        'title' => _p('advancedmarketplace_limit_must_greater_or_equal_0',
            ['var_name' => 'Max file size for photos upload']),
    ],
    'flood_control_advancedmarketplace' => [
        'def' => 'int:required',
        'min' => '0',
        'title' => _p('advancedmarketplace_limit_must_greater_or_equal_0',
            ['var_name' => 'How many minutes should a user wait before they can create another advancedmarketplace listing?']),
    ],
    'total_photo_upload_limit' => [
        'def' => 'int:required',
        'min' => '0',
        'title' => _p('advancedmarketplace_limit_must_greater_or_equal_0',
            ['var_name' => 'Number of photos that a user can upload to a advancedmarketplace listing each time']),
    ],
    'points_advancedmarketplace' => [
        'def' => 'int:required',
        'min' => '0',
        'title' => _p('advancedmarketplace_limit_must_greater_or_equal_0',
            ['var_name' => 'Number of activity points user get when adding a new listing']),
    ],
];

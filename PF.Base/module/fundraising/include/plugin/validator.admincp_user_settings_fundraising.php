<?php
$aValidation = [
    'max_upload_size_fundraising' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max file size for fundraising photos in kilobits (KB)" must be greater than or equal to 0'),
    ],
    'total_photo_upload_limit' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Control how many photos a user can upload to a campaign" must be greater than or equal to 0'),
    ],
    'points_fundraising' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Specify how many points the user will receive when adding a new fundraising" must be greater than or equal to 0'),
    ],
    'flood_control_fundraising' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"How many minutes should a user wait before they can submit another fundraising? " must be greater than or equal to 0'),
    ],
];

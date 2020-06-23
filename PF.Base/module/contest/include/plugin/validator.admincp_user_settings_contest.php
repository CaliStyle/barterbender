<?php
defined('PHPFOX') or exit('NO DICE!');

$aValidation = [
    'contest_ending_soon_fee' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Fee For a Contest to Appear in Ending Soon List" must be greater than or equal to 0'),
    ],
    'contest_publish_fee' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Fee to Publish a Contest" must be greater than or equal to 0'),
    ],
    'max_upload_size_contest' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max upload size" must be greater than or equal to 0'),
    ],
    'contest_premium_fee' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Fee For a Contest to Appear in Premium List" must be greater than or equal to 0'),
    ],
    'points_contest' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Points" must be greater than or equal to 0'),
    ],
    'contest_feature_fee' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Fee For a Contest to Appear in Featured List" must be greater than or equal to 0'),
    ]

];
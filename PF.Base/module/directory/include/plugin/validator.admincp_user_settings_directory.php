<?php
defined('PHPFOX') or exit('NO DICE!');

$aValidation = [
    'number_business_user_can_create' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Business for user to create" must be greater than or equal to 0'),
    ],
    'points_directory' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Points for create a new business" must be greater than or equal to 0'),
    ]

];
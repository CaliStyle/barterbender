<?php

$aValidation['ynaf_number_users_per_level_network_clients'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => 'Number of users per level must be greater than 0',
];

$aValidation['ynaf_minimum_request_points'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => 'Minimum Request Points must be greater than 0',
];

$aValidation['ynaf_maximum_request_points'] = [
    'def' => 'int:required',
    'min' => '1',
    'requirements'=>[
	    'min'=> '$ynaf_minimum_request_points',
    ],
    'title' => 'Maxim Request Points must be greater than 0, and greater than or equal to Minimum Request Points',
];

$aValidation['ynaf_delay_time_refunds_and_disputes'] = [
    'def' => 'int:required',
    'min' => '0',
    'title' => 'Delay time for refunds must be greater or equal to 0',
];

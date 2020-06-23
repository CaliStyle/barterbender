<?php
$aValidation = [
    'default_min_fundraising' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Default Minimum Donation Amount" must be greater than or equal to 0'),
    ],
    'default_signature_goal' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Default Campaign Goal" must be greater than or equal to 0'),
    ],
];


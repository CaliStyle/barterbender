<?php

$aValidation = [
    'number_of_fetched_donors' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Number of donors which will be fetched limit" must be greater than 0'),
    ],
    'max_size_for_donation_image_button' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Maximum file size for uploaded donation button image" must be greater than 0'),
    ]
];

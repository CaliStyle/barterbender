<?php

$aValidation = [
    'number_friend_suggestion_header_bar' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Number of item on Friend Suggestions block in Friend Request at header bar" must be greater than 0'),
    ],
    'number_people_may_you_know_header_bar' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Number of item on People You May Know in Friend Request at header bar" must be greater than 0'),
    ],
    'number_item_on_other_block' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Number of item on other blocks" must be greater than 0'),
    ],
];

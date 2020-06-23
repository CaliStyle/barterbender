<?php
$aValidation = [
    'fevent_number_of_event_upcoming_ongoing_block_home_page' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of Events on Upcoming Events, On Going Events Block" must be greater than or equal to 0'),
    ],
    'fevent_max_instance_repeat_event' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Maximum instances of each repeat events" must be greater than 0'),
    ],
    'subscribe_within_day' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Send email to subscribers information of new events which happen within days" must be greater than 0'),
    ]
];


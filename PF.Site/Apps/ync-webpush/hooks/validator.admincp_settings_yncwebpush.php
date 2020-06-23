<?php

header("X-XSS-Protection: 0");

$aValidation = [
    'yncwebpush_skip_times_to_stop_request_banner' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('The maximum skip time must be greater than 0'),
    ],
    'yncwebpush_notification_expiration_time' => [
        'def' => 'int:required',
        'min' => '0',
        'max' => '2419200',
        'title' => _p('Notification expiration time must be greater than 0 and less than 2419201'),
    ],
    'yncwebpush_time_period_to_appear_banner' => [
        'def' => 'string:required',
        'title' => _p('Time to reappear request banner cannot be empty'),
    ],
    'yncwebpush_text_of_banner_for_guest' => [
        'def' => 'string:required',
        'maxlen' => '250',
        'title' => _p('Text of banner for guest cannot be empty and it allows maximum 250 characters'),
    ],
    'yncwebpush_text_of_banner_for_user' => [
        'def' => 'string:required',
        'maxlen' => '250',
        'title' => _p('Text of banner for user cannot be empty and it allows maximum 250 characters'),
    ],
    'yncwebpush_server_key' => [
        'def' => 'string:required',
        'title' => _p('Project server key cannot be empty'),
    ],
    'yncwebpush_auth_code_snippet' => [
        'def' => 'string:required',
        'title' => _p('Auth Code Snippet cannot be empty'),
    ],
];

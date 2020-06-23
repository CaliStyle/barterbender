<?php

$aValidation = [
    'display_notification_seconds' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"The seconds when the Advanced Feed Notification box should hide" must be greater than 0'),
    ],
    'notification_refresh_time' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"The seconds before the notifications refresh" must be greater than 0'),
    ]
];

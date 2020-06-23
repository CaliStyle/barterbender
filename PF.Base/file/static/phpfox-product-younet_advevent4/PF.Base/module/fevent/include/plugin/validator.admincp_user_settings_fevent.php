<?php
$aValidation = [
    'points_fevent' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Points received when adding a events within the advanced event" must be greater than or equal to 0'),
    ],
    'max_upload_size_event' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max file size for events photos" must be greater than or equal to 0'),
    ],
    'total_mass_emails_per_hour' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Time to wait until user are allowed to send out another mass email" must be greater than or equal to 0'),
    ],
    'flood_control_events' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Time to wait before user can add new event" must be greater than or equal to 0'),
    ],
    'fevent_sponsor_price' => [
        'def' => 'currency',
        'min' => '0',
        'title' => _p('"Sponsor event price" must be greater than or equal to 0'),
    ],
    'max_upload_image_event' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max photos for events" must be greater than or equal to 0'),
    ],
];
<?php
defined('PHPFOX') or exit('NO DICE!');
$aValidation = [
    'ultimatevideo.points_ultimatevideo_video' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Activity points" must be greater than or equal to 0'),
    ],
    'ultimatevideo.points_ultimatevideo_playlist' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Activity points" must be greater than or equal to 0'),
    ],
    'ynuv_time_before_share_other_video' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('ultimatevideo_time_share_error_message'),
    ],
    'ynuv_max_file_size_photos_upload' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('ultimatevideo_file_size_upload_error_message'),
    ],
    'ynuv_file_size_limit_in_megabytes' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('ultimatevideo_file_size_limit_in_megabytes_error_message'),
    ],
    'ynuv_how_many_video_user_can_add' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('ultimatevideo_videos_user_can_add'),
    ],
    'ynuv_how_many_video_user_can_add_to_playlist' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('ultimatevideo_videos_for_playlist'),
    ],
    'points_ultimatevideo_playlist' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('ultimatevideo_points_playlist'),
    ]

];
<?php

$aValidation = [
    'ynccomment_comment_show_on_activity_feeds'=>[
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of comment will be shown on activity feeds" must be greater than or equal to 0'),
    ],
    'ynccomment_comments_show_on_item_details'=>[
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of comment will be shown on item details" must be greater than or equal to 0'),
    ],
    'ynccomment_replies_show_on_activity_feeds'=>[
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of replies will be shown on each comment on activity feeds" must be greater than or equal to 0'),
    ],
    'ynccomment_replies_show_on_item_details'=>[
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Number of replies will be shown on each comment on item details" must be greater than or equal to 0'),
    ],
];

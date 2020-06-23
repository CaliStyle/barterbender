<?php
defined('PHPFOX') or exit('NO DICE!');
$aValidation = [
    'limit_maximum_resume_active' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum resumes that are active for each user group" must be greater than or equal to 0'),
    ],
    'resume_category_numbers' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Maximum number of categories that a resume can include" must be greater than 0'),
    ],
    'resume_viewer_numbers' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum number of viewers that showed on "Who\'s viewed me"" must be greater than or equal to 0'),
    ],
    'maximum_resumes' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum resume" must be greater than or equal to 0'),
    ]
];
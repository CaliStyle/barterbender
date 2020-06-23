<?php
$aValidation = [
    'jobposting_maximum_upload_size_photo' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum upload size for photos (KB)" must be greater than or equal to 0'),
    ],
    'jobposting_fee_to_sponsor_company' => [
        'def' => 'int:required',
        'min' => '0',
        'title' => _p('"Fee to sponsor company" must be greater than or equal to 0'),
    ],
    'jobposting_maximum_upload_size_resume' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum upload size for resume (KB)" must be greater than or equal to 0'),
    ],
    'fee_feature_job' => [
        'def' => 'int:required',
        'min' => '0',
        'title' => _p('"Fee to feature Job" must be greater than or equal to 0'),
    ],
];

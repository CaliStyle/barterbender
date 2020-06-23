<?php

$aValidation = [
    'points_document' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Points received when creating new document" must be greater than or equal to 0',
            ['var_name' => 'Points received when creating new document']),
    ],
    'document_max_file_size' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum file size of documents uploaded" must be greater than or equal to 0',
            ['var_name' => 'Maximum file size of documents uploaded']),
    ],
    'document_max_image_size' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum file size of image uploaded" must be greater than or equal to 0',
            ['var_name' => 'Maximum file size of image uploaded']),
    ]
];

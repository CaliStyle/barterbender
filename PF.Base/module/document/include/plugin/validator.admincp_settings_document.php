<?php

$aValidation = [
    'new_documents_period'=>[
        'def' => 'int',
        'min' => '0',
        'title' => _p('"New Document Criteria" must be greater than or equal to 0'),
    ],
    'document_width' => [
        'def' => 'int',
        'min' => '1',
        'title' => _p('"Viewer Width" must be greater than or equal to 1'),
    ],
    'document_height' => [
        'def' => 'int',
        'min' => '1',
        'title' => _p('"Viewer Height" must be greater than 1'),
    ],

];

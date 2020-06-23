<?php
$aValidation = [
    'points_contactimporter_sentinvitations' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Points the user will receive when sent a invitation" must be greater than or equal to 0'),
    ],
    'points_invite' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Points the invitee and the inviter will receive upon a successfully request" must be greater than or equal to 0'),
    ],
    'points_contactimporter' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Points the user will receive when your invitation join this site" must be greater than or equal to 0'),
    ],
];
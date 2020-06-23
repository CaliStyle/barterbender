<?php

$aValidation['number_of_friend_list'] = [
    'def' => 'int:required',
    'min' => '0',
    'title' => '"Number of friend(s) in list" must be greater than or equal to 0',
];
$aValidation['number_of_old_message'] = [
    'def' => 'int:required',
    'min' => '0',
    'title' => 'Number of shown messages must be greater than or equal to 0',
];
$aValidation['placement_of_chat_frame'] = [
    'def' => 'int:required',
    'min' => '1',
    'max' => '2',
    'title' => 'Placement of chat frame must be 1 or 2',
];
$aValidation['number_of_friend_when_searching_friend_setting'] = [
    'def' => 'int:required',
    'min' => '0',
    'title' => '"Limit the number of friend when searching friend settings" must be greater than or equal to 0',
];
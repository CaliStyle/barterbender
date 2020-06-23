<?php

//$iGroupId = count($aCustomGroups) + 1;
$iGroupId = 999;

$aPlacesGroup = [
    'group_id' => $iGroupId,
    'module_id' => 'ynmember',
    'product_id' => 'ynmember',
    'user_group_id' => '0',
    'type_id' => 'user_profile',
    'phrase_var_name' => 'My Places',
    'is_active' => '1',
    'ordering' => '1',
];

$aCustomGroups[] = $aPlacesGroup;
$aGroupCache[$iGroupId] = true;
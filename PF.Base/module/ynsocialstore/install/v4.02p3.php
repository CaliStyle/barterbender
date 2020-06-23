<?php
defined('PHPFOX') or exit('NO DICE!');

$db = db();

$db->delete(':setting', 'module_id=\'ynsocialstore\' AND var_name=\'max_item_block_super_deals\'');

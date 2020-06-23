<?php

defined('PHPFOX') or exit('NO DICE!');


function ynff_install303p2()
{
    $oDatabase = Phpfox::getLib('database');

  $oDatabase->query("INSERT INTO `".Phpfox::getT('foxfavorite_setting')."` (`module_id`, `title`, `product`, `is_active`) VALUES
    ('directory', 'directory', 'younetco', 1);"
  );
}

ynff_install303p2();

?>
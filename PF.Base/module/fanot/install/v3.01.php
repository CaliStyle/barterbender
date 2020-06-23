<?php
  defined('PHPFOX') or exit('NO DICE!');
  
  function alter_notificaiton()
  {
    $oDb = Phpfox::getLib('phpfox.database');
    $sTable = Phpfox::getT('notification');
    
    if (!$oDb->isField($sTable, 'is_hide'))
    {
      $sql = "ALTER TABLE  `".$sTable."` ADD  `is_hide` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0'";
      $oDb->query($sql);
    }      
  }
  
  function alter_friend_request()
  {
    $oDb = Phpfox::getLib('phpfox.database');
    $sTable = Phpfox::getT('friend_request');
    
    if (!$oDb->isField($sTable, 'is_hide'))
    {
      $sql = "ALTER TABLE  `".$sTable."` ADD  `is_hide` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0'";
      $oDb->query($sql);
    }      
  }
  
  alter_notificaiton();
  alter_friend_request();
?>

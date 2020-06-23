<?php

defined('PHPFOX') or exit('NO DICE!');

$this->database()->query("DELETE FROM `".phpfox::getT('socialpublishers_modules')."` WHERE `id` = 13");$this->database()->query("UPDATE `".phpfox::getT('language_phrase')."`SET `text`= replace(`text`, ' \"{title}\"', '{title}') WHERE `text` LIKE '%\"{title}\"%' AND `product_id` LIKE 'younet_socialpublisher'");
?>
<?php

Phpfox::getLib('database')->query("DELETE FROM `" . Phpfox::getT('block') . "` WHERE `product_id` = 'younet_chat4' AND `component` = 'maincontent'");
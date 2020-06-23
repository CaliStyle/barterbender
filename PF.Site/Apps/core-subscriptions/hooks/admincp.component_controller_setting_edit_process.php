<?php
defined('PHPFOX') or exit('NO DICE!');

if($sModuleId == 'subscribe')
{
    $this->template()->setHeader('cache', [
        'head' => ['colorpicker/css/colpick.css' => 'static_script'],
    ]);
}
<?php
	defined('PHPFOX') or exit('NO DICE!');
    $iPageId = $this->request()->get('req2');
    if ($iPageId == 0)
    {
	  $iPageId = $this->request()->get('id');
    }

    Phpfox::getLib('session')->set('socialintegration_pageId', $iPageId);
?>
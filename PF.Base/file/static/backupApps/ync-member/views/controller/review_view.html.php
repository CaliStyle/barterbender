<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: view.html.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item_view">
    {$aItem.text}
    {module name='feed.comment'}
</div>
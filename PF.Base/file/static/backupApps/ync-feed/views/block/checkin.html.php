<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: display.html.php 4176 2012-05-16 10:49:38Z Raymond_Benc $
 * This fileis called from the form.html.php template in the feed module
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<a href="#" type="button" id="btn_ynfeed_display_check_in" class="activity_feed_share_this_one_link parent {if isset($aForms.location_name)}has_data{/if}" onclick="return false;" title="{_p var='check_in'}">
	<i class="ico ico-checkin-o"></i><span class="item-text">{_p var='check_in'}</span>
</a>

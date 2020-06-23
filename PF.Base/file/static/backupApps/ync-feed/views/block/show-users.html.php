<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Comment
 * @version 		$Id: rating.html.php 1251 2009-11-09 21:02:59Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="ynfeed_users_popup  popup-user-total-container">
    {foreach from=$aUsers item=aUser}
    <div class="popup-user-item">
        <div class="item-outer">
            <div class="item-media">
                {img user=$aUser suffix='_50_square' max_width=50 max_height=50}
            </div>
            <div class="item-name">
                {$aUser|user:'':'':30}
            </div>
        </div>
    </div>
    {/foreach}
</div>

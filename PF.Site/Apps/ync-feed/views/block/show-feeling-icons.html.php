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

<div class="ynfeed_feeling_icons_popup">
    {foreach from=$aFeelings item=aFeeling}
    <div class="ynfeed_feeling_icon_item">
        <img src="{$aFeeling.image}" title="{$aFeeling.code}" style="width: 50px; height: 50px;" onclick="$Core.ynfeed.replaceFeelingIcon('{$aFeeling.image}');return js_box_remove(this);">
    </div>
    {/foreach}
    <div class="clearfix"></div>
</div>

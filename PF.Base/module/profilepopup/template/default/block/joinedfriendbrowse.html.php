<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<style>
        div.row1, div.row2{
                margin-bottom: 0px;
                padding-bottom: 5px;
        }
</style>
{/literal}

{if !$iPage && !count($aFriends)}
<div class="extra_info">
        No joined friends found.
</div>
{else}
{foreach from=$aFriends name=friends item=aFriend}
<div class="no-popup row1{if $phpfox.iteration.friends == 1 && !$iPage} row_first{/if}">
        <div class="go_left" style="width:55px; text-align:center; float: left">
                {img user=$aFriend suffix='_50_square' max_width=50 max_height=50}	
        </div>
        <div style="margin-left:65px;">
                {$aFriend|user}
        </div>
        <div class="clear"></div>
</div>
{/foreach}
<div id="js_friend_mutual_browse_append_pager">
        {pager}
</div>
{if !$iPage}
<div id="js_friend_mutual_browse_append"></div>
{/if}
{/if}
<script type="text/javascript">
    $Behavior.globalInit();
</script>
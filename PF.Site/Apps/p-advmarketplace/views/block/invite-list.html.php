<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: list.html.php 1163 2009-10-09 08:02:14Z Anna_Eliasson $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if !PHPFOX_IS_AJAX}
<div class="advancedmarketplace-list-invite" id="js_mp_item_holder">
{/if}
{if count($aInvites)}
{foreach from=$aInvites name=invites item=aInvite}
    <div class="go_left t_center" style="width:20%; padding:4px;" id="js_mp_member_{$aInvite.invite_id}">
        <div class="js_mp_fix_holder" style="width:75px; margin:auto; position:relative;">
            {img user=$aInvite suffix='_50_square'}
        </div>
        <div class="p_4">
            {if !$aInvite.invited_user_id}
                {$aInvite.invited_email|hide_email}
            {else}
                {$aInvite|user}
            {/if}
        </div>
    </div>
    {if is_int($phpfox.iteration.invites / 3)}
    <div class="clear"></div>
    {/if}
{/foreach}
<div class="clear"></div>
{else}
<div class="extra_info">
{if $iType == 1}
    {phrase var='advancedmarketplace.no_visits'}
{else}
    {phrase var='advancedmarketplace.no_results'}
{/if}
</div>
{/if}
{pager}
{if !PHPFOX_IS_AJAX}
</div>
{/if}
{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
    $Core.loadInit();
</script>
{/literal}
{/if}

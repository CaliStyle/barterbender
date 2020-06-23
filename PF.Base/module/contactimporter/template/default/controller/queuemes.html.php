<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aQueues)}
<form method="post" action="{url link='current'}" id="js_form" class="yncotact_form_invitations">
    <div class="main_break yncontactimporter_queue">
        {foreach from=$aQueues name=queue item=aQueue}
        <div id="js_queue_{$aQueue.id}" class="js_selector_class_{$aQueue.id} {if is_int($phpfox.iteration.queue/2)}row1{else}row2{/if}{if $phpfox.iteration.queue == 1} row_first{/if}">
            <div class="go_left t_center">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aQueue.id}" id="check{$aQueue.id}" />
                </label>
            </div>
            <div class="go_left">     
                <div>                                  
				{$aQueue.count}. {if ( isset($aQueue.name) && $aQueue.name !='')} {$aQueue.name|shorten:50:'...'} {/if}  ({$aQueue.provider} ({$aQueue.friend_id}))
				</div>
            </div>
            <div class="t_right">
                <ul id = "resend_{$aQueue.id}" class="yncontact_invitations_action">
                	<li>
	                    <a title="{_p var='delete_queue'}" href="{url link='contactimporter.queuemes' page=$iPage del=$aQueue.id}"><i class="text-danger ico ico-close"></a>
                	</li>
                </ul>
            </div> 
            <div class="clear"></div>
        </div>
        {/foreach}
    </div>
</form>

{pager}
{moderation}
{else}
    {if $iPage ==0}
        <div class="extra_info">
            {_p var='there_are_no_queue_messages'}
            <ul class="action">
                <li><a href="{url link='contactimporter'}">{phrase var='invite.invite_your_friends'}</a></li>
            </ul>
        </div>
    {/if}
{/if}
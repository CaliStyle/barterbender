<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

{if $aContest.can_edit_contest}
	<li><a href="{url link="contest.add" id=$aContest.contest_id}">{phrase var='contest.edit'}</a></li>
{/if}

{if $aContest.can_feature_contest}
	 <li id="js_contest_feature_{$aContest.contest_id}">
        {if $aContest.is_feature}
                <a href="#" title="{phrase var='contest.un_feature_this_contest'}" onclick="$.ajaxCall('contest.feature', 'contest_id={$aContest.contest_id}&amp;type=0', 'GET'); return false;">{phrase var='contest.un_feature'}</a>
        {else}
                <a href="#" title="{phrase var='contest.feature_this_contest'}" onclick="$.ajaxCall('contest.feature', 'contest_id={$aContest.contest_id}&amp;type=1', 'GET'); return false;">{phrase var='contest.feature'}</a>
        {/if}
    </li>
{/if}


{if $aContest.can_premium_contest}
	 <li id="js_contest_premium_{$aContest.contest_id}">
        {if $aContest.is_premium}
                <a href="#" title="{phrase var='contest.un_premium_this_contest'}" onclick="$.ajaxCall('contest.premium', 'contest_id={$aContest.contest_id}&amp;type=0', 'GET'); return false;">{phrase var='contest.un_premium'}</a>
        {else}
                <a href="#" title="{phrase var='contest.premium_this_contest'}" onclick="$.ajaxCall('contest.premium', 'contest_id={$aContest.contest_id}&amp;type=1', 'GET'); return false;">{phrase var='contest.premium'}</a>
        {/if}
    </li>
{/if}

{if $aContest.can_ending_soon_contest}
	 <li id="js_contest_ending_soon_{$aContest.contest_id}">
        {if $aContest.is_ending_soon}
                <a href="#" title="{phrase var='contest.un_ending_soon_this_contest'}" onclick="$.ajaxCall('contest.endingSoon', 'contest_id={$aContest.contest_id}&amp;type=0', 'GET'); return false;">{phrase var='contest.un_ending_soon'}</a>
        {else}
                <a href="#" title="{phrase var='contest.ending_soon_this_contest'}" onclick="$.ajaxCall('contest.endingSoon', 'contest_id={$aContest.contest_id}&amp;type=1', 'GET'); return false;">{phrase var='contest.ending_soon'}</a>
        {/if}
    </li>
{/if}

{if $aContest.can_close_contest}
        <li id="js_contest_close_{$aContest.contest_id}">
			<a href="#" title="{phrase var='contest.close_this_contest'}" onclick="yncontest.confirmActionOnContest({$aContest.contest_id},'close'); return false;">{phrase var='contest.close'}</a>

        </li>
{/if}


{if $aContest.can_delete_contest}
        <li id="js_contest_delete__{$aContest.contest_id}">
			<a href="#" title="{phrase var='contest.delete_this_contest'}" onclick="yncontest.confirmActionOnContest({$aContest.contest_id},'delete'); return false;">{phrase var='contest.delete'}</a>

        </li>
{/if}

{if $aContest.can_publish_contest}
        <li id="js_contest_publish__{$aContest.contest_id}">
			<a href="#" title="{phrase var='contest.publish_this_contest'}" onclick="yncontest.confirmActionOnContest({$aContest.contest_id},'publish'); return false;">{phrase var='contest.publish'}</a>

        </li>
{/if}


{if $aContest.can_register_service}
	<li id="js_contest_register_service__{$aContest.contest_id}">
		<a href="#" title="{phrase var='contest.register_services'}" onclick="yncontest.addContest.showPayPopup({$aContest.contest_id}); return false;">{phrase var='contest.register_services'}</a>
    </li>
{/if}

{if $aContest.can_approve_deny_contest}
    <li id="js_contest_approve__{$aContest.contest_id}">
        <a href="#" title="{phrase var='contest.approve_this_contest'}" onclick="yncontest.confirmActionOnContest({$aContest.contest_id},'approve'); return false;">{phrase var='contest.approve'}</a>
    </li>

    <li id="js_contest_deny_{$aContest.contest_id}">
        <a href="#" title="{phrase var='contest.deny_this_contest'}" onclick="yncontest.confirmActionOnContest({$aContest.contest_id},'deny'); return false;">{phrase var='contest.deny'}</a>
    </li>
{/if}

{if $aContest.can_view_wining_entries_action}
    <li id="js_contest_view_winning__{$aContest.contest_id}">
        <a href="{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name view=winning}" title="{phrase var='contest.view_winning_entries'}" >{phrase var='contest.view_winning_entries'}</a>
    </li>
{/if}

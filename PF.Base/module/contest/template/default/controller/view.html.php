<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<!-- View contest details -->
	{if isset($sContestWarningMessage) && $sContestWarningMessage}
	<div class="message js_moderation_off" id="js_approve_message">{$sContestWarningMessage}</div>
	{/if}
<div class="wrapper_contest">

	{if $sView == 'entry'}
	<!-- View entry details -->
	{template file='contest.block.entry.view'}

	{else}
	

	<a title="{$aContest.contest_name|clean}" href="{permalink module='contest' id=$aContest.contest_id title=$aContest.contest_name}"><h2 class="yc_view_contest_name fw-bold">{$aContest.contest_name|clean|shorten:250:'...'|split:20}</h2></a>
	<div class="ycontest_info">
		{phrase var='contest.create_by'}: <a href="{url link=''}{$aContest.user_name}/">{$aContest.full_name}</a>
		<span>.</span> {phrase var='contest.contest_type'}: <a  href="{$aContest.link_type_contest}">{$aContest.type_contest}</a>
		{if isset($aContest.sCategory) && $aContest.sCategory!=""} <span>.</span> {phrase var='contest.category'}: {$aContest.sCategory}{/if}
	</div>

	{if $aContest.have_action_on_contest}
	<div class="item_bar contest_view_action">
		<div class="item_bar_action_holder">
			<a role="button" data-toggle="dropdown" class="item_bar_action"><i class="ico ico-gear-o"></i></a>
			<ul class="dropdown-menu dropdown-menu-right">
				{template file='contest.block.contest.action-link'}
			</ul>
		</div>
	</div>
	{/if}

	{if $sView != 'add'}
	<div class="ycontest_details">
		<!-- Duration -->
		<div class="ycleft_details">
			<div class="start_end_date">
				<h3>{phrase var='contest.contest_duration'}</h3>
				<p><b>{phrase var='contest.start'}:</b> {$aContest.begin_time_parsed}</p>
				<p><b>{phrase var='contest.end'}:</b> {$aContest.end_time_parsed}</p>
				<span class="day_lefts">
					{if $aContest.contest_timeline == 'end' || $aContest.is_manual_closed}
						{phrase var='contest.end'}
					{elseif $aContest.contest_timeline == 'on_going'}
						{$aContest.contest_countdown}
					{elseif $aContest.contest_timeline == 'opening'}
						{phrase var='contest.opening'}
					{/if}
				</span>
				{if $aContest.is_manual_closed}
				<div class="manual_closed">
					{if empty($aContest.user_close)}
					Closed manually
					{else}
					Closed by {$aContest.user_close|user}
					{/if}
				</div>
				{/if}
			</div>
			<div class="yc_entry_voting ycsubmit_entry">
				<p>{phrase var='contest.submit_entries'} <i>{if $aContest.is_manual_closed}{elseif $aContest.submit_timeline == 'opening'}({phrase var='contest.opening'}){elseif $aContest.submit_timeline == 'on_going'}({phrase var='contest.on_going'}){/if}</i></p>
				{if $aContest.submit_timeline == 'end' || $aContest.is_manual_closed}
				<span class="count_down">{phrase var='contest.end'}</span>
				{elseif $aContest.submit_timeline == 'on_going'}
				<span class="count_down">{$aContest.submit_countdown}</span>
				{elseif $aContest.submit_timeline == 'opening'}
				<span class="view_time"><b>{phrase var='contest.start'}:</b> {$aContest.start_time_parsed}</span>
				{/if}
			</div>
			<div class="yc_entry_voting ycvoting">
				<p>{phrase var='contest.voting'} <i>{if $aContest.is_manual_closed}{elseif $aContest.vote_timeline == 'opening'}({phrase var='contest.opening'}){elseif $aContest.vote_timeline == 'on_going'}({phrase var='contest.on_going'}){/if}</i></p>
				{if $aContest.vote_timeline == 'end' || $aContest.is_manual_closed}
				<span class="count_down">{phrase var='contest.end'}</span>
				{elseif $aContest.vote_timeline == 'on_going'}
				<span class="count_down">{$aContest.vote_countdown}</span>
				{elseif $aContest.vote_timeline == 'opening'}
				<span class="view_time"><b>{phrase var='contest.start'}:</b> {$aContest.start_vote_parsed}</span>
				{/if}
			</div>
		</div>
		<!-- //Duration -->

		<!-- Statistic -->
		<ul class="yc_view_statistic">
			<li><h4>{phrase var='contest.contest_statistics'}</h4></li>
			<li class="ycstat ycparticipants">
				<span>{phrase var='contest.participants'}:</span>
				<b>{$aContest.total_participant}</b>
			</li>
			<li class="ycstat ycentries">
				<span>{phrase var='contest.entries'}:</span>
				<b>{$aContest.total_entry}</b>
			</li>
			<li class="ycstat yclikes">
				<span>{phrase var='contest.likes'}:</span>
				<b>{$aContest.total_like}</b>
			</li>
			<li class="ycstat ycviews">
				<span>{phrase var='contest.views'}:</span>
				<b>{$aContest.total_view}</b>
			</li>
		</ul>
		<!-- //Statistic -->
	</div>
	<div class="clear"></div>
	{/if}

	<div class="line_sep"></div>

    {if !empty($sAddThisBtn)}
        {$sAddThisBtn}
    {else}
    <!-- AddThis Button BEGIN -->
    <div class="addthis_toolbox addthis_32x32_style">
        <a class="addthis_button_preferred_1"></a>
        <a class="addthis_button_preferred_2"></a>
        <a class="addthis_button_preferred_3"></a>
        <a class="addthis_button_preferred_4"></a>
        <a class="addthis_button_compact"></a>
    </div>
    <!-- AddThis Button END -->
    {/if}

    {literal}
    <script type="text/javascript">
        $Behavior.initAddthis = function(){
            PF.event.on('on_page_change_end', function(){
                addthis.toolbox('.addthis_toolbox');
            });
        };
    </script>
    {/literal}

    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={$sAddThisKey}"></script>

	{if $sView != 'add'}
	<!-- tab -->
	<input type="hidden" id='init_tab_one_time' value="0">
	<div class='yncontest_tab'>
		<div id="tabs_view" class="yc_view_tab">
			<ul>
				<li><a href="#tabs-1">{phrase var='contest.description'}</a></li>
				<li><a href="#tabs-2">{phrase var='contest.award'}</a></li>
				<li><a href="#tabs-3">{phrase var='contest.announcement'}</a></li>
			</ul>
			<div id="tabs-1">
				{template file='contest.block.contest.contest-description'}
			</div>
			<div id="tabs-2">
				<div class="yc_detail_contes item_view_contentt">
					{$aContest.award_description_show}
				</div>
			</div>
			<div id="tabs-3">
				{module name='contest.announcement.list'}
			</div>
		</div>
	</div>
	<!-- End tab -->
	{/if}

	<!-- View participants of contest -->
	{if $sView == 'participants'}
	<div class="block yc_content_block">
		<div class="title">
			<span>{phrase var='contest.participants'}</span>
		</div>
		<div class="content">
			{module name='contest.participant.participant_contest'}
		</div>
	</div>

	<!-- View winning entries -->
	{elseif $sView == 'winning'}
	<div class="block yc_content_block">
		<div class="title">
			<span>{phrase var='contest.winning_entries'}</span>
		</div>
		<div class="content">
			{module name='contest.entry.winning-entries'}
		</div>
	</div>

	<!-- Submit entry -->
	{elseif $sView == 'add'}
		{module name='contest.entry.add'}

	<!-- Default view of contest -->
	{else}
		{if count($aEntries)>0}
			<div class="block yc_content_block">
				<div class="title">
					<span>{phrase var='contest.entries'}</span>
				</div>
				<div class="content">
					{if $aContest.type_name == 'blog' || $aContest.type_name == 'music'}
						<div  class="wrap_list_items">
							{foreach from=$aEntries name=entry item=aEntry}
								{template file='contest.block.entry.listing-item-entries'}
							{/foreach}
						</div>
					{else}
						<div class="wrap_list_items list_items_tabs">
							{foreach from=$aEntries name=entry item=aEntry}
								{template file='contest.block.entry.listing-item-entries-large'}
							{/foreach}
						</div>
					{/if}
				</div>
			</div>

			{if !isset($is_hidden_action) || $is_hidden_action!=1}
				{moderation}
			{/if}
		{else}
			<div class="block">
				{phrase var='contest.no_entry_found'}
			</div>
		{/if}

	   {template file='contest.block.pager'}
	{/if}

	<div {if $aContest.contest_status != 4 && $aContest.contest_status != 5}style="display:none;" class="js_moderation_on"{/if}>
		<div class="item-detail-feedcomment">
			{module name='feed.comment'}
		</div>
	</div>

	{if isset($announcement) && $announcement!=0}
	<script type="text/javascript">
	$Behavior.defaultAnnouncement = function(){l}
		$('.ui-tabs-nav li').eq(2).find('a').trigger('click');
	{r}
	</script>
	{/if}

	<script type="text/javascript">
	$Behavior.yncontestInitilizeTabView = function() {l}
		yncontest.initForTabView();
	{r};
	$Behavior.initLoadContest = function(){l}
		/*$Core.loadInit = yncontest.overridedLoadInitForTabView;*/
	{r}
	</script>

	{if isset($bIsShowRegisterService) && $bIsShowRegisterService}
	<script type="text/javascript">
	setTimeout(function() {l}
		yncontest.addContest.showPayPopup({$aContest.contest_id});
	{r}, 2000);
	</script>
	{/if}

	{/if}
</div>

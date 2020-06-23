{if $aSaRows}
<div class="table-responsive">
	<table class="table table-striped" cellpadding="0" cellspacing="0">
		<tr>
			<th class="ynsaFirstColumn">{phrase var='start_date'}</th>
			<th>{phrase var='end_date'}</th>
			<th>{phrase var='ad'}</th>
			<th>{phrase var='campaign'}</th>
			<th>{phrase var='reach'}</th>
			<th>{phrase var='impressions'}</th>
			<th>{phrase var='clicks'}</th>
			<th>{phrase var='unique_clicks'}</th>
		</tr>
		{foreach from=$aSaRows name=arow item=aRow}
		<tr{if is_int($phpfox.iteration.arow/2)} class="on"{/if}>
			<td class="t_center ynsaFirstColumn">
				{$aRow.start_date_text}
			</td>
			<td title="{phrase var='end_date'}">{$aRow.end_date_text}</td>
			<td title="{phrase var='ad'}">
				{$aRow.ad_title|clean}
			</td>
			<td title="{phrase var='campaign'}">{$aRow.campaign_name}</td>
			<td title="{phrase var='reach'}">{$aRow.total_reach}</td>
			<td title="{phrase var='impressions'}">{$aRow.total_impression}</td>
			<td title="{phrase var='clicks'}">{$aRow.total_click}</td>
			<td title="{phrase var='unique_clicks'}">{$aRow.total_unique_click}</td>
		</tr>
		{/foreach}
	</table>
</div>
{module name='socialad.paging'}
{else}
	<div class="extra_info"> {phrase var='no_data_found'}</div>
{/if}


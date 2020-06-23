
<table class="ynsaTable " cellpadding="0" cellspacing="0">
	<tr>
		<th>{phrase var='date'}</th>
		<th>{phrase var='impressions'}</th>
		<th class="ynsaLastColumn">{phrase var='clicks'}</th>
	</tr>
{foreach from=$aSaDatas name=adata item=aData}
	<tr{if is_int($phpfox.iteration.adata/2)} class="on"{/if}>
		<td title="{phrase var='date'}" class="t_center ynsaFirstColumn"> {$aData.start_date_text} </td>
		<td title="{phrase var='impressions'}" class="t_center">{$aData.total_impression}</td>
		<td title="{phrase var='clicks'}" class="t_center ynsaLastColumn">{$aData.total_click}</td>
	</tr>
{foreachelse}	
	<tr>
		<td colspan="5">
			<div class="extra_info">
				{phrase var='no_statistics_found'}
			</div>
		</td>
	</tr>
{/foreach}
</table>

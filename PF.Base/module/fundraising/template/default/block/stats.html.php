<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
	<style>
		.ynfr_stats_title{
			font-size: 14px;
		}

		td{
			padding-bottom: 5px;
		}

	</style>
{/literal}

<table width="100%"  style="border-collapse:separate; border-spacing:0px">
	<tr>
		<td>
			<a href="{url link='fundraising' view='ongoing'}"> 
				<span class="ynfr_stats_title">{phrase var='on_going'} :</span>
			</a>
		&nbsp;{$aStats.ongoing}</td>
	</tr>

	<tr>
		<td>
			<a href="{url link='fundraising' view='reached'}"> 
				<span class="ynfr_stats_title">{phrase var='reached'} :</span>
			</a>

		&nbsp;{$aStats.reached}</td>
	</tr>

	<tr>
		<td>
			<a href="{url link='fundraising' view='expired'}">
				<span class="ynfr_stats_title">{phrase var='expired'} :</span>
			</a>

		&nbsp;{$aStats.expired}</td>
	</tr>

	<tr>
		<td>
			<a href="{url link='fundraising' view='closed'}">
				<span class="ynfr_stats_title">{phrase var='closed'} :</span>
			</a>
		
		&nbsp;{$aStats.closed}</td>
	</tr>
</table>
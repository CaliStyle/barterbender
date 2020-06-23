<div>
	{if count($aPolls) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}
	<div class="yndirectory-content-row">
	{foreach from=$aPolls item=aPoll key=iKey name=polls}
		<div class="yndirectory-poll-item">
			<div class="yndirectory-content-row-image">
				{img user=$aPoll suffix='_50_square' max_width=50 max_height=50}
			</div>
			<div class="yndirectory-content-row-info">
				<div class="yndirectory-item-title"><a href="{permalink module='poll' id=$aPoll.poll_id title=$aPoll.question}" id="poll_inner_title_{$aPoll.poll_id}" class="link">{$aPoll.question|clean|shorten:55:'...'|split:40}</a></div>
				<div class="yndirectory-item-info">{$aPoll.time_stamp|convert_time} {phrase var='poll.by'} {$aPoll|user}</div>
			</div>
			<div class="poll_large_image" {if $aPoll.image_path}style="background-image: url('{img server_id=$aPoll.server_id path='poll.url_image' file=$aPoll.image_path suffix='' return_url=true}')"{/if})></div>
		</div>
	{/foreach}
	</div>
	<div class="clear"></div>
	{module name='directory.paging'}	
</div>

{literal}
<style type="text/css">
	.poll_answer_percentage {
	    line-height: 8px;
	}	
</style>

{/literal}
{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}
<?php

defined('PHPFOX') or exit('NO DICE!');
?>

<!-- Main entry -->
<div class="yc_small_item">

    {if $aItem.type == 1 || $aItem.type == 4}
    	{if $aItem.user_image != ''}
    	<div class="item_left" style="background-image:url('{img return_url=true user=$aItem suffix='_100_square'}');"></div>
    	{else}
		<div class="item_left" >
        	{img user=$aItem suffix='_50_square'}
    	</div>
    	{/if}
    {else}
    	<div class="item_left" style="background-image:url('{if $aItem.type == 2}{img return_url=true server_id=$aItem.server_id path='core.url_pic' file=$aItem.image_path suffix='_200'}
	    {elseif $aItem.type == 3}{img return_url=true server_id=$aItem.server_id path='core.url_pic' file=$aItem.image_path suffix='_120'}
	    {/if}');"></div>
    {/if}

	<div class="item_right">
		<input type="hidden" value="{$aItem.user_id}" name="val[{$aItem.entry_id}][user_id]"/>
		<input type="hidden" value="{$aItem.entry_id}" name="val[{$aItem.entry_id}][entry_id]"/>
        <div>
			<p><a class="small_title" style="display: inline-block" href="{permalink module='contest' id=$aItem.contest_id title=$aItem.contest_name}entry_{$aItem.entry_id}/" title="{$aItem.title|clean}">
				{$aItem.title}
			</a></p>
			<p class="extra_info">
			{phrase var='contest.by'} <a href="{url link=''}{$aItem.user_name}/">{$aItem.full_name}</a>
			</p>
			<div class="extra_info">
				<div>
					<div class="table_left yc_set_wining">
						{phrase var='contest.award'}:
					</div>
					<div class="table_right">
						 <input type="text" class="form-control" name='val[{$aItem.entry_id}][award]'/>
					</div>
				</div>

				<div class="table_clear">
					<div class="table_left yc_set_wining">
						{phrase var='contest.rank'}:
					</div>
					<div class="table_right">
						<select class="form-control" name='val[{$aItem.entry_id}][rank]'>
							{for $i = 1; $i <= $abc; $i++}
								<option value='{$i}'>{$i}</option>
							{/for}
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

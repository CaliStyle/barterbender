<div class="ync-feature">
	<div class="ync-feature-info">
		<div class = "ync-left">
			<a href="javascript:void(0);" title="">					
			{if $item.image_path != ""}
				<span class="ynjp-featured-bg-img" style="background-image:url({img return_url=true server_id=$item.image_server_id path='core.url_pic' file="jobposting/".$item.image_path suffix='_1024' max_width=241 max_height=150});">
				</span>
			{else}
				<span class="ynjp-featured-bg-img" style="background-image:url({$coreUrlModule}jobposting/static/image/default/default_ava.png)">
				</span>
			{/if}
			</a>			
		</div>
		<div class = "ync-feature-content">
			<div id="js_coupon_edit_title[id]" class="ync-title">
				<a href="{permalink module='jobposting' id=$item.job_id title=$item.title}" id="js_coupon_edit_inner_title[id]" class="link ajax_link">{$item.title|clean|shorten:55:'...'|split:50}</a>
			</div>
			<p class="ynjp_featureContent_coName"><i class="fa fa-building"></i>&nbsp; {$item.name}</p>
			<p class="ynjp_featureContent_location"><i class="fa fa-map-marker"></i>&nbsp; {$item.location}</p>
			<p class="ynjp_featureContent_industry">
				<i class="fa fa-folder-open"></i>&nbsp; 
				{if isset($item.industrial_phrase) && $item.industrial_phrase!=""}{$item.industrial_phrase}{else}N/A (Industry){/if}
			</p>
			<p><i class="fa fa-clock-o"></i>&nbsp; {phrase var='expire_on'}: {$item.time_expire_phrase}</p>
		</div>
		<a href="{permalink module='jobposting' id=$item.job_id title=$item.title}" class="ynjp_viewmore">{phrase var='view_more'}...</a>
	</div>
</div>               
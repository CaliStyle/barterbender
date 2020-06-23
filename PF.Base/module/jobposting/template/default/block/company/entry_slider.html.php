<div class="ync-feature">
	<div class="ync-feature-info">
		<a href="{permalink module='jobposting.company' id=$item.company_id title=$item.name}"  title="">

			{if $item.image_path != ""}
			<span class="ync-featured-bg-img" style="background-image:url({img return_url=true server_id=$item.server_id path='core.url_pic' file="jobposting/".$item.image_path suffix='_1024' max_width=700 max_height=500});">

			</span>
			{else}
				
				<span class="ync-featured-bg-img" style="background-image:url({$coreUrlModule}jobposting/static/image/default/default_ava.png)">
				</span>
			{/if}
		</a>			

		<div class = "ync-feature-content">
			<div class="ync-feature-content-box">
				<div id="js_coupon_edit_title[id]" class="ync-title">
					<a href="{permalink module='jobposting.company' id=$item.company_id title=$item.name}" id="js_coupon_edit_inner_title[id]" class="link ajax_link">{$item.name|clean|shorten:55:'...'|split:50}</a>
				</div>

				<div class="ync_featureDesc">
					{$item.description_parsed_phrase|clean|shorten:150:'...'|split:50}		
				</div>
			</div>

			<div class="ync-feature-content-box">
				<p class="ync_featureContent_industry">
					<i class="fa fa-folder-open"></i>&nbsp; 
					 {if isset($item.industrial_phrase) && $item.industrial_phrase!=""}{$item.industrial_phrase}{else}{phrase var='n_a'} {phrase var='industry'}{/if}
				</p>

				<p class="ync_featureContent_location"><i class="fa fa-map-marker"></i>&nbsp; {$item.location}</p>

				<p class="ync_featureContent_users">
					<i class="fa fa-users"></i>&nbsp; 
					{if isset($item.size_from) && (int)$item.size_from > 0
						&& isset($item.size_to) && (int)$item.size_to > 0
					}
					<b>{$item.size_from} - {$item.size_to}</b> {phrase var='employees'}&nbsp;.&nbsp;
					{/if}
					<b>{$item.total_follow}</b> {phrase var='followers'}
				</p>	
			</div>
		</div>
		
			<a href="{permalink module='jobposting.company' id=$item.company_id title=$item.name}" class="ynjp_viewmore">{phrase var='view_more'}...</a>
		
	</div>
</div>  
        
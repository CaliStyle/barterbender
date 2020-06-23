<div class="ync-item-content">
	<!-- Image content -->
	<div class="ynjp-image-blockCol">
        <a title="Postcards" href="{permalink module='jobposting.company' id=$item.company_id title=$item.name}">
			{if $item.image_path != ""}
				{img server_id=$item.server_id path='core.url_pic' file="jobposting/".$item.image_path suffix='_50' max_width='50' max_height='50' class='js_mp_fix_width'}
			{else}
				<img src="{$coreUrlModule}jobposting/static/image/default/default_ava.png" style="max-width:50px;max-height:50px;" />
			{/if}
		</a>
    </div>
	<!-- Information content -->
	<div class="ync_title_info">
	    <p class="ync-title" id="js_coupon_edit_title50">
	        <strong><a class="link ajax_link" id="js_coupon_edit_inner_title50" href="{permalink module='jobposting.company' id=$item.company_id title=$item.name}">{$item.name|clean|shorten:35:'...'|split:50}</a></strong>
	    </p>
	    <div class="extra_info">	       
	        {if $type_id==1}
		        <p>
		        	{$item.total_job} {phrase var='job_s'}
		        </p>
	        {else}
	        	{$item.total_follow} {phrase var='follower_s'}
	        {/if}	        
	    </div>
	</div>
	<div class="clear"></div>
</div>

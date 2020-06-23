
<div id="js_jp_job_entry_{$aJob.job_id}" class="ync-item-content ync_titleMiddle_content {if $list_show=='List'}ynjp_middleContent_listView_holder{/if}">
	{if $bIsShowModerator}
    <div class="moderation_row">
        <label class="item-checkbox">
            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aJob.job_id}" id="check{$aJob.job_id}" />
            <i class="ico ico-square-o"></i>
        </label>
    </div>
	{/if}


	<div class="ynjp_middleContent_holder clearfix {if $list_show=='List'}photo_row_height image_hover_holder{/if}">
        {if $aJob.action}
        <div class="item_bar">
            <div class="dropdown">
                <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="ico ico-gear-o"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    {template file='jobposting.block.job.action-link'}
                </ul>
            </div>
        </div>
        {/if}
		<!-- Image content -->
		{if $list_show!="List"}
		<div class="ynjp-image-blockCol photo_row_height image_hover_holder">
		{/if}
			<a href="#" class="image_hover_menu_link">Link</a>
			{if $list_show!="List"}

				{if $aJob.image_path}
				<a href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}"
				   class="ynjp_bg-img"
				style="background-image: url({img server_id=$aJob.image_server_id path='core.url_pic' file="jobposting/".$aJob.image_path suffix='_500'
					max_width='120' max_height='115' return_url=true})"></a>

				{else}
				<a href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}" class="ynjp_bg-img"
				style="background-image: url({$coreUrlModule}jobposting/static/image/default/default_ava.png)"></a>

				{/if}


			{/if}
			{if $aJob.canDeleteJob}
			<div class="video_moderate_link"><a href="#{$aJob.job_id}" class="moderate_link" rel="">Moderate</a></div>
			{/if}
		{if $list_show!="List"}
		</div>
		{/if}
		
		{if $aJob.is_featured == 1} 
			<div class="{if $list_show!='List'}small_feature_icon{else}small_feature_icon_listView{/if} small_ynjp_icon_holder">
				<span>{phrase var='feature'}</span>
			</div>		
		{/if}		
		<!-- Information content -->
		<div class="ync_titleMiddle_info">
			<p class="ync-title">
				<strong>
					<a class="link ajax_link" href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}">
						{$aJob.title}
					</a>
				</strong>
			</p>
			<div class="extra_info">
				<p class="ynjp_featureContent_coName">
					
					<a href="{permalink module='jobposting.company' id=$aJob.company_id title=$aJob.name}"><i class="fa fa-building"></i> &nbsp;	{$aJob.name}</a>
				</p>
				{if $aJob.location}
				<p>{$aJob.location}</p>
				{/if}
				
				<p class="ynjp_featureContent_industry"><i class="fa fa-folder-open"></i> &nbsp;{if isset($aJob.industrial_phrase) && $aJob.industrial_phrase!=""}{$aJob.industrial_phrase}{else}{phrase var='n_a'} {phrase var='industry'}{/if} </p>
				<p><i class="fa fa-clock-o"></i>&nbsp; {phrase var='expire_on'}: {$aJob.time_expire_phrase}</p>
			</div>
            <!-- Follow/Favorite -->
            <div class="ynjp_link_action">
                {if isset($sView) && $sView=='favorite'}
                <a href="#" onclick="$.ajaxCall('jobposting.unfavorite', 'type=job&id={$aJob.job_id}'); return false;">{phrase var='unfavorite'}</a>
                {/if}
                
                {if isset($sView) && $sView=='following'}
                <a href="#" onclick="$.ajaxCall('jobposting.unfollow', 'type=job&id={$aJob.job_id}'); return false;">{phrase var='unfollow'}</a>
                {/if}
            </div>
		</div>
	</div>
	<div class="clear"></div>
</div>

<div id="js_jp_company_entry_{$aCompany.company_id}"
	 class="ync-item-content ync_titleMiddle_content {if $list_show=='List'}ynjp_middleContent_listView_holder{/if}">
	{if $bIsShowModerator}
	<div class="moderation_row">
		<label class="item-checkbox">
            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aCompany.company_id}" id="check{$aCompany.company_id}">
            <i class="ico ico-square-o"></i>
        </label>
	</div>
	{/if}
	<div class="ynjp_middleContent_holder clearfix {if $list_show=='List'}photo_row_height image_hover_holder{/if}">
		{if $aCompany.action && $aCompany.canDeleteCompany}
			<div class="item_bar">
				<div class="dropdown">
	                <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
	                    <i class="ico ico-gear-o"></i>
	                </a>

			        <ul class="dropdown-menu dropdown-menu-right">
			            {template file='jobposting.block.company.action-link'}
			        </ul>
				</div>
			</div>
		{/if}
		<!-- Image content -->
		{if $list_show!="List"}
		<div class="ynjp-image-blockCol photo_row_height image_hover_holder">
		{/if}
			<a href="#" class="image_hover_menu_link">{phrase var='link'}</a>

			{if $list_show!="List"}		

			{if $aCompany.image_path != ""}
			<a href="{permalink module='jobposting.company' id=$aCompany.company_id title=$aCompany.name}" class="ynjp_bg-img" 
			style="background-image: url({img server_id=$aCompany.server_id path='core.url_pic' file="jobposting/".$aCompany.image_path suffix='_500' max_width='120' max_height='115' return_url=true})"></a>
			
				{else}
				<a href="{permalink module='jobposting.company' id=$aCompany.company_id title=$aCompany.name}" class="ynjp_bg-img" 
				style="background-image: url({$coreUrlModule}jobposting/static/image/default/default_ava.png)"></a>

				{/if}

			{/if}
			{if $aCompany.canDeleteCompany}
			<div class="video_moderate_link"><a href="#{$aCompany.company_id}" class="moderate_link" rel="">{phrase var='moderate'}</a></div>
			{/if}			
		{if $list_show!="List"}		
		</div>
		{/if}
		{if $aCompany.is_sponsor == 1}
		<div class="{if $list_show!='List'}small_sponsored_icon{else}small_sponsored_icon_listView{/if} small_ynjp_icon_holder">
			<span>{phrase var='sponsored'}</span>
		</div>
		{/if}		
		<!-- Information content -->
		<div class="ync_titleMiddle_info" >
			<p class="ync-title">
				<strong>
					<a class="link ajax_link" href="{permalink module='jobposting.company' id=$aCompany.company_id title=$aCompany.name}">
						{$aCompany.name|shorten:20:'...'}
					</a>
				</strong>
			</p>
			<div class="extra_info">
				<p class="ynjp_featureContent_industry">
					<i class="fa fa-folder-open"></i> &nbsp;
					{if isset($aCompany.industrial_phrase) && $aCompany.industrial_phrase!=""}{$aCompany.industrial_phrase}{else}{phrase var='n_a_industry'}{/if}
					</p>
				<p> 
					<i class="fa fa-users"></i> &nbsp;
					{if isset($aCompany.size_from) && (int)$aCompany.size_from > 0
						&& isset($aCompany.size_to) && (int)$aCompany.size_to > 0
					}
						{$aCompany.size_from}-{$aCompany.size_to} {phrase var='employees'}&nbsp;|&nbsp;
					{/if}
					{$aCompany.total_follow} {phrase var='followers'}
				</p>
				<p><i class="fa fa-map-marker"></i> &nbsp;{$aCompany.location}</p>	        
			</div>
            <!-- Follow/Favorite -->
            <div class="ynjp_link_action">
                {if isset($sView) && $sView=='favoritecompany'}
                <a href="#" onclick="$.ajaxCall('jobposting.unfavorite', 'type=company&id={$aCompany.company_id}'); return false;">{phrase var='unfavorite'}</a>
                {/if}
                
                {if isset($sView) && $sView=='followingcompany'}
                <a href="#" onclick="$.ajaxCall('jobposting.unfollow', 'type=company&id={$aCompany.company_id}'); return false;">{phrase var='unfollow'}</a>
                {/if}
            </div>
		</div>
	</div>
	<div class="clear"></div>
</div>
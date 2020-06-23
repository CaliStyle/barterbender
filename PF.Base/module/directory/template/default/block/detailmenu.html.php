<div class="sub-section-menu">
	<ul class="action">
		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
			&& Phpfox::getService('directory.helper')->isPhoto() && isset($aModuleView.photos) && $aModuleView.photos.is_show}
			<li {if $aModuleView.photos.active}class='active'{/if}>
				<a href="{$aModuleView.photos.link}" >
					<span class="yndirectory-textmenu">
						<span class="ico ico-photos-alt-o"></span>
						{$aModuleView.photos.module_phrase|convert}
					</span>
					<span class="yndirectory-numonmenu">{$aNumberOfItem.photos}</span>
				</a>				
			</li>
		{/if}

		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
		&& Phpfox::getService('directory.helper')->isVideoChannel() && isset($aModuleView.videos) && $aModuleView.videos.is_show}
		<li {if $aModuleView.videos.active}class='active'{/if}>
		<a href="{$aModuleView.videos.link}">
			<span class="yndirectory-textmenu">
				<span class="ico ico-video"></span>
				{$aModuleView.videos.module_phrase|convert}
			</span>
			<span class="yndirectory-numonmenu">{$aNumberOfItem.videos}</span>
		</a>
		</li>
		{/if}

		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
		&& Phpfox::getService('directory.helper')->isUltVideo() && isset($aModuleView.ultimatevideo) && $aModuleView.ultimatevideo.is_show}
		<li {if $aModuleView.ultimatevideo.active}class='active'{/if}>
		<a href="{$aModuleView.ultimatevideo.link}">
			<span class="yndirectory-textmenu">
				<span class="ico ico-video"></span>
				{$aModuleView.ultimatevideo.module_phrase|convert}
			</span>
			<span class="yndirectory-numonmenu">{$aNumberOfItem.ultimatevideo}</span>
		</a>
		</li>
		{/if}

        {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
		&& Phpfox::getService('directory.helper')->isVideo() && isset($aModuleView.v) && $aModuleView.v.is_show}
		<li {if $aModuleView.v.active}class='active'{/if}>
		<a href="{$aModuleView.v.link}">
			<span class="yndirectory-textmenu">
				<span class="ico ico-video"></span>
				{$aModuleView.v.module_phrase|convert}
			</span>
			<span class="yndirectory-numonmenu">{$aNumberOfItem.v}</span>
		</a>
		</li>
		{/if}

		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
			&& Phpfox::getService('directory.helper')->isMusic() && isset($aModuleView.musics) && $aModuleView.musics.is_show}
			<li {if $aModuleView.musics.active}class='active'{/if}>
				<a href="{$aModuleView.musics.link}" >
					<span class="yndirectory-textmenu">
						<span class="ico ico-music-note-o"></span>
						{$aModuleView.musics.module_phrase|convert}
					</span>
					<span class="yndirectory-numonmenu">{$aNumberOfItem.musics}</span>
				</a>				
			</li>
		{/if}
		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
			&& Phpfox::getService('directory.helper')->isBlog() && isset($aModuleView.blogs) && $aModuleView.blogs.is_show}
			<li {if $aModuleView.blogs.active}class='active'{/if}>
				<a href="{$aModuleView.blogs.link}">
					<span class="yndirectory-textmenu">
						<span class="ico ico-compose-alt"></span>
						{$aModuleView.blogs.module_phrase|convert} 
					</span>
					<span class="yndirectory-numonmenu">{$aNumberOfItem.blogs}</span>
				</a>				
			</li>
		{/if}
        {if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
			&& Phpfox::getService('directory.helper')->isAdvBlog() && isset($aModuleView.ynblog) && $aModuleView.ynblog.is_show}
			<li {if $aModuleView.ynblog.active}class='active'{/if}>
				<a href="{$aModuleView.ynblog.link}">
					<span class="yndirectory-textmenu">
						<span class="ico ico-compose-alt"></span>
						{$aModuleView.ynblog.module_phrase|convert}
					</span>
					<span class="yndirectory-numonmenu">{$aNumberOfItem.ynblog}</span>
				</a>
			</li>
		{/if}
		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
			&& Phpfox::getService('directory.helper')->isPoll() && isset($aModuleView.polls) && $aModuleView.polls.is_show}
			<li {if $aModuleView.polls.active}class='active'{/if}>
				<a href="{$aModuleView.polls.link}" >
					<span class="yndirectory-textmenu">
						<span class="ico ico-bar-chart2"></span>
						{$aModuleView.polls.module_phrase|convert} 
					</span>
					<span class="yndirectory-numonmenu">{$aNumberOfItem.polls}</span>
				</a>				
			</li>
		{/if}
		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
			&& Phpfox::getService('directory.helper')->isCoupon() && isset($aModuleView.coupons) && $aModuleView.coupons.is_show}
			<li {if $aModuleView.coupons.active}class='active'{/if}>
				<a href="{$aModuleView.coupons.link}" >
					<span class="yndirectory-textmenu">
						<span class="ico ico-box-o"></span>
						{$aModuleView.coupons.module_phrase|convert}
					</span>
					<span class="yndirectory-numonmenu">{$aNumberOfItem.coupons}</span>
				</a>				
			</li>
		{/if}

		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
			&& Phpfox::getService('directory.helper')->isEvent() && isset($aModuleView.events) && $aModuleView.events.is_show}
			<li {if $aModuleView.events.active}class='active'{/if}>
				<a href="{$aModuleView.events.link}">
					<span class="yndirectory-textmenu">
						<span class="ico ico-calendar-check-o"></span>
						{$aModuleView.events.module_phrase|convert}
					</span>
					<span class="yndirectory-numonmenu">{$aNumberOfItem.events}</span>
				</a>				
			</li>
		{/if}

		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
			&& Phpfox::getService('directory.helper')->isJob() && isset($aModuleView.jobs) && $aModuleView.jobs.is_show}
			<li {if $aModuleView.jobs.active}class='active'{/if}>
				<a href="{$aModuleView.jobs.link}">
					<span class="yndirectory-textmenu">
						<span class="ico ico-box-o"></span>
						{$aModuleView.jobs.module_phrase|convert}
					</span>
					<span class="yndirectory-numonmenu">{$aNumberOfItem.jobs}</span>
				</a>				
			</li>
		{/if}

		{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
			&& Phpfox::getService('directory.helper')->isMarketplace() && isset($aModuleView.marketplace) && $aModuleView.marketplace.is_show}
			<li {if $aModuleView.marketplace.active}class='active'{/if}>
				<a href="{$aModuleView.marketplace.link}">
					<span class="yndirectory-textmenu">
						<span class="ico ico-box-o"></span>
						{$aModuleView.marketplace.module_phrase|convert}
					</span>
					<span class="yndirectory-numonmenu">{$aNumberOfItem.marketplace}</span>
				</a>				
			</li>
		{/if}

		{foreach from=$aPagesModule item=aPage}
			{if ($aBusiness.business_status != (int)Phpfox::getService('directory.helper')->getConst('business.status.draft'))
				&& $aPage.module_type == 'contentpage'}
				<li {if $aPage.active}class='active'{/if}>
					<a href="{$aPage.link}" >
						<span class="yndirectory-textmenu"pan>
							<span class="ico ico-box-o"></span>
							{$aPage.module_phrase|convert}
						</span>
					</a>
				</li>
			{/if}

		{/foreach}

	</ul>
</div>
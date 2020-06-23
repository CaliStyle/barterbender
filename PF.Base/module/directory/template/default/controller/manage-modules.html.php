<div id='yndirectory_manage_modules'>	
	<form method="post" action="{url link='directory.manage-modules.id_'.$iBusinessid}" id="js_manage_modules" onsubmit="" enctype="multipart/form-data">

		<input type="hidden" name="val[business_id]" value="{$iBusinessid}" >

		<div id='yndirectory_modules' class="yndirectory-table">
			<div class="yndirectory-th">
				<span></span>
				<span>{phrase var='manage_modules'}</span>
				<span>{phrase var='action'}</span>
			</div>

			{if in_array('members',$aModules.0)}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/members.png" /></div>
					<div class="title">{phrase var='members_up'}</div>
					<div class="action">
						<div class="action-view">
							<a href="{$aModuleActions.members.view_link}">{phrase var='view_members'}</a>
						</div>
					</div>
				</div>
			{/if}
			{if in_array('photos',$aModules.0) && Phpfox::getService('directory.helper')->isPhoto()}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/photos.png" /></div>
					<div class="title">{phrase var='photos'}</div>
					<div class="action">
						<div class="action-add">
							{if $aModuleActions.photos.bCanAddPhotoInBusiness}
								<a href="javascript:void(0)" id="yndirectory_managemodules_photos" onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'photos'); return false;" data-link="{$aModuleActions.photos.add_link}" data-type="photos" data-businessid="{$iBusinessid}">{phrase var='add_photos'}</a>
							{/if}
						</div>
						<div class="action-view">
							<a href="{$aModuleActions.photos.view_link}">{phrase var='view_photos'}</a>
						</div>
					</div>
				</div>
			{/if}
			
			{if in_array('videos',$aModules.0) && Phpfox::getService('directory.helper')->isVideoChannel()}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/videos.png" /></div>
					<div class="title">{phrase var='video_channel'}</div>
					<div class="action">
						<div class="action-add">
							{if $aModuleActions.videos.bCanAddVideoInBusiness}
								<a href="javascript:void(0)" id="yndirectory_managemodules_videos" 
									onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'videos'); return false;"
									data-link="{$aModuleActions.videos.add_link}"
									data-type="videos" >{phrase var='add_videos'}</a>
							{/if}
						</div>
						<div class="action-view">
							<a href="{$aModuleActions.videos.view_link}">{phrase var='view_videos'}</a>
						</div>
					</div>
				</div>
			{/if}
			{if in_array('ultimatevideo',$aModules.0) && Phpfox::getService('directory.helper')->isUltVideo()}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/videos.png" /></div>
					<div class="title">{phrase var='ultimate_videos'}</div>
					<div class="action">
						<div class="action-add">
							{if $aModuleActions.videos.bCanAddVideoInBusiness}
								<a href="javascript:void(0)" id="yndirectory_managemodules_ultimatevideo" 
									onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'ultimatevideo'); return false;" 
									data-link="{$aModuleActions.ultimatevideo.add_link}" 
									data-type="ultimatevideo" >{phrase var='add_videos'}</a>
							{/if}
						</div>
						<div class="action-view">
							<a href="{$aModuleActions.ultimatevideo.view_link}">{phrase var='view_videos'}</a>
						</div>
					</div>
				</div>
			{/if}

            {if in_array('video',$aModules.0) && Phpfox::getService('directory.helper')->isVideo()}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/videos.png" /></div>
					<div class="title">{phrase var='videos'}</div>
					<div class="action">
						<div class="action-add">
							{if $aModuleActions.v.bCanAddVideoInBusiness}
								<a href="javascript:void(0)" id="yndirectory_managemodules_v"
									onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'v'); return false;"
									data-link="{$aModuleActions.v.add_link}"
									data-type="ultimatevideo" >{phrase var='add_videos'}</a>
							{/if}
						</div>
						<div class="action-view">
							<a href="{$aModuleActions.v.view_link}">{phrase var='view_videos'}</a>
						</div>
					</div>
				</div>
			{/if}
			
			{if in_array('musics',$aModules.0) && Phpfox::getService('directory.helper')->isMusic()}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/musics.png" /></div>
					<div class="title">{phrase var='musics'}</div>
					<div class="action">
						<div class="action-add">
							{if $aModuleActions.musics.bCanAddMusicInBusiness}
								<a href="javascript:void(0)" id="yndirectory_managemodules_musics" 
									onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'musics'); return false;" 
									data-link="{$aModuleActions.musics.add_link}" 
									data-type="musics" >{phrase var='add_musics'}</a>
							{/if}
						</div>
						<div class="action-view">
							<a href="{$aModuleActions.musics.view_link}">{phrase var='view_musics'}</a>
						</div>

					</div>
				</div>
			{/if}
			{if in_array('blogs',$aModules.0) && Phpfox::getService('directory.helper')->isBlog()}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/blogs.png" /></div>
					<div class="title">{phrase var='blogs'}</div>
					<div class="action">
									<div class="action-add">
							{if $aModuleActions.blogs.bCanAddBlogInBusiness}
								<a href="javascript:void(0)" id="yndirectory_managemodules_blogs" 
									onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'blogs'); return false;" 
									data-link="{$aModuleActions.blogs.add_link}" 
									data-type="blogs" >{phrase var='add_blogs'}</a>
							{/if}
						</div>
						<div class="action-view">
							<a href="{$aModuleActions.blogs.view_link}">{phrase var='view_blogs'}</a>
						</div>

					</div>
				</div>
			{/if}
            {if in_array('advanced-blog',$aModules.0) && Phpfox::getService('directory.helper')->isAdvBlog()}
            <div class="yndirectory-tr">
                <div class="module-image"><img src="{$core_path}module/directory/static/image/blogs.png" /></div>
                <div class="title">{phrase var='ynblog'}</div>
                <div class="action">
                    <div class="action-add">
                        {if $aModuleActions.ynblog.bCanAddYnBlogInBusiness}
                        <a href="javascript:void(0)" id="yndirectory_managemodules_ynblog"
                           onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'ynblog'); return false;"
                           data-link="{$aModuleActions.ynblog.add_link}"
                           data-type="ynblog" >{phrase var='add_ynblog'}</a>
                        {/if}
                    </div>
                    <div class="action-view">
                        <a href="{$aModuleActions.ynblog.view_link}">{phrase var='view_ynblog'}</a>
                    </div>

                </div>
            </div>
            {/if}
			{if in_array('polls',$aModules.0) && Phpfox::getService('directory.helper')->isBlog()}
			<div class="yndirectory-tr">
				<div class="module-image"><img src="{$core_path}module/directory/static/image/polls.png" /></div>
				<div class="title">{phrase var='polls'}</div>
				<div class="action">
					<div class="action-add">
						{if $aModuleActions.polls.bCanAddPollsInBusiness}
						<a href="javascript:void(0)" id="yndirectory_managemodules_polls"
						   onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'polls'); return false;"
						   data-link="{$aModuleActions.polls.add_link}"
						   data-type="polls" >{phrase var='add_polls'}</a>
						{/if}
					</div>
					<div class="action-view">
						<a href="{$aModuleActions.polls.view_link}">{phrase var='view_polls'}</a>
					</div>

				</div>
			</div>
			{/if}
			{if in_array('coupons',$aModules.0) && Phpfox::getService('directory.helper')->isCoupon()}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/coupons.png" /></div>
					<div class="title">{phrase var='coupons'}</div>
					<div class="action">
									<div class="action-add">
							{if $aModuleActions.coupons.bCanAddCouponInBusiness}
								<a href="javascript:void(0)" id="yndirectory_managemodules_coupons" 
									onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'coupons'); return false;" 
									data-link="{$aModuleActions.coupons.add_link}" 
									data-type="coupons" >{phrase var='add_coupons'}</a>
							{/if}
						</div>
						<div class="action-view">
							<a href="{$aModuleActions.coupons.view_link}">{phrase var='view_coupons'}</a>
						</div>

					</div>
				</div>
			{/if}
			{if in_array('events',$aModules.0) && Phpfox::getService('directory.helper')->isEvent()}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/events.png" /></div>
					<div class="title">{phrase var='events'}</div>
					<div class="action">
						<div class="action-add">
							{if $aModuleActions.events.bCanAddEventInBusiness}
								<a href="javascript:void(0)" id="yndirectory_managemodules_events" 
									onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'events'); return false;" 
									data-link="{$aModuleActions.events.add_link}" 
									data-type="events" >{phrase var='add_events'}</a>
							{/if}
						</div>
						<div class="action-view">
							<a href="{$aModuleActions.events.view_link}">{phrase var='view_events'}</a>
						</div>

					</div>
				</div>
			{/if}
			{if in_array('jobs',$aModules.0) && Phpfox::getService('directory.helper')->isJob()}
				<div class="yndirectory-tr">
					<div class="module-image"><img src="{$core_path}module/directory/static/image/jobs.png" /></div>
					<div class="title">{phrase var='jobs'}</div>
					<div class="action">
									<div class="action-add">
							{if $aModuleActions.jobs.bCanAddJobInBusiness}
								<a href="javascript:void(0)" id="yndirectory_managemodules_jobs" 
									onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'jobs'); return false;" 
									data-link="{$aModuleActions.jobs.add_link}" 
									data-type="jobs" >{phrase var='add_job_posting'}</a>
							{/if}
						</div>
						<div class="action-view">
							<a href="{$aModuleActions.jobs.view_link}">{phrase var='view_job_posting'}</a>
						</div>

					</div>
				</div>
			{/if}
			{if in_array('marketplace',$aModules.0) && Phpfox::getService('directory.helper')->isMarketplace()}
			<div class="yndirectory-tr">
				<div class="module-image"><img src="{$core_path}module/directory/static/image/marketplaces.png" /></div>
				<div class="title">{phrase var='marketplace'}</div>
				<div class="action">
					<div class="action-add">
						{if $aModuleActions.marketplace.bCanAddMarketplaceInBusiness}
						<a href="javascript:void(0)" id="yndirectory_managemodules_marketplace"
						   onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'marketplace'); return false;"
						   data-link="{$aModuleActions.marketplace.add_link}"
						   data-type="marketplace" >{phrase var='add_listing'}</a>
						{/if}
					</div>
					<div class="action-view">
						<a href="{$aModuleActions.marketplace.view_link}">{phrase var='view_listing'}</a>
					</div>
				</div>
			</div>
			{/if}
            {if in_array('v',$aModules.0) && Phpfox::getService('directory.helper')->isVideo()}
			<div class="yndirectory-tr">
				<div class="module-image"><img src="{$core_path}module/directory/static/image/videos.png" /></div>
				<div class="title">{phrase var='video'}</div>
				<div class="action">
					<div class="action-add">
						{if $aModuleActions.v.bCanAddVideoInBusiness}
						<a href="javascript:void(0)" id="yndirectory_managemodules_v"
						   onclick="yndirectory.addAjaxForCreateNewItemInDashboard({$iBusinessid}, 'v'); return false;"
						   data-link="{$aModuleActions.v.add_link}"
						   data-type="v" >{phrase var='add_videos'}</a>
						{/if}
					</div>
					<div class="action-view">
						<a href="{$aModuleActions.v.view_link}">{phrase var='view_listing'}</a>
					</div>
				</div>
			</div>
			{/if}
		</div>
	</form>
</div>

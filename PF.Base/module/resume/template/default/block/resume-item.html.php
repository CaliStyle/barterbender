<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 *
 */
?>
{literal}
<style type="text/css">

</style>
{/literal}
<!-- Insert layout from here -->
<div class="resume_item clearfix

">
	<!-- Resume thumbnail image -->
	<div class="resume_item_left">
		<a href="{permalink module='resume.view'
		id=$aResume.resume_id title=$aResume.headline}">
			{if $aResume.image_path!=""}
				{img server_id=$aResume.server_id
			path='core.url_pic'
			file='resume/'.$aResume.image_path suffix='_200'
			max_width='120'
			max_height='120'}
			{else}
				<img class="default_resume_image"
					 src="{$sCorePath}module/resume/static/image/profile.png"
					 />
			{/if}
		</a>
	</div>
	<!-- Resume content summary -->
	<div class="resume_item_right">
		<!-- title -->
		<h4>
			<a href="{permalink module='resume.view' id=$aResume.resume_id title=$aResume.headline}">
				<strong>
					{if isset($aResume.resume_full_name) && $aResume.resume_full_name}
						{$aResume.resume_full_name|shorten:50:"...":false}
					{elseif $aResume.full_name}
					    {$aResume.full_name|shorten:50:"...":false}
					{else}
						{phrase var="resume.resume_headline"}
					{/if}
				</strong>
			</a>
			{if $aResume.is_viewed and $aResume.time_update > $aResume.time_view}
				<span class="yns-item fa yns-reload" title="{_p var='updated'}">{phrase var="resume.updated"}</span>
			{else}
				{if $aResume.is_viewed==1}
				<span class="yns-item fa yns-search" title="{_p var='viewed'}">{phrase var="resume.viewed"}</span>
				{/if}
			{/if}
			{if $aResume.sent_messages > 0 }
				<span class="yns-item fa yns-mail" title="{_p var='contacted'}">{phrase var="resume.contacted"}</span>
			{/if}
			{if $aResume.noted != "" }
				{literal}
					<script style="text/javascript">


							$Behavior.loadNote{/literal}{$aResume.resume_id}{literal} = function(){
							var abc="{/literal}<?php echo str_replace(array("\n", "\r",'"'),array(" ", " ",'\"'), $this->_aVars['aResume']['noted']); ?>{literal}";
							$('#note_{/literal}{$aResume.resume_id}{literal}').aToolTip({
								clickIt: true,
								tipContent: abc
							});
							};


					</script>
				{/literal}
				<a href="javascript:void(0);" class="yns-item yns-note dont-unbind" id="note_{$aResume.resume_id}">{phrase var="resume.note"}</a>
			{/if}
		</h4>
		<h4>
			<a href="{permalink module='resume.view' id=$aResume.resume_id title=$aResume.headline}">
				<strong>{$aResume.headline}</strong>
			</a>
		</h4>
		<!-- creation/updated date - views - favorites -->
		<div class="yns-res-info">
			<p>{phrase var ="resume.updated"}: {$aResume.time_update|date:'core.global_update_time'} - {$aResume.total_favorite} {phrase var="resume.favorites"}</p>
		</div>
		<!-- Categories -->
		<div class="yns-res-info">
			<p>{phrase var ="resume.categories"}:
				{if $aResume.categories}
					{$aResume.categories}
				{else}
					{phrase var="resume.not_selected"}
				{/if}
			</p>
		</div>
		<!-- Note Information -->
		{if $sView == "noted"}
		<div class="yns-res-info">
			<div>
				<strong>
					{phrase var="resume.note"}:
				</strong>
				<a href="javascript:void(0);" onclick="$Core.box('resume.editNote',500,'resume_id={$aResume.resume_id}&user_id={$aResume.user_id}')">{phrase var="resume.edit"}</a>
			</div>
			<div class="yns-note-content">
				{$aResume.note|parse}
			</div>
		</div>
		{/if}
		{if $sView == 'favorite'}
			<a class="yns-viewall yns-item yns-un-fav" href="javascript:void(0);" onclick="FavoriteAction('unfavorite',{$aResume.resume_id},'my'); return false;" id="js_favorite_link_unlike_{$aResume.resume_id}">{_p var='unfavorite'}</a>
		{elseif $sView == 'pending'}
		<div class="yns-viewall">
			<a class="button btn btn-primary btn-sm"  href="javascript:void(0);" onclick="return approveResume('{$aResume.resume_id}');">{_p var='approve'}</a>
							
			<a class="button btn btn-danger btn-sm"  href="javascript:void(0);" onclick="return denyResume('{$aResume.resume_id}');">{_p var='deny'}</a>
		</div>			
		{else}
			<a class="yns-viewall button btn btn-success btn-sm" href="{permalink module='resume.view' id=$aResume.resume_id title=$aResume.headline}">{phrase var="resume.view_detail"}</a>
		{/if}
	</div>
	<div class="clear"></div>
</div>


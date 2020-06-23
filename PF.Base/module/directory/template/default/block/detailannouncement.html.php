{if $iCnt > 0}
	<div class="yndirectory_annoucement_group">
		{foreach from=$aAnnoucements key=iAnnoucement item=aAnnouncement}
			<div id="yndirectory_annoucement_{$iAnnoucement}" 
				class="yndirectory-annoucement" 
			{if $iAnnoucement != 0}
				style="display:none;" 
			{/if}
			>
				<div class="yndirectory-annoucement-main">
					<div>
						<div class="yndirectory-annoucement-title">{$aAnnouncement.announcement_title|parse}</div>
						<div class="yndirectory-annoucement-more" onclick="yndirectory.showAnnouncement(this, {$aAnnouncement.announcement_id}); return false;"><img src="{$core_path}module/directory/static/image/icon-detail-about-us.png"></div>
					</div>
					<div class="yndirectory-annoucement-content">{$aAnnouncement.announcement_content_parse|parse}</div>
				</div>
				<div class="yndirectory-annoucement-footer">
					<div class="annoucement-footer-left"><span>{$aAnnouncement.current_text}</span>/{$iCnt}</div>
					<div class="annoucement-footer-right">
						<a onclick="changeAnnoucement({$aAnnouncement.prev})" {$aAnnouncement.prev}><i class="fa fa-chevron-left"></i></a>
						<a onclick="changeAnnoucement({$aAnnouncement.next})" ><i class="fa fa-chevron-right"></i></a>
					</div>
					<a onclick="$.ajaxCall('directory.markAsRead', 'item_id={$aAnnouncement.announcement_id}'); return false;">{phrase var='mark_as_read'}</a>
					
				</div>
			</div>
		{/foreach}

		{literal}
			<script type="text/javascript">
				function changeAnnoucement(id){
					$('.yndirectory-annoucement').hide();
					$('#yndirectory_annoucement_'+id).show();
				}
					
				$Behavior.changeAnnoucement = function(){

				}
			</script>
		{/literal}
	</div>
{/if}

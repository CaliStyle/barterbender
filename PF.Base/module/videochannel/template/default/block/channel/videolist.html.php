{if !isset($aVideos) || (isset($aVideos) && count($aVideos) == 0)}
   {phrase var='videochannel.no_videos_found'}
{else}   
   
	<select name="video[]" id="video_select_box" multiple="multiple">
	{foreach from=$aVideos key=iCount item=aVideo}
	      <option value="{$aVideo.url}" id="video_{$aVideo.video_id}">video_{$aVideo.video_id}</option>
   	{/foreach}
   	</select>   
   
   {foreach from=$aVideos key=iCount item=aVideo}
   {if $iCount % $iLimit == 0}
   <ul class="channel_add_video_list {if $iCount == 0} active{/if}" id="js_channel_video_list_{$iCount+1}">
   {/if}
      <li class="video_entry" id="js_video_id_{$aVideo.video_id}">
         <div class="image_hover_holder" style="width: 120px">            
            {if !(isset($act) && $act == 'no')}

            <div class="_moderator">
               <a rel="video_" class="moderate_link built"
                  onclick="selectVideo(this,{$aVideo.video_id}); return false;"
                  href="#"><i class="fa"></i></a>
            </div>


            {/if}
            
            {if isset($act) && $act=='no'}
            {if ((Phpfox::getUserParam('videochannel.can_delete_own_video') && $aVideo.user_id == Phpfox::getUserId()) || (Phpfox::getUserParam('videochannel.can_delete_other_video') && $aVideo.user_id != Phpfox::getUserId()))
                  || (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))
                  }                  
            <a href="#" class="image_hover_menu_link">{phrase var='videochannel.link'}</a>
            <div class="image_hover_menu">
                  <ul>                  
                        <li class="item_delete"><a href="#" title="{phrase var='videochannel.delete_this_video'}" onclick="if (confirm('{phrase var='videochannel.are_you_sure' phpfox_squote=true}')) $.ajaxCall('videochannel.delete', 'video_id={$aVideo.video_id}'); return false;">{phrase var='videochannel.delete'}</a></li>
                  </ul>
            </div>
            {/if}
            {/if}
            {if !empty($aVideo.image_path)}
               <img width="120" height="90" alt="{$aVideo.title}" src="{$aVideo.image_path}"/>
            {else}
               {img theme='noimage/item.png'}
            {/if}
            {if $aVideo.duration != ""}<div class="video_duration">{$aVideo.duration}</div>{/if}
         </div>
         <div class="video_title">
            <a title="{$aVideo.title|clean|shorten:50}" href="{$aVideo.url}" target="_blank">
               {$aVideo.title}
            </a>
         </div>
      </li>
   {if ($iCount + 1) % $iLimit == 0 || ($iCount + 1) == count($aVideos)}
   </ul>
   {/if}
   {/foreach}      
   <div class="clear"></div>
   <ul class="pager">
      <li>{phrase var='videochannel.total_about_x_videos' iCount=$iVideoCount}&nbsp; &nbsp;</li>
      {if count($aVideos) > $iLimit}
      <li id="prev" class="first" style="display: none">
         <a href="javascript:void(0);" onclick="prevVideoList(this,{$iLimit}); return false;">
            {phrase var='core.previous'}         
         </a>
      </li>
      <li id="next" class="first">
         <a href="javascript:void(0);" onclick="nextVideoList(this,{$iLimit}); return false;">
            {phrase var='core.next'}         
         </a>
      </li>
      {/if}
   </ul>   
{/if}

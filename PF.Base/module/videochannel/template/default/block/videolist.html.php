{foreach from=$aVideos key=iCount item=aVideo}
{if $iCount % $iLimit == 0}
<ul class="channel_add_video_list {if $iCount == 0} active{/if}" id="js_channel_video_list_{$iCount+1}">
{/if}
   <li class="video_entry" {if ($iCount + 1) % 4 == 0} style="margin-right: 0" {/if} >
      <div class="image_hover_holder">
         <input type="hidden" name="video[{$aVideo.video_id}]" value="{$aVideo.url}" id="video_{$aVideo.video_id}" disabled="disabled" />
         <div class="video_moderate_link">
            <a class="moderate_link" href="javascript:void(0);" onclick="selectVideo(this,{$aVideo.video_id}); return false;">Moderate</a>
         </div>
         <img lass="js_mp_fix_width photo_holder" width="120" height="90" alt="{$aVideo.title}" src="{$aVideo.image_path}"></a>
      </div>
   </li>
{if ($iCount + 1) % $iLimit == 0 || ($iCount + 1) == count($aVideos)}
</ul>
{/if}
{/foreach}
{if count($aVideos) > 8}
<ul class="pager">
   <li id="prev" class="first" style="display: none">
      <a href="javascript:void(0);" onclick="prevVideoList(this,{$iLimit}); return false;">
         {phrase var='core.previous'}         
      </a>
   </li>
   <li id="next">
      <a href="javascript:void(0);" onclick="nextVideoList(this,{$iLimit}); return false;">
         {phrase var='core.next'}         
      </a>
   </li>
</ul>
{/if}
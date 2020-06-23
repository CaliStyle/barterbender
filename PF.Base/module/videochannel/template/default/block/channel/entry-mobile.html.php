<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_channel_entry_{$channel.channel_id}" class="channel_entry mobile-video-entry">
   <div class='post_info' style="display: none">
      <div class="en_img">{$channel.en_video_image}</div>            
      <div class="en_url">{$channel.en_url}</div>            
      <div class="en_title">{$channel.en_title}</div>            
      <div class="en_summary">{$channel.en_summary}</div>            
   </div>
   <div class="{if $count % 2 == 0} row2 {else} row1 {/if}  {if $count == 0}row_first{/if}">
      <div class="row_title">
         <div class="row_title_image">            
               {if isset($channel.isExist)}
                  {if (Phpfox::getUserParam('videochannel.can_add_channels') && !isset($sSubmitUrl)) || ($bCanAddChannelInPage)}
                  <div class="video_moderate_link">
                     <a href="#{$channel.isExist}" class="moderate_link" rel="videochannel">{phrase var='videochannel.moderate'}</a>
                  </div>				
                  {/if}
                  <a title="{$channel.title}" href="{$channel.link}">
               {else}
                  <a title="{$channel.title}" href="{$channel.link}" target="_blank">
               {/if}            
               {if !empty($channel.video_image)}               
               <img class="channel_image" src="{$channel.video_image}" alt="{$channel.title}" height="90" width="120"/>
               {else}
               {img theme='noimage/item.png'}
               {/if}
            </a>            
         </div>
         <div class="row_title_info">
            <span style="font-size: 12px; font-weight: bold;">
               {if isset($channel.isExist)}
               <a  class="channel_title" title="{$channel.title|clean}" href="{$channel.link|clean}">{$channel.title|clean|shorten:50:'...'|split:20}</a>
               {else}
               <a  class="channel_title" title="{$channel.title|clean}" href="{$channel.link|clean}" target="_blank">{$channel.title}</a>
               {/if}
            </span>  
			{if Phpfox::getUserParam('videochannel.can_add_channels') || ($bCanAddChannelInPage)}  
            <div class="chanel_action">               		   
               {if  isset($channel.isExist)}
                  {if isset($sSubmitUrl)}
                  <span id="highlight_{$channel.isExist}" class="highlight" {if isset($channel.isBrowse) &&  $channel.isBrowse == true} style="display:none" {/if} />{phrase var='videochannel.this_channel_is_already_added'}</span>
                  {/if}                  
                  {if !isset($sSubmitUrl)}                  
                  <div id="js_channel_processing_{$channel.isExist}" class="channel_processing">                  
                     {img theme='ajax/small.gif' id='channel_loading' align='middle' style='margin-right: 10px;'}
                     {phrase var='videochannel.processing'}
                  </div>
                     
                  <div class="item_bar">
                     <div class="item_bar_action_holder">
                        <a role="button" data-toggle="dropdown" class="item_bar_action"><span>{phrase var='videochannel.actions'}</span></a>
                        <ul class="dropdown-menu">
                           <li><a id="js_channel_add_more_{$channel.isExist}" onclick="return autoUpdate({$channel.isExist}, '{$sModuleId}', {$iItem});" href="javascript:void(0)">{phrase var='videochannel.auto_update'}</a></li>
                           <li><a id="js_channel_add_more_{$channel.isExist}" onclick="return editChannel(this,{$channel.isExist},'yes', '{$sModuleId}', {$iItem});" href="javascript:void(0)">{phrase var='videochannel.add_more_videos'}</a></li>
                           <li><a id="js_channel_edit_{$channel.isExist}" onclick="return editChannel(this,{$channel.isExist},'no', '{$sModuleId}', {$iItem});" href="javascript:void(0)">{phrase var='videochannel.edit'}</a></li>
                           <li><a id="js_channel_delete_{$channel.isExist}" onclick="if (confirm('{phrase var='videochannel.are_you_sure' phpfox_squote=true}')) return deleteChannel({$channel.isExist}, '{$sModuleId}', {$iItem});" href="javascript:void(0)">{phrase var='videochannel.delete'}</a></li>
                        </ul>
                     </div>
                  </div>
                  {else}                  
                     <div class="item_bar">                     
                        <ul>                           
                           <li>
                              <div id="js_channel_processing_{$channel.isExist}" class="channel_processing" style="margin-right: 10px">
                                 {img theme='ajax/small.gif' id='channel_loading' align='middle' style='margin-right: 10px;'}
                                 {phrase var='videochannel.processing'}
                              </div>
                           </li>
                           <li><input type="button" class="button btn-primary" id="js_channel_add_more_{$channel.isExist}" onclick="return autoUpdate({$channel.isExist}, '{$sModule}', {$iItem});" value="{phrase var='videochannel.auto_update'}"/></li>
                           <li><input type="button" class="button btn-default" class="moderation_action" id="js_channel_add_more_{$channel.isExist}" onclick="return editChannel(this,{$channel.isExist},'yes', '{$sModule}', {$iItem});" value="{phrase var='videochannel.add_more_videos'}"/></li>
                        </ul>
                     </div>                  
                  {/if}
               {else}               
                  <div class="item_bar">                     
                        <ul>
                           <li>
                              <div id="js_channel_processing_add_{$channel.channel_id}" class="channel_processing" style="margin-right: 10px">
                                 {img theme='ajax/small.gif' id='channel_loading' align='middle' style='margin-right: 10px;'}
                                 {phrase var='videochannel.processing'}
                              </div>
                           </li>
                           <li><input type="button" class="button btn-danger" class="moderation_action" id="js_channel_add" name="add_channel" onclick="return addChannel(this,{$channel.channel_id}, '{$sModule}', {$iItem});" value="{phrase var='core.add'}"/></li>
                        </ul>                     
                  </div>
               {/if}
            </div>
            {/if}
            <div class="channel_description">
               <div class="extra_info">
                  {$channel.summary|clean|shorten:300:'...'|split:20}
               </div>
            </div>
            
         </div>
      </div>
   </div>
</div>

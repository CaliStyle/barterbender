<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<form id="channel_add" method="post" action="#"
      onsubmit="$(this).ajaxCall('videochannel.channel.saveChannel','id={$aForms.channel_id}'); $('#img_action').show(); $('.btn_submit').hide(); return false;">
    <div class="channel_edit_row">
        <!-- Channel Information -->
        <div id="channel_info" class="clearfix" {if $act==
        "yes"} style="display: none" {/if} >
        <div class="t_center">
            {if !empty($aForms.img)}
                <img width="120" class="js_mp_fix_width photo_holder" alt="{$aForms.title}" src="{$aForms.img}"/>
            {else}
                <img width="120" class="js_mp_fix_width photo_holder" alt="" src="{$no_image}"/>
            {/if}
        </div>


        {if ($sModule != 'pages')}
            {if isset($aForms) && $aForms.channel_id > 0}
                {if Phpfox::isModule('privacy')}
                <div id="js_custom_privacy_input_holder">
                    {module name='privacy.build' privacy_item_id=$aForms.channel_id privacy_module_id='videochannel_channel'}
                </div>
                <div class="form-group">
                    <label for="">{phrase var='videochannel.privacy'}:</label>
                    {if Phpfox::getUserParam('videochannel.can_set_allow_list_on_videos')}
                        {module name='privacy.form' privacy_item_id=$aForms.channel_id privacy_name='privacy' privacy_module_id='videochannel' privacy_info='videochannel.control_who_can_view_this_channel' default_privacy='videochannel.display_on_profile'}
                    {else}
                        {module name='privacy.form' privacy_item_id=$aForms.channel_id privacy_name='privacy' privacy_module_id='videochannel' privacy_info='videochannel.control_who_can_view_this_channel' default_privacy='videochannel.display_on_profile' privacy_no_custom=true}
                    {/if}
                </div>
                {/if}
            {else}
                {if Phpfox::isModule('privacy')}
                <div id="js_custom_privacy_input_holder">
                </div>
                <div class="form-group">
                    <label for=""> {phrase var='videochannel.privacy'}:</label>
                    {if Phpfox::getUserParam('videochannel.can_set_allow_list_on_videos')}
                        {module name='privacy.form' privacy_name='privacy' privacy_info='videochannel.control_who_can_view_this_channel'}
                    {else}
                        {module name='privacy.form' privacy_name='privacy' privacy_info='videochannel.control_who_can_view_this_channel' privacy_no_custom=true}
                    {/if}
                </div>
                {/if}
            {/if}
        {/if}


        <div class="channel_edit_info">
            <div><input type="hidden" name="val[site_id]" value="{$aForms.site_id}"/></div>
            <div><input type="hidden" name="val[url]" value="{$aForms.url}"/></div>
            <div class="form-group">
                <label for="">{required}{phrase var='videochannel.title'}:</label>
                <input class="form-control" type="text" value="{$aForms.title}" name="val[title]"/>
            </div>
            <div class="form-group">
                <label for="">{required}{phrase var='videochannel.category'}:</label>
                <div id="videochannel_section_category">
                    {$aForms.aCategories}
                </div>
            </div>
            <div class="form-group">
                <label for="">{phrase var='videochannel.summary'}:</label>
                <textarea class="form-control" cols="35" rows="4" name="val[description]">{$aForms.summary|convert}</textarea>
            </div>
        </div>
    </div>
    <!-- End Channel Information -->
    <div class="clear"></div>
    <!-- Videos list -->
    <div id="video_list_action" class="brd_bottom clear clearfix">
        <h1 style="float: left">{phrase var='videochannel.videos_list'}</h1>
        {if $act != 'no'}
        <a href="javascript:void(0);" class="selectall" onclick="selectAllVideo(this); return false;">{phrase
            var='core.select_all'}</a>
        <a href="javascript:void(0);" class="unselectall" onclick="selectAllVideo(this); return false;"
           style="display: none;">{phrase var='core.un_select_all'}</a>
        {/if}
    </div>
    <div class="form-group" id="channel_video_list">
        {if $act == 'no'}
        <script type="text/javascript"> activeId = 0; </script>
        {template file='videochannel.block.channel.videolist'}
        {else}
        {img theme='ajax/add.gif'}
        {/if}
    </div>
    <!-- End Videos list -->
    </div>
    <div class="clear"></div>
    {img theme='ajax/add.gif' id='img_action' style='display: none'}
    {if isset($sShowCategory)}
    {if $act == 'yes'}
    <script>loadVideoList("{$aForms.url_encode}");</script>
    {/if}
    <input id='js_channel_btn_update' class="button btn-primary" type="submit" name="val[action]"
           value="{phrase var='core.update'}"/>
    {if $act == 'no'}
    {if isset($aVideos) && count($aVideos) && (Phpfox::getUserParam('videochannel.can_delete_own_video') ||
    Phpfox::getUserParam('videochannel.can_delete_other_video'))}
    <input id='js_channel_btn_deleteall' class="button btn-primary" type="button"
           value="{phrase var='videochannel.delete_all'}"
           onclick="if(confirm('{phrase var='videochannel.delete_all_videos_belong_to_this_channel'}')) deleteAllVideos({$aForms.channel_id}); return false;"/>
    {/if}
    {/if}
    {$sShowCategory}
    {else}
    <input id='js_channel_btn_add' class="button btn-primary" type="submit" name="val[action]"
           value="{phrase var='core.add'}"/>
    <script>loadVideoList("{$aForms.url_encode}");</script>
    {/if}
    <div><input type="hidden" name="val[callback_module]" value="{$sModule}"/></div>
    <div><input type="hidden" name="val[callback_item_id]" value="{$iItem}"/></div>
    <div><input type="hidden" name="iIndex" value="{$iIndex}"/></div>
</form>
{if $sCategories}
<script type="text/javascript">
    $Behavior.EditCategory = function()
    {l}
        var aCategories = JSON.parse("[{$sCategories}]");
        var categorySection;

        for (var i = 0; i < aCategories.length; i++)
        {l}
            categorySection = $('#videochannel_section_category');
            $(categorySection).find('.js_mp_category_item_' + aCategories[i]).attr('selected', true);
            $(categorySection).find('#js_mp_holder_' + aCategories[i]).show();
        {r}
    {r}
</script>
{/if}
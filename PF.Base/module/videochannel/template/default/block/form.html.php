<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="form-group">
            <label for="">{required}{phrase var='videochannel.video_title'}:</label>
            <input class="form-control" type="text" name="val[title]" value="{value type='input' id='title'}" size="30" id="js_video_title" maxlength="200" />
        </div>

        {if (!isset($sModule) || $sModule == false) }
            <div class="form-group">
                <label for="category">{required}{phrase var='videochannel.category'}:</label>
                <div id="videochannel_section_category">
                    {$sCategories}
                </div>
            </div>
        {/if}

        <div class="form-group">
            <label for="">{phrase var='videochannel.description'}:</label>
            <textarea cols="40" rows="10" name="val[text]" class="js_edit_video_form form-control">{value id='text' type='textarea'}</textarea>
        </div>

        {if Phpfox::isModule('tag') && Phpfox::getUserParam('tag.can_add_tags_on_blogs')}
            {if isset($sModule) && $sModule != ''}
                {module name='tag.add' sType=video_group}
            {else}
                {module name='tag.add' sType=video}
            {/if}
        {/if}

        <div id="js_custom_privacy_input_holder">
            {if isset($aForms.video_id)}
                {module name='privacy.build' privacy_item_id=$aForms.video_id privacy_module_id='videochannel'}
            {/if}

        </div>
        {if isset($aForms) && $aForms.item_id > 0 && (!isset($sModule) || empty($sModule))}
            {if Phpfox::isModule('privacy')}
            <div class="form-group">
                <label for="">{phrase var='videochannel.privacy'}:</label>
                {if Phpfox::getUserParam('videochannel.can_set_allow_list_on_videos')}
                    {module name='privacy.form' privacy_item_id=$aForms.video_id privacy_name='privacy' privacy_module_id='videochannel' privacy_info='videochannel.control_who_can_view_this_channel' default_privacy='videochannel.display_on_profile'}
                {else}
                    {module name='privacy.form' privacy_item_id=$aForms.video_id privacy_name='privacy' privacy_module_id='videochannel' privacy_info='videochannel.control_who_can_view_this_channel' default_privacy='videochannel.display_on_profile' privacy_no_custom=true}
                {/if}
            </div>
            {/if}
        {else}
            {if Phpfox::isModule('privacy')}
                <div class="form-group">
                    <label for=""> {phrase var='videochannel.privacy'}:</label>
                    {if Phpfox::getUserParam('videochannel.can_set_allow_list_on_videos')}
                        {module name='privacy.form' privacy_name='privacy' privacy_info='videochannel.control_who_can_view_this_channel' default_privacy='videochannel.display_on_profile'}
                    {else}
                        {module name='privacy.form' privacy_name='privacy' privacy_info='videochannel.control_who_can_view_this_channel' default_privacy='videochannel.display_on_profile' privacy_no_custom=true}
                    {/if}
                </div>
            {/if}
        {/if}
    </div>
</div>